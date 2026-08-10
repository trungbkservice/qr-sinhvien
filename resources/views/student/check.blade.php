<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('quiz.form.title') }} - FPT University</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --fpt-orange: #f26522;
            --fpt-orange-hover: #d9530f;
            --fpt-blue: #0054A6;
        }

        body {
            background: #f8fafc;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            margin: 0;
        }

        .check-card {
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(226, 232, 240, 0.8);
            width: 100%;
            max-width: 420px;
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .brand-header {
            background: linear-gradient(135deg, #ffffff 0%, #fff7ed 100%);
            padding: 28px 20px 20px 20px;
            text-align: center;
            border-bottom: 2px dashed #ffedd5;
        }

        .logo-img {
            max-width: 180px;
            height: auto;
            margin-bottom: 12px;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.05));
        }

        .input-custom {
            border: 2px solid #e2e8f0;
            border-radius: 14px;
            padding: 14px 18px;
            font-size: 1.1rem;
            letter-spacing: 1px;
            transition: all 0.25s ease;
            text-transform: uppercase;
        }

        .input-custom:focus {
            border-color: var(--fpt-orange);
            box-shadow: 0 0 0 4px rgba(242, 101, 34, 0.15);
            outline: none;
        }

        .btn-fpt {
            background: linear-gradient(135deg, var(--fpt-orange) 0%, #f97316 100%);
            color: white;
            border: none;
            padding: 14px;
            border-radius: 14px;
            font-size: 1.05rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            box-shadow: 0 8px 20px rgba(242, 101, 34, 0.3);
            transition: all 0.25s ease;
        }

        .btn-fpt:hover {
            background: linear-gradient(135deg, var(--fpt-orange-hover) 0%, var(--fpt-orange) 100%);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 12px 24px rgba(242, 101, 34, 0.4);
        }

        .btn-fpt:active {
            transform: translateY(0);
        }

        .badge-fpt {
            background: rgba(242, 101, 34, 0.1);
            color: var(--fpt-orange);
            font-weight: 600;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.85rem;
        }
    </style>
</head>
<body>

<div class="check-card">
    <!-- Branding Header -->
    <div class="brand-header">
        <img src="{{ asset('images/fpt-logo.png') }}" 
             onerror="this.onerror=null; this.src='https://upload.wikimedia.org/wikipedia/commons/thumb/1/11/FPT_logo.svg/1024px-FPT_logo.svg.png';" 
             alt="FPT University Logo" 
             class="logo-img">
        <div class="mt-2">
            <span class="badge-fpt"><i class="fa-solid fa-tree me-1"></i> CÂY TRI THỨC LIVE</span>
        </div>
    </div>

    <!-- Form Body -->
    <div class="p-4">
        <div class="text-center mb-4">
            <h5 class="fw-bold text-dark mb-1">{{ config('quiz.form.title') }}</h5>
            <p class="text-muted small">{{ config('quiz.form.subtitle') }}</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger border-0 shadow-sm rounded-3 p-3 mb-3 small d-flex align-items-center">
                <i class="fa-solid fa-circle-exclamation me-2 fs-5"></i>
                <div>{{ $errors->first() }}</div>
            </div>
        @endif

        <form action="{{ route('student.process_check') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="form-label small fw-bold text-secondary">{{ config('quiz.form.input_label') }}</label>
                <div class="position-relative">
                    <input type="text" 
                           name="student_id" 
                           class="form-control input-custom text-center fw-bold text-uppercase" 
                           placeholder="{{ config('quiz.form.input_placeholder') }}" 
                           required 
                           autofocus 
                           autocomplete="off">
                </div>
            </div>

            <button type="submit" class="btn btn-fpt w-100 d-flex align-items-center justify-content-center gap-2">
                <span>{{ config('quiz.form.button_text') }}</span>
                <i class="fa-solid fa-arrow-right"></i>
            </button>
        </form>
    </div>
</div>

</body>
</html>