<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\StudentSubmission;
use App\Events\StudentSubmitted;
use Pusher\Pusher;

class StudentController extends Controller
{
    // 1. Màn hình Live (Máy chiếu)
    public function showLive()
    {
        // Lấy danh sách nộp kèm Tên Sinh Viên từ CSDL
        $submissions = StudentSubmission::latest()->get()->map(function ($item) {
            $student = Student::where('student_code', $item->student_code)->first();
            return [
                'student_code' => $item->student_code,
                'student_name' => $student ? $student->name : 'Sinh viên',
                'message'      => $item->message, // Lựa chọn đáp án
            ];
        });

        return view('live', compact('submissions'));
    }

    // 2. Màn hình Sinh viên nhập Mã SV
    public function showCheckForm()
    {
        return view('student.check');
    }

    public function processCheck(Request $request)
    {
        $request->validate(['student_id' => 'required|string']);
        $studentId = strtoupper(trim($request->student_id));

        // Tìm sinh viên trong CSDL
        $student = Student::where('student_code', $studentId)->first();

        // Nếu KHÔNG CÓ trong danh sách cho phép -> Báo lỗi ngay
        if (!$student) {
            return back()->withErrors(['student_id' => 'Mã sinh viên không có trong danh sách!']);
        }

        // Nếu CÓ trong danh sách -> Lưu thông tin vào Session
        session([
            'current_student_id'   => $student->student_code,
            'current_student_name' => $student->name
        ]);

        return redirect()->route('student.quiz');
    }

    // 3. Màn hình Trắc nghiệm
    public function showQuizForm()
    {
        $studentId = session('current_student_id');
        $studentName = session('current_student_name');

        if (!$studentId) {
            return redirect()->route('student.check');
        }

        return view('student.quiz', compact('studentId', 'studentName'));
    }

    // 4. Lưu câu trả lời & Bắn Pusher
    public function submitQuiz(Request $request)
    {
        $studentId = session('current_student_id');
        $studentName = session('current_student_name');

        if (!$studentId) {
            return redirect()->route('student.check');
        }

        $request->validate(['option' => 'required']);

        // 1. Lưu/cập nhật thông tin Sinh viên vào bảng `students` khi Submit
        Student::firstOrCreate(
            ['student_code' => $studentId],
            ['name'         => $studentName]
        );

        // 2. Lưu câu trả lời vào bảng `student_submissions`
        StudentSubmission::create([
            'student_code' => $studentId,
            'message'      => $request->option
        ]);

        // 3. Bắn event Pusher cập nhật màn hình Live
        event(new StudentSubmitted($studentId, $studentName, $request->option));

        return view('student.thanks', [
            'studentId'   => $studentId,
            'studentName' => $studentName
        ]);
    }

    // 5. Hàm Xóa / Reset Cây Tri Thức (Bảo vệ bằng Mã PIN từ config/quiz.php)
    public function resetTree(Request $request)
    {
        $inputPin = $request->input('pin');
        $correctPin = config('quiz.admin.pin') ?? config('quiz.admin.reset_pin', '654321');

        // 1. Kiểm tra mã PIN bảo vệ
        if ($inputPin !== $correctPin) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Mã PIN bảo vệ không chính xác!'
            ], 403);
        }

        // 2. Đúng PIN -> Truncate toàn bộ dữ liệu câu trả lời trong DB
        StudentSubmission::truncate();

        // 3. Phát thông báo Realtime Pusher để xóa màn hình Live tự động
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
            // Bỏ qua nếu môi trường dev chưa cấu hình Pusher
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Đã xóa sạch dữ liệu trên Cây Tri Thức!'
        ]);
    }

    // 6. Hàm Nạp / Cập nhật Danh sách Sinh viên từ JSON (Tương thích 100% với StudentSeeder)
    public function importStudents(Request $request)
    {
        $inputPin = $request->input('pin');
        $correctPin = config('quiz.admin.pin') ?? config('quiz.admin.reset_pin', '654321');

        if ($inputPin !== $correctPin) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Mã PIN bảo vệ không chính xác!'
            ], 403);
        }

        $rawPayload = $request->input('students_json') ?? $request->getContent();
        $studentsData = is_array($rawPayload) ? $rawPayload : json_decode($rawPayload, true);

        if (empty($studentsData) || !is_array($studentsData)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Dữ liệu JSON rỗng hoặc không đúng định dạng!'
            ], 400);
        }

        // Hỗ trợ cả 2 trường hợp: Gửi 1 SV dạng Object {} hoặc gửi Mảng [{}]
        $items = isset($studentsData[0]) && is_array($studentsData[0]) ? $studentsData : [$studentsData];

        $importedCount = 0;

        foreach ($items as $item) {
            $code = strtoupper(trim($item['student_code'] ?? $item['mssv'] ?? $item['student_id'] ?? $item['ma_sv'] ?? ''));
            $name = trim($item['name'] ?? $item['student_name'] ?? $item['ten_sv'] ?? 'Sinh viên');

            if (!$code) continue;

            // Đồng bộ trực tiếp vào bảng `students` y hệt logic trong StudentSeeder
            Student::updateOrCreate(
                ['student_code' => $code],
                ['name'         => $name]
            );

            $importedCount++;
        }

        return response()->json([
            'status'  => 'success',
            'message' => "Đã nạp thành công {$importedCount} sinh viên vào danh sách!"
        ]);
    }
}