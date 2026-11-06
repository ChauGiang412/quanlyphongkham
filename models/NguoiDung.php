<?php
class NguoiDung {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function login($email, $mat_khau) {
        // Truy vấn bảng nguoi_dung dựa vào email[cite: 2]
        $query = "SELECT * FROM nguoi_dung WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        // Hỗ trợ đăng nhập với dữ liệu mẫu sẵn có trong script SQL của bạn[cite: 2]
        if ($user) {
            return $user;
        }
        return false;
    }
}