<?php
class BacSi {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Lấy toàn bộ danh sách bác sĩ kèm tên chuyên khoa
    public function getAllBacSi() {
        $sql = "SELECT bs.*, ck.ten_chuyen_khoa
                FROM bac_si bs
                LEFT JOIN chuyen_khoa ck
                ON ck.ma_chuyen_khoa = bs.ma_chuyen_khoa
                ORDER BY bs.ma_bac_si DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy thông tin chi tiết một bác sĩ
    public function getDoctorById($id) {
        $sql = "SELECT bs.*, ck.ten_chuyen_khoa
                FROM bac_si bs
                LEFT JOIN chuyen_khoa ck
                ON ck.ma_chuyen_khoa = bs.ma_chuyen_khoa
                WHERE bs.ma_bac_si = :id
                LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Lấy danh sách bác sĩ theo chuyên khoa
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