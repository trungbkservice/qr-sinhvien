<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cây Tri Thức Live - FPT University</title>
    <!-- Pusher JS Library -->
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        html, body {
            background-color: #0f172a;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow: hidden;
            width: 100vw;
            height: 100vh;
        }

        /* Khung chứa cây tràn toàn màn hình */
        .tree-wrapper {
            position: absolute;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #0f172a;
        }

        #tree-container {
            position: relative;
            width: 100vw;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        #tree-container img.tree-bg {
            width: 100%;
            height: 100%;
            object-fit: contain;
            display: block;
        }

        /* Bông hoa kích thước 62px x 62px */
        .flower-dot {
            cursor: pointer;
            pointer-events: auto;
            z-index: 10;
            object-fit: contain;
            width: 62px !important;
            height: 62px !important;
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.4));
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), filter 0.3s ease;
        }

        /* Hiệu ứng HOVER: Phóng to nhẹ bông hoa */
        .flower-dot:hover {
            transform: translate(-50%, -50%) scale(1.35) !important;
            z-index: 999 !important;
            filter: drop-shadow(0 8px 18px rgba(255, 255, 255, 0.8));
        }

        /* Tooltip hiển thị thông tin khi di chuột */
        #flower-tooltip {
            position: absolute;
            display: none;
            background: rgba(15, 23, 42, 0.95);
            color: #ffffff;
            padding: 10px 14px;
            border-radius: 12px;
            font-size: 13px;
            min-width: 200px;
            max-width: 280px;
            border: 1px solid rgba(56, 189, 248, 0.4);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(8px);
            pointer-events: none;
            z-index: 10000;
            transform: translate(-50%, -120%);
            transition: opacity 0.2s ease, transform 0.2s ease;
            text-align: left;
        }
        
        .tooltip-name {
            font-weight: bold;
            font-size: 14px;
            color: #38bdf8;
            margin-bottom: 4px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding-bottom: 4px;
        }
        
        .tooltip-item {
            margin-top: 3px;
            font-size: 12px;
            color: #cbd5e1;
            line-height: 1.4;
        }

        .tooltip-item strong {
            color: #f1f5f9;
        }

        .tooltip-choice {
            color: #facc15;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <!-- 1. NÚT PHÓNG TOÀN MÀN HÌNH -->
    <button onclick="toggleFullScreen()" style="position: fixed; top: 20px; right: 20px; z-index: 1000; background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.3); color: white; padding: 10px 18px; border-radius: 12px; cursor: pointer; backdrop-filter: blur(8px); font-weight: bold; transition: all 0.2s ease;" onmouseover="this.style.background='rgba(255,255,255,0.3)'" onmouseout="this.style.background='rgba(255,255,255,0.15)'">
        🖥️ Toàn Màn Hình
    </button>

    <!-- 2. KHUNG CHÍNH HIỂN THỊ CÂY TRÀN MÀN HÌNH -->
    <div class="tree-wrapper">
        <div id="tree-container">
            <!-- Ảnh cây lấy từ config/quiz.php -->
            <img class="tree-bg" src="{{ asset(config('quiz.live.background_image')) }}" alt="Cây Tri Thức">

            <!-- Lớp chứa các bông hoa -->
            <div id="flowers-layer" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none;"></div>

            <!-- Khung Tooltip -->
            <div id="flower-tooltip">
                <div class="tooltip-name" id="tooltip-student-name">Nguyễn Văn A</div>
                <div class="tooltip-item"><strong>Mã SV:</strong> <span id="tooltip-student-code">SV001</span></div>
                <div class="tooltip-item"><strong>Lựa chọn:</strong> <span class="tooltip-choice" id="tooltip-student-option">Đáp án A</span></div>
            </div>
        </div>
    </div>

    <!-- 3. KHUNG QR CODE GÓC DƯỚI BÊN TRÁI -->
    <div style="position: fixed; bottom: 20px; left: 20px; z-index: 100; background: white; padding: 10px; border-radius: 12px; text-align: center; box-shadow: 0 4px 12px rgba(0,0,0,0.4);">
        @if(class_exists('SimpleSoftwareIO\QrCode\Facades\QrCode'))
            {!! QrCode::size(110)->generate(config('quiz.live.qr_target_url') ?: url('/quiz')) !!}
        @else
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=110x110&data={{ urlencode(config('quiz.live.qr_target_url') ?: url('/quiz')) }}" alt="QR Code">
        @endif
        <p style="margin: 6px 0 0 0; font-size: 11px; font-weight: bold; color: #1e293b;">
            {{ config('quiz.live.qr_subtext') }}
        </p>
    </div>

    <!-- 4. PHẦN DỮ LIỆU JSON TRÁNH LỖI VS CODE -->
    <script id="submissions-data" type="application/json">
        {!! json_encode($submissions) !!}
    </script>

    <script id="flower-images-data" type="application/json">
        {!! json_encode(array_map(fn($img) => asset($img), config('quiz.live.flower_images'))) !!}
    </script>

    <!-- 5. SCRIPT JAVASCRIPT XỬ LÝ -->
    <script>
        // Lấy danh sách các loài hoa từ thẻ JSON
        const flowerImages = JSON.parse(document.getElementById('flower-images-data').textContent || '[]');

        // Tọa độ các nhánh cây chính (đơn vị %)
        const branchSpots = [
            // Ngọn & tán trên
            { x: 50, y: 16 }, { x: 44, y: 22 }, { x: 56, y: 22 }, { x: 38, y: 28 }, { x: 62, y: 28 },
            // Tán bên trái
            { x: 28, y: 35 }, { x: 22, y: 44 }, { x: 32, y: 48 }, { x: 18, y: 55 }, { x: 35, y: 58 },
            // Tán bên phải
            { x: 72, y: 35 }, { x: 78, y: 44 }, { x: 68, y: 48 }, { x: 82, y: 55 }, { x: 65, y: 58 },
            // Tán trung tâm
            { x: 48, y: 32 }, { x: 52, y: 36 }, { x: 42, y: 42 }, { x: 58, y: 42 }
        ];

        // Mảng lưu tọa độ các điểm đã xuất hiện hoa để chống đè
        const placedPositions = [];

        // Tính khoảng cách pixel thực tế giữa 2 điểm
        function getDistanceInPixels(pos1, pos2) {
            const container = document.getElementById('tree-container');
            const width = container ? container.clientWidth : window.innerWidth;
            const height = container ? container.clientHeight : window.innerHeight;

            const dx = (pos1.x - pos2.x) * (width / 100);
            const dy = (pos1.y - pos2.y) * (height / 100);

            return Math.sqrt(dx * dx + dy * dy);
        }

        // Tìm vị trí MỚI cách xa tất cả bông hoa cũ tối thiểu MIN_DIST_PX (68px)
        function findNonOverlappingPosition() {
            const MIN_DIST_PX = 68; // Kích thước hoa 62px + 6px khoảng cách an toàn
            let bestCandidate = null;
            let maxMinDist = -1;

            for (let i = 0; i < 120; i++) {
                const randomSpot = branchSpots[Math.floor(Math.random() * branchSpots.length)];
                const angle = Math.random() * Math.PI * 2;
                const radius = Math.random() * 4.5;

                const candidate = {
                    x: Math.min(Math.max(randomSpot.x + Math.cos(angle) * radius, 15), 85),
                    y: Math.min(Math.max(randomSpot.y + Math.sin(angle) * radius, 12), 68)
                };

                if (placedPositions.length === 0) {
                    placedPositions.push(candidate);
                    return candidate;
                }

                let minDist = Infinity;
                for (const pos of placedPositions) {
                    const dist = getDistanceInPixels(candidate, pos);
                    if (dist < minDist) {
                        minDist = dist;
                    }
                }

                if (minDist >= MIN_DIST_PX) {
                    placedPositions.push(candidate);
                    return candidate;
                }

                if (minDist > maxMinDist) {
                    maxMinDist = minDist;
                    bestCandidate = candidate;
                }
            }

            const finalPos = bestCandidate || branchSpots[Math.floor(Math.random() * branchSpots.length)];
            placedPositions.push(finalPos);
            return finalPos;
        }

        function getStudentCode(sub) {
            if (!sub) return 'N/A';
            const code = sub.student_code || sub.ma_sv || sub.mssv || sub.student_id;
            return code ? String(code).trim() : 'N/A';
        }

        function getStudentName(sub) {
            if (!sub) return 'Sinh viên';
            const name = sub.student_name || sub.name || sub.ten_sv || sub.full_name;
            return name ? String(name).trim() : 'Sinh viên';
        }

        function getStudentOption(sub) {
            if (!sub) return 'Chưa chọn';
            const opt = sub.selected_option || sub.option || sub.choice || sub.dap_an || sub.message;
            return opt ? String(opt).trim() : 'Chưa chọn';
        }

        // Tạo bông hoa mới mỗi lần gửi
        function addFlower(rawData) {
            const sub = rawData.submission || rawData.data || rawData;
            const studentCode = getStudentCode(sub);

            if (!studentCode || studentCode === 'N/A') return;

            const pos = findNonOverlappingPosition();
            const randomFlowerImg = flowerImages.length > 0 
                ? flowerImages[Math.floor(Math.random() * flowerImages.length)]
                : "{{ asset('images/mau-do.png') }}";

            const flower = document.createElement('img');
            flower.src = randomFlowerImg;
            flower.className = 'flower-dot';
            flower.style.position = 'absolute';
            flower.style.left = pos.x + '%';
            flower.style.top = pos.y + '%';
            flower.style.transform = 'translate(-50%, -50%) scale(0)'; 

            const tooltip = document.getElementById('flower-tooltip');
            const tooltipName = document.getElementById('tooltip-student-name');
            const tooltipCode = document.getElementById('tooltip-student-code');
            const tooltipOption = document.getElementById('tooltip-student-option');

            flower.addEventListener('mouseenter', () => {
                tooltipName.textContent = getStudentName(sub);
                tooltipCode.textContent = studentCode;
                tooltipOption.textContent = getStudentOption(sub);

                tooltip.style.left = pos.x + '%';
                tooltip.style.top = pos.y + '%';
                tooltip.style.display = 'block';
            });

            flower.addEventListener('mouseleave', () => {
                tooltip.style.display = 'none';
            });

            const layer = document.getElementById('flowers-layer');
            if (layer) {
                layer.appendChild(flower);
                setTimeout(() => {
                    flower.style.transform = 'translate(-50%, -50%) scale(1)';
                }, 50);
            }
        }

        function toggleFullScreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(err => {
                    alert(`Không thể bật toàn màn hình: ${err.message}`);
                });
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                }
            }
        }

        // Load danh sách ban đầu từ Database
        const initialSubmissions = JSON.parse(document.getElementById('submissions-data').textContent || '[]');
        initialSubmissions.forEach(sub => addFlower(sub));

        // Lắng nghe Realtime từ Pusher
        Pusher.logToConsole = false;
        const pusher = new Pusher('{{ env("PUSHER_APP_KEY") }}', {
            cluster: '{{ env("PUSHER_APP_CLUSTER") }}',
            forceTLS: true
        });

        const channel = pusher.subscribe('quiz-channel');
        channel.bind('student-submitted', function(data) {
            addFlower(data);
        });
    </script>
</body>
</html>