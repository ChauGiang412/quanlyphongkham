
<?php
require_once __DIR__ . '/header.php';

$trangThai = $chiTiet['trang_thai'] ?? '';
$phieuKham = $phieuKham ?? null;
$danhGia = $danhGia ?? null;

$maLichKham = (int)($chiTiet['ma_lich_kham'] ?? 0);

$daCoPhieuKham = is_array($phieuKham) && !empty($phieuKham);
$daDanhGia = is_array($danhGia) && !empty($danhGia);

// Chỉ lịch đã xác nhận hoặc đã khám mới được xem phiếu
$duocXemPhieu = in_array($trangThai, ['da_xac_nhan', 'da_kham'], true);
$hienThiPhieu = $duocXemPhieu && $daCoPhieuKham;

$trangThaiText = [
    'cho_xac_nhan' => 'Chờ admin xác nhận',
    'da_xac_nhan' => 'Đã xác nhận',
    'da_kham' => 'Đã khám',
    'da_huy' => 'Đã hủy'
];

$trangThaiClass = [
    'cho_xac_nhan' => 'bg-amber-50 text-amber-700 border-amber-200',
    'da_xac_nhan' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
    'da_kham' => 'bg-blue-50 text-blue-700 border-blue-200',
    'da_huy' => 'bg-rose-50 text-rose-700 border-rose-200'
];

function eLich($value, $fallback = 'Chưa cập nhật') {
    $value = ($value === null || $value === '') ? $fallback : $value;
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function ngayLich($value) {
    if (empty($value)) {
        return 'Chưa cập nhật';
    }

    $timestamp = strtotime($value);

    return $timestamp ? date('d/m/Y', $timestamp) : 'Chưa cập nhật';
}
?>

<style>
@media print {
    body * {
        visibility: hidden !important;
    }

    #phieu-kham-in,
    #phieu-kham-in * {
        visibility: visible !important;
    }

    #phieu-kham-in {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        margin: 0;
        padding: 18mm;
        border: 0 !important;
        box-shadow: none !important;
    }

    .khong-in {
        display: none !important;
    }
}
</style>

<main class="min-h-screen bg-slate-50 py-10">
<div class="max-w-4xl mx-auto px-4 sm:px-6">

    <!-- Thanh điều hướng -->
    <div class="flex items-center justify-between mb-6 khong-in">
        <a href="index.php?act=lich-su"
           class="text-sm font-semibold text-slate-500 hover:text-teal-700">
            <i class="fa-solid fa-arrow-left mr-2"></i>
            Quay lại lịch sử khám
        </a>

        <span class="text-xs text-slate-400 font-bold">
            Mã lịch #<?= $maLichKham; ?>
        </span>
    </div>

    <section class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6 md:p-8 space-y-8">

        <!-- Tiêu đề -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b pb-6">
            <div>
                <p class="text-xs uppercase tracking-wider font-bold text-teal-600">
                    Hồ sơ y tế cá nhân
                </p>

                <h1 class="text-2xl font-extrabold text-slate-900 mt-1">
                    Chi tiết lịch khám
                </h1>
            </div>

            <span class="inline-flex px-4 py-2 rounded-full border text-xs font-bold
                <?= $trangThaiClass[$trangThai] ?? 'bg-slate-50 text-slate-600 border-slate-200'; ?>">
                <?= eLich($trangThaiText[$trangThai] ?? $trangThai); ?>
            </span>
        </div>

        <!-- I. Thông tin bệnh nhân -->
        <div>
            <h2 class="font-extrabold text-slate-900 mb-4">
                <i class="fa-solid fa-user text-teal-600 mr-2"></i>
                I. THÔNG TIN BỆNH NHÂN
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 text-sm">

                <div>
                    <p class="text-slate-400 text-xs mb-1">Họ và tên</p>
                    <p class="font-bold text-slate-800">
                        <?= eLich($chiTiet['ho_ten'] ?? ''); ?>
                    </p>
                </div>

                <div>
                    <p class="text-slate-400 text-xs mb-1">Số điện thoại</p>
                    <p class="font-bold text-slate-800">
                        <?= eLich($chiTiet['so_dien_thoai'] ?? ''); ?>
                    </p>
                </div>

                <div>
                    <p class="text-slate-400 text-xs mb-1">Email</p>
                    <p class="font-bold text-slate-800">
                        <?= eLich($chiTiet['email'] ?? ''); ?>
                    </p>
                </div>

                <div>
                    <p class="text-slate-400 text-xs mb-1">Mã lịch khám</p>
                    <p class="font-bold text-slate-800">
                        #<?= $maLichKham; ?>
                    </p>
                </div>

            </div>
        </div>

        <!-- II. Thông tin lịch khám -->
        <div class="border-t pt-6">
            <h2 class="font-extrabold text-slate-900 mb-4">
                <i class="fa-solid fa-calendar-check text-teal-600 mr-2"></i>
                II. THÔNG TIN LỊCH KHÁM
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 text-sm">

                <div>
                    <p class="text-slate-400 text-xs mb-1">Chuyên khoa</p>
                    <p class="font-bold text-slate-800">
                        <?= eLich($chiTiet['ten_chuyen_khoa'] ?? ''); ?>
                    </p>
                </div>

                <div>
                    <p class="text-slate-400 text-xs mb-1">Bác sĩ phụ trách</p>
                    <p class="font-bold text-slate-800">
                        BS. <?= eLich($chiTiet['ten_bac_si'] ?? ''); ?>
                    </p>
                </div>

                <div>
                    <p class="text-slate-400 text-xs mb-1">Ngày khám</p>
                    <p class="font-bold text-slate-800">
                        <?= ngayLich($chiTiet['ngay_kham'] ?? null); ?>
                    </p>
                </div>

                <div>
                    <p class="text-slate-400 text-xs mb-1">Giờ khám</p>
                    <p class="font-bold text-slate-800">
                        <?= eLich($chiTiet['gio_kham'] ?? ''); ?>
                    </p>
                </div>

                <div>
                    <p class="text-slate-400 text-xs mb-1">Lý do khám</p>
                    <p class="font-bold text-slate-800">
                        <?= eLich($chiTiet['ly_do_kham'] ?? ''); ?>
                    </p>
                </div>

                <div>
                    <p class="text-slate-400 text-xs mb-1">Phí khám</p>
                    <p class="font-bold text-teal-700">
                        <?= number_format((float)($chiTiet['phi_kham'] ?? 0)); ?> VNĐ
                    </p>
                </div>

                <div>
                    <p class="text-slate-400 text-xs mb-1">Phương thức thanh toán</p>
                    <p class="font-bold text-slate-800">
                        <?= eLich($chiTiet['phuong_thuc_thanh_toan'] ?? ''); ?>
                    </p>
                </div>

            </div>
        </div>

        <!-- Trạng thái chờ xác nhận -->
        <?php if ($trangThai === 'cho_xac_nhan'): ?>

            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 text-amber-800">
                <h2 class="font-bold">
                    <i class="fa-solid fa-clock mr-2"></i>
                    Lịch khám đang chờ xác nhận
                </h2>

                <p class="text-sm mt-2 leading-6">
                    Lịch hẹn của bạn đang được phòng khám tiếp nhận.
                    Sau khi lịch được xác nhận, bạn có thể xem phiếu khám,
                    hướng dẫn chuẩn bị và quy trình khám tại bệnh viện.
                </p>
            </div>

            <div class="flex justify-end khong-in">
                <form action="index.php?act=huy-lich"
                      method="POST"
                      onsubmit="return confirm('Bạn chắc chắn muốn hủy lịch khám này?');">

                    <input type="hidden"
                           name="ma_lich_kham"
                           value="<?= $maLichKham; ?>">

                    <button type="submit"
                            class="px-5 py-3 rounded-xl bg-rose-50 text-rose-700 border border-rose-200 font-bold text-sm hover:bg-rose-100 transition">
                        <i class="fa-solid fa-ban mr-2"></i>
                        Hủy lịch khám
                    </button>
                </form>
            </div>

        <?php elseif ($trangThai === 'da_huy'): ?>

            <div class="rounded-2xl border border-rose-200 bg-rose-50 p-5 text-rose-800 text-sm">
                <h2 class="font-bold mb-2">
                    <i class="fa-solid fa-circle-xmark mr-2"></i>
                    Lịch khám đã bị hủy
                </h2>

                Lịch hẹn này không còn hiệu lực sử dụng.
                Bạn có thể đặt một lịch khám mới nếu vẫn có nhu cầu.
            </div>

        <?php endif; ?>

        <!-- Phiếu khám -->
        <?php if ($hienThiPhieu): ?>

            <section id="phieu-kham-in"
                     class="border-2 border-slate-200 rounded-2xl p-5 md:p-8 space-y-6">

                <!-- Tiêu đề phiếu -->
                <header class="text-center border-b-2 border-slate-800 pb-5">
                    <p class="font-extrabold text-teal-800 text-lg">
                        PHÒNG KHÁM ĐA KHOA
                    </p>

                    <p class="text-xs text-slate-500 mt-1">
                        PHIẾU KHÁM BỆNH ĐIỆN TỬ
                    </p>

                    <h2 class="text-2xl font-black text-slate-900 mt-4">
                        PHIẾU XÁC NHẬN LỊCH KHÁM
                    </h2>

                    <p class="text-sm text-slate-500 mt-2">
                        Mã phiếu:
                        PK-<?= str_pad(
                            (string)($phieuKham['ma_phieu_kham'] ?? 0),
                            6,
                            '0',
                            STR_PAD_LEFT
                        ); ?>
                    </p>

                    <span class="inline-block mt-3 rounded-full bg-emerald-100 text-emerald-800 px-4 py-1 text-xs font-bold">
                        ĐÃ XÁC NHẬN
                    </span>
                </header>

                <!-- Hướng dẫn chuẩn bị -->
                <div class="border-t pt-5">
                    <h3 class="font-extrabold text-slate-900 mb-3">
                        III. HƯỚNG DẪN CHUẨN BỊ TRƯỚC KHI KHÁM
                    </h3>

                    <div class="rounded-xl bg-blue-50 p-4 text-sm leading-7 whitespace-pre-line text-slate-700">
                        <?= eLich(
                            $phieuKham['huong_dan_chuan_bi'] ?? '',
                            'Phòng khám chưa bổ sung hướng dẫn chuẩn bị.'
                        ); ?>
                    </div>
                </div>

                <!-- Quy trình khám -->
                <div class="border-t pt-5">
                    <h3 class="font-extrabold text-slate-900 mb-3">
                        IV. QUY TRÌNH KHÁM TẠI BỆNH VIỆN
                    </h3>

                    <div class="rounded-xl bg-slate-50 p-4 text-sm leading-7 whitespace-pre-line text-slate-700">
                        <?= eLich(
                            $phieuKham['quy_trinh_kham'] ?? '',
                            'Phòng khám chưa bổ sung quy trình khám.'
                        ); ?>
                    </div>
                </div>

                <!-- Lưu ý -->
                <div class="border-t pt-5">
                    <h3 class="font-extrabold text-slate-900 mb-3">
                        V. NHỮNG ĐIỀU CẦN LƯU Ý
                    </h3>

                    <div class="rounded-xl bg-amber-50 p-4 text-sm leading-7 whitespace-pre-line text-slate-700">
                        <?= eLich(
                            $phieuKham['luu_y'] ?? '',
                            'Không có lưu ý riêng.'
                        ); ?>
                    </div>
                </div>

                <!-- Kết quả khám -->
                <div class="border-t pt-5">
                    <h3 class="font-extrabold text-slate-900 mb-3">
                        VI. KẾT QUẢ KHÁM VÀ DẶN DÒ SAU KHÁM
                    </h3>

                    <?php if ($trangThai === 'da_kham'): ?>

                        <div class="space-y-4 text-sm">

                            <div>
                                <b>Chẩn đoán:</b>
                                <p class="mt-1 whitespace-pre-line">
                                    <?= eLich($phieuKham['chuan_doan'] ?? ''); ?>
                                </p>
                            </div>

                            <div>
                                <b>Đơn thuốc:</b>
                                <p class="mt-1 whitespace-pre-line">
                                    <?= eLich(
                                        $phieuKham['don_thuoc'] ?? '',
                                        'Không có đơn thuốc.'
                                    ); ?>
                                </p>
                            </div>

                            <div>
                                <b>Lời dặn của bác sĩ:</b>
                                <p class="mt-1 whitespace-pre-line">
                                    <?= eLich($phieuKham['loi_dan_bac_si'] ?? ''); ?>
                                </p>
                            </div>

                            <div>
                                <b>Ngày tái khám:</b>
                                <?= ngayLich($phieuKham['ngay_tai_kham'] ?? null); ?>
                            </div>

                        </div>

                    <?php else: ?>

                        <p class="text-sm text-slate-500 leading-6">
                            Nội dung chẩn đoán, đơn thuốc, kết quả và lời dặn
                            của bác sĩ sẽ được cập nhật sau khi hoàn thành buổi khám.
                        </p>

                    <?php endif; ?>
                </div>

                <footer class="border-t pt-4 text-center text-xs text-slate-400">
                    Phiếu được tạo từ hệ thống quản lý phòng khám.
                    Vui lòng xuất trình phiếu khi đến khám.
                </footer>

            </section>

            <!-- In phiếu -->
            <div class="flex justify-end khong-in">
                <button type="button"
                        onclick="window.print()"
                        class="bg-teal-600 hover:bg-teal-700 text-white px-6 py-3 rounded-xl font-bold text-sm transition">
                    <i class="fa-solid fa-print mr-2"></i>
                    In / Lưu phiếu khám
                </button>
            </div>

        <?php elseif ($duocXemPhieu): ?>

            <div class="rounded-2xl border border-blue-200 bg-blue-50 p-5 text-blue-800">
                <h2 class="font-bold">
                    <i class="fa-solid fa-file-circle-exclamation mr-2"></i>
                    Phiếu khám chưa được cập nhật
                </h2>

                <p class="text-sm mt-2 leading-6">
                    Lịch khám của bạn đã được xác nhận nhưng hệ thống chưa tìm thấy
                    thông tin phiếu khám. Vui lòng liên hệ phòng khám để được hỗ trợ.
                </p>
            </div>

        <?php endif; ?>

        <!-- Đánh giá sau khám -->
        <?php if ($trangThai === 'da_kham' && $daCoPhieuKham): ?>

            <section class="border-t pt-6 khong-in">

                <h3 class="font-extrabold text-slate-900 mb-4">
                    <i class="fa-solid fa-star text-amber-400 mr-2"></i>
                    ĐÁNH GIÁ CHẤT LƯỢNG KHÁM
                </h3>

                <?php if ($daDanhGia): ?>

                    <div class="rounded-xl bg-amber-50 p-4">

                        <p class="text-amber-500 mb-2">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="<?= $i <= (int)$danhGia['so_sao']
                                    ? 'fa-solid'
                                    : 'fa-regular'; ?> fa-star"></i>
                            <?php endfor; ?>

                            <span class="text-slate-600 text-xs">
                                <?= (int)$danhGia['so_sao']; ?>/5 sao
                            </span>
                        </p>

                        <p class="text-sm whitespace-pre-line">
                            <?= eLich($danhGia['noi_dung'] ?? '', 'Không có nội dung.'); ?>
                        </p>

                    </div>

                <?php else: ?>

                    <form action="index.php?act=gui-danh-gia"
                          method="POST"
                          class="space-y-3">

                        <input type="hidden"
                               name="ma_lich_kham"
                               value="<?= $maLichKham; ?>">

                        <select name="so_sao"
                                required
                                class="w-full p-3 border rounded-xl text-sm">

                            <option value="">-- Chọn số sao --</option>
                            <option value="5">5 sao - Rất hài lòng</option>
                            <option value="4">4 sao - Hài lòng</option>
                            <option value="3">3 sao - Bình thường</option>
                            <option value="2">2 sao - Chưa hài lòng</option>
                            <option value="1">1 sao - Không hài lòng</option>

                        </select>

                        <textarea name="noi_dung_danh_gia"
                                  rows="3"
                                  class="w-full p-3 border rounded-xl text-sm"
                                  placeholder="Chia sẻ trải nghiệm của bạn..."></textarea>

                        <button type="submit"
                                class="bg-teal-600 hover:bg-teal-700 text-white px-5 py-3 rounded-xl font-bold transition">
                            Gửi đánh giá
                        </button>

                    </form>

                <?php endif; ?>

            </section>

        <?php endif; ?>

    </section>
</div>
</main>

<?php require_once __DIR__ . '/footer.php'; ?>
