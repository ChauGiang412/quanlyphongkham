
<?php
require_once 'config/Database.php';
require_once 'models/ChuyenKhoa.php';
require_once 'models/GoiKham.php';
require_once 'models/BacSi.php';

class ChatController
{
    public function reply()
    {
        header('Content-Type: application/json; charset=utf-8');

        $message = mb_strtolower(trim($_POST['message'] ?? ''), 'UTF-8');

        if ($message === '') {
            $this->sendResponse([
                'type' => 'text',
                'reply' => 'Xin chào! Tôi có thể giúp gì cho bạn về dịch vụ khám chữa bệnh?'
            ]);
        }

        try {
            $db = (new Database())->getConnection();

            if (!$db) {
                throw new Exception('Không thể kết nối cơ sở dữ liệu.');
            }

            // 1. Danh sách chuyên khoa
            if (str_istr_contains($message, [
                'chuyên khoa',
                'danh sách khoa',
                'các khoa',
                'khoa khám'
            ])) {
                $model = new ChuyenKhoa($db);
                $list = $model->getAllChuyenKhoa();

                $this->sendResponse([
                    'type' => 'specialties',
                    'reply' => empty($list)
                        ? 'Hiện tại chưa có thông tin chuyên khoa.'
                        : 'Dưới đây là danh sách chuyên khoa tại Elite Clinic:',
                    'items' => $list
                ]);
            }

            // 2. Danh sách gói khám
            elseif (str_istr_contains($message, [
                'gói khám',
                'bảng giá',
                'chi phí',
                'giá khám'
            ])) {
                $model = new GoiKham($db);
                $list = $model->getAllGoiKham();

                $this->sendResponse([
                    'type' => 'packages',
                    'reply' => empty($list)
                        ? 'Hiện tại chưa có thông tin gói khám.'
                        : 'Dưới đây là các gói khám đang có tại Elite Clinic:',
                    'items' => $list
                ]);
            }

            // 3. Danh sách bác sĩ
            elseif (str_istr_contains($message, [
                'bác sĩ',
                'chuyên gia',
                'đội ngũ'
            ])) {
                $model = new BacSi($db);
                $list = $model->getAllBacSi();

                $this->sendResponse([
                    'type' => 'doctors',
                    'reply' => empty($list)
                        ? 'Hiện tại chưa có thông tin bác sĩ.'
                        : 'Dưới đây là danh sách bác sĩ tại Elite Clinic:',
                    'items' => $list
                ]);
            }

            // 4. Địa chỉ và thời gian làm việc
            elseif (str_istr_contains($message, [
                'địa chỉ',
                'ở đâu',
                'mấy giờ',
                'giờ làm việc',
                'thời gian'
            ])) {
                $this->sendResponse([
                    'type' => 'text',
                    'reply' => 'Elite Clinic làm việc từ 08:00 - 20:00 (Thứ 2 - Thứ 6) và 08:00 - 17:00 (Thứ 7 - Chủ Nhật). Địa chỉ: Trường Đại Học Thương Mại, Cầu Giấy, Hà Nội.'
                ]);
            }

            // 5. Mặc định
            else {
                $this->sendResponse([
                    'type' => 'text',
                    'reply' => 'Cảm ơn bạn đã liên hệ! Để đặt lịch khám nhanh chóng, bạn vui lòng chọn nút "Đặt Lịch Ngay" hoặc hotline 1900 8888 để được hỗ trợ.'
                ]);
            }

        } catch (Throwable $e) {
            $this->sendResponse([
                'type' => 'text',
                'reply' => 'Xin lỗi, hiện tại hệ thống chưa thể lấy dữ liệu. Vui lòng thử lại sau.'
            ]);
        }
    }

    private function sendResponse($data)
    {
        echo json_encode(
            $data,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );
        exit();
    }
}

// Hàm kiểm tra từ khóa
function str_istr_contains($haystack, $needles)
{
    foreach ($needles as $needle) {
        if (mb_strpos($haystack, $needle, 0, 'UTF-8') !== false) {
            return true;
        }
    }

    return false;
}
