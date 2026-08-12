<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;

// 1. Màn hình Live (Máy chiếu)[cite: 11]
Route::get('/', [StudentController::class, 'showLive']);
Route::get('/live', [StudentController::class, 'showLive']);

// 2. Luồng Sinh viên[cite: 11]
Route::get('/form', [StudentController::class, 'showCheckForm'])->name('student.check');
Route::post('/check-student', [StudentController::class, 'processCheck'])->name('student.process_check');

Route::get('/quiz', [StudentController::class, 'showQuizForm'])->name('student.quiz');
Route::post('/submit-quiz', [StudentController::class, 'submitQuiz'])->name('student.submit_quiz');

// 3. Màn hình Admin (Mở sẵn file text)[cite: 11]
Route::get('/admin', function () {
    $filePath = storage_path('app/students.txt');
    $studentsText = file_exists($filePath) ? file_get_contents($filePath) : '';
    return view('admin', compact('studentsText'));
})->name('admin.dashboard');

// Route bảo mật bằng PIN
Route::post('/admin/reset-tree', [StudentController::class, 'resetTree'])->name('admin.reset_tree');
Route::post('/admin/save-students', [StudentController::class, 'saveStudentsText'])->name('admin.save_students');