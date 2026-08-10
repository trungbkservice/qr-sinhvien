<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chọn đáp án - FPT University Quiz</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --fpt-orange: #f26522;
            --fpt-orange-light: #fff7ed;
            --fpt-blue: #0054A6;
        }

        body {
            background-color: #f8fafc;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            min-height: 100vh;
            padding: 20px 0;
        }

        .quiz-card {
            background: #ffffff;
            border-radius: 24px;
            border: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.06);
            overflow: hidden;
        }

        .header-bar {
            background: #ffffff;
            border-bottom: 2px solid #f1f5f9;
            padding: 16px 20px;
        }

        .logo-img-small {
            height: 38px;
            width: auto;
        }

        .student-pill {
            background: var(--fpt-orange-light);
            border: 1px solid rgba(242, 101, 34, 0.2);
            padding: 6px 14px;
            border-radius: 50px;
        }

        .question-box {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #ffffff;
            border-radius: 18px;
            padding: 22px;
            box-shadow: 0 10px 20px rgba(15, 23, 42, 0.15);
        }

        .option-card {
            border: 2px solid #e2e8f0;
            border-radius: 16px;
            padding: 16px;
            background: #ffffff;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            user-select: none;
            -webkit-user-select: none;
            display: flex;
            align-items: center;
        }

        .option-card:hover {
            border-color: var(--fpt-orange);
            background: var(--fpt-orange-light);
            transform: translateY(-2px);
        }

        .option-card:has(input[type="radio"]:checked) {
            border-color: var(--fpt-orange);
            background: #fff7ed;
            box-shadow: 0 0 0 3px rgba(242, 101, 34, 0.15);
        }

        .custom-radio {
            width: 22px;
            height: 22px;
            accent-color: var(--fpt-orange);
            cursor: pointer;
        }

        .btn-submit {
            background: linear-gradient(135deg, var(--fpt-orange) 0%, #f97316 100%);
            color: #ffffff;
            border: none;
            padding: 16px;
            border-radius: 16px;
            font-size: 1.1rem;
            font-weight: 700;
            box-shadow: 0 10px 22px rgba(242, 101, 34, 0.35);
            transition: all 0.25s ease;
        }

        .btn-submit:hover {
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 14px 28px rgba(242, 101, 34, 0.45);
        }
    </style>
</head>
<body class="d-flex align-items-center">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-7 col-lg-5">
                
                <div class="quiz-card">
                    <!-- Top Bar Header -->
                    <div class="header-bar d-flex justify-content-between align-items-center">
                        <img src="{{ asset('images/fpt-logo.png') }}" 
                             onerror="this.onerror=null; this.src='https://upload.wikimedia.org/wikipedia/commons/thumb/1/11/FPT_logo.svg/1024px-FPT_logo.svg.png';" 
                             alt="FPT Logo" 
                             class="logo-img-small">
                        
                        <div class="student-pill d-flex align-items-center gap-2">
                            <i class="fa-solid fa-user-graduate text-warning"></i>
                            <span class="fw-bold text-dark small">{{ $studentName }}</span>
                            <span class="badge bg-danger rounded-pill px-2">{{ $studentId }}</span>
                        </div>
                    </div>

                    <div class="p-4">
                        <!-- Khung Câu hỏi lấy từ Config -->
                        <div class="question-box mb-4">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="badge bg-warning text-dark fw-bold">CÂU HỎI LIVE</span>
                            </div>
                            <h5 class="fw-bold m-0 lh-base">
                                {{ config('quiz.quiz.question_title') }}
                            </h5>
                        </div>

                        <!-- Form chọn đáp án lấy từ Config -->
                        <form action="{{ route('student.submit_quiz') }}" method="POST">
                            @csrf
                            
                            <div class="mb-4">
                                @foreach(config('quiz.quiz.options') as $index => $option)
                                    <div class="option-card mb-3" onclick="document.getElementById('opt-{{ $index }}').click();">
                                        <input class="form-check-input custom-radio me-3" 
                                               type="radio" 
                                               name="option" 
                                               id="opt-{{ $index }}" 
                                               value="{{ $option }}" 
                                               required 
                                               onclick="event.stopPropagation();">
                                        <label class="fw-semibold text-dark w-100 m-0" style="cursor: pointer;" for="opt-{{ $index }}">
                                            {{ $option }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>

                            <button type="submit" class="btn btn-submit w-100 d-flex align-items-center justify-content-center gap-2">
                                <i class="fa-solid fa-paper-plane"></i>
                                <span>{{ config('quiz.quiz.button_text') }}</span>
                            </button>
                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>

</body>
</html>