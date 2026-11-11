<?php
class LichKham {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Lấy các khung giờ đã được đặt của bác sĩ trong ngày
    public function getGioDaDat($ma_bac_si, $ngay_kham) {
        $sql = "SELECT gio_kham
                FROM lich_kham
                WHERE ma_bac_si = :ma_bac_si
                AND ngay_kham = :ngay_kham
                AND trang_thai <> 'da_huy'";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':ma_bac_si' => $ma_bac_si,
            ':ngay_kham' => $ngay_kham
        ]);

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    // Lấy lịch khám của một bệnh nhân
    public function getByNguoiDung($ma_nguoi_dung) {
        $sql = "SELECT lk.*, bs.ten_bac_si, ck.ten_chuyen_khoa
                FROM lich_kham lk
                LEFT JOIN bac_si bs
                ON bs.ma_bac_si = lk.ma_bac_si
                LEFT JOIN chuyen_khoa ck
                ON ck.ma_chuyen_khoa = lk.ma_chuyen_khoa
                WHERE lk.ma_nguoi_dung = :ma_nguoi_dung
                ORDER BY lk.ngay_kham DESC, lk.gio_kham DESC";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':ma_nguoi_dung' => $ma_nguoi_dung
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}