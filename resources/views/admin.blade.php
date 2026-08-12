<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bảng Điều Khiển Admin - Cây Tri Thức</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-white py-5 min-vh-100 d-flex align-items-center justify-content-center">

    <div class="container" style="max-width: 520px;">
        
        <div class="text-center mb-4">
            <h3 class="fw-bold text-warning"><i class="fa-solid fa-sliders me-2"></i>BẢNG ĐIỀU KHIỂN ADMIN</h3>
            <p class="text-light small">Quản lý Màn Hình Cây, Câu Hỏi & Danh Sách Sinh Viên</p>
        </div>

        <!-- MÃ PIN BẢO VỆ DÙNG CHUNG -->
        <div class="card bg-secondary text-white p-3 rounded-4 shadow-lg mb-4 border-0">
            <label class="form-label fw-bold text-warning mb-2 text-center d-block">
                🔑 NHẬP MÃ PIN BẢO VỆ ADMIN:
            </label>
            <input type="password" id="admin-pin" class="form-control text-center fs-4 fw-bold font-monospace" placeholder="******" value="654321">
        </div>

        <!-- KHUNG 1: CHỈNH SỬA FILE QUIZ.TXT (CÂU HỎI & ĐÁP ÁN) -->
        <div class="card bg-secondary text-white p-4 rounded-4 shadow-lg mb-4 border-0">
            <h5 class="fw-bold mb-2 text-warning"><i class="fa-solid fa-pen-to-square me-2"></i>1. Cấu Hình Câu Hỏi Live</h5>
            
            <div class="mb-3">
                <label class="form-label small fw-bold text-light">Nội dung câu hỏi:</label>
                <input type="text" id="quiz-question" class="form-control font-monospace text-dark fw-bold" placeholder="Nhập câu hỏi..." value="{{ $questionTitle ?? '' }}">
            </div>

            <div class="mb-3">
                <label class="form-label small fw-bold text-light">Danh sách đáp án <small class="text-light-50">(mỗi đáp án một dòng)</small>:</label>
                <textarea id="quiz-options" class="form-control font-monospace text-dark" rows="5" placeholder="A. Đáp án 1&#10;B. Đáp án 2&#10;C. Đáp án 3">{{ $optionsText ?? '' }}</textarea>
            </div>

            <button onclick="saveQuizText()" class="btn btn-warning fw-bold py-2 shadow w-100 text-dark">
                💾 LƯU CÂU HỎI QUIZ
            </button>
        </div>

        <!-- KHUNG 2: CHỈNH SỬA FILE STUDENTS.TXT -->
        <div class="card bg-secondary text-white p-4 rounded-4 shadow-lg mb-4 border-0">
            <h5 class="fw-bold mb-2 text-info"><i class="fa-solid fa-file-pen me-2"></i>2. Danh Sách Sinh Viên</h5>
            <p class="text-light small mb-2">Định dạng: MA_SV|TEN_SINH_VIEN (mỗi SV một dòng)</p>
            
            <div class="mb-3">
                <textarea id="students-text" class="form-control font-monospace text-dark" rows="6" placeholder="SV001|Nguyễn Văn An&#10;SV002|Trần Thị Bình">{{ $studentsText ?? '' }}</textarea>
            </div>

            <button onclick="saveStudentsText()" class="btn btn-info fw-bold py-2 shadow w-100 text-dark">
                💾 LƯU FILE DANH SÁCH SV
            </button>
        </div>

        <!-- KHUNG 3: XÓA CÂY TRI THỨC -->
        <div class="card bg-secondary text-white p-4 rounded-4 shadow-lg mb-4 border-0 text-center">
            <h5 class="fw-bold mb-2 text-danger"><i class="fa-solid fa-trash-can me-2"></i>3. Xóa Dữ Liệu Cây</h5>
            <p class="text-light small mb-3">Xóa sạch các bông hoa hiện tại trên màn hình Live để làm bài test mới.</p>

            <button onclick="resetTree()" class="btn btn-danger btn-lg w-100 fw-bold py-3 shadow">
                🗑️ XÓA SẠCH DỮ LIỆU CÂY
            </button>
        </div>

        <!-- THÔNG BÁO TRẠNG THÁI -->
        <div id="status-msg" class="mt-2 text-center small fw-bold"></div>

    </div>

    <script>
        // 1. Hàm lưu cấu hình Quiz (Yêu cầu PIN)
        function saveQuizText() {
            const pin = document.getElementById('admin-pin').value;
            const question = document.getElementById('quiz-question').value;
            const options = document.getElementById('quiz-options').value;
            const msg = document.getElementById('status-msg');

            if (!pin) {
                alert("Vui lòng nhập mã PIN!");
                return;
            }

            fetch("{{ route('admin.save_quiz') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    pin: pin,
                    question_title: question,
                    options_text: options
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    msg.innerHTML = '<span class="text-success fw-bold">✅ ' + data.message + '</span>';
                } else {
                    msg.innerHTML = '<span class="text-warning fw-bold">❌ ' + data.message + '</span>';
                }
            })
            .catch(err => {
                msg.innerHTML = '<span class="text-danger">Lỗi kết nối server!</span>';
            });
        }

        // 2. Hàm lưu file text sinh viên (Yêu cầu PIN)[cite: 7]
        function saveStudentsText() {
            const pin = document.getElementById('admin-pin').value;
            const content = document.getElementById('students-text').value;
            const msg = document.getElementById('status-msg');

            if (!pin) {
                alert("Vui lòng nhập mã PIN!");
                return;
            }

            fetch("{{ route('admin.save_students') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    pin: pin,
                    students_text: content
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    msg.innerHTML = '<span class="text-success fw-bold">✅ ' + data.message + '</span>';
                } else {
                    msg.innerHTML = '<span class="text-warning fw-bold">❌ ' + data.message + '</span>';
                }
            })
            .catch(err => {
                msg.innerHTML = '<span class="text-danger">Lỗi kết nối server!</span>';
            });
        }

        // 3. Hàm xóa cây tri thức (Yêu cầu PIN)[cite: 7]
        function resetTree() {
            const pin = document.getElementById('admin-pin').value;
            const msg = document.getElementById('status-msg');

            if (!pin) {
                alert("Vui lòng nhập mã PIN!");
                return;
            }

            if (!confirm("Bạn có chắc chắn muốn xóa toàn bộ bông hoa trên cây không?")) return;

            fetch("{{ route('admin.reset_tree') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ pin: pin })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    msg.innerHTML = '<span class="text-success fw-bold">✅ ' + data.message + '</span>';
                } else {
                    msg.innerHTML = '<span class="text-warning fw-bold">❌ ' + data.message + '</span>';
                }
            })
            .catch(err => {
                msg.innerHTML = '<span class="text-danger">Lỗi kết nối server!</span>';
            });
        }
    </script>
</body>
</html>