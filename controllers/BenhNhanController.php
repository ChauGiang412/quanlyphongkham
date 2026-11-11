
<?php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/ChuyenKhoa.php';
require_once __DIR__ . '/../models/BacSi.php';
require_once __DIR__ . '/../models/GoiKham.php';
require_once __DIR__ . '/../models/LichKham.php';

class BenhNhanController
{
    private $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // =====================================================
    // HÀM DÙNG CHUNG
    // =====================================================

    // Lấy mã người dùng đang đăng nhập
    private function getMaNguoiDung()
    {
        if (empty($_SESSION['user']) || !is_array($_SESSION['user'])) {
            return null;
        }

        $id = $_SESSION['user']['ma_nguoi_dung']
            ?? $_SESSION['user']['id']
            ?? $_SESSION['user']['user_id']
            ?? null;

        return filter_var($id, FILTER_VALIDATE_INT) ?: null;
    }

    // Kiểm tra đăng nhập
    private function requireLogin()
    {
        $maNguoiDung = $this->getMaNguoiDung();

        if (!$maNguoiDung) {
            header('Location: index.php?act=login');
            exit();
        }

        return $maNguoiDung;
    }

    // Hiển thị thông báo và chuyển trang
    private function redirectWithMessage($message, $url)
    {
        echo '<script>';
        echo 'alert(' . json_encode(
            $message,
            JSON_UNESCAPED_UNICODE
        ) . ');';
        echo 'window.location.href=' . json_encode($url) . ';';
        echo '</script>';
        exit();
    }

    // Kiểm tra ngày giờ khám không nằm trong quá khứ
    private function isValidAppointmentDateTime($date, $time)
    {
        if (!$date || !$time) {
            return false;
        }

        $appointment = DateTime::createFromFormat(
            'Y-m-d H:i',
            $date . ' ' . substr($time, 0, 5)
        );

        if (!$appointment) {
            return false;
        }

        $errors = DateTime::getLastErrors();

        if (
            $errors !== false &&
            ($errors['warning_count'] > 0 || $errors['error_count'] > 0)
        ) {
            return false;
        }

        return $appointment->format('Y-m-d H:i') ===
            $date . ' ' . substr($time, 0, 5)
            && $appointment > new DateTime();
    }

    // Kiểm tra bác sĩ có tồn tại và thuộc chuyên khoa
    private function checkDoctor($maBacSi, $maChuyenKhoa = null)
    {
        $sql = "SELECT ma_bac_si, ma_chuyen_khoa
                FROM bac_si
                WHERE ma_bac_si = :ma_bac_si
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':ma_bac_si' => $maBacSi
        ]);

        $bacSi = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$bacSi) {
            return false;
        }

        if (
            $maChuyenKhoa !== null &&
            (int)$bacSi['ma_chuyen_khoa'] !== (int)$maChuyenKhoa
        ) {
            return false;
        }

        return $bacSi;
    }

    // Kiểm tra khung giờ bác sĩ đã có người đặt chưa
    private function isTimeBooked($maBacSi, $ngayKham, $gioKham)
    {
        $sql = "SELECT COUNT(*)
                FROM lich_kham
                WHERE ma_bac_si = :ma_bac_si
                  AND ngay_kham = :ngay_kham
                  AND gio_kham = :gio_kham
                  AND trang_thai != 'da_huy'";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':ma_bac_si' => $maBacSi,
            ':ngay_kham' => $ngayKham,
            ':gio_kham' => $gioKham
        ]);

        return (int)$stmt->fetchColumn() > 0;
    }

    // =====================================================
    // 1. TRANG CHỦ
    // =====================================================

    public function home()
    {
        $chuyenKhoaModel = new ChuyenKhoa($this->db);
        $bacSiModel = new BacSi($this->db);
        $goiKhamModel = new GoiKham($this->db);

        $danhSachChuyenKhoa =
            $chuyenKhoaModel->getAllChuyenKhoa();

        $danhSachBacSi =
            $bacSiModel->getAllBacSi();

        $danhSachGoiKham =
            $goiKhamModel->getAllGoiKham();

        require_once __DIR__ . '/../views/home.php';
    }

    // =====================================================
    // 2. CHI TIẾT CHUYÊN KHOA
    // =====================================================

    public function chiTietChuyenKhoa()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            header('Location: index.php?act=home');
            exit();
        }

        $chuyenKhoaModel = new ChuyenKhoa($this->db);
        $bacSiModel = new BacSi($this->db);

        // Tên biến phải khớp với View
        $chiTiet = $chuyenKhoaModel->getChuyenKhoaById($id);

        if (!$chiTiet) {
            $this->redirectWithMessage(
                'Không tìm thấy chuyên khoa!',
                'index.php?act=home'
            );
        }

        $danhSachBacSiTheoCK =
            $bacSiModel->getDoctorsByChuyenKhoa($id);

        require_once __DIR__ . '/../views/chi_tiet_chuyen_khoa.php';
    }

    // =====================================================
    // 3. CHI TIẾT BÁC SĨ
    // =====================================================

    public function chiTietBacSi()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            header('Location: index.php?act=home');
            exit();
        }

        $bacSiModel = new BacSi($this->db);

        // Tên biến phải khớp với View
        $chiTiet = $bacSiModel->getDoctorById($id);

        if (!$chiTiet) {
            $this->redirectWithMessage(
                'Không tìm thấy bác sĩ!',
                'index.php?act=home'
            );
        }

        require_once __DIR__ . '/../views/chi_tiet_bac_si.php';
    }

    // =====================================================
    // 4. CHI TIẾT GÓI KHÁM
    // =====================================================

    public function chiTietGoiKham()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            header('Location: index.php?act=home');
            exit();
        }

        $goiKhamModel = new GoiKham($this->db);

        $chiTiet = $goiKhamModel->getGoiKhamById($id);

        if (
            !$chiTiet ||
            ($chiTiet['trang_thai'] ?? '') !== 'dang_hoat_dong'
        ) {
            $this->redirectWithMessage(
                'Gói khám không tồn tại hoặc đã ngừng hoạt động!',
                'index.php?act=home'
            );
        }

        require_once __DIR__ . '/../views/chi_tiet_goi_kham.php';
    }

    // =====================================================
    // 5. CHỌN LOẠI ĐẶT LỊCH
    // =====================================================

    public function chonLoaiLich()
    {
        require_once __DIR__ . '/../views/chon_loai_lich.php';
    }

    // =====================================================
    // 6. FORM ĐẶT LỊCH KHÁM THÔNG THƯỜNG
    // =====================================================

    public function index()
    {
        $chuyenKhoaModel = new ChuyenKhoa($this->db);
        $bacSiModel = new BacSi($this->db);
        $goiKhamModel = new GoiKham($this->db);

        $danhSachChuyenKhoa =
            $chuyenKhoaModel->getAllChuyenKhoa();

        $danhSachBacSi =
            $bacSiModel->getAllBacSi();

        $danhSachGoiKham =
            $goiKhamModel->getAllGoiKham();

        require_once __DIR__ . '/../views/dat_lich.php';
    }

    // =====================================================
    // 7. API LẤY KHUNG GIỜ ĐÃ ĐẶT
    // =====================================================

    public function getGioDaDatApi()
    {
        $maBacSi = filter_input(
            INPUT_GET,
            'ma_bac_si',
            FILTER_VALIDATE_INT
        );

        $ngayKham = $_GET['ngay_kham'] ?? '';

        header('Content-Type: application/json; charset=utf-8');

        if (
            !$maBacSi ||
            !preg_match('/^\d{4}-\d{2}-\d{2}$/', $ngayKham)
        ) {
            echo json_encode([]);
            exit();
        }

        $lichKhamModel = new LichKham($this->db);

        $gioDaDat = $lichKhamModel->getGioDaDat(
            $maBacSi,
            $ngayKham
        );

        echo json_encode(
            $gioDaDat,
            JSON_UNESCAPED_UNICODE
        );

        exit();
    }

    // =====================================================
    // 8. XỬ LÝ ĐẶT LỊCH KHÁM THÔNG THƯỜNG
    // =====================================================

    public function datLich()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?act=dat-lich');
            exit();
        }

        $maNguoiDung = $this->requireLogin();

        $maChuyenKhoa = filter_var(
            $_POST['ma_chuyen_khoa'] ?? null,
            FILTER_VALIDATE_INT
        );

        $maBacSi = filter_var(
            $_POST['ma_bac_si'] ?? null,
            FILTER_VALIDATE_INT
        );

        $ngayKham = $_POST['ngay_kham'] ?? '';
        $gioKham = $_POST['gio_kham'] ?? '';
        $lyDoKham = trim($_POST['ly_do_kham'] ?? '');

        if (
            !$maChuyenKhoa ||
            !$maBacSi ||
            !$ngayKham ||
            !$gioKham
        ) {
            $this->redirectWithMessage(
                'Vui lòng chọn chuyên khoa, bác sĩ, ngày và giờ khám!',
                'javascript:history.back()'
            );
        }

        if (!$this->isValidAppointmentDateTime($ngayKham, $gioKham)) {
            $this->redirectWithMessage(
                'Ngày giờ khám không hợp lệ hoặc đã qua!',
                'index.php?act=dat-lich'
            );
        }

        $bacSi = $this->checkDoctor(
            $maBacSi,
            $maChuyenKhoa
        );

        if (!$bacSi) {
            $this->redirectWithMessage(
                'Bác sĩ không tồn tại hoặc không thuộc chuyên khoa đã chọn!',
                'index.php?act=dat-lich'
            );
        }

        if ($this->isTimeBooked($maBacSi, $ngayKham, $gioKham)) {
            $this->redirectWithMessage(
                'Khung giờ này đã có người đặt. Vui lòng chọn giờ khác!',
                'index.php?act=dat-lich'
            );
        }

        $query = "INSERT INTO lich_kham (
                    ma_nguoi_dung,
                    ma_bac_si,
                    ma_chuyen_khoa,
                    ngay_kham,
                    gio_kham,
                    ly_do_kham,
                    trang_thai
                  )
                  VALUES (
                    :ma_nguoi_dung,
                    :ma_bac_si,
                    :ma_chuyen_khoa,
                    :ngay_kham,
                    :gio_kham,
                    :ly_do_kham,
                    'cho_xac_nhan'
                  )";

        $stmt = $this->db->prepare($query);

        $success = $stmt->execute([
            ':ma_nguoi_dung' => $maNguoiDung,
            ':ma_bac_si' => $maBacSi,
            ':ma_chuyen_khoa' => $maChuyenKhoa,
            ':ngay_kham' => $ngayKham,
            ':gio_kham' => $gioKham,
            ':ly_do_kham' => $lyDoKham
        ]);

        if ($success) {
            $this->redirectWithMessage(
                'Đặt lịch khám thành công! Vui lòng chờ xác nhận.',
                'index.php?act=lich-su'
            );
        }

        $this->redirectWithMessage(
            'Có lỗi xảy ra, vui lòng thử lại!',
            'index.php?act=dat-lich'
        );
    }

    // =====================================================
    // 9. FORM ĐẶT GÓI KHÁM
    // =====================================================

    public function formDatGoiKham()
    {
        $goiKhamModel = new GoiKham($this->db);
        $bacSiModel = new BacSi($this->db);

        $danhSachGoiKham =
            $goiKhamModel->getAllGoiKham();

        $danhSachBacSi =
            $bacSiModel->getAllBacSi();

        $goiHienTai = null;

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if ($id) {
            $goiHienTai = $goiKhamModel->getGoiKhamById($id);

            if (
                !$goiHienTai ||
                ($goiHienTai['trang_thai'] ?? '') !== 'dang_hoat_dong'
            ) {
                $this->redirectWithMessage(
                    'Gói khám không tồn tại hoặc đã ngừng hoạt động!',
                    'index.php?act=home'
                );
            }
        }

        require_once __DIR__ . '/../views/dat_goi_kham.php';
    }

    // =====================================================
    // 10. XỬ LÝ ĐẶT GÓI KHÁM
    // =====================================================

    public function xuLyDatGoiKham()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?act=home');
            exit();
        }

        $maNguoiDung = $this->requireLogin();

        $maGoiKham = filter_var(
            $_POST['ma_goi_kham'] ?? null,
            FILTER_VALIDATE_INT
        );

        $maBacSi = filter_var(
            $_POST['ma_bac_si'] ?? null,
            FILTER_VALIDATE_INT
        );

        $ngayKham = $_POST['ngay_kham'] ?? '';
        $gioKham = $_POST['gio_kham'] ?? '';
        $ghiChu = trim($_POST['ghi_chu'] ?? '');

        if (
            !$maGoiKham ||
            !$maBacSi ||
            !$ngayKham ||
            !$gioKham
        ) {
            $this->redirectWithMessage(
                'Vui lòng điền đầy đủ thông tin đặt gói khám!',
                'index.php?act=home'
            );
        }

        if (!$this->isValidAppointmentDateTime($ngayKham, $gioKham)) {
            $this->redirectWithMessage(
                'Ngày giờ khám không hợp lệ hoặc đã qua!',
                'index.php?act=home'
            );
        }

        $goiKhamModel = new GoiKham($this->db);

        $goiKham = $goiKhamModel->getGoiKhamById($maGoiKham);

        if (
            !$goiKham ||
            ($goiKham['trang_thai'] ?? '') !== 'dang_hoat_dong'
        ) {
            $this->redirectWithMessage(
                'Gói khám không tồn tại hoặc đã ngừng hoạt động!',
                'index.php?act=home'
            );
        }

        $bacSi = $this->checkDoctor($maBacSi);

        if (!$bacSi) {
            $this->redirectWithMessage(
                'Bác sĩ không tồn tại!',
                'index.php?act=home'
            );
        }

        if (
            !empty($goiKham['ma_chuyen_khoa']) &&
            (int)$bacSi['ma_chuyen_khoa'] !==
            (int)$goiKham['ma_chuyen_khoa']
        ) {
            $this->redirectWithMessage(
                'Bác sĩ không thuộc chuyên khoa của gói khám!',
                'index.php?act=home'
            );
        }

        if ($this->isTimeBooked($maBacSi, $ngayKham, $gioKham)) {
            $this->redirectWithMessage(
                'Khung giờ này đã có người đặt. Vui lòng chọn giờ khác!',
                'index.php?act=home'
            );
        }

        $tenGoi = $goiKham['ten_goi'] ?? 'Gói khám';

        $lyDoKham = '[ĐẶT GÓI: ' . $tenGoi . ']';

        if ($ghiChu !== '') {
            $lyDoKham .= ' ' . $ghiChu;
        }

        $query = "INSERT INTO lich_kham (
                    ma_nguoi_dung,
                    ma_bac_si,
                    ma_chuyen_khoa,
                    ngay_kham,
                    gio_kham,
                    ly_do_kham,
                    trang_thai
                  )
                  VALUES (
                    :ma_nguoi_dung,
                    :ma_bac_si,
                    :ma_chuyen_khoa,
                    :ngay_kham,
                    :gio_kham,
                    :ly_do_kham,
                    'cho_xac_nhan'
                  )";

        $stmt = $this->db->prepare($query);

        $success = $stmt->execute([
            ':ma_nguoi_dung' => $maNguoiDung,
            ':ma_bac_si' => $maBacSi,
            ':ma_chuyen_khoa' => $goiKham['ma_chuyen_khoa'],
            ':ngay_kham' => $ngayKham,
            ':gio_kham' => $gioKham,
            ':ly_do_kham' => $lyDoKham
        ]);

        if ($success) {
            $this->redirectWithMessage(
                'Đặt gói khám thành công! Vui lòng chờ xác nhận.',
                'index.php?act=lich-su'
            );
        }

        $this->redirectWithMessage(
            'Có lỗi xảy ra, vui lòng thử lại!',
            'index.php?act=home'
        );
    }

    // =====================================================
    // 11. LỊCH SỬ KHÁM BỆNH
    // =====================================================

    public function lichSu()
    {
        $maNguoiDung = $this->requireLogin();

        $lichKhamModel = new LichKham($this->db);

        $danhSachLich = $lichKhamModel->getByNguoiDung(
            $maNguoiDung
        );

        require_once __DIR__ . '/../views/lich_su.php';
    }

    // =====================================================
    // 12. CHI TIẾT LỊCH KHÁM VÀ PHIẾU KHÁM
    // =====================================================

    public function chiTietLich()
    {
        $maNguoiDung = $this->requireLogin();

        $maLichKham = filter_input(
            INPUT_GET,
            'id',
            FILTER_VALIDATE_INT
        );

        if (!$maLichKham) {
            header('Location: index.php?act=lich-su');
            exit();
        }

        $sql = "SELECT lk.*,
                       bs.ten_bac_si,
                       ck.ten_chuyen_khoa,
                       nd.ho_ten,
                       nd.email,
                       nd.so_dien_thoai
                FROM lich_kham lk
                LEFT JOIN bac_si bs
                    ON lk.ma_bac_si = bs.ma_bac_si
                LEFT JOIN chuyen_khoa ck
                    ON lk.ma_chuyen_khoa = ck.ma_chuyen_khoa
                LEFT JOIN nguoi_dung nd
                    ON lk.ma_nguoi_dung = nd.ma_nguoi_dung
                WHERE lk.ma_lich_kham = :id
                  AND lk.ma_nguoi_dung = :ma_nguoi_dung
                LIMIT 1";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':id' => $maLichKham,
            ':ma_nguoi_dung' => $maNguoiDung
        ]);

        $chiTiet = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$chiTiet) {
            $this->redirectWithMessage(
                'Không tìm thấy lịch khám hoặc bạn không có quyền xem!',
                'index.php?act=lich-su'
            );
        }

        $sqlPhieu = "SELECT *
                     FROM phieu_kham_benh
                     WHERE ma_lich_kham = :ma_lich_kham
                     LIMIT 1";

        $stmtPhieu = $this->db->prepare($sqlPhieu);

        $stmtPhieu->execute([
            ':ma_lich_kham' => $maLichKham
        ]);

        $phieuKham = $stmtPhieu->fetch(PDO::FETCH_ASSOC) ?: null;

        $sqlDanhGia = "SELECT *
                       FROM danh_gia
                       WHERE ma_lich_kham = :ma_lich_kham
                         AND ma_nguoi_dung = :ma_nguoi_dung
                       LIMIT 1";

        $stmtDanhGia = $this->db->prepare($sqlDanhGia);

        $stmtDanhGia->execute([
            ':ma_lich_kham' => $maLichKham,
            ':ma_nguoi_dung' => $maNguoiDung
        ]);

        $danhGia = $stmtDanhGia->fetch(PDO::FETCH_ASSOC) ?: null;

        require __DIR__ . '/../views/chi_tiet_lich.php';
    }

    // =====================================================
    // 13. HỦY LỊCH KHÁM
    // =====================================================

    public function huyLich()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?act=lich-su');
            exit();
        }

        $maNguoiDung = $this->requireLogin();

        $maLichKham = filter_input(
            INPUT_POST,
            'ma_lich_kham',
            FILTER_VALIDATE_INT
        );

        if (!$maLichKham) {
            header('Location: index.php?act=lich-su');
            exit();
        }

        $sql = "UPDATE lich_kham
                SET trang_thai = 'da_huy'
                WHERE ma_lich_kham = :id
                  AND ma_nguoi_dung = :ma_nguoi_dung
                  AND trang_thai = 'cho_xac_nhan'";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':id' => $maLichKham,
            ':ma_nguoi_dung' => $maNguoiDung
        ]);

        if ($stmt->rowCount() === 1) {
            $this->redirectWithMessage(
                'Đã hủy lịch hẹn thành công!',
                'index.php?act=lich-su'
            );
        }

        $this->redirectWithMessage(
            'Chỉ có thể hủy lịch của bạn đang chờ xác nhận!',
            'index.php?act=lich-su'
        );
    }

    // =====================================================
    // 14. HỒ SƠ CÁ NHÂN
    // =====================================================

    public function hoSo()
    {
        $maNguoiDung = $this->requireLogin();

        $sql = "SELECT *
                FROM nguoi_dung
                WHERE ma_nguoi_dung = :id
                LIMIT 1";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':id' => $maNguoiDung
        ]);

        $userInfo = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$userInfo) {
            $this->redirectWithMessage(
                'Không tìm thấy thông tin tài khoản!',
                'index.php?act=home'
            );
        }

        require_once __DIR__ . '/../views/ho_so.php';
    }

    // =====================================================
    // 15. CẬP NHẬT HỒ SƠ CÁ NHÂN
    // =====================================================

    public function capNhatHoSo()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?act=ho-so');
            exit();
        }

        $maNguoiDung = $this->requireLogin();

        $hoTen = trim($_POST['ho_ten'] ?? '');
        $soDienThoai = trim($_POST['so_dien_thoai'] ?? '');
        $email = trim($_POST['email'] ?? '');

        if ($hoTen === '' || $email === '') {
            $this->redirectWithMessage(
                'Họ tên và email không được để trống!',
                'index.php?act=ho-so'
            );
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->redirectWithMessage(
                'Địa chỉ email không hợp lệ!',
                'index.php?act=ho-so'
            );
        }

        // Kiểm tra email có thuộc tài khoản khác không
        $check = $this->db->prepare(
            "SELECT ma_nguoi_dung
             FROM nguoi_dung
             WHERE email = :email
               AND ma_nguoi_dung != :id
             LIMIT 1"
        );

        $check->execute([
            ':email' => $email,
            ':id' => $maNguoiDung
        ]);

        if ($check->fetch()) {
            $this->redirectWithMessage(
                'Email này đã được sử dụng bởi tài khoản khác!',
                'index.php?act=ho-so'
            );
        }

        $sql = "UPDATE nguoi_dung
                SET ho_ten = :ho_ten,
                    so_dien_thoai = :so_dien_thoai,
                    email = :email
                WHERE ma_nguoi_dung = :id";

        $stmt = $this->db->prepare($sql);

        $success = $stmt->execute([
            ':ho_ten' => $hoTen,
            ':so_dien_thoai' => $soDienThoai,
            ':email' => $email,
            ':id' => $maNguoiDung
        ]);

        if ($success) {
            $_SESSION['user']['ho_ten'] = $hoTen;
            $_SESSION['user']['email'] = $email;
            $_SESSION['user']['so_dien_thoai'] = $soDienThoai;

            $this->redirectWithMessage(
                'Cập nhật thông tin cá nhân thành công!',
                'index.php?act=ho-so'
            );
        }

        $this->redirectWithMessage(
            'Có lỗi xảy ra, vui lòng thử lại!',
            'index.php?act=ho-so'
        );
    }

    // =====================================================
    // 16. FORM ĐĂNG NHẬP
    // =====================================================

    public function formDangNhap()
    {
        $path = __DIR__ . '/../login.php';

        if (file_exists($path)) {
            require_once $path;
            return;
        }

        echo '<h3>Không tìm thấy login.php!</h3>';
    }

    // =====================================================
    // 17. XỬ LÝ ĐĂNG NHẬP
    // =====================================================

    public function xuLyDangNhap()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?act=login');
            exit();
        }

        $email = trim($_POST['email'] ?? '');
        $matKhau = $_POST['mat_khau'] ?? '';

        if ($email === '' || $matKhau === '') {
            $this->redirectWithMessage(
                'Vui lòng nhập email và mật khẩu!',
                'index.php?act=login'
            );
        }

        $sql = "SELECT *
                FROM nguoi_dung
                WHERE email = :email
                LIMIT 1";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':email' => $email
        ]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        $validPassword = false;

        if ($user) {
            $storedPassword = $user['mat_khau'];

            // Hỗ trợ cả mật khẩu cũ và mật khẩu đã mã hóa
            if (password_verify($matKhau, $storedPassword)) {
                $validPassword = true;
            } elseif (
                hash_equals((string)$storedPassword, (string)$matKhau)
            ) {
                $validPassword = true;

                // Chuyển mật khẩu cũ sang dạng mã hóa sau đăng nhập
                $newHash = password_hash(
                    $matKhau,
                    PASSWORD_DEFAULT
                );

                $update = $this->db->prepare(
                    "UPDATE nguoi_dung
                     SET mat_khau = :mat_khau
                     WHERE ma_nguoi_dung = :id"
                );

                $update->execute([
                    ':mat_khau' => $newHash,
                    ':id' => $user['ma_nguoi_dung']
                ]);
            }
        }

        if ($user && $validPassword) {
            session_regenerate_id(true);

            $_SESSION['user'] = $user;

            // Đồng bộ mật khẩu đã mã hóa vào session
            if (isset($newHash)) {
                $_SESSION['user']['mat_khau'] = $newHash;
            }

            $this->redirectWithMessage(
                'Đăng nhập thành công!',
                'index.php?act=home'
            );
        }

        $this->redirectWithMessage(
            'Email hoặc mật khẩu không chính xác!',
            'index.php?act=login'
        );
    }

    // =====================================================
    // 18. FORM ĐĂNG KÝ
    // =====================================================

    public function formDangKy()
    {
        $path = __DIR__ . '/../register.php';

        if (file_exists($path)) {
            require_once $path;
            return;
        }

        echo '<h3>Không tìm thấy register.php!</h3>';
    }

    // =====================================================
    // 19. XỬ LÝ ĐĂNG KÝ
    // =====================================================

    public function xuLyDangKy()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?act=register');
            exit();
        }

        $hoTen = trim($_POST['ho_ten'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $soDienThoai = trim($_POST['so_dien_thoai'] ?? '');
        $matKhau = $_POST['mat_khau'] ?? '';

        if (
            $hoTen === '' ||
            $email === '' ||
            $matKhau === ''
        ) {
            $this->redirectWithMessage(
                'Vui lòng điền đầy đủ thông tin bắt buộc!',
                'index.php?act=register'
            );
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->redirectWithMessage(
                'Email không hợp lệ!',
                'index.php?act=register'
            );
        }

        if (strlen($matKhau) < 6) {
            $this->redirectWithMessage(
                'Mật khẩu phải có ít nhất 6 ký tự!',
                'index.php?act=register'
            );
        }

        $check = $this->db->prepare(
            "SELECT ma_nguoi_dung
             FROM nguoi_dung
             WHERE email = :email
             LIMIT 1"
        );

        $check->execute([
            ':email' => $email
        ]);

        if ($check->fetch()) {
            $this->redirectWithMessage(
                'Email này đã được đăng ký!',
                'index.php?act=register'
            );
        }

        // Mã hóa mật khẩu trước khi lưu
        $hashedPassword = password_hash(
            $matKhau,
            PASSWORD_DEFAULT
        );

        $sql = "INSERT INTO nguoi_dung (
                    ho_ten,
                    email,
                    so_dien_thoai,
                    mat_khau,
                    vai_tro
                )
                VALUES (
                    :ho_ten,
                    :email,
                    :so_dien_thoai,
                    :mat_khau,
                    'benh_nhan'
                )";

        $stmt = $this->db->prepare($sql);

        $success = $stmt->execute([
            ':ho_ten' => $hoTen,
            ':email' => $email,
            ':so_dien_thoai' => $soDienThoai,
            ':mat_khau' => $hashedPassword
        ]);

        if ($success) {
            $this->redirectWithMessage(
                'Đăng ký tài khoản thành công! Vui lòng đăng nhập.',
                'index.php?act=login'
            );
        }

        $this->redirectWithMessage(
            'Có lỗi xảy ra, vui lòng thử lại!',
            'index.php?act=register'
        );
    }
}
