<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('quiz.form.title', 'Nhập thông tin sinh viên') }}</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; max-width: 400px; margin: 0 auto; }
        input, textarea, button { width: 100%; margin-bottom: 15px; padding: 10px; box-sizing: border-box; }
        button { background: #007bff; color: white; border: none; font-weight: bold; cursor: pointer; border-radius: 4px; }
        .alert { background: #d4edda; color: #155724; padding: 10px; margin-bottom: 15px; border-radius: 4px; }
    </style>
</head>
<body>
    <h2>{{ config('quiz.form.title', 'Nhập thông tin') }}</h2>

    @if(session('success'))
        <div class="alert">{{ session('success') }}</div>
    @endif

    <form action="{{ route('student.submit') }}" method="POST">
        @csrf
        <label>{{ config('quiz.form.input_label', 'Mã Sinh Viên:') }}</label>
        <input type="text" 
               name="student_code" 
               required 
               placeholder="{{ config('quiz.form.input_placeholder', 'Ví dụ: SV12345') }}">

        <label>{{ config('quiz.form.message_label', 'Lời nhắn / Nội dung:') }}</label>
        <textarea name="message" 
                  rows="4" 
                  placeholder="{{ config('quiz.form.message_placeholder', 'Nhập nội dung...') }}"></textarea>

        <button type="submit">{{ config('quiz.form.button_text', 'Submit Dữ Liệu') }}</button>
    </form>
</body>
</html>