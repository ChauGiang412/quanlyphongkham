<?php
require_once 'config/Database.php';
require_once 'models/ChuyenKhoa.php';
require_once 'models/GoiKham.php';
require_once 'models/BacSi.php';

class ChatController {
    public function reply() {
        header('Content-Type: application/json; charset=utf-8');

        $message = mb_strtolower(trim($_POST['message'] ?? ''), 'UTF-8');
        if (empty($message)) {
            echo json_encode(['reply' => 'Xin chào! Tôi có thể giúp gì cho bạn về dịch vụ khám chữa bệnh?']);
            exit();
        }

        $db = (new Database())->getConnection();
        $reply = "";

        // 1. Hỏi về Chuyên khoa
        if (str_istr_contains($message, ['chuyên khoa', 'khoa', 'danh sách khoa'])) {
            $chuyenKhoaModel = new ChuyenKhoa($db);
            $list = $chuyenKhoaModel->getAllChuyenKhoa();
            $tenKhoa = array_column($list, 'ten_chuyen_khoa');
            $reply = "Elite Clinic hiện có các chuyên khoa mũi nhọn: " . implode(', ', $tenKhoa) . ". Bạn muốn tìm hiểu chi tiết về khoa nào?";
        } 
        // 2. Hỏi về Gói khám / Chi phí
        elseif (str_istr_contains($message, ['gói khám', 'bảng giá', 'giá', 'chi phí'])) {
            $goiKhamModel = new GoiKham($db);
            $list = $goiKhamModel->getAllGoiKham();
            $reply = "Hệ thống đang cung cấp các gói khám nổi bật:\n";
            foreach ($list as $goi) {
                $reply .= "- " . $goi['ten_goi'] . ": " . number_format($goi['gia']) . " đ\n";
            }
            $reply .= "Bạn có thể bấm trực tiếp vào từng gói khám trên trang chủ để xem chi tiết danh mục!";
        } 
        // 3. Hỏi về Bác sĩ
        elseif (str_istr_contains($message, ['bác sĩ', 'chuyên gia', 'đội ngũ'])) {
            $bacSiModel = new BacSi($db);
            $list = $bacSiModel->getAllBacSi();
            $reply = "Elite Clinic quy tụ đội ngũ bác sĩ giàu kinh nghiệm. Bạn có thể tham khảo hồ sơ bác sĩ trực tiếp trên trang chủ hoặc đăng ký đặt lịch khám chọn bác sĩ theo yêu cầu!";
        } 
        // 4. Hỏi về Địa chỉ / Thời gian làm việc
        elseif (str_istr_contains($message, ['địa chỉ', 'ở đâu', 'mấy giờ', 'giờ làm việc', 'thời gian'])) {
            $reply = "Elite Clinic làm việc từ 08:00 - 20:00 (Thứ 2 - Thứ 6) và 08:00 - 17:00 (Thứ 7 - Chủ Nhật). Địa chỉ: Trường Đại Học Thương Mại, Cầu Giấy, Hà Nội.";
        } 
        // 5. Mặc định
        else {
            $reply = "Cảm ơn bạn đã liên hệ! Để đặt lịch khám nhanh chóng, bạn vui lòng chọn nút 'Đặt Lịch Ngay' ở góc màn hình hoặc hotline 1900 8888 để được hỗ trợ 24/7.";
        }

        echo json_encode(['reply' => $reply]);
        exit();
    }
}

// Hàm bổ trợ kiểm tra từ khóa
function str_istr_contains($haystack, $needles) {
    foreach ($needles as $needle) {
        if (mb_strpos($haystack, $needle, 0, 'UTF-8') !== false) return true;
    }
    return false;
}