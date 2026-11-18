<?php
class GoiKham {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Lấy danh sách gói khám đang hoạt động
    public function getAllGoiKham($limit = 100000, $offset = 0) {
        $limit = max(1, min(100000, (int)$limit)); $offset = max(0, (int)$offset);
        $sql = "SELECT *
                FROM goi_kham
                WHERE trang_thai = 'dang_hoat_dong'
                ORDER BY ma_goi_kham DESC
                LIMIT {$limit} OFFSET {$offset}";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countAll() {
        return (int)$this->conn->query("SELECT COUNT(*) FROM goi_kham WHERE trang_thai = 'dang_hoat_dong'")->fetchColumn();
    }

    // Lấy chi tiết gói khám
    public function getGoiKhamById($id) {
        $sql = "SELECT gk.*, ck.ten_chuyen_khoa
                FROM goi_kham gk
                LEFT JOIN chuyen_khoa ck
                ON ck.ma_chuyen_khoa = gk.ma_chuyen_khoa
                WHERE gk.ma_goi_kham = :id
                LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Lấy toàn bộ gói khám dành cho quản trị
    public function getAllForAdmin() {
        $sql = "SELECT *
                FROM goi_kham
                ORDER BY ma_goi_kham DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}