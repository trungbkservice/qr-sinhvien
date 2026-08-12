<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('quiz.thanks.title') }} - FPT University</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --fpt-orange: #f26522;
            --fpt-blue: #0054A6;
        }

        body { 
            background: #f8fafc; 
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            display: flex; 
            align-items: center; 
            justify-content: center; 
            min-height: 100vh; 
            margin: 0; 
            padding: 20px;
        }

        .thanks-card { 
            background: #ffffff;
            border-radius: 24px; 
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08); 
            border: 1px solid rgba(226, 232, 240, 0.8);
            width: 100%; 
            max-width: 420px; 
            text-align: center;
            position: relative;
        }

        /* 🟢 CHỈNH ONG BEE TO HƠN & KÉO THỤT VÀO TRONG */
        .bee-mascot-thanks {
            position: absolute;
            top: 10px;        /* Hạ vị trí xuống vừa đẹp tầm mắt */
            right: 15px;      /* 👈 Kéo thụt vào hẳn trong khung (không bị trôi ra rìa nữa) */
            height: 125px;    /* 👈 Tăng kích thước to rõ nét (cũ là 95px) */
            width: auto;
            z-index: 99;
            pointer-events: none;
            filter: drop-shadow(2px 4px 8px rgba(0, 0, 0, 0.15));
        }

        @media (max-width: 480px) {
            .bee-mascot-thanks {
                top: 10px;
                right: 10px;
                height: 95px;
            }
        }

        .header-bg {
            background: linear-gradient(135deg, #ffffff 0%, #fff7ed 100%);
            padding: 28px 20px 20px 20px;
            border-bottom: 2px dashed #ffedd5;
            border-top-left-radius: 24px;
            border-top-right-radius: 24px;
        }

        .logo-img {
            max-width: 170px;
            height: auto;
            margin-bottom: 10px;
        }

        .check-icon-box {
            width: 80px;
            height: 80px;
            background: #dcfce7;
            color: #16a34a;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: -40px auto 20px auto;
            border: 4px solid #ffffff;
            box-shadow: 0 10px 20px rgba(22, 163, 74, 0.2);
            font-size: 2.2rem;
            animation: popIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        @keyframes popIn {
            0% { transform: scale(0); }
            100% { transform: scale(1); }
        }

        .student-badge {
            background: #f1f5f9;
            color: #334155;
            padding: 8px 16px;
            border-radius: 50px;
            font-weight: 700;
            display: inline-block;
        }

        .btn-back {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #cbd5e1;
            padding: 12px;
            border-radius: 12px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s ease;
        }

        .btn-back:hover {
            background: #e2e8f0;
            color: #1e293b;
        }
    </style>
</head>
<body>

<div class="thanks-card">
    <!-- Linh vật Ong Bee nằm bên phải trong khung Header -->
    <img src="{{ asset('images/STROKE-3.png') }}" 
         alt="FPT Bee Mascot" 
         class="bee-mascot-thanks">

    <div class="header-bg">
        <img src="{{ asset('images/fpt-logo-2.png') }}" 
             onerror="this.onerror=null; this.src='https://upload.wikimedia.org/wikipedia/commons/thumb/1/11/FPT_logo.svg/1024px-FPT_logo.svg.png';" 
             alt="FPT Logo" 
             class="logo-img">
    </div>

    <div class="p-4 pt-0">
        <!-- Animated Icon Check -->
        <div class="check-icon-box">
            <i class="fa-solid fa-check"></i>
        </div>

        <h3 class="fw-bold text-dark mb-2">{{ config('quiz.thanks.title') }}</h3>
        
        <p class="text-secondary mb-3">
            Sinh viên <strong class="text-dark">{{ $studentName ?? 'Sinh viên' }}</strong><br>
            <span class="student-badge mt-2">MSSV: {{ $studentId }}</span>
        </p>

        <!-- Nút BACK quay lại route student.quiz -->
        <a href="{{ route('student.quiz') }}" class="btn-back w-100">
            <i class="fa-solid fa-rotate-left"></i>
            <span>BACK</span>
        </a>
    </div>
</div>

</body>
</html>