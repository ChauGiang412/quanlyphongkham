
<?php

require_once __DIR__ . '/../config/Database.php';

class AuthController
{
    private ?PDO $db = null;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        try {
            $database = new Database();
            $this->db = $database->getConnection();
        } catch (Throwable $e) {
            $this->db = null;
        }
    }

    /**
     * Kiểm tra kết nối cơ sở dữ liệu
     */
    private function checkDatabase()
    {
        if (!$this->db instanceof PDO) {
            die('Không thể kết nối cơ sở dữ liệu. Vui lòng kiểm tra config/Database.php.');
        }
    }

    /**
     * Tạo CSRF token
     */
    private function getCsrfToken(): string
    {
        if (empty($_SESSION['auth_csrf'])) {
            $_SESSION['auth_csrf'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['auth_csrf'];
    }

    /**
     * Kiểm tra CSRF token
     */
    private function validateCsrf(): bool
    {
        $token = $_POST['csrf_token'] ?? '';

        return !empty($token)
            && !empty($_SESSION['auth_csrf'])
            && hash_equals($_SESSION['auth_csrf'], $token);
    }

    /**
     * Chuyển hướng theo vai trò tài khoản
     */
    private function redirectByRole($vaiTro)
    {
        switch ($vaiTro) {
            case 'quan_tri':
                header('Location: index.php?act=admin-dashboard');
                exit;

            case 'bac_si':
                header('Location: index.php?act=bac-si-dashboard');
                exit;

            default:
                header('Location: index.php?act=home');
                exit;
        }
    }

    /**
     * Đăng nhập
     */
    public function login()
    {
        $this->checkDatabase();

        $error = '';
        $success = $_SESSION['password_reset_success'] ?? '';
        unset($_SESSION['password_reset_success']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $matKhau = $_POST['mat_khau'] ?? '';

            if ($email === '' || $matKhau === '') {
                $error = 'Vui lòng nhập đầy đủ email và mật khẩu.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Địa chỉ email không hợp lệ.';
            } else {
                try {
                    $sql = "SELECT * FROM nguoi_dung WHERE email = :email LIMIT 1";
                    $stmt = $this->db->prepare($sql);
                    $stmt->execute(['email' => $email]);

                    $user = $stmt->fetch(PDO::FETCH_ASSOC);

                    if (!$user) {
                        $error = 'Email hoặc mật khẩu không chính xác.';
                    } else {
                        $storedPassword = $user['mat_khau'];

                        // Kiểm tra mật khẩu đã mã hóa
                        $passwordValid = password_verify(
                            $matKhau,
                            $storedPassword
                        );

                        // Hỗ trợ tài khoản cũ đang lưu mật khẩu dạng văn bản
                        if (!$passwordValid && hash_equals(
                            (string)$storedPassword,
                            (string)$matKhau
                        )) {
                            $passwordValid = true;

                            // Chuyển mật khẩu cũ sang dạng mã hóa
                            $newHash = password_hash(
                                $matKhau,
                                PASSWORD_DEFAULT
                            );

                            $update = $this->db->prepare(
                                "UPDATE nguoi_dung
                                 SET mat_khau = :mat_khau
                                 WHERE ma_nguoi_dung = :ma_nguoi_dung"
                            );

                            $update->execute([
                                'mat_khau' => $newHash,
                                'ma_nguoi_dung' => $user['ma_nguoi_dung']
                            ]);

                            $user['mat_khau'] = $newHash;
                        }

                        if (!$passwordValid) {
                            $error = 'Email hoặc mật khẩu không chính xác.';
                        } elseif (($user['trang_thai_tai_khoan'] ?? 'hoat_dong') === 'bi_khoa') {
                            $error = 'Tài khoản đã bị khóa. Vui lòng liên hệ phòng khám để được hỗ trợ.';
                        } else {
                            session_regenerate_id(true);

                            unset($user['mat_khau']);

                            $_SESSION['user'] = $user;
                            $_SESSION['vai_tro'] = $user['vai_tro'];

                            $this->redirectByRole($user['vai_tro']);
                        }
                    }
                } catch (PDOException $e) {
                    $error = 'Có lỗi xảy ra khi đăng nhập. Vui lòng thử lại.';
                }
            }
        }

        $view = __DIR__ . '/../login.php';

        if (!file_exists($view)) {
            $view = __DIR__ . '/../views/login.php';
        }

        require $view;
    }

    /**
     * Đăng ký tài khoản bệnh nhân
     */
    public function register()
    {
        $this->checkDatabase();

        $error = '';
        $success = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $hoTen = trim($_POST['ho_ten'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $soDienThoai = trim($_POST['so_dien_thoai'] ?? '');
            $matKhau = $_POST['mat_khau'] ?? '';
            $xacNhanMatKhau = $_POST['xac_nhan_mat_khau'] ?? '';

            if (
                $hoTen === '' ||
                $email === '' ||
                $soDienThoai === '' ||
                $matKhau === '' ||
                $xacNhanMatKhau === ''
            ) {
                $error = 'Vui lòng nhập đầy đủ thông tin.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Địa chỉ email không hợp lệ.';
            } elseif (mb_strlen($matKhau) < 6) {
                $error = 'Mật khẩu phải có ít nhất 6 ký tự.';
            } elseif ($matKhau !== $xacNhanMatKhau) {
                $error = 'Mật khẩu xác nhận không khớp.';
            } else {
                try {
                    // Kiểm tra email đã tồn tại
                    $check = $this->db->prepare(
                        "SELECT ma_nguoi_dung
                         FROM nguoi_dung
                         WHERE email = :email
                         LIMIT 1"
                    );

                    $check->execute(['email' => $email]);

                    if ($check->fetch()) {
                        $error = 'Email này đã được sử dụng.';
                    } else {
                        $hashedPassword = password_hash(
                            $matKhau,
                            PASSWORD_DEFAULT
                        );

                        $sql = "INSERT INTO nguoi_dung
                                (ho_ten, email, so_dien_thoai, mat_khau, vai_tro)
                                VALUES
                                (:ho_ten, :email, :so_dien_thoai, :mat_khau, 'benh_nhan')";

                        $stmt = $this->db->prepare($sql);

                        $stmt->execute([
                            'ho_ten' => $hoTen,
                            'email' => $email,
                            'so_dien_thoai' => $soDienThoai,
                            'mat_khau' => $hashedPassword
                        ]);

                        $success = 'Đăng ký tài khoản thành công. Bạn có thể đăng nhập ngay.';
                    }
                } catch (PDOException $e) {
                    $error = 'Có lỗi xảy ra khi đăng ký. Vui lòng thử lại.';
                }
            }
        }

        $view = __DIR__ . '/../register.php';

        if (!file_exists($view)) {
            $view = __DIR__ . '/../views/register.php';
        }

        require $view;
    }

    /**
     * Quên mật khẩu - đổi mật khẩu trực tiếp bằng email
     * Không sử dụng Gmail, SMS hoặc OTP.
     */
    public function forgotPassword()
    {
        $this->checkDatabase();

        $error = '';
        $success = '';
        $csrfToken = $this->getCsrfToken();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (!$this->validateCsrf()) {
                $error = 'Phiên làm việc không hợp lệ. Vui lòng tải lại trang.';
            } else {
                $email = trim($_POST['email'] ?? '');
                $matKhau = $_POST['mat_khau'] ?? '';
                $xacNhanMatKhau = $_POST['xac_nhan_mat_khau'] ?? '';

                if (
                    $email === '' ||
                    $matKhau === '' ||
                    $xacNhanMatKhau === ''
                ) {
                    $error = 'Vui lòng nhập đầy đủ thông tin.';
                } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $error = 'Địa chỉ email không hợp lệ.';
                } elseif (mb_strlen($matKhau) < 8) {
                    $error = 'Mật khẩu mới phải có ít nhất 8 ký tự.';
                } elseif ($matKhau !== $xacNhanMatKhau) {
                    $error = 'Mật khẩu xác nhận không khớp.';
                } else {
                    try {
                        // Kiểm tra email có tồn tại trong hệ thống
                        $check = $this->db->prepare(
                            "SELECT ma_nguoi_dung
                             FROM nguoi_dung
                             WHERE email = :email
                             LIMIT 1"
                        );

                        $check->execute(['email' => $email]);

                        $user = $check->fetch(PDO::FETCH_ASSOC);

                        if (!$user) {
                            $error = 'Email này chưa được đăng ký trong hệ thống.';
                        } else {
                            // Mã hóa mật khẩu mới
                            $hashedPassword = password_hash(
                                $matKhau,
                                PASSWORD_DEFAULT
                            );

                            // Cập nhật mật khẩu
                            $update = $this->db->prepare(
                                "UPDATE nguoi_dung
                                 SET mat_khau = :mat_khau
                                 WHERE ma_nguoi_dung = :ma_nguoi_dung"
                            );

                            $update->execute([
                                'mat_khau' => $hashedPassword,
                                'ma_nguoi_dung' => $user['ma_nguoi_dung']
                            ]);

                            $success = 'Đổi mật khẩu thành công! Bạn có thể quay lại trang đăng nhập.';
                        }
                    } catch (PDOException $e) {
                        $error = 'Có lỗi xảy ra khi đổi mật khẩu. Vui lòng thử lại.';
                    }
                }
            }
        }

        require __DIR__ . '/../views/forgot_password.php';
    }

    /**
     * Đăng xuất
     */
    public function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();

        header('Location: index.php?act=home');
        exit;
    }
}
