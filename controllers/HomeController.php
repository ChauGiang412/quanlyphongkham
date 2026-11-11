<?php
require_once __DIR__ . '/../config/Database.php';

class HomeController {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // Hiển thị trang chủ với danh sách chuyên khoa, bác sĩ và gói khám
    public function index() {
        // Lấy danh sách chuyên khoa
        $danhSachChuyenKhoa = $this->db->query("SELECT * FROM chuyen_khoa ORDER BY ma_chuyen_khoa DESC")->fetchAll(PDO::FETCH_ASSOC);

        // Lấy danh sách bác sĩ kèm tên chuyên khoa
        $queryBacSi = "SELECT bs.*, ck.ten_chuyen_khoa 
                       FROM bac_si bs 
                       LEFT JOIN chuyen_khoa ck ON bs.ma_chuyen_khoa = ck.ma_chuyen_khoa 
                       ORDER BY bs.ma_bac_si DESC";
        $danhSachBacSi = $this->db->query($queryBacSi)->fetchAll(PDO::FETCH_ASSOC);

        // Lấy danh sách gói khám
        $danhSachGoiKham = $this->db->query("SELECT * FROM goi_kham ORDER BY ma_goi_kham DESC")->fetchAll(PDO::FETCH_ASSOC);

        // Gọi hiển thị giao diện trang chủ
        include_once 'views/home.php';
    }

    // Xem chi tiết một Chuyên Khoa
    public function chiTietChuyenKhoa() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $stmt = $this->db->prepare("SELECT * FROM chuyen_khoa WHERE ma_chuyen_khoa = :id");
            $stmt->execute([':id' => $id]);
            $chiTiet = $stmt->fetch(PDO::FETCH_ASSOC);

            // Lấy danh sách bác sĩ thuộc chuyên khoa này
            $stmtBs = $this->db->prepare("SELECT * FROM bac_si WHERE ma_chuyen_khoa = :id");
            $stmtBs->execute([':id' => $id]);
            $danhSachBacSiTheoCK = $stmtBs->fetchAll(PDO::FETCH_ASSOC);

            // Kiểm tra xem view chi tiết chuyên khoa có tồn tại không, nếu có thì include, không thì quay về trang chủ
            if (file_exists('views/chi_tiet_chuyen_khoa.php')) {
                include_once 'views/chi_tiet_chuyen_khoa.php';
            } else {
                include_once 'views/home.php';
            }
        } else {
            header('Location: index.php?act=home');
            exit();
        }
    }

    // Xem chi tiết một Bác Sĩ
    public function chiTietBacSi() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $stmt = $this->db->prepare("SELECT bs.*, ck.ten_chuyen_khoa 
                                        FROM bac_si bs 
                                        LEFT JOIN chuyen_khoa ck ON bs.ma_chuyen_khoa = ck.ma_chuyen_khoa 
                                        WHERE bs.ma_bac_si = :id");
            $stmt->execute([':id' => $id]);
            $chiTiet = $stmt->fetch(PDO::FETCH_ASSOC);

            if (file_exists('views/chi_tiet_bac_si.php')) {
                include_once 'views/chi_tiet_bac_si.php';
            } else {
                include_once 'views/home.php';
            }
        } else {
            header('Location: index.php?act=home');
            exit();
        }
    }

    // Xem chi tiết một Gói Khám
    public function chiTietGoiKham() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $stmt = $this->db->prepare("SELECT * FROM goi_kham WHERE ma_goi_kham = :id");
            $stmt->execute([':id' => $id]);
            $chiTiet = $stmt->fetch(PDO::FETCH_ASSOC);

            if (file_exists('views/chi_tiet_goi_kham.php')) {
                include_once 'views/chi_tiet_goi_kham.php';
            } else {
                include_once 'views/home.php';
            }
        } else {
            header('Location: index.php?act=home');
            exit();
        }
    }
}