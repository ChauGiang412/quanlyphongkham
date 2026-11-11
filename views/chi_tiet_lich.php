<?php
require_once __DIR__ . '/header.php';

$trangThai = $chiTiet['trang_thai'] ?? '';
$phieuKham = $phieuKham ?? null;
$danhGia = $danhGia ?? null;
$daCoPhieuKham = is_array($phieuKham) && !empty($phieuKham);
$daDanhGia = is_array($danhGia) && !empty($danhGia);

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
?>
<style>
@media print {
    body * { visibility: hidden !important; }
    #phieu-kham-in, #phieu-kham-in * { visibility: visible !important; }
    #phieu-kham-in { position: absolute; left: 0; top: 0; width: 100%; margin: 0; padding: 18mm; border: 0 !important; box-shadow: none !important; }
    .khong-in { display: none !important; }
}
</style>

<main class="min-h-screen bg-slate-50 py-10">
<div class="max-w-4xl mx-auto px-4 sm:px-6">
    <div class="flex items-center justify-between mb-6 khong-in">
        <a href="index.php?act=lich-su" class="text-sm font-semibold text-slate-500 hover:text-teal-700">
            <i class="fa-solid fa-arrow-left mr-2"></i>Quay lại lịch sử khám
        </a>
        <span class="text-xs text-slate-400 font-bold">Mã lịch #<?= (int)$chiTiet['ma_lich_kham']; ?></span>
    </div>

    <section class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6 md:p-8 space-y-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b pb-6">
            <div>
                <p class="text-xs uppercase tracking-wider font-bold text-teal-600">Thông tin đặt lịch</p>
                <h1 class="text-2xl font-extrabold text-slate-900 mt-1">Chi tiết lịch khám</h1>
            </div>
            <span class="inline-flex px-4 py-2 rounded-full border text-xs font-bold <?= $trangThaiClass[$trangThai] ?? 'bg-slate-50 text-slate-600 border-slate-200'; ?>">
                <?= eLich($trangThaiText[$trangThai] ?? $trangThai); ?>
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 text-sm">
            <div><p class="text-slate-400 text-xs mb-1">Họ và tên bệnh nhân</p><p class="font-bold text-slate-800"><?= eLich($chiTiet['ho_ten'] ?? ''); ?></p></div>
            <div><p class="text-slate-400 text-xs mb-1">Số điện thoại</p><p class="font-bold text-slate-800"><?= eLich($chiTiet['so_dien_thoai'] ?? ''); ?></p></div>
            <div><p class="text-slate-400 text-xs mb-1">Email</p><p class="font-bold text-slate-800"><?= eLich($chiTiet['email'] ?? ''); ?></p></div>
            <div><p class="text-slate-400 text-xs mb-1">Chuyên khoa</p><p class="font-bold text-slate-800"><?= eLich($chiTiet['ten_chuyen_khoa'] ?? ''); ?></p></div>
            <div><p class="text-slate-400 text-xs mb-1">Bác sĩ phụ trách</p><p class="font-bold text-slate-800">BS. <?= eLich($chiTiet['ten_bac_si'] ?? '', 'Chưa cập nhật'); ?></p></div>
            <div><p class="text-slate-400 text-xs mb-1">Lý do khám</p><p class="font-bold text-slate-800"><?= eLich($chiTiet['ly_do_kham'] ?? ''); ?></p></div>
            <div><p class="text-slate-400 text-xs mb-1">Ngày khám</p><p class="font-bold text-slate-800"><?= !empty($chiTiet['ngay_kham']) ? date('d/m/Y', strtotime($chiTiet['ngay_kham'])) : 'Chưa cập nhật'; ?></p></div>
            <div><p class="text-slate-400 text-xs mb-1">Giờ khám</p><p class="font-bold text-slate-800"><?= eLich($chiTiet['gio_kham'] ?? ''); ?></p></div>
            <div><p class="text-slate-400 text-xs mb-1">Phí khám</p><p class="font-bold text-teal-700"><?= number_format((float)($chiTiet['phi_kham'] ?? 0)); ?> VNĐ</p></div>
            <div><p class="text-slate-400 text-xs mb-1">Phương thức thanh toán</p><p class="font-bold text-slate-800"><?= eLich($chiTiet['phuong_thuc_thanh_toan'] ?? ''); ?></p></div>
        </div>

        <?php if ($trangThai === 'cho_xac_nhan'): ?>
        <div class="flex justify-end khong-in">
            <form action="index.php?act=huy-lich" method="POST" onsubmit="return confirm('Bạn chắc chắn muốn hủy lịch khám này?');">
                <input type="hidden" name="ma_lich_kham" value="<?= (int)$chiTiet['ma_lich_kham']; ?>">
                <button class="px-5 py-3 rounded-xl bg-rose-50 text-rose-700 border border-rose-200 font-bold text-sm">
                    <i class="fa-solid fa-ban mr-2"></i>Hủy lịch khám
                </button>
            </form>
        </div>
        <?php endif; ?>

        <?php if ($trangThai === 'cho_xac_nhan'): ?>
            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 text-amber-800">
                <h2 class="font-bold"><i class="fa-solid fa-clock mr-2"></i>Lịch khám đang chờ xác nhận</h2>
                <p class="text-sm mt-2">Sau khi admin xác nhận, hệ thống sẽ tự động tạo phiếu khám và gửi thông báo đến tài khoản của bạn.</p>
            </div>
        <?php elseif ($trangThai === 'da_huy'): ?>
            <div class="rounded-2xl border border-rose-200 bg-rose-50 p-5 text-rose-800 text-sm">Lịch khám đã bị hủy. Phiếu khám (nếu đã tạo) không còn hiệu lực sử dụng cho lịch hẹn này.</div>
        <?php endif; ?>

        <?php if ($daCoPhieuKham && $trangThai !== 'da_huy'): ?>
        <section id="phieu-kham-in" class="border-2 border-slate-200 rounded-2xl p-5 md:p-8 space-y-6">
            <header class="text-center border-b-2 border-slate-800 pb-5">
                <p class="font-extrabold text-teal-800 text-lg">PHÒNG KHÁM ĐA KHOA</p>
                <p class="text-xs text-slate-500 mt-1">PHIẾU KHÁM BỆNH ĐIỆN TỬ</p>
                <h2 class="text-2xl font-black text-slate-900 mt-4">PHIẾU XÁC NHẬN LỊCH KHÁM</h2>
                <p class="text-sm text-slate-500 mt-2">Mã phiếu: PK-<?= str_pad((string)$phieuKham['ma_phieu_kham'], 6, '0', STR_PAD_LEFT); ?></p>
                <span class="inline-block mt-3 rounded-full bg-emerald-100 text-emerald-800 px-4 py-1 text-xs font-bold">ĐÃ XÁC NHẬN</span>
            </header>

            <div>
                <h3 class="font-extrabold text-slate-900 mb-3">I. THÔNG TIN BỆNH NHÂN</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                    <p><b>Họ tên:</b> <?= eLich($chiTiet['ho_ten'] ?? ''); ?></p>
                    <p><b>Số điện thoại:</b> <?= eLich($chiTiet['so_dien_thoai'] ?? ''); ?></p>
                    <p><b>Email:</b> <?= eLich($chiTiet['email'] ?? ''); ?></p>
                    <p><b>Mã lịch:</b> #<?= (int)$chiTiet['ma_lich_kham']; ?></p>
                </div>
            </div>

            <div class="border-t pt-5">
                <h3 class="font-extrabold text-slate-900 mb-3">II. THÔNG TIN LỊCH KHÁM</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                    <p><b>Chuyên khoa:</b> <?= eLich($chiTiet['ten_chuyen_khoa'] ?? ''); ?></p>
                    <p><b>Bác sĩ:</b> BS. <?= eLich($chiTiet['ten_bac_si'] ?? ''); ?></p>
                    <p><b>Ngày khám:</b> <?= date('d/m/Y', strtotime($chiTiet['ngay_kham'])); ?></p>
                    <p><b>Giờ khám:</b> <?= eLich($chiTiet['gio_kham'] ?? ''); ?></p>
                    <p><b>Lý do khám:</b> <?= eLich($chiTiet['ly_do_kham'] ?? ''); ?></p>
                    <p><b>Chi phí dự kiến:</b> <?= number_format((float)($chiTiet['phi_kham'] ?? 0)); ?> VNĐ</p>
                </div>
            </div>

            <div class="border-t pt-5">
                <h3 class="font-extrabold text-slate-900 mb-3">III. HƯỚNG DẪN CHUẨN BỊ</h3>
                <div class="rounded-xl bg-blue-50 p-4 text-sm leading-7 whitespace-pre-line text-slate-700"><?= eLich($phieuKham['huong_dan_chuan_bi'] ?? '', 'Phòng khám chưa bổ sung hướng dẫn.'); ?></div>
            </div>

            <div class="border-t pt-5">
                <h3 class="font-extrabold text-slate-900 mb-3">IV. QUY TRÌNH KHÁM BỆNH</h3>
                <div class="rounded-xl bg-slate-50 p-4 text-sm leading-7 whitespace-pre-line text-slate-700"><?= eLich($phieuKham['quy_trinh_kham'] ?? '', 'Phòng khám chưa bổ sung quy trình.'); ?></div>
            </div>

            <div class="border-t pt-5">
                <h3 class="font-extrabold text-slate-900 mb-3">V. LƯU Ý</h3>
                <div class="rounded-xl bg-amber-50 p-4 text-sm leading-7 whitespace-pre-line text-slate-700"><?= eLich($phieuKham['luu_y'] ?? '', 'Không có lưu ý riêng.'); ?></div>
            </div>

            <div class="border-t pt-5">
                <h3 class="font-extrabold text-slate-900 mb-3">VI. KẾT QUẢ KHÁM VÀ HƯỚNG DẪN SAU KHÁM</h3>
                <?php if ($trangThai === 'da_kham'): ?>
                    <div class="space-y-4 text-sm">
                        <div><b>Chẩn đoán:</b><p class="mt-1 whitespace-pre-line"><?= eLich($phieuKham['chuan_doan'] ?? '', 'Chưa cập nhật.'); ?></p></div>
                        <div><b>Đơn thuốc:</b><p class="mt-1 whitespace-pre-line"><?= eLich($phieuKham['don_thuoc'] ?? '', 'Không có đơn thuốc.'); ?></p></div>
                        <div><b>Lời dặn bác sĩ:</b><p class="mt-1 whitespace-pre-line"><?= eLich($phieuKham['loi_dan_bac_si'] ?? '', 'Chưa cập nhật.'); ?></p></div>
                        <div><b>Ngày tái khám:</b> <?= !empty($phieuKham['ngay_tai_kham']) ? date('d/m/Y', strtotime($phieuKham['ngay_tai_kham'])) : 'Không có'; ?></div>
                    </div>
                <?php else: ?>
                    <p class="text-sm text-slate-500">Bác sĩ sẽ cập nhật chẩn đoán, đơn thuốc, lời dặn và lịch tái khám sau khi hoàn thành buổi khám.</p>
                <?php endif; ?>
            </div>

            <footer class="border-t pt-4 text-center text-xs text-slate-400">Phiếu được tạo tự động từ hệ thống quản lý phòng khám. Vui lòng xuất trình phiếu khi đến khám.</footer>
        </section>
        <div class="flex justify-end khong-in">
            <button type="button" onclick="window.print()" class="bg-teal-600 hover:bg-teal-700 text-white px-6 py-3 rounded-xl font-bold text-sm">
                <i class="fa-solid fa-print mr-2"></i>In / Lưu phiếu khám
            </button>
        </div>
        <?php endif; ?>

        <?php if ($trangThai === 'da_kham' && $daCoPhieuKham): ?>
        <section class="border-t pt-6 khong-in">
            <h3 class="font-extrabold text-slate-900 mb-4">ĐÁNH GIÁ CHẤT LƯỢNG KHÁM</h3>
            <?php if ($daDanhGia): ?>
                <div class="rounded-xl bg-amber-50 p-4">
                    <p class="text-amber-500 mb-2"><?php for ($i=1; $i<=5; $i++): ?><i class="<?= $i <= (int)$danhGia['so_sao'] ? 'fa-solid' : 'fa-regular'; ?> fa-star"></i><?php endfor; ?> <span class="text-slate-600 text-xs"><?= (int)$danhGia['so_sao']; ?>/5 sao</span></p>
                    <p class="text-sm whitespace-pre-line"><?= eLich($danhGia['noi_dung'] ?? '', 'Không có nội dung.'); ?></p>
                </div>
            <?php else: ?>
                <form action="index.php?act=gui-danh-gia" method="POST" class="space-y-3">
                    <input type="hidden" name="ma_lich_kham" value="<?= (int)$chiTiet['ma_lich_kham']; ?>">
                    <select name="so_sao" required class="w-full p-3 border rounded-xl text-sm">
                        <option value="">-- Chọn số sao --</option>
                        <option value="5">5 sao - Rất hài lòng</option><option value="4">4 sao - Hài lòng</option><option value="3">3 sao - Bình thường</option><option value="2">2 sao - Chưa hài lòng</option><option value="1">1 sao - Không hài lòng</option>
                    </select>
                    <textarea name="noi_dung_danh_gia" rows="3" class="w-full p-3 border rounded-xl text-sm" placeholder="Chia sẻ trải nghiệm của bạn..."></textarea>
                    <button class="bg-teal-600 text-white px-5 py-3 rounded-xl font-bold">Gửi đánh giá</button>
                </form>
            <?php endif; ?>
        </section>
        <?php endif; ?>
    </section>
</div>
</main>
<?php require_once __DIR__ . '/footer.php'; ?>
