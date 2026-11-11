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

// Lấy tên chức năng.
$act = $_GET['act'] ?? 'home';

// Khởi tạo các controller dùng chung.
$benhNhanController = new BenhNhanController();
$authController = new AuthController();

// Điều hướng.
switch ($act) {

    // =====================================
    // TRANG CHỦ
    // =====================================

    case 'home':
        $benhNhanController->home();
        break;


    // =====================================
    // ĐĂNG NHẬP - ĐĂNG KÝ - ĐĂNG XUẤT
    // =====================================

    case 'login':
        $authController->login();
        break;

    case 'register':
        $authController->register();
        break;

    case 'logout':
    case 'dang-xuat':
        $authController->logout();
        break;


    // =====================================
    // CHỨC NĂNG CÔNG KHAI VÀ BỆNH NHÂN
    // =====================================

    case 'chi-tiet-chuyen-khoa':
        $benhNhanController->chiTietChuyenKhoa();
        break;

    case 'chi-tiet-bac-si':
        $benhNhanController->chiTietBacSi();
        break;

    case 'chi-tiet-goi-kham':
        $benhNhanController->chiTietGoiKham();
        break;

    case 'chon-loai-lich':
        $benhNhanController->chonLoaiLich();
        break;

    case 'dat-lich':
    case 'dat-lich-chuyen-khoa':
        $benhNhanController->index();
        break;

    case 'dat-lich-exec':
    case 'xac-nhan-dat-lich':
        $benhNhanController->datLich();
        break;

    case 'dat-goi-kham':
        $benhNhanController->formDatGoiKham();
        break;

    case 'dat-goi-kham-exec':
    case 'xac-nhan-dat-goi':
        $benhNhanController->xuLyDatGoiKham();
        break;

    case 'api-gio-da-dat':
        $benhNhanController->getGioDaDatApi();
        break;

    case 'lich-su':
        $benhNhanController->lichSu();
        break;

    case 'chi-tiet-lich':
        $benhNhanController->chiTietLich();
        break;

    case 'huy-lich':
        $benhNhanController->huyLich();
        break;

    case 'gui-danh-gia':
        $benhNhanController->guiDanhGia();
        break;

    case 'ho-so':
        $benhNhanController->hoSo();
        break;

    case 'cap-nhat-ho-so':
        $benhNhanController->capNhatHoSo();
        break;


    // =====================================
    // CHAT
    // =====================================

    case 'chat-reply':

        $chatController = new ChatController();
        $chatController->reply();

        break;


    // =====================================
    // PHÂN HỆ BÁC SĨ
    // =====================================

    case 'bac-si':
    case 'bac-si-dashboard':

        $bacSiController = new BacSiController();
        $bacSiController->dashboard();

        break;

    case 'bac-si-lich-kham':

        $bacSiController = new BacSiController();
        $bacSiController->lichKham();

        break;

    case 'bac-si-chi-tiet-lich':

        $bacSiController = new BacSiController();
        $bacSiController->chiTietLich();

        break;

    case 'bac-si-cap-nhat-trang-thai':

        $bacSiController = new BacSiController();
        $bacSiController->capNhatTrangThai();

        break;

    case 'bac-si-luu-ket-qua':

        $bacSiController = new BacSiController();
        $bacSiController->luuKetQuaKham();

        break;

    case 'bac-si-benh-nhan':

        $bacSiController = new BacSiController();
        $bacSiController->benhNhan();

        break;

    case 'bac-si-danh-gia':

        $bacSiController = new BacSiController();
        $bacSiController->danhGia();

        break;

    case 'bac-si-ho-so':

        $bacSiController = new BacSiController();
        $bacSiController->hoSo();

        break;

    case 'bac-si-cap-nhat-ho-so':

        $bacSiController = new BacSiController();
        $bacSiController->capNhatHoSo();

        break;


    // =====================================
    // PHÂN HỆ QUẢN TRỊ VIÊN
    // =====================================

    case 'admin':
    case 'admin-dashboard':

        $adminController = new AdminController();
        $adminController->dashboard();

        break;

    case 'admin-chuyen-khoa':

        $adminController = new AdminController();
        $adminController->quanLyChuyenKhoa();

        break;

    case 'admin-xoa-chuyen-khoa':

        $adminController = new AdminController();
        $adminController->xoaChuyenKhoa();

        break;

    case 'admin-bac-si':

        $adminController = new AdminController();
        $adminController->quanLyBacSi();

        break;

    case 'admin-xoa-bac-si':

        $adminController = new AdminController();
        $adminController->xoaBacSi();

        break;

    case 'admin-goi-kham':

        $adminController = new AdminController();
        $adminController->quanLyGoiKham();

        break;

    case 'admin-xoa-goi-kham':

        $adminController = new AdminController();
        $adminController->xoaGoiKham();

        break;

    case 'admin-lich-kham':

        $adminController = new AdminController();
        $adminController->quanLyLichKham();

        break;

    case 'admin-duyet-lich':

        $adminController = new AdminController();
        $adminController->duyetLich();

        break;

    case 'admin-huy-lich':

        $adminController = new AdminController();
        $adminController->huyLich();

        break;

    case 'admin-da-kham':

        $adminController = new AdminController();
        $adminController->daKham();

        break;

    case 'admin-benh-nhan':

        $adminController = new AdminController();
        $adminController->quanLyBenhNhan();

        break;

    case 'admin-danh-gia':

        $adminController = new AdminController();
        $adminController->quanLyDanhGia();

        break;

    case 'admin-thong-bao':

        $adminController = new AdminController();
        $adminController->quanLyThongBao();

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
            <meta name="viewport"
                  content="width=device-width, initial-scale=1.0">

            <title>404 - Không tìm thấy trang</title>

            <script src="https://cdn.tailwindcss.com"></script>
        </head>

        <body class="bg-slate-50 min-h-screen flex items-center justify-center">

            <div class="text-center px-6">

                <h1 class="text-8xl font-bold text-teal-600 mb-4">
                    404
                </h1>

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