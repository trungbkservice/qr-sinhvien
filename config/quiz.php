<?php

return [

    /*
    |--------------------------------------------------------------------------
    | FILE CẤU HÌNH ĐẦU NÃO (TẬP TRUNG TẤT CẢ THÔNG SỐ CỦA HỆ THỐNG)
    |--------------------------------------------------------------------------
    */

    // 1. CẤU HÌNH TRANG LIVE CÂY TRI THỨC (/live)
    'live' => [
        // Ảnh nền cây tri thức (nằm trong thư mục public/)
        'background_image' => 'images/cay-tri-thuc-1.webp',

        // Link đích khi quét QR Code (để trống '' nếu muốn tự lấy link /quiz)
        'qr_target_url' => '', 

        'qr_subtext' => 'Quét để gửi câu trả lời',

        // Danh sách 6 loài hoa hiển thị trên cây
        'flower_images' => [
            'images/mau-do.png',
            'images/mau-hong.png',
            'images/mau-tim.png',
            'images/mau-trang.png',
            'images/mau-vang.png',
            'images/mau-xanh.png',
        ],
    ],

    // 2. CẤU HÌNH TRANG CÂU HỎI TRẮC NGHIỆM (/quiz)
    'quiz' => [
        'question_title' => 'Theo bạn, yếu tố quan trọng nhất để thành công là gì?',
        
        'options' => [
            'A. Kiên trì & Nỗ lực',
            'B. Tư duy Sáng tạo',
            'C. Kỹ năng Làm việc nhóm',
            'D. May mắn',
        ],
        
        'button_text' => 'Gửi câu trả lời ngay',
    ],

    // 3. CẤU HÌNH TRANG ĐIỂM DANH / XÁC NHẬN SINH VIÊN (/check hoặc /form)
    'form' => [
        'title' => 'Xác Nhận Sinh Viên',
        'subtitle' => 'Vui lòng nhập Mã Sinh Viên của bạn để tham gia',
        'input_label' => 'MÃ SINH VIÊN (MSSV)',
        'input_placeholder' => 'VD: SV180000',
        'message_label' => 'Lời nhắn / Nội dung:',
        'message_placeholder' => 'Nhập nội dung...',
        'button_text' => 'Xác nhận & Tiếp tục',
    ],

    // 4. CẤU HÌNH TRANG CẢM ƠN HẬU SUBMIT (/thanks)
    'thanks' => [
        'title' => 'GỬI THÀNH CÔNG!',
        'message' => 'Câu trả lời của bạn đã xuất hiện Realtime lên Cây Tri Thức! 🚀',
    ],

    // 5. CẤU HÌNH BẢO MẬT ADMIN (Dùng chung cho Nạp SV & Reset Cây)
    'admin' => [
        'pin' => '65432111', // Mã PIN bảo vệ admin duy nhất
    ],
];