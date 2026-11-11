<?php
require_once 'config/Database.php';
require_once 'models/ChuyenKhoa.php';

class ChuyenKhoaController {
    public function detail() {
        $ma_chuyen_khoa = $_GET['id'] ?? 0;
        
        $db = (new Database())->getConnection();
        $chuyenKhoaModel = new ChuyenKhoa($db);

        // Lấy thông tin chi tiết chuyên khoa và danh sách bác sĩ trực thuộc
        $chuyenKhoa = $chuyenKhoaModel->getChuyenKhoaById($ma_chuyen_khoa);
        $danhSachBacSi = $chuyenKhoaModel->getDoctorsByChuyenKhoa($ma_chuyen_khoa);

        if (!$chuyenKhoa) {
            header("Location: index.php?act=home");
            exit();
        }

        require_once 'views/header.php';
        require_once 'views/chi_tiet_chuyen_khoa.php';
        require_once 'views/footer.php';
    }
}