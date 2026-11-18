<?php
class NguoiDung {
    private $conn;
    public function __construct($db) {
        $this->conn = $db;
    }
    public function login($email, $mat_khau) {
        $query = "SELECT * FROM nguoi_dung WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();
        if ($user) {
            return $user;
        }
        return false;
    }
}