
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

    // Lấy danh sách lịch khám của một bệnh nhân
    public function getByNguoiDung($ma_nguoi_dung, $limit = null, $offset = 0) {
        $sql = "SELECT lk.*,
                       bs.ten_bac_si,
                       ck.ten_chuyen_khoa
                FROM lich_kham lk
                LEFT JOIN bac_si bs ON bs.ma_bac_si = lk.ma_bac_si
                LEFT JOIN chuyen_khoa ck ON ck.ma_chuyen_khoa = lk.ma_chuyen_khoa
                WHERE lk.ma_nguoi_dung = :ma_nguoi_dung
                ORDER BY lk.ngay_kham DESC, lk.gio_kham DESC";

        if ($limit !== null) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':ma_nguoi_dung', (int)$ma_nguoi_dung, PDO::PARAM_INT);
        if ($limit !== null) {
            $stmt->bindValue(':limit', max(1, (int)$limit), PDO::PARAM_INT);
            $stmt->bindValue(':offset', max(0, (int)$offset), PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countByNguoiDung($ma_nguoi_dung) {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM lich_kham WHERE ma_nguoi_dung = :ma_nguoi_dung"
        );
        $stmt->execute([':ma_nguoi_dung' => (int)$ma_nguoi_dung]);
        return (int)$stmt->fetchColumn();
    }

    // Lấy chi tiết phiếu khám của bệnh nhân
    // Chỉ cho phép xem lịch đã được xác nhận
    public function getChiTietPhieuKham($ma_lich_kham, $ma_nguoi_dung) {
        $sql = "SELECT lk.*,
                       bs.ten_bac_si,
                       ck.ten_chuyen_khoa
                FROM lich_kham lk
                LEFT JOIN bac_si bs
                    ON bs.ma_bac_si = lk.ma_bac_si
                LEFT JOIN chuyen_khoa ck
                    ON ck.ma_chuyen_khoa = lk.ma_chuyen_khoa
                WHERE lk.ma_lich_kham = :ma_lich_kham
                AND lk.ma_nguoi_dung = :ma_nguoi_dung
                AND lk.trang_thai = 'da_xac_nhan'
                LIMIT 1";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':ma_lich_kham' => $ma_lich_kham,
            ':ma_nguoi_dung' => $ma_nguoi_dung
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
