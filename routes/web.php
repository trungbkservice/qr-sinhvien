<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;

// 1. Màn hình Live (Máy chiếu)[cite: 6]
Route::get('/', [StudentController::class, 'showLive']);
Route::get('/live', [StudentController::class, 'showLive']);

// 2. Luồng Sinh viên[cite: 6]
Route::get('/form', [StudentController::class, 'showCheckForm'])->name('student.check');
Route::post('/check-student', [StudentController::class, 'processCheck'])->name('student.process_check');

Route::get('/quiz', [StudentController::class, 'showQuizForm'])->name('student.quiz');
Route::post('/submit-quiz', [StudentController::class, 'submitQuiz'])->name('student.submit_quiz');

// 3. Màn hình Admin (Mở sẵn file students.txt và quiz.txt)[cite: 6]
Route::get('/admin', function () {
    // Đọc danh sách sinh viên
    $studentsPath = storage_path('app/students.txt');
    $studentsText = file_exists($studentsPath) ? file_get_contents($studentsPath) : '';

    // Đọc file câu hỏi quiz.txt
    $quizPath = storage_path('app/quiz.txt');
    $questionTitle = config('quiz.quiz.question_title');
    $optionsText = implode("\n", config('quiz.quiz.options'));

    if (file_exists($quizPath)) {
        $lines = explode("\n", str_replace("\r", "", file_get_contents($quizPath)));
        $options = [];
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            $parts = explode('|', $line, 2);
            $type = strtoupper(trim($parts[0] ?? ''));
            $val = trim($parts[1] ?? '');
            if ($type === 'QUESTION') {
                $questionTitle = $val;
            } elseif ($type === 'OPTION' && $val !== '') {
                $options[] = $val;
            }
        }
        if (!empty($options)) {
            $optionsText = implode("\n", $options);
        }
    }

    return view('admin', compact('studentsText', 'questionTitle', 'optionsText'));
})->name('admin.dashboard');

// Route bảo mật bằng PIN[cite: 6]
Route::post('/admin/reset-tree', [StudentController::class, 'resetTree'])->name('admin.reset_tree');
Route::post('/admin/save-students', [StudentController::class, 'saveStudentsText'])->name('admin.save_students');
Route::post('/admin/save-quiz', [StudentController::class, 'saveQuizText'])->name('admin.save_quiz');