-- 2. Xóa và tạo lại toàn bộ cơ sở dữ liệu
DROP DATABASE IF EXISTS quan_ly_phong_kham;
CREATE DATABASE IF NOT EXISTS quan_ly_phong_kham DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci; 
USE quan_ly_phong_kham;

-- 3. Tạo lại các bảng theo đúng chuẩn
CREATE TABLE nguoi_dung (
    ma_nguoi_dung INT AUTO_INCREMENT PRIMARY KEY,
    ho_ten VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    mat_khau VARCHAR(255) NOT NULL,
    so_dien_thoai VARCHAR(20) NULL,
    gioi_tinh ENUM('Nam', 'Nu', 'Khac') NULL,
    ngay_sinh DATE NULL,
    dia_chi VARCHAR(255) NULL,
    vai_tro ENUM('benh_nhan', 'bac_si', 'quan_tri') NOT NULL DEFAULT 'benh_nhan',
    ngay_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE chuyen_khoa (
    ma_chuyen_khoa INT AUTO_INCREMENT PRIMARY KEY,
    ten_chuyen_khoa VARCHAR(100) NOT NULL UNIQUE,
    mo_ta TEXT NULL,
    hinh_anh VARCHAR(255) NULL,
    ngay_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE bac_si (
    ma_bac_si INT AUTO_INCREMENT PRIMARY KEY,
    ma_nguoi_dung INT NOT NULL UNIQUE,
    ma_chuyen_khoa INT NOT NULL,
    ten_bac_si VARCHAR(100) NOT NULL,
    bang_cap VARCHAR(150) NULL,
    so_nam_kinh_nghiem INT NOT NULL DEFAULT 0,
    gioi_thieu TEXT NULL,
    phi_kham DECIMAL(12,2) NOT NULL DEFAULT 0,
    hinh_anh VARCHAR(255) NULL,
    ngay_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_bac_si_nguoi_dung
        FOREIGN KEY (ma_nguoi_dung) REFERENCES nguoi_dung(ma_nguoi_dung)
        ON UPDATE CASCADE ON DELETE CASCADE,

    CONSTRAINT fk_bac_si_chuyen_khoa
        FOREIGN KEY (ma_chuyen_khoa) REFERENCES chuyen_khoa(ma_chuyen_khoa)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE lich_kham (
    ma_lich_kham INT AUTO_INCREMENT PRIMARY KEY,
    ma_nguoi_dung INT NOT NULL,
    ma_bac_si INT NOT NULL,
    ma_chuyen_khoa INT NOT NULL,
    ngay_kham DATE NOT NULL,
    gio_kham TIME NOT NULL,
    ly_do_kham VARCHAR(255) NULL,
    phi_kham DECIMAL(12,2) NOT NULL DEFAULT 0,
    phuong_thuc_thanh_toan VARCHAR(50) DEFAULT 'tien_mat',
    trang_thai_thanh_toan ENUM('chua_thanh_toan', 'da_thanh_toan') DEFAULT 'chua_thanh_toan',
    trang_thai ENUM('cho_xac_nhan', 'da_xac_nhan', 'da_kham', 'da_huy') DEFAULT 'cho_xac_nhan',
    ngay_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (ma_nguoi_dung) REFERENCES nguoi_dung(ma_nguoi_dung),
    FOREIGN KEY (ma_bac_si) REFERENCES bac_si(ma_bac_si),
    FOREIGN KEY (ma_chuyen_khoa) REFERENCES chuyen_khoa(ma_chuyen_khoa)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE goi_kham (
    ma_goi_kham INT AUTO_INCREMENT PRIMARY KEY,
    ten_goi VARCHAR(100) NOT NULL,
    ma_chuyen_khoa INT NULL,
    mo_ta TEXT NULL,
    gia DECIMAL(12,2) NOT NULL,
    thoi_gian_du_kien INT NULL,
    hinh_anh VARCHAR(255) NULL,
    trang_thai ENUM('dang_hoat_dong', 'ngung_hoat_dong') DEFAULT 'dang_hoat_dong',
    ngay_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (ma_chuyen_khoa) REFERENCES chuyen_khoa(ma_chuyen_khoa)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE danh_gia (
    ma_danh_gia INT AUTO_INCREMENT PRIMARY KEY,
    ma_nguoi_dung INT NOT NULL,
    ma_bac_si INT NOT NULL,
    ma_lich_kham INT NULL,
    so_sao INT NOT NULL CHECK (so_sao BETWEEN 1 AND 5),
    noi_dung TEXT NULL,
    ngay_danh_gia TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (ma_nguoi_dung) REFERENCES nguoi_dung(ma_nguoi_dung),
    FOREIGN KEY (ma_bac_si) REFERENCES bac_si(ma_bac_si),
    FOREIGN KEY (ma_lich_kham) REFERENCES lich_kham(ma_lich_kham)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE thong_bao (
    ma_thong_bao INT AUTO_INCREMENT PRIMARY KEY,
    ma_nguoi_dung INT NOT NULL,
    tieu_de VARCHAR(150) NOT NULL,
    noi_dung TEXT NOT NULL,
    loai_thong_bao ENUM('lich_kham', 'nhac_nho', 'he_thong') NOT NULL,
    trang_thai ENUM('chua_doc', 'da_doc') DEFAULT 'chua_doc',
    ngay_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (ma_nguoi_dung) REFERENCES nguoi_dung(ma_nguoi_dung)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE phieu_kham_benh (
    ma_phieu_kham INT AUTO_INCREMENT PRIMARY KEY,
    ma_lich_kham INT NOT NULL UNIQUE, 
    chuan_doan TEXT NOT NULL, 
    don_thuoc TEXT NULL, 
    loi_dan_bac_si TEXT NULL, 
    ngay_tai_kham DATE NULL, 
    ngay_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_phieu_kham_lich_kham
        FOREIGN KEY (ma_lich_kham) REFERENCES lich_kham(ma_lich_kham)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Nạp dữ liệu mẫu chuẩn xác
INSERT INTO nguoi_dung (ma_nguoi_dung, ho_ten, email, mat_khau, so_dien_thoai, gioi_tinh, ngay_sinh, dia_chi, vai_tro) VALUES
(1, 'Quản Trị Viên Hệ Thống', 'admin@medbooking.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0901111222', 'Khac', '1990-01-01', 'Hà Nội', 'quan_tri'),
(2, 'BS.CKI Phạm Minh Quân', 'dr.quan@medbooking.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0944444444', 'Nam', '1985-05-15', 'Đống Đa, Hà Nội', 'bac_si'),
(3, 'PGS.TS Nguyễn Văn Hùng', 'dr.hung@medbooking.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0988888888', 'Nam', '1978-10-20', 'Cầu Giấy, Hà Nội', 'bac_si'),
(4, 'PGS.TS Lê Đình Minh', 'dr.minh@medbooking.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0977112233', 'Nam', '1975-03-12', 'Ba Đình, Hà Nội', 'bac_si'),
(5, 'BS.CKII Phạm Quỳnh Nga', 'dr.nga@medbooking.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0966223344', 'Nu', '1982-08-22', 'Hai Bà Trưng, Hà Nội', 'bac_si'),
(6, 'ThS.BS Hoàng Hải Nam', 'dr.nam@medbooking.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0955334455', 'Nam', '1988-11-05', 'Thanh Xuân, Hà Nội', 'bac_si'),
(7, 'BS. Trần Thị Mai', 'dr.mai@medbooking.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0933556677', 'Nu', '1990-12-01', 'Long Biên, Hà Nội', 'bac_si'),
(8, 'BS. Nguyễn Thu Hà', 'dr.ha@medbooking.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0922667788', 'Nu', '1992-04-18', 'Hoàng Mai, Hà Nội', 'bac_si'),
(9, 'Nguyễn Châu Giang', 'giang@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0912345678', 'Nu', '2006-08-29', 'Hà Nội', 'benh_nhan'),
(10, 'Trần Minh Hoàng', 'hoang.tran@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0933333333', 'Nam', '2000-12-12', 'Thanh Xuân, Hà Nội', 'benh_nhan'),
(11, 'BS.CKI Đỗ Văn Thành', 'thanh.do@medbooking.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0911223344', 'Nam', '1984-06-15', 'Hà Nội', 'bac_si'),
(12, 'ThS.BS Lê Thị Thanh Hương', 'huong.le@medbooking.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0922334455', 'Nu', '1989-09-20', 'Hà Nội', 'bac_si');

INSERT INTO chuyen_khoa (ma_chuyen_khoa, ten_chuyen_khoa, mo_ta, hinh_anh) VALUES
(1, 'Khoa Răng Hàm Mặt', 'Chuyên chẩn đoán, điều trị các bệnh lý về răng, hàm, mặt, chỉnh nha và nha khoa thẩm mỹ công nghệ cao.', 'rang_ham_mat.jpg'),
(2, 'Khoa Tim Mạch', 'Tầm soát, tư vấn và điều trị chuyên sâu các bệnh lý về tim mạch, huyết áp và mạch vành.', 'tim_mach.jpg'),
(3, 'Khoa Nhi Khoa', 'Khám chữa bệnh và chăm sóc sức khỏe toàn diện cho trẻ sơ sinh, nhi đồng và thiếu niên.', 'nhi_khoa.jpg'),
(4, 'Khoa Thần Kinh', 'Chuyên khoa tiếp nhận và điều trị các rối loạn hệ thần kinh trung ương, đau đầu, chóng mặt và tiền đình.', 'than_kinh.jpg'),
(5, 'Khoa Tổng Quát', 'Khám sức khỏe tổng quát định kỳ, tầm soát bệnh lý ban đầu và tư vấn lối sống khỏe mạnh.', 'tong_quat.jpg');

INSERT INTO bac_si (ma_bac_si, ma_nguoi_dung, ma_chuyen_khoa, ten_bac_si, bang_cap, so_nam_kinh_nghiem, gioi_thieu, phi_kham, hinh_anh) VALUES
(1, 2, 1, 'BS.CKI Phạm Minh Quân', 'BS.CKI - Đại học Y Hà Nội', 10, 'Chuyên gia đầu ngành trong lĩnh vực nha khoa thẩm mỹ, phục hình răng sứ và chỉnh nha.', 300000.00, 'https://images.unsplash.com/photo-1537368910025-700350fe46c7?auto=format&fit=crop&q=80&w=300'),
(2, 4, 1, 'PGS.TS Lê Đình Minh', 'PGS.TS - Chuyên khoa Răng Hàm Mặt', 22, 'Nguyên trưởng khoa Răng Hàm Mặt bệnh viện lớn, chuyên gia cấy ghép Implant phức tạp.', 600000.00, 'https://images.unsplash.com/photo-1622253692010-333f2da6031d?auto=format&fit=crop&q=80&w=300'),
(3, 5, 1, 'BS.CKII Phạm Quỳnh Nga', 'BS.CKII - Chỉnh nha Quốc tế', 15, 'Hơn 15 năm kinh nghiệm niềng răng mắc cài và trong suốt (Invisalign).', 450000.00, 'https://images.unsplash.com/photo-1594824813576-905c13b3d905?auto=format&fit=crop&q=80&w=300'),
(4, 3, 2, 'PGS.TS Nguyễn Văn Hùng', 'PGS.TS - Chuyên khoa Tim Mạch', 20, 'Nguyên trưởng khoa Tim Mạch, có nhiều năm kinh nghiệm điều trị bệnh lý mạch vành và huyết áp.', 500000.00, 'https://images.unsplash.com/photo-1622253692010-333f2da6031d?auto=format&fit=crop&q=80&w=300'),
(5, 6, 2, 'ThS.BS Hoàng Hải Nam', 'ThS.BS Tim Mạch học', 12, 'Chuyên gia điều trị rối loạn nhịp tim và siêu âm tim mạch cao cấp.', 400000.00, 'https://images.unsplash.com/photo-1537368910025-700350fe46c7?auto=format&fit=crop&q=80&w=300'),
(6, 7, 3, 'BS. Trần Thị Mai', 'ThS.BS Nhi Khoa', 11, 'Tận tâm, nhẹ nhàng, chuyên điều trị các bệnh lý đường hô hấp và tiêu hóa ở trẻ em.', 300000.00, 'https://images.unsplash.com/photo-1594824813576-905c13b3d905?auto=format&fit=crop&q=80&w=300'),
(7, 8, 4, 'BS. Nguyễn Thu Hà', 'BS.CKI Thần Kinh', 9, 'Chuyên gia tư vấn và điều trị đau nửa đầu, tiền đình, mất ngủ kinh niên.', 350000.00, 'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?auto=format&fit=crop&q=80&w=300'),
(8, 11, 5, 'BS.CKI Đỗ Văn Thành', 'BS.CKI - Y học Tổng quát', 14, 'Chuyên khám sức khỏe tổng quát định kỳ, tư vấn tầm soát bệnh lý mạn tính.', 250000.00, 'https://images.unsplash.com/photo-1622253692010-333f2da6031d?auto=format&fit=crop&q=80&w=300'),
(9, 12, 5, 'ThS.BS Lê Thị Thanh Hương', 'ThS.BS Tổng quát và Dinh dưỡng', 10, 'Tư vấn dinh dưỡng, lối sống lành mạnh và khám sức khỏe tổng quát toàn diện.', 300000.00, 'https://images.unsplash.com/photo-1594824813576-905c13b3d905?auto=format&fit=crop&q=80&w=300');

INSERT INTO goi_kham (ma_goi_kham, ten_goi, ma_chuyen_khoa, mo_ta, gia, thoi_gian_du_kien, trang_thai) VALUES
(1, 'Gói Khám Tổng Quát Cơ Bản', 5, 'Tầm soát sức khỏe định kỳ ban đầu, kiểm tra các chỉ số sinh hiệu và xét nghiệm máu.', 1500000.00, 60, 'dang_hoat_dong'),
(2, 'Gói Tầm Soát Tim Mạch VIP', 2, 'Đánh giá nguy cơ xơ vữa động mạch, tầm soát sớm đột quỵ và các bệnh lý hệ tuần hoàn cao cấp.', 3200000.00, 120, 'dang_hoat_dong'),
(3, 'Gói Chăm Sóc Răng Thẩm Mỹ', 1, 'Vệ sinh cao răng công nghệ sóng siêu âm không đau, tẩy trắng răng công nghệ hiện đại.', 850000.00, 45, 'dang_hoat_dong');

-- Thêm cột nội dung giới thiệu chi tiết cho chuyên khoa
ALTER TABLE chuyen_khoa ADD COLUMN gioi_thieu_chi_tiet TEXT AFTER mo_ta;

-- Cập nhật dữ liệu bài viết giới thiệu riêng biệt cho từng khoa
UPDATE chuyen_khoa 
SET gioi_thieu_chi_tiet = 'Khoa Tim Mạch tại Elite Clinic quy tụ đội ngũ chuyên gia đầu ngành trong chẩn đoán và điều trị các bệnh lý tim mạch, cao huyết áp, xơ vữa động mạch và rối loạn nhịp tim. Chúng tôi trang bị hệ thống siêu âm tim màu 4D thế hệ mới cùng máy đo điện tâm đồ gắng sức hiện đại, giúp phát hiện sớm các nguy cơ đột quỵ và biến chứng tim mạch nguy hiểm.'
WHERE ten_chuyen_khoa LIKE '%Tim%';

UPDATE chuyen_khoa 
SET gioi_thieu_chi_tiet = 'Khoa Nhi cung cấp dịch vụ chăm sóc sức khỏe toàn diện cho trẻ sơ sinh đến 15 tuổi. Không gian khám chữa bệnh thân thiện, thiết kế ngộ nghĩnh giúp bé giải tỏa tâm lý sợ hãi. Đội ngũ bác sĩ Nhi khoa tận tâm, giàu kinh nghiệm cam kết hạn chế tối đa việc sử dụng kháng sinh không cần thiết.'
WHERE ten_chuyen_khoa LIKE '%Nhi%';

UPDATE chuyen_khoa 
SET gioi_thieu_chi_tiet = 'Khoa Mắt ứng dụng các công nghệ chẩn đoán và điều trị tật khúc xạ, cườm khô, cườm nước tiên tiến. Hệ thống sinh hiển vi soi đáy mắt và máy đo nhãn áp không tiếp xúc giúp quá trình thăm khám diễn ra nhẹ nhàng, chính xác và không gây đau đớn cho bệnh nhân.'
WHERE ten_chuyen_khoa LIKE '%Mắt%';

USE quan_ly_phong_kham;
ALTER TABLE goi_kham ADD COLUMN quy_trinh TEXT NULL AFTER mo_ta;
-- 1. Gói Khám Tổng Quát Cơ Bản (Mã 1)
UPDATE goi_kham 
SET mo_ta = 'Tầm soát sức khỏe định kỳ ban đầu, kiểm tra các chỉ số sinh hiệu, xét nghiệm máu và đánh giá chức năng gan thận tổng quát.',
    quy_trinh = 'Bước 1: Lấy số thứ tự và làm thủ tục tiếp đón tại quầy lễ tân.\nBước 2: Khám nội tổng quát, đo huyết áp và nhịp tim.\nBước 3: Lấy máu và nước tiểu để thực hiện các xét nghiệm sinh hóa cơ bản.\nBước 4: Chụp X-quang ngực thẳng và siêu âm ổ bụng tổng quát.\nBước 5: Bác sĩ tổng kết kết quả, tư vấn chế độ dinh dưỡng và hẹn tái khám.'
WHERE ma_goi_kham = 1;

-- 2. Gói Tầm Soát Tim Mạch VIP (Mã 2)
UPDATE goi_kham 
SET mo_ta = 'Đánh giá nguy cơ xơ vữa động mạch, tầm soát sớm đột quỵ, bệnh lý mạch vành và các rối loạn hệ tuần hoàn cao cấp.',
    quy_trinh = 'Bước 1: Khám chuyên khoa Tim mạch với PGS.TS/Bác sĩ chuyên sâu.\nBước 2: Đo điện tâm đồ (ECG) và siêu âm tim màu 4D thế hệ mới.\nBước 3: Thực hiện nghiệm pháp điện tâm đồ gắng sức (nếu có chỉ định).\nBước 4: Xét nghiệm chuyên sâu bộ mỡ máu, HbA1c,hs-CRP và các men tim.\nBước 5: Bác sĩ chuyên khoa hội chẩn kết quả, đưa ra phác đồ điều trị và dự phòng đột quỵ.'
WHERE ma_goi_kham = 2;

-- 3. Gói Chăm Sóc Răng Thẩm Mỹ (Mã 3)
UPDATE goi_kham 
SET mo_ta = 'Vệ sinh cao răng công nghệ sóng siêu âm không đau, đánh bóng và tẩy trắng răng công nghệ hiện đại giúp nụ cười rạng rỡ.',
    quy_trinh = 'Bước 1: Bác sĩ Răng Hàm Mặt thăm khám tổng quát tình trạng sâu răng, viêm nướu và cao răng.\nBước 2: Tiến hành cạo vôi răng bằng máy siêu âm sóng âm dịu nhẹ, hoàn toàn không ê buốt.\nBước 3: Đánh bóng bề mặt răng để loại bỏ mảng bám cứng đầu.\nBước 4: Thực hiện chiếu đèn laser tẩy trắng răng công nghệ cao an toàn cho men răng.\nBước 5: Bác sĩ hướng dẫn cách vệ sinh răng miệng và chăm sóc định kỳ tại nhà.'
WHERE ma_goi_kham = 3;

DESCRIBE phieu_kham_benh;
DESCRIBE danh_gia;

ALTER TABLE phieu_kham_benh
ADD COLUMN huong_dan_chuan_bi TEXT NULL,
ADD COLUMN quy_trinh_kham TEXT NULL,
ADD COLUMN luu_y TEXT NULL,
ADD COLUMN trang_thai_phieu
    ENUM('da_gui', 'da_kham')
    DEFAULT 'da_gui';

ALTER TABLE phieu_kham_benh
MODIFY COLUMN chuan_doan TEXT NULL;