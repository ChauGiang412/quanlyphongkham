<?php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/BacSiQuanLy.php';

class BacSiController
{
    private PDO $db;
    private BacSiQuanLy $model;
    private int $maNguoiDung;
    private int $maBacSi;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Kiểm tra đăng nhập và quyền bác sĩ
        if (
            empty($_SESSION['user']) ||
            ($_SESSION['user']['vai_tro'] ?? '') !== 'bac_si'
        ) {
            header('Location: index.php?act=login');
            exit();
        }

        $this->maNguoiDung = (int)(
            $_SESSION['user']['ma_nguoi_dung']
            ?? $_SESSION['ma_nguoi_dung']
            ?? 0
        );

        if ($this->maNguoiDung <= 0) {
            header('Location: index.php?act=login');
            exit();
        }

        // Kết nối cơ sở dữ liệu
        $database = new Database();
        $this->db = $database->getConnection();

        if (!$this->db instanceof PDO) {
            http_response_code(500);
            exit('Không thể kết nối cơ sở dữ liệu.');
        }

        $this->model = new BacSiQuanLy($this->db);

        // Lấy thông tin bác sĩ theo tài khoản đăng nhập
        $doctor = $this->model->getDoctorByUserId(
            $this->maNguoiDung
        );

        if (!$doctor) {
            http_response_code(403);
            exit(
                'Tài khoản bác sĩ chưa được liên kết với hồ sơ bác sĩ trong hệ thống.'
            );
        }

        $this->maBacSi = (int)$doctor['ma_bac_si'];

        $_SESSION['bac_si'] = $doctor;
    }

    /**
     * Hiển thị giao diện bác sĩ.
     * Các file giao diện được đặt trực tiếp trong thư mục views.
     */
    private function render(
        string $view,
        string $title,
        string $active,
        array $data = []
    ): void {
        extract($data, EXTR_SKIP);

        $pageTitle = $title;
        $activeMenu = $active;

        $viewFile = __DIR__ . '/../views/' . $view . '.php';
        $layoutFile = __DIR__ . '/../views/layout_bac_si.php';

        if (!is_file($viewFile)) {
            http_response_code(500);
            exit('Không tìm thấy giao diện: ' . htmlspecialchars(
                $view,
                ENT_QUOTES,
                'UTF-8'
            ));
        }

        if (!is_file($layoutFile)) {
            http_response_code(500);
            exit('Không tìm thấy giao diện layout_bac_si.php.');
        }

        require $layoutFile;
    }

    /**
     * Chuyển trang và gửi thông báo.
     */
    private function redirect(
        string $url,
        string $message,
        string $type = 'success'
    ): void {
        $_SESSION['bs_flash'] = [
            'message' => $message,
            'type' => $type
        ];

        header('Location: ' . $url);
        exit();
    }

    /**
     * Trang tổng quan bác sĩ.
     */
    public function dashboard(): void
    {
        $stats = $this->model->getDashboardStats(
            $this->maBacSi
        );

        $lichSapToi = $this->model->getUpcomingAppointments(
            $this->maBacSi
        );

        $this->render(
            'dashboard_bac_si',
            'Tổng quan',
            'dashboard',
            compact('stats', 'lichSapToi')
        );
    }

    /**
     * Danh sách lịch khám.
     */
    public function lichKham(): void
    {
        $status = trim($_GET['trang_thai'] ?? '');
        $date = trim($_GET['ngay_kham'] ?? '');

        $danhSachLich = $this->model->getAppointments(
            $this->maBacSi,
            $status,
            $date
        );

        $this->render(
            'lich_kham_bac_si',
            'Lịch khám của tôi',
            'lich-kham',
            compact('status', 'date', 'danhSachLich')
        );
    }

    /**
     * Chi tiết một lịch khám.
     */
    public function chiTietLich(): void
    {
        $id = filter_input(
            INPUT_GET,
            'id',
            FILTER_VALIDATE_INT
        );

        if (!$id || $id <= 0) {
            $this->redirect(
                'index.php?act=bac-si-lich-kham',
                'Mã lịch khám không hợp lệ.',
                'error'
            );
        }

        $lich = $this->model->getAppointmentDetail(
            (int)$id,
            $this->maBacSi
        );

        if (!$lich) {
            $this->redirect(
                'index.php?act=bac-si-lich-kham',
                'Không tìm thấy lịch khám hoặc bạn không có quyền xem lịch này.',
                'error'
            );
        }

        $phieuKham = $this->model->getMedicalRecord(
            (int)$id
        );

        $this->render(
            'chi_tiet_lich_bac_si',
            'Chi tiết lịch khám',
            'lich-kham',
            compact('lich', 'phieuKham')
        );
    }

    /**
     * Cập nhật trạng thái lịch khám.
     */
    public function capNhatTrangThai(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(
                'index.php?act=bac-si-lich-kham',
                'Yêu cầu không hợp lệ.',
                'error'
            );
        }

        $id = filter_input(
            INPUT_POST,
            'ma_lich_kham',
            FILTER_VALIDATE_INT
        );

        $status = $_POST['trang_thai'] ?? '';

        if (
            !$id ||
            !in_array(
                $status,
                ['da_xac_nhan', 'da_huy'],
                true
            )
        ) {
            $this->redirect(
                'index.php?act=bac-si-lich-kham',
                'Dữ liệu cập nhật không hợp lệ.',
                'error'
            );
        }

        $ok = $this->model->updateAppointmentStatus(
            (int)$id,
            $this->maBacSi,
            $status
        );

        $message = $status === 'da_xac_nhan'
            ? 'Đã xác nhận lịch khám.'
            : 'Đã hủy lịch khám.';

        $this->redirect(
            'index.php?act=bac-si-chi-tiet-lich&id=' . (int)$id,
            $ok ? $message : 'Không thể cập nhật trạng thái lịch khám.',
            $ok ? 'success' : 'error'
        );
    }

    /**
     * Lưu kết quả khám bệnh.
     */
    public function luuKetQuaKham(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(
                'index.php?act=bac-si-lich-kham',
                'Yêu cầu không hợp lệ.',
                'error'
            );
        }

        $id = filter_input(
            INPUT_POST,
            'ma_lich_kham',
            FILTER_VALIDATE_INT
        );

        $chuanDoan = trim($_POST['chuan_doan'] ?? '');
        $donThuoc = trim($_POST['don_thuoc'] ?? '');
        $loiDan = trim($_POST['loi_dan_bac_si'] ?? '');
        $ngayTaiKham = trim($_POST['ngay_tai_kham'] ?? '');

        if (!$id || $chuanDoan === '') {
            $this->redirect(
                'index.php?act=bac-si-lich-kham',
                'Vui lòng nhập mã lịch khám và chẩn đoán hợp lệ.',
                'error'
            );
        }

        if ($ngayTaiKham !== '') {
            $date = DateTime::createFromFormat(
                'Y-m-d',
                $ngayTaiKham
            );

            if (
                !$date ||
                $date->format('Y-m-d') !== $ngayTaiKham
            ) {
                $this->redirect(
                    'index.php?act=bac-si-chi-tiet-lich&id=' . (int)$id,
                    'Ngày tái khám không hợp lệ.',
                    'error'
                );
            }
        }

        $ok = $this->model->saveMedicalRecord(
            (int)$id,
            $this->maBacSi,
            $chuanDoan,
            $donThuoc,
            $loiDan,
            $ngayTaiKham !== '' ? $ngayTaiKham : null
        );

        $this->redirect(
            'index.php?act=bac-si-chi-tiet-lich&id=' . (int)$id,
            $ok
                ? 'Đã lưu kết quả khám và hoàn thành lịch khám.'
                : 'Không thể lưu kết quả. Lịch có thể đã bị hủy hoặc không thuộc quyền của bạn.',
            $ok ? 'success' : 'error'
        );
    }

    /**
     * Danh sách bệnh nhân.
     */
    public function benhNhan(): void
    {
        $danhSachBenhNhan = $this->model->getPatients(
            $this->maBacSi
        );

        $this->render(
            'benh_nhan_bac_si',
            'Danh sách bệnh nhân',
            'benh-nhan',
            compact('danhSachBenhNhan')
        );
    }

    /**
     * Danh sách đánh giá.
     */
    public function danhGia(): void
    {
        $danhSachDanhGia = $this->model->getReviews(
            $this->maBacSi
        );

        $this->render(
            'danh_gia_bac_si',
            'Đánh giá của bệnh nhân',
            'danh-gia',
            compact('danhSachDanhGia')
        );
    }

    /**
     * Hồ sơ bác sĩ.
     */
    public function hoSo(): void
    {
        $hoSo = $this->model->getProfile(
            $this->maBacSi
        );

        $this->render(
            'ho_so_bac_si',
            'Hồ sơ bác sĩ',
            'ho-so',
            compact('hoSo')
        );
    }

    /**
     * Cập nhật hồ sơ bác sĩ.
     */
    public function capNhatHoSo(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(
                'index.php?act=bac-si-ho-so',
                'Yêu cầu không hợp lệ.',
                'error'
            );
        }

        $hoTen = trim($_POST['ho_ten'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $soDienThoai = trim($_POST['so_dien_thoai'] ?? '');
        $tenBacSi = trim($_POST['ten_bac_si'] ?? '');
        $bangCap = trim($_POST['bang_cap'] ?? '');
        $kinhNghiem = filter_var(
            $_POST['so_nam_kinh_nghiem'] ?? 0,
            FILTER_VALIDATE_INT
        );
        $gioiThieu = trim($_POST['gioi_thieu'] ?? '');

        if (
            $hoTen === '' ||
            $tenBacSi === '' ||
            !filter_var($email, FILTER_VALIDATE_EMAIL)
        ) {
            $this->redirect(
                'index.php?act=bac-si-ho-so',
                'Vui lòng nhập họ tên, tên bác sĩ và email hợp lệ.',
                'error'
            );
        }

        if (
            $kinhNghiem === false ||
            $kinhNghiem < 0 ||
            $kinhNghiem > 80
        ) {
            $this->redirect(
                'index.php?act=bac-si-ho-so',
                'Số năm kinh nghiệm phải từ 0 đến 80.',
                'error'
            );
        }

        if (
            $soDienThoai !== '' &&
            !preg_match('/^[0-9+\s().-]{8,20}$/', $soDienThoai)
        ) {
            $this->redirect(
                'index.php?act=bac-si-ho-so',
                'Số điện thoại không hợp lệ.',
                'error'
            );
        }

        $ok = $this->model->updateProfile(
            $this->maBacSi,
            $this->maNguoiDung,
            $hoTen,
            $email,
            $soDienThoai,
            $tenBacSi,
            $bangCap,
            (int)$kinhNghiem,
            $gioiThieu
        );

        if ($ok) {
            $_SESSION['user']['ho_ten'] = $hoTen;
            $_SESSION['user']['email'] = $email;
        }

        $this->redirect(
            'index.php?act=bac-si-ho-so',
            $ok
                ? 'Cập nhật hồ sơ thành công.'
                : 'Email đã được tài khoản khác sử dụng.',
            $ok ? 'success' : 'error'
        );
    }
}