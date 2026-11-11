
<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Nạp các controller.
require_once __DIR__ . '/controllers/HomeController.php';
require_once __DIR__ . '/controllers/BenhNhanController.php';
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/AdminController.php';
require_once __DIR__ . '/controllers/ChatController.php';
require_once __DIR__ . '/controllers/BacSiController.php';

// Lấy chức năng cần thực hiện.
$act = $_GET['act'] ?? 'home';

// Hàm kiểm tra vai trò.
function requireRole(array $allowedRoles): void
{
    if (empty($_SESSION['user'])) {
        header('Location: index.php?act=login');
        exit;
    }

    $role = $_SESSION['user']['vai_tro']
        ?? $_SESSION['vai_tro']
        ?? '';

    if (!in_array($role, $allowedRoles, true)) {
        http_response_code(403);
        exit('Bạn không có quyền truy cập chức năng này.');
    }
}

switch ($act) {

    // =====================================
    // TRANG CHỦ
    // =====================================

    case 'home':
        $controller = new BenhNhanController();
        $controller->home();
        break;


    // =====================================
    // ĐĂNG NHẬP - ĐĂNG KÝ - ĐĂNG XUẤT
    // =====================================

    case 'login':
        $controller = new AuthController();
        $controller->login();
        break;

    case 'register':
        $controller = new AuthController();
        $controller->register();
        break;

    case 'logout':
    case 'dang-xuat':
        $controller = new AuthController();
        $controller->logout();
        break;


    // =====================================
    // CHỨC NĂNG BỆNH NHÂN
    // =====================================

    case 'chi-tiet-chuyen-khoa':
    case 'chi-tiet-bac-si':
    case 'chi-tiet-goi-kham':

        $controller = new BenhNhanController();

        if ($act === 'chi-tiet-chuyen-khoa') {
            $controller->chiTietChuyenKhoa();
        } elseif ($act === 'chi-tiet-bac-si') {
            $controller->chiTietBacSi();
        } else {
            $controller->chiTietGoiKham();
        }

        break;

    case 'chon-loai-lich':
        requireRole(['benh_nhan']);
        $controller = new BenhNhanController();
        $controller->chonLoaiLich();
        break;

    case 'dat-lich':
    case 'dat-lich-chuyen-khoa':
        requireRole(['benh_nhan']);
        $controller = new BenhNhanController();
        $controller->index();
        break;

    case 'dat-lich-exec':
    case 'xac-nhan-dat-lich':
        requireRole(['benh_nhan']);
        $controller = new BenhNhanController();
        $controller->datLich();
        break;

    case 'dat-goi-kham':
        requireRole(['benh_nhan']);
        $controller = new BenhNhanController();
        $controller->formDatGoiKham();
        break;

    case 'dat-goi-kham-exec':
    case 'xac-nhan-dat-goi':
        requireRole(['benh_nhan']);
        $controller = new BenhNhanController();
        $controller->xuLyDatGoiKham();
        break;

    case 'api-gio-da-dat':
        $controller = new BenhNhanController();
        $controller->getGioDaDatApi();
        break;

    case 'lich-su':
        requireRole(['benh_nhan']);
        $controller = new BenhNhanController();
        $controller->lichSu();
        break;

    case 'chi-tiet-lich':
        requireRole(['benh_nhan']);
        $controller = new BenhNhanController();
        $controller->chiTietLich();
        break;

    case 'huy-lich':
        requireRole(['benh_nhan']);
        $controller = new BenhNhanController();
        $controller->huyLich();
        break;

    case 'gui-danh-gia':
        requireRole(['benh_nhan']);
        $controller = new BenhNhanController();
        $controller->guiDanhGia();
        break;

    case 'ho-so':
        requireRole(['benh_nhan']);
        $controller = new BenhNhanController();
        $controller->hoSo();
        break;

    case 'cap-nhat-ho-so':
        requireRole(['benh_nhan']);
        $controller = new BenhNhanController();
        $controller->capNhatHoSo();
        break;


    // =====================================
    // CHAT
    // =====================================

    case 'chat-reply':
        requireRole(['benh_nhan']);
        $controller = new ChatController();
        $controller->reply();
        break;


    // =====================================
    // PHÂN HỆ BÁC SĨ
    // =====================================

    case 'bac-si':
    case 'bac-si-dashboard':
        requireRole(['bac_si']);
        $controller = new BacSiController();
        $controller->dashboard();
        break;

    case 'bac-si-lich-kham':
        requireRole(['bac_si']);
        $controller = new BacSiController();
        $controller->lichKham();
        break;

    case 'bac-si-chi-tiet-lich':
        requireRole(['bac_si']);
        $controller = new BacSiController();
        $controller->chiTietLich();
        break;

    case 'bac-si-cap-nhat-trang-thai':
        requireRole(['bac_si']);
        $controller = new BacSiController();
        $controller->capNhatTrangThai();
        break;

    case 'bac-si-luu-ket-qua':
        requireRole(['bac_si']);
        $controller = new BacSiController();
        $controller->luuKetQuaKham();
        break;

    case 'bac-si-benh-nhan':
        requireRole(['bac_si']);
        $controller = new BacSiController();
        $controller->benhNhan();
        break;

    case 'bac-si-danh-gia':
        requireRole(['bac_si']);
        $controller = new BacSiController();
        $controller->danhGia();
        break;

    case 'bac-si-ho-so':
        requireRole(['bac_si']);
        $controller = new BacSiController();
        $controller->hoSo();
        break;

    case 'bac-si-cap-nhat-ho-so':
        requireRole(['bac_si']);
        $controller = new BacSiController();
        $controller->capNhatHoSo();
        break;


    // =====================================
    // PHÂN HỆ QUẢN TRỊ VIÊN
    // =====================================

    case 'admin':
    case 'admin-dashboard':
        requireRole(['quan_tri']);
        $controller = new AdminController();
        $controller->dashboard();
        break;

    case 'admin-chuyen-khoa':
        requireRole(['quan_tri']);
        $controller = new AdminController();
        $controller->quanLyChuyenKhoa();
        break;

    case 'admin-xoa-chuyen-khoa':
        requireRole(['quan_tri']);
        $controller = new AdminController();
        $controller->xoaChuyenKhoa();
        break;

    case 'admin-bac-si':
        requireRole(['quan_tri']);
        $controller = new AdminController();
        $controller->quanLyBacSi();
        break;

    case 'admin-xoa-bac-si':
        requireRole(['quan_tri']);
        $controller = new AdminController();
        $controller->xoaBacSi();
        break;

    case 'admin-goi-kham':
        requireRole(['quan_tri']);
        $controller = new AdminController();
        $controller->quanLyGoiKham();
        break;

    case 'admin-xoa-goi-kham':
        requireRole(['quan_tri']);
        $controller = new AdminController();
        $controller->xoaGoiKham();
        break;

    case 'admin-lich-kham':
        requireRole(['quan_tri']);
        $controller = new AdminController();
        $controller->quanLyLichKham();
        break;

    case 'admin-duyet-lich':
        requireRole(['quan_tri']);
        $controller = new AdminController();
        $controller->duyetLich();
        break;

    case 'admin-huy-lich':
        requireRole(['quan_tri']);
        $controller = new AdminController();
        $controller->huyLich();
        break;

    case 'admin-da-kham':
        requireRole(['quan_tri']);
        $controller = new AdminController();
        $controller->daKham();
        break;

    case 'admin-benh-nhan':
        requireRole(['quan_tri']);
        $controller = new AdminController();
        $controller->quanLyBenhNhan();
        break;

    case 'admin-danh-gia':
        requireRole(['quan_tri']);
        $controller = new AdminController();
        $controller->quanLyDanhGia();
        break;

    case 'admin-thong-bao':
        requireRole(['quan_tri']);
        $controller = new AdminController();
        $controller->quanLyThongBao();
        break;


    // =====================================
    // KHÔNG TÌM THẤY TRANG
    // =====================================

    default:
        http_response_code(404);
        ?>

        <!DOCTYPE html>
        <html lang="vi">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>404 - Không tìm thấy trang</title>
            <script src="https://cdn.tailwindcss.com"></script>
        </head>

        <body class="bg-slate-50 min-h-screen flex items-center justify-center">
            <div class="text-center px-6">
                <h1 class="text-8xl font-bold text-teal-600 mb-4">404</h1>

                <h2 class="text-2xl font-bold text-slate-800 mb-3">
                    Không tìm thấy trang
                </h2>

                <p class="text-slate-500 mb-6">
                    Trang bạn đang tìm kiếm không tồn tại hoặc đã được di chuyển.
                </p>

                <a href="index.php?act=home"
                   class="inline-block bg-teal-600 hover:bg-teal-700 text-white px-6 py-3 rounded-xl font-semibold transition">
                    Quay về trang chủ
                </a>
            </div>
        </body>
        </html>

        <?php
        break;
}
