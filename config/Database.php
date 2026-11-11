
<?php
class Database {
    private $host = "127.0.0.1";
    private $db_name = "quanlyphongkham";
    private $username = "root";
    private $password = "";
    private $port = "3307";

    public function getConnection(): PDO {
        $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->db_name};charset=utf8mb4";

        try {
            $conn = new PDO($dsn, $this->username, $this->password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]);

            return $conn;

        } catch (PDOException $e) {
            throw new RuntimeException(
                "Không thể kết nối cơ sở dữ liệu: " . $e->getMessage(),
                0,
                $e
            );
        }
    }
}
