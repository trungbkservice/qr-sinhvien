<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StudentSubmission;
use App\Events\StudentSubmitted;
use Pusher\Pusher;

class StudentController extends Controller
{
    /**
     * Hàm đọc trực tiếp dữ liệu từ file storage/app/students.txt
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

    // 1. Màn hình Live (Máy chiếu)
    public function showLive()
    {
        $studentsMap = $this->getStudentsFromTextFile();

        // Lấy danh sách nộp từ CSDL và khớp với Tên Sinh Viên từ file text[cite: 8]
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

    // 2. Màn hình Sinh viên nhập Mã SV[cite: 8]
    public function showCheckForm()
    {
        return view('student.check');
    }

    public function processCheck(Request $request)
    {
        $request->validate(['student_id' => 'required|string']);
        $studentId = strtoupper(trim($request->student_id));

        $studentsMap = $this->getStudentsFromTextFile();

        // Kiểm tra xem MSSV có trong file text không
        if (!array_key_exists($studentId, $studentsMap)) {
            return back()->withInput()->withErrors(['student_id' => 'Mã sinh viên không có trong danh sách!']);
        }

        // Lưu thông tin vào Session
        session([
            'current_student_id'   => $studentId,
            'current_student_name' => $studentsMap[$studentId]
        ]);

        return redirect()->route('student.quiz');
    }

    // 3. Màn hình Trắc nghiệm[cite: 8]
    public function showQuizForm()
    {
        $studentId = session('current_student_id');
        $studentName = session('current_student_name');

        if (!$studentId) {
            return redirect()->route('student.check');
        }

        return view('student.quiz', compact('studentId', 'studentName'));
    }

    // 4. Lưu câu trả lời & BẮN PUSHER REALTIME (GIỮ NGUYÊN 100% CỦA BẠN)[cite: 8]
    public function submitQuiz(Request $request)
    {
        $studentId = session('current_student_id');
        $studentName = session('current_student_name');

        if (!$studentId) {
            return redirect()->route('student.check');
        }

        $request->validate(['option' => 'required']);

        // 1. Lưu câu trả lời vào CSDL[cite: 8]
        StudentSubmission::create([
            'student_code' => $studentId,
            'message'      => $request->option
        ]);

        // 2. ⚡ BẮN EVENT PUSHER CẬP NHẬT TRỰC TIẾP MÀN HÌNH /LIVE (Lệnh gốc của bạn)[cite: 8]
        event(new StudentSubmitted($studentId, $studentName, $request->option));

        return view('student.thanks', [
            'studentId'   => $studentId,
            'studentName' => $studentName
        ]);
    }

    // 5. Hàm Xóa / Reset Cây Tri Thức (Yêu cầu PIN từ config/quiz.php)[cite: 8]
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

        // Xóa bài nộp trong DB[cite: 8]
        StudentSubmission::truncate();

        // Bắn tín hiệu xóa bông hoa Realtime qua Pusher[cite: 8]
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

    // 6. Hàm Lưu File students.txt Từ Trang Admin (Yêu cầu PIN)
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
}