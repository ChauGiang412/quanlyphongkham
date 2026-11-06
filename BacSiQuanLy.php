

<?php


class BacSiQuanLy {
    private PDO $db;


    public function __construct(PDO $db) {
        $this->db = $db;
    }


    // Lấy hồ sơ bác sĩ theo tài khoản đăng nhập
    public function getDoctorByUserId(int $maNguoiDung): ?array {
        $sql = "SELECT bs.*, nd.ho_ten, nd.email, nd.so_dien_thoai,
                       nd.gioi_tinh, nd.ngay_sinh, nd.dia_chi,
                       ck.ten_chuyen_khoa
                FROM bac_si bs
                INNER JOIN nguoi_dung nd
                    ON nd.ma_nguoi_dung = bs.ma_nguoi_dung
                LEFT JOIN chuyen_khoa ck
                    ON ck.ma_chuyen_khoa = bs.ma_chuyen_khoa
                WHERE bs.ma_nguoi_dung = :id
                LIMIT 1";


        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $maNguoiDung]);


        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }


    // Thống kê trang tổng quan
    public function getDashboardStats(int $maBacSi): array {
        $sql = "SELECT
                    COUNT(*) AS tong_lich,
                    SUM(CASE WHEN trang_thai = 'cho_xac_nhan'
                        THEN 1 ELSE 0 END) AS cho_xac_nhan,
                    SUM(CASE WHEN trang_thai = 'da_xac_nhan'
                        THEN 1 ELSE 0 END) AS da_xac_nhan,
                    SUM(CASE WHEN trang_thai = 'da_kham'
                        THEN 1 ELSE 0 END) AS da_kham,
                    SUM(CASE WHEN trang_thai = 'da_huy'
                        THEN 1 ELSE 0 END) AS da_huy,
                    SUM(CASE WHEN ngay_kham = CURDATE()
                        AND trang_thai <> 'da_huy'
                        THEN 1 ELSE 0 END) AS hom_nay
                FROM lich_kham
                WHERE ma_bac_si = :ma_bac_si";


        $stmt = $this->db->prepare($sql);
        $stmt->execute([':ma_bac_si' => $maBacSi]);


        $row = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];


        foreach ([
            'tong_lich',
            'cho_xac_nhan',
            'da_xac_nhan',
            'da_kham',
            'da_huy',
            'hom_nay'
        ] as $key) {
            $row[$key] = (int)($row[$key] ?? 0);
        }


        return $row;
    }


    // Lấy các lịch khám sắp tới
    public function getUpcomingAppointments(
        int $maBacSi,
        int $limit = 8
    ): array {
        $limit = max(1, min(30, $limit));


        $sql = "SELECT lk.*,
                       nd.ho_ten AS ten_benh_nhan,
                       nd.so_dien_thoai,
                       nd.email,
                       ck.ten_chuyen_khoa
                FROM lich_kham lk
                INNER JOIN nguoi_dung nd
                    ON nd.ma_nguoi_dung = lk.ma_nguoi_dung
                LEFT JOIN chuyen_khoa ck
                    ON ck.ma_chuyen_khoa = lk.ma_chuyen_khoa
                WHERE lk.ma_bac_si = :ma_bac_si
                  AND lk.trang_thai IN (
                      'cho_xac_nhan',
                      'da_xac_nhan'
                  )
                ORDER BY lk.ngay_kham ASC, lk.gio_kham ASC
                LIMIT {$limit}";


        $stmt = $this->db->prepare($sql);
        $stmt->execute([':ma_bac_si' => $maBacSi]);


        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Danh sách lịch khám, có lọc trạng thái và ngày
    public function getAppointments(
        int $maBacSi,
        string $status = '',
        string $date = ''
    ): array {
        $where = ['lk.ma_bac_si = :ma_bac_si'];
        $params = [':ma_bac_si' => $maBacSi];


        $allowedStatuses = [
            'cho_xac_nhan',
            'da_xac_nhan',
            'da_kham',
            'da_huy'
        ];


        if ($status !== '' &&
            in_array($status, $allowedStatuses, true)) {
            $where[] = 'lk.trang_thai = :trang_thai';
            $params[':trang_thai'] = $status;
        }


        if (
            $date !== '' &&
            preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)
        ) {
            $where[] = 'lk.ngay_kham = :ngay_kham';
            $params[':ngay_kham'] = $date;
        }


        $sql = "SELECT lk.*,
                       nd.ho_ten AS ten_benh_nhan,
                       nd.so_dien_thoai,
                       nd.email,
                       ck.ten_chuyen_khoa
                FROM lich_kham lk
                INNER JOIN nguoi_dung nd
                    ON nd.ma_nguoi_dung = lk.ma_nguoi_dung
                LEFT JOIN chuyen_khoa ck
                    ON ck.ma_chuyen_khoa = lk.ma_chuyen_khoa
                WHERE " . implode(' AND ', $where) . "
                ORDER BY lk.ngay_kham DESC, lk.gio_kham DESC";


        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);


        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Chi tiết một lịch khám, chỉ lấy lịch của bác sĩ đang đăng nhập
    public function getAppointmentDetail(
        int $maLichKham,
        int $maBacSi
    ): ?array {
        $sql = "SELECT lk.*,
                       nd.ho_ten AS ten_benh_nhan,
                       nd.email,
                       nd.so_dien_thoai,
                       nd.gioi_tinh,
                       nd.ngay_sinh,
                       nd.dia_chi,
                       ck.ten_chuyen_khoa
                FROM lich_kham lk
                INNER JOIN nguoi_dung nd
                    ON nd.ma_nguoi_dung = lk.ma_nguoi_dung
                LEFT JOIN chuyen_khoa ck
                    ON ck.ma_chuyen_khoa = lk.ma_chuyen_khoa
                WHERE lk.ma_lich_kham = :ma_lich_kham
                  AND lk.ma_bac_si = :ma_bac_si
                LIMIT 1";


        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':ma_lich_kham' => $maLichKham,
            ':ma_bac_si' => $maBacSi
        ]);


        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }


    // Lấy phiếu khám
    public function getMedicalRecord(int $maLichKham): ?array {
        $sql = "SELECT *
                FROM phieu_kham_benh
                WHERE ma_lich_kham = :id
                LIMIT 1";


        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $maLichKham]);


        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }


    // Xác nhận hoặc hủy lịch khám
    public function updateAppointmentStatus(
        int $maLichKham,
        int $maBacSi,
        string $status
    ): bool {
        if (!in_array(
            $status,
            ['da_xac_nhan', 'da_huy'],
            true
        )) {
            return false;
        }


        $sql = "UPDATE lich_kham
                SET trang_thai = :trang_thai
                WHERE ma_lich_kham = :ma_lich_kham
                  AND ma_bac_si = :ma_bac_si
                  AND trang_thai IN (
                      'cho_xac_nhan',
                      'da_xac_nhan'
                  )";


        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':trang_thai' => $status,
            ':ma_lich_kham' => $maLichKham,
            ':ma_bac_si' => $maBacSi
        ]);


        return $stmt->rowCount() > 0;
    }


    // Lưu kết quả khám và chuyển trạng thái sang đã khám
    public function saveMedicalRecord(
        int $maLichKham,
        int $maBacSi,
        string $chuanDoan,
        string $donThuoc,
        string $loiDan,
        ?string $ngayTaiKham
    ): bool {
        try {
            $this->db->beginTransaction();


            // Kiểm tra lịch khám có thuộc bác sĩ không
            $check = $this->db->prepare(
                "SELECT trang_thai
                 FROM lich_kham
                 WHERE ma_lich_kham = :id
                   AND ma_bac_si = :bac_si
                 FOR UPDATE"
            );


            $check->execute([
                ':id' => $maLichKham,
                ':bac_si' => $maBacSi
            ]);


            $appointment = $check->fetch(PDO::FETCH_ASSOC);


            if (
                !$appointment ||
                !in_array(
                    $appointment['trang_thai'],
                    ['da_xac_nhan', 'da_kham'],
                    true
                )
            ) {
                $this->db->rollBack();
                return false;
            }


            // Thêm mới hoặc cập nhật phiếu khám
            $sql = "INSERT INTO phieu_kham_benh
                        (
                            ma_lich_kham,
                            chuan_doan,
                            don_thuoc,
                            loi_dan_bac_si,
                            ngay_tai_kham
                        )
                    VALUES
                        (
                            :ma_lich_kham,
                            :chuan_doan,
                            :don_thuoc,
                            :loi_dan,
                            :ngay_tai_kham
                        )
                    ON DUPLICATE KEY UPDATE
                        chuan_doan = VALUES(chuan_doan),
                        don_thuoc = VALUES(don_thuoc),
                        loi_dan_bac_si = VALUES(loi_dan_bac_si),
                        ngay_tai_kham = VALUES(ngay_tai_kham)";


            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':ma_lich_kham' => $maLichKham,
                ':chuan_doan' => $chuanDoan,
                ':don_thuoc' => $donThuoc !== ''
                    ? $donThuoc : null,
                ':loi_dan' => $loiDan !== ''
                    ? $loiDan : null,
                ':ngay_tai_kham' => $ngayTaiKham
            ]);


            // Hoàn thành lịch khám
            $update = $this->db->prepare(
                "UPDATE lich_kham
                 SET trang_thai = 'da_kham'
                 WHERE ma_lich_kham = :id
                   AND ma_bac_si = :bac_si"
            );


            $update->execute([
                ':id' => $maLichKham,
                ':bac_si' => $maBacSi
            ]);


            $this->db->commit();
            return true;


        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }


            throw $e;
        }
    }


    // Danh sách bệnh nhân từng khám với bác sĩ
    public function getPatients(int $maBacSi): array {
        $sql = "SELECT
                    nd.ma_nguoi_dung,
                    nd.ho_ten,
                    nd.email,
                    nd.so_dien_thoai,
                    COUNT(lk.ma_lich_kham) AS tong_lan_kham,
                    MAX(lk.ngay_kham) AS lan_kham_gan_nhat
                FROM lich_kham lk
                INNER JOIN nguoi_dung nd
                    ON nd.ma_nguoi_dung = lk.ma_nguoi_dung
                WHERE lk.ma_bac_si = :ma_bac_si
                GROUP BY
                    nd.ma_nguoi_dung,
                    nd.ho_ten,
                    nd.email,
                    nd.so_dien_thoai
                ORDER BY lan_kham_gan_nhat DESC";


        $stmt = $this->db->prepare($sql);
        $stmt->execute([':ma_bac_si' => $maBacSi]);


        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Danh sách đánh giá của bệnh nhân
    public function getReviews(int $maBacSi): array {
        $sql = "SELECT
                    dg.*,
                    nd.ho_ten AS ten_benh_nhan,
                    lk.ngay_kham,
                    lk.gio_kham
                FROM danh_gia dg
                INNER JOIN nguoi_dung nd
                    ON nd.ma_nguoi_dung = dg.ma_nguoi_dung
                LEFT JOIN lich_kham lk
                    ON lk.ma_lich_kham = dg.ma_lich_kham
                WHERE dg.ma_bac_si = :ma_bac_si
                ORDER BY dg.ma_danh_gia DESC";


        $stmt = $this->db->prepare($sql);
        $stmt->execute([':ma_bac_si' => $maBacSi]);


        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Lấy hồ sơ bác sĩ
    public function getProfile(int $maBacSi): ?array {
        $sql = "SELECT
                    bs.*,
                    nd.ma_nguoi_dung,
                    nd.ho_ten,
                    nd.email,
                    nd.so_dien_thoai,
                    nd.gioi_tinh,
                    nd.ngay_sinh,
                    nd.dia_chi,
                    ck.ten_chuyen_khoa
                FROM bac_si bs
                INNER JOIN nguoi_dung nd
                    ON nd.ma_nguoi_dung = bs.ma_nguoi_dung
                LEFT JOIN chuyen_khoa ck
                    ON ck.ma_chuyen_khoa = bs.ma_chuyen_khoa
                WHERE bs.ma_bac_si = :id
                LIMIT 1";


        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $maBacSi]);


        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }


    // Cập nhật hồ sơ bác sĩ
    public function updateProfile(
        int $maBacSi,
        int $maNguoiDung,
        string $hoTen,
        string $email,
        string $soDienThoai,
        string $tenBacSi,
        string $bangCap,
        int $kinhNghiem,
        string $gioiThieu
    ): bool {
        try {
            $this->db->beginTransaction();


            // Kiểm tra email có bị trùng không
            $check = $this->db->prepare(
                "SELECT ma_nguoi_dung
                 FROM nguoi_dung
                 WHERE email = :email
                   AND ma_nguoi_dung <> :id
                 LIMIT 1"
            );


            $check->execute([
                ':email' => $email,
                ':id' => $maNguoiDung
            ]);


            if ($check->fetch()) {
                $this->db->rollBack();
                return false;
            }


            // Cập nhật thông tin tài khoản
            $user = $this->db->prepare(
                "UPDATE nguoi_dung
                 SET ho_ten = :ho_ten,
                     email = :email,
                     so_dien_thoai = :phone
                 WHERE ma_nguoi_dung = :id"
            );


            $user->execute([
                ':ho_ten' => $hoTen,
                ':email' => $email,
                ':phone' => $soDienThoai !== ''
                    ? $soDienThoai : null,
                ':id' => $maNguoiDung
            ]);


            // Cập nhật thông tin chuyên môn
            $doctor = $this->db->prepare(
                "UPDATE bac_si
                 SET ten_bac_si = :ten,
                     bang_cap = :bang_cap,
                     so_nam_kinh_nghiem = :kinh_nghiem,
                     gioi_thieu = :gioi_thieu
                 WHERE ma_bac_si = :id
                   AND ma_nguoi_dung = :user_id"
            );


            $doctor->execute([
                ':ten' => $tenBacSi,
                ':bang_cap' => $bangCap !== ''
                    ? $bangCap : null,
                ':kinh_nghiem' => $kinhNghiem,
                ':gioi_thieu' => $gioiThieu !== ''
                    ? $gioiThieu : null,
                ':id' => $maBacSi,
                ':user_id' => $maNguoiDung
            ]);


            $this->db->commit();
            return true;


        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }


            throw $e;
        }
    }
}





