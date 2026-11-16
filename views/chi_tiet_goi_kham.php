
<?php
require_once 'views/header.php';

$tenGoi = $chiTiet['ten_goi'] ?? 'Gói khám dịch vụ';
$gia = (float)($chiTiet['gia'] ?? 0);
$moTa = $chiTiet['mo_ta'] ?? '';
$quyTrinh = trim($chiTiet['quy_trinh'] ?? '');
$hinhAnh = $chiTiet['hinh_anh'] ?? '';
$thoiGian = $chiTiet['thoi_gian_du_kien'] ?? '';
?>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

    <!-- NÚT QUAY LẠI -->
    <div>
        <a href="index.php?act=home"
           class="inline-flex items-center space-x-2 text-xs font-bold text-slate-600 hover:text-brand-600 transition">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Quay lại trang chủ</span>
        </a>
    </div>

    <?php if (!empty($chiTiet)): ?>

        <!-- THÔNG TIN CHÍNH -->
        <div class="bg-white p-8 md:p-10 rounded-3xl border border-slate-100 shadow-sm space-y-8">

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">

                <!-- ẢNH GÓI KHÁM -->
                <div class="lg:col-span-5">
                    <?php if (!empty($hinhAnh)): ?>
                        <img
                            src="<?= htmlspecialchars($hinhAnh); ?>"
                            alt="<?= htmlspecialchars($tenGoi); ?>"
                            class="w-full h-72 md:h-80 object-cover rounded-2xl border border-slate-100 shadow-sm"
                        >
                    <?php else: ?>
                        <div class="w-full h-72 md:h-80 rounded-2xl bg-brand-50 flex flex-col items-center justify-center text-brand-300">
                            <i class="fa-solid fa-notes-medical text-6xl mb-3"></i>
                            <span class="text-sm font-semibold">Chưa có hình ảnh gói khám</span>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- THÔNG TIN GÓI -->
                <div class="lg:col-span-7 space-y-5">

                    <span class="bg-brand-600 text-white text-xs font-bold px-3.5 py-1.5 rounded-full uppercase tracking-wider inline-block shadow-sm">
                        Gói khám sức khỏe
                    </span>

                    <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900">
                        <?= htmlspecialchars($tenGoi); ?>
                    </h1>

                    <div class="text-2xl md:text-3xl font-extrabold text-brand-700">
                        <?= number_format($gia, 0, ',', '.'); ?> đ
                    </div>

                    <?php if (!empty($thoiGian)): ?>
                        <div class="flex items-center gap-2 text-sm text-slate-600">
                            <i class="fa-regular fa-clock text-brand-600"></i>
                            <span>
                                Thời gian dự kiến:
                                <strong><?= htmlspecialchars($thoiGian); ?></strong>
                            </span>
                        </div>
                    <?php endif; ?>

                    <div class="pt-4">
                        <a href="index.php?act=dat-goi-kham&id=<?= (int)$chiTiet['ma_goi_kham']; ?>"
                           class="inline-flex items-center space-x-2 bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs px-6 py-3.5 rounded-2xl shadow transition uppercase tracking-wider">
                            <i class="fa-solid fa-calendar-check"></i>
                            <span>Đặt Gói Khám Ngay</span>
                        </a>
                    </div>

                </div>
            </div>

            <!-- MÔ TẢ GÓI KHÁM -->
            <div class="pt-6 border-t border-slate-100 space-y-4">

                <h3 class="font-bold text-slate-900 text-sm uppercase tracking-wide flex items-center space-x-2">
                    <i class="fa-solid fa-circle-info text-brand-600"></i>
                    <span>Chi Tiết Nội Dung Gói Khám</span>
                </h3>

                <div class="text-xs md:text-sm text-slate-600 leading-relaxed bg-slate-50/60 p-6 rounded-2xl border border-slate-100">
                    <?php if (!empty($moTa)): ?>
                        <?= nl2br(htmlspecialchars($moTa)); ?>
                    <?php else: ?>
                        <p class="italic text-slate-400">
                            Chưa có thông tin mô tả chi tiết cho gói khám này.
                        </p>
                    <?php endif; ?>
                </div>

            </div>

            <!-- QUY TRÌNH KHÁM LẤY TỪ ADMIN -->
            <div class="pt-6 border-t border-slate-100 space-y-4">

                <h3 class="font-bold text-slate-900 text-sm uppercase tracking-wide flex items-center space-x-2">
                    <i class="fa-solid fa-list-check text-brand-600"></i>
                    <span>Quy Trình Thực Hiện Gói Khám</span>
                </h3>

                <div class="bg-brand-50/50 p-6 rounded-2xl border border-brand-100 text-xs md:text-sm text-slate-600 leading-relaxed">

                    <?php if (!empty($quyTrinh)): ?>

                        <?= nl2br(htmlspecialchars($quyTrinh)); ?>

                    <?php else: ?>

                        <p class="italic text-slate-400">
                            Admin chưa cập nhật quy trình thực hiện cho gói khám này.
                        </p>

                    <?php endif; ?>

                </div>

            </div>

        </div>

    <?php else: ?>

        <div class="bg-white p-8 rounded-3xl text-center border border-slate-100 shadow-sm">
            <i class="fa-solid fa-circle-exclamation text-3xl text-slate-300 mb-3"></i>
            <p class="text-slate-500 text-sm">
                Không tìm thấy thông tin gói khám.
            </p>

            <a href="index.php?act=home"
               class="inline-block mt-4 bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs px-5 py-3 rounded-xl">
                Quay lại trang chủ
            </a>
        </div>

    <?php endif; ?>

</main>

<?php require_once 'views/footer.php'; ?>
