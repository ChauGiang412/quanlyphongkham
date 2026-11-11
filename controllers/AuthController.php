
<?php

require_once __DIR__ . '/../config/Database.php';

class AuthController
{
    private PDO $db;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $database = new Database();
        $this->db = $database->getConnection();
    }

    /**
     * Chuyển hướng người dùng theo vai trò.
     */
    private function redirectByRole(string $role): void
    {
        $role = strtolower(trim($role));

        switch ($role) {
            case 'quan_tri':
                header('Location: index.php?act=admin-dashboard');
                exit();

            case 'bac_si':
                header('Location: index.php?act=bac-si-dashboard');
                exit();

            case 'benh_nhan':
            default:
                header('Location: index.php?act=home');
                exit();
        }
    }

    /**
     * Hiển thị trang đăng nhập.
     */
    public function login(): void
    {
        $error = '';

        // Nếu đã đăng nhập và chỉ truy cập trang login,
        // chuyển người dùng đến trang phù hợp với vai trò.
        if (
            $_SERVER['REQUEST_METHOD'] !== 'POST'
            && !empty($_SESSION['user'])
        ) {
            $this->redirectByRole(
                $_SESSION['user']['vai_tro'] ?? 'benh_nhan'
            );
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['mat_khau'] ?? '';

            if ($email === '' || $password === '') {
                $error = 'Vui lòng nhập đầy đủ email và mật khẩu.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Địa chỉ email không hợp lệ.';
            } else {
                try {
                    $sql = "
                        SELECT *
                        FROM nguoi_dung
                        WHERE email = :email
                        LIMIT 1
                    ";

                    $stmt = $this->db->prepare($sql);
                    $stmt->execute([
                        ':email' => $email
                    ]);

                    $user = $stmt->fetch(PDO::FETCH_ASSOC);

                    if (!$user) {
                        $error = 'Email hoặc mật khẩu không chính xác.';
                    } else {
                        $storedPassword = $user['mat_khau'];

                        // Hỗ trợ mật khẩu đã mã hóa bằng password_hash.
                        // Đồng thời hỗ trợ dữ liệu mật khẩu cũ dạng văn bản.
                        $passwordCorrect = password_verify(
                            $password,
                            $storedPassword
                        );

                        if (!$passwordCorrect && $password === $storedPassword) {
                            $passwordCorrect = true;

                            // Mã hóa lại mật khẩu cũ sau khi đăng nhập thành công.
                            $newPassword = password_hash(
                                $password,
                                PASSWORD_DEFAULT
                            );

                            $update = $this->db->prepare("
                                UPDATE nguoi_dung
                                SET mat_khau = :password
                                WHERE ma_nguoi_dung = :id
                            ");

                            $update->execute([
                                ':password' => $newPassword,
                                ':id' => $user['ma_nguoi_dung']
                            ]);
                        }

                        if (!$passwordCorrect) {
                            $error = 'Email hoặc mật khẩu không chính xác.';
                        } else {
                            // Xóa session cũ và tạo session mới.
                            session_regenerate_id(true);

                            unset($user['mat_khau']);

                            $_SESSION['user'] = $user;
                            $_SESSION['ma_nguoi_dung'] =
                                $user['ma_nguoi_dung'];

                            $_SESSION['vai_tro'] =
                                trim($user['vai_tro']);

                            // Chuyển đến trang theo vai trò thực tế trong DB.
                            $this->redirectByRole(
                                $user['vai_tro']
                            );
                        }
                    }
                } catch (PDOException $e) {
                    $error = 'Có lỗi xảy ra khi đăng nhập. Vui lòng thử lại.';
                }
            }
        }

        // login.php nằm cùng cấp với index.php.
        require __DIR__ . '/../login.php';
    }

    /**
     * Hiển thị trang đăng ký.
     */
    public function register(): void
    {
        $error = '';
        $success = '';

        if (
            $_SERVER['REQUEST_METHOD'] !== 'POST'
            && !empty($_SESSION['user'])
        ) {
            $this->redirectByRole(
                $_SESSION['user']['vai_tro'] ?? 'benh_nhan'
            );
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $hoTen = trim($_POST['ho_ten'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $matKhau = $_POST['mat_khau'] ?? '';
            $xacNhanMatKhau = $_POST['xac_nhan_mat_khau']
                ?? $_POST['confirm_password']
                ?? '';

            $soDienThoai = trim(
                $_POST['so_dien_thoai'] ?? ''
            );

            if (
                $hoTen === '' ||
                $email === '' ||
                $matKhau === ''
            ) {
                $error = 'Vui lòng nhập đầy đủ thông tin bắt buộc.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Email không đúng định dạng.';
            } elseif (strlen($matKhau) < 6) {
                $error = 'Mật khẩu phải có ít nhất 6 ký tự.';
            } elseif (
                $xacNhanMatKhau !== '' &&
                $matKhau !== $xacNhanMatKhau
            ) {
                $error = 'Mật khẩu xác nhận không khớp.';
            } else {
                try {
                    // Kiểm tra email đã tồn tại hay chưa.
                    $check = $this->db->prepare("
                        SELECT ma_nguoi_dung
                        FROM nguoi_dung
                        WHERE email = :email
                        LIMIT 1
                    ");

                    $check->execute([
                        ':email' => $email
                    ]);

                    if ($check->fetch()) {
                        $error = 'Email này đã được sử dụng.';
                    } else {
                        $hashedPassword = password_hash(
                            $matKhau,
                            PASSWORD_DEFAULT
                        );

                        // Người đăng ký mới mặc định là bệnh nhân.
                        $sql = "
                            INSERT INTO nguoi_dung (
                                ho_ten,
                                email,
                                mat_khau,
                                so_dien_thoai,
                                vai_tro
                            ) VALUES (
                                :ho_ten,
                                :email,
                                :mat_khau,
                                :so_dien_thoai,
                                'benh_nhan'
                            )
                        ";

                        $stmt = $this->db->prepare($sql);

                        $stmt->execute([
                            ':ho_ten' => $hoTen,
                            ':email' => $email,
                            ':mat_khau' => $hashedPassword,
                            ':so_dien_thoai' =>
                                $soDienThoai !== ''
                                    ? $soDienThoai
                                    : null
                        ]);

                        // Đăng ký xong chuyển về trang đăng nhập.
                        header('Location: index.php?act=login');
                        exit();
                    }
                } catch (PDOException $e) {
                    $error = 'Không thể đăng ký tài khoản. Vui lòng thử lại.';
                }
            }
        }

        // register.php nằm cùng cấp với index.php.
        require __DIR__ . '/../register.php';
    }

    /**
     * Đăng xuất tài khoản.
     */
    public function logout(): void
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
        exit();
    }
}
