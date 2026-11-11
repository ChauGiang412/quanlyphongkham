<?php
class ChuyenKhoa {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Lấy toàn bộ chuyên khoa
    public function getAllChuyenKhoa() {
        $sql = "SELECT *
                FROM chuyen_khoa
                ORDER BY ma_chuyen_khoa DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy thông tin một chuyên khoa theo mã
    public function getChuyenKhoaById($id) {
        $sql = "SELECT *
                FROM chuyen_khoa
                WHERE ma_chuyen_khoa = :id
                LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Lấy danh sách bác sĩ thuộc chuyên khoa
    public function getDoctorsByChuyenKhoa($id) {
        $sql = "SELECT bs.*, ck.ten_chuyen_khoa
                FROM bac_si bs
                LEFT JOIN chuyen_khoa ck
                ON ck.ma_chuyen_khoa = bs.ma_chuyen_khoa
                WHERE bs.ma_chuyen_khoa = :id
                ORDER BY bs.ma_bac_si DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}