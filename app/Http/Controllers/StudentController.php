<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StudentSubmission;
use App\Events\StudentSubmitted;
use Pusher\Pusher;

class StudentController extends Controller
{
    /**
     * 1. Đọc danh sách sinh viên từ file storage/app/students.txt
     */
    private function getStudentsFromTextFile(): array
    {
        $filePath = storage_path('app/students.txt');
        if (!file_exists($filePath)) {
            return [];
        }

        $content = file_get_contents($filePath);
        $lines = explode("\n", str_replace("\r", "", $content));
        $students = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            $parts = explode('|', $line);
            $code = strtoupper(trim($parts[0] ?? ''));
            $name = trim($parts[1] ?? 'Sinh viên');

            if ($code !== '') {
                $students[$code] = $name;
            }
        }

        return $students;
    }

    /**
     * 2. Đọc Cấu hình Câu hỏi & Đáp án từ storage/app/quiz.txt (Fallback về config nếu chưa có file)
     */
    private function getQuizFromTextFile(): array
    {
        $filePath = storage_path('app/quiz.txt');
        
        if (!file_exists($filePath)) {
            return [
                'question_title' => config('quiz.quiz.question_title'),
                'options'        => config('quiz.quiz.options'),
            ];
        }

        $content = file_get_contents($filePath);
        $lines = explode("\n", str_replace("\r", "", $content));
        
        $questionTitle = '';
        $options = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            $parts = explode('|', $line, 2);
            $type  = strtoupper(trim($parts[0] ?? ''));
            $value = trim($parts[1] ?? '');

            if ($type === 'QUESTION') {
                $questionTitle = $value;
            } elseif ($type === 'OPTION' && $value !== '') {
                $options[] = $value;
            }
        }

        return [
            'question_title' => $questionTitle ?: config('quiz.quiz.question_title'),
            'options'        => !empty($options) ? $options : config('quiz.quiz.options'),
        ];
    }

    // 1. Màn hình Live (Máy chiếu)[cite: 5]
    public function showLive()
    {
        $studentsMap = $this->getStudentsFromTextFile();

        $submissions = StudentSubmission::latest()->get()->map(function ($item) use ($studentsMap) {
            $code = strtoupper(trim($item->student_code));
            return [
                'student_code' => $item->student_code,
                'student_name' => $studentsMap[$code] ?? 'Sinh viên',
                'message'      => $item->message,
            ];
        });

        return view('live', compact('submissions'));
    }

    // 2. Màn hình Sinh viên nhập Mã SV[cite: 5]
    public function showCheckForm()
    {
        return view('student.check');
    }

    public function processCheck(Request $request)
    {
        $request->validate(['student_id' => 'required|string']);
        $studentId = strtoupper(trim($request->student_id));

        $studentsMap = $this->getStudentsFromTextFile();

        if (!array_key_exists($studentId, $studentsMap)) {
            return back()->withInput()->withErrors(['student_id' => 'Mã sinh viên không có trong danh sách!']);
        }

        session([
            'current_student_id'   => $studentId,
            'current_student_name' => $studentsMap[$studentId]
        ]);

        return redirect()->route('student.quiz');
    }

    // 3. Màn hình Trắc nghiệm (Đọc dữ liệu linh hoạt từ quiz.txt)[cite: 5]
    public function showQuizForm()
    {
        $studentId = session('current_student_id');
        $studentName = session('current_student_name');

        if (!$studentId) {
            return redirect()->route('student.check');
        }

        $quizData = $this->getQuizFromTextFile();

        return view('student.quiz', [
            'studentId'     => $studentId,
            'studentName'   => $studentName,
            'questionTitle' => $quizData['question_title'],
            'options'       => $quizData['options']
        ]);
    }

    // 4. Lưu câu trả lời & BẮN PUSHER REALTIME[cite: 5]
    public function submitQuiz(Request $request)
    {
        $studentId = session('current_student_id');
        $studentName = session('current_student_name');

        if (!$studentId) {
            return redirect()->route('student.check');
        }

        $request->validate(['option' => 'required']);

        StudentSubmission::create([
            'student_code' => $studentId,
            'message'      => $request->option
        ]);

        event(new StudentSubmitted($studentId, $studentName, $request->option));

        return view('student.thanks', [
            'studentId'   => $studentId,
            'studentName' => $studentName
        ]);
    }

    // 5. Hàm Xóa / Reset Cây Tri Thức[cite: 5]
    public function resetTree(Request $request)
    {
        $inputPin = $request->input('pin');
        $correctPin = config('quiz.admin.pin') ?? config('quiz.admin.reset_pin', '654321');

        if ($inputPin !== $correctPin) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Mã PIN bảo vệ không chính xác!'
            ], 403);
        }

        StudentSubmission::truncate();

        try {
            $options = [
                'cluster' => env('PUSHER_APP_CLUSTER'),
                'useTLS'  => true
            ];
            $pusher = new Pusher(
                env('PUSHER_APP_KEY'),
                env('PUSHER_APP_SECRET'),
                env('PUSHER_APP_ID'),
                $options
            );
            $pusher->trigger('quiz-channel', 'tree-reset', ['status' => 'cleared']);
        } catch (\Exception $e) {
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Đã xóa sạch dữ liệu trên Cây Tri Thức!'
        ]);
    }

    // 6. Hàm Lưu File students.txt Từ Trang Admin[cite: 5]
    public function saveStudentsText(Request $request)
    {
        $inputPin = $request->input('pin');
        $correctPin = config('quiz.admin.pin') ?? config('quiz.admin.reset_pin', '654321');

        if ($inputPin !== $correctPin) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Mã PIN bảo vệ không chính xác!'
            ], 403);
        }

        $content = $request->input('students_text', '');
        $filePath = storage_path('app/students.txt');

        file_put_contents($filePath, $content);

        return response()->json([
            'status'  => 'success',
            'message' => 'Đã lưu danh sách sinh viên vào file thành công!'
        ]);
    }

    // 7. Hàm Lưu File quiz.txt Từ Trang Admin (QUESTION|... và OPTION|...)
    public function saveQuizText(Request $request)
    {
        $inputPin = $request->input('pin');
        $correctPin = config('quiz.admin.pin') ?? config('quiz.admin.reset_pin', '654321');

        if ($inputPin !== $correctPin) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Mã PIN bảo vệ không chính xác!'
            ], 403);
        }

        $questionTitle = trim($request->input('question_title', ''));
        $optionsRaw    = $request->input('options_text', '');

        $lines = explode("\n", str_replace("\r", "", $optionsRaw));
        
        $content = "QUESTION|" . $questionTitle . "\n";
        foreach ($lines as $line) {
            $line = trim($line);
            if (!empty($line)) {
                $content .= "OPTION|" . $line . "\n";
            }
        }

        $filePath = storage_path('app/quiz.txt');
        file_put_contents($filePath, $content);

        return response()->json([
            'status'  => 'success',
            'message' => 'Đã lưu câu hỏi và danh sách đáp án vào file quiz.txt thành công!'
        ]);
    }
}