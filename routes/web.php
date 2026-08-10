<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;

// 1. Màn hình Live (Máy chiếu)
Route::get('/', [StudentController::class, 'showLive']);
Route::get('/live', [StudentController::class, 'showLive']);

// 2. Luồng Sinh viên quét QR làm bài
// Bước 1: Nhập mã sinh viên
Route::get('/form', [StudentController::class, 'showCheckForm'])->name('student.check');
Route::post('/check-student', [StudentController::class, 'processCheck'])->name('student.process_check');

// Bước 2: Chọn đáp án / Gửi câu trả lời
Route::get('/quiz', [StudentController::class, 'showQuizForm'])->name('student.quiz');
Route::post('/submit-quiz', [StudentController::class, 'submitQuiz'])->name('student.submit_quiz');

// 3. Màn hình Quản trị Admin riêng biệt
Route::get('/admin', function () {
    return view('admin');
})->name('admin.dashboard');

// Route xử lý Xóa cây & Nạp danh sách Sinh viên
Route::post('/admin/reset-tree', [StudentController::class, 'resetTree'])->name('admin.reset_tree');
Route::post('/admin/import-students', [StudentController::class, 'importStudents'])->name('admin.import_students');