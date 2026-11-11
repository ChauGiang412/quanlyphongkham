<?php 
require_once 'views/header.php'; 
?>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    <!-- Nút quay lại -->
    <div>
        <a href="index.php?act=home" class="inline-flex items-center space-x-2 text-xs font-bold text-slate-600 hover:text-brand-600 transition">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Quay lại trang chủ</span>
        </a>
    </div>

    <!-- Thông tin chi tiết chuyên khoa (Phần giới thiệu dài và trực quan) -->
    <?php if (!empty($chiTiet)): ?>
        <div class="bg-white p-8 md:p-10 rounded-3xl border border-slate-100 shadow-sm space-y-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                <!-- Cột hình ảnh chuyên khoa -->
                <div class="lg:col-span-5">
                    <?php if (!empty($chiTiet['hinh_anh'])): ?>
                        <img src="<?= htmlspecialchars($chiTiet['hinh_anh']); ?>" alt="<?= htmlspecialchars($chiTiet['ten_chuyen_khoa']); ?>" class="w-full h-72 lg:h-80 object-cover rounded-2xl border shadow-sm">
                    <?php else: ?>
                        <div class="w-full h-72 lg:h-80 bg-brand-50 rounded-2xl flex items-center justify-center text-brand-600 border">
                            <i class="fa-solid fa-stethoscope text-6xl"></i>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Cột tiêu đề và nút đặt lịch -->
                <div class="lg:col-span-7 space-y-4">
                    <span class="bg-brand-50 text-brand-600 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider inline-block">Chuyên Khoa Y Tế</span>
                    <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900"><?= htmlspecialchars($chiTiet['ten_chuyen_khoa']); ?></h1>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Hệ thống y tế Elite Clinic cung cấp dịch vụ khám và điều trị chuyên sâu đạt chuẩn quốc tế với trang thiết bị tối tân.
                    </p>
                    <div class="pt-4">
                        <a href="index.php?act=chon-loai-lich" class="inline-flex items-center space-x-2 bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs px-6 py-3.5 rounded-2xl shadow transition uppercase tracking-wider">
                            <i class="fa-solid fa-calendar-check"></i>
                            <span>Đặt Lịch Khám Chuyên Khoa Này</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Khung nội dung giới thiệu chi tiết mở rộng -->
            <div class="pt-6 border-t border-slate-100 space-y-4">
                <h3 class="font-bold text-slate-900 text-sm uppercase tracking-wide flex items-center space-x-2">
                    <i class="fa-solid fa-circle-info text-brand-600"></i>
                    <span>Giới Thiệu Chi Tiết Về Chuyên Khoa</span>
                </h3>
                <div class="text-xs md:text-sm text-slate-600 leading-relaxed space-y-3 bg-slate-50/60 p-6 rounded-2xl border border-slate-100">
                    <?= nl2br(htmlspecialchars($chiTiet['mo_ta'] ?? 'Chưa có mô tả chi tiết cho chuyên khoa này.')); ?>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="bg-white p-8 rounded-3xl text-center text-slate-400 italic text-xs">Không tìm thấy thông tin chuyên khoa.</div>
    <?php endif; ?>

    <!-- Đội ngũ bác sĩ thuộc chuyên khoa -->
    <div class="space-y-6 pt-4">
        <div>
            <span class="text-brand-600 font-bold text-xs uppercase tracking-wider block mb-1">Chuyên Gia</span>
            <h2 class="text-xl md:text-2xl font-bold text-slate-900">Đội Ngũ Bác Sĩ Thuộc Chuyên Khoa</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php if (!empty($danhSachBacSiTheoCK)): ?>
                <?php foreach ($danhSachBacSiTheoCK as $bs): ?>
                    <a href="index.php?act=chi-tiet-bac-si&id=<?= $bs['ma_bac_si']; ?>" class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm text-center space-y-3 hover:shadow-md transition block group">
                        <?php if (!empty($bs['hinh_anh'])): ?>
                            <img src="<?= htmlspecialchars($bs['hinh_anh']); ?>" alt="Bác sĩ" class="w-20 h-20 object-cover rounded-full mx-auto shadow-inner border group-hover:scale-105 transition">
                        <?php else: ?>
                            <img src="https://images.unsplash.com/photo-1537368910025-700350fe46c7?auto=format&fit=crop&q=80&w=300" alt="Bác sĩ" class="w-20 h-20 object-cover rounded-full mx-auto shadow-inner border group-hover:scale-105 transition">
                        <?php endif; ?>
                        <h3 class="font-bold text-slate-900 text-sm group-hover:text-brand-600 transition"><?= htmlspecialchars($bs['ten_bac_si']); ?></h3>
                        <p class="text-xs text-brand-600 font-semibold"><?= htmlspecialchars($bs['bang_cap'] ?? 'Bác sĩ chuyên khoa'); ?></p>
                        <p class="text-xs text-slate-500 line-clamp-2"><?= htmlspecialchars($bs['gioi_thieu'] ?? 'Đang cập nhật tiểu sử...'); ?></p>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-span-3 bg-white p-8 rounded-3xl text-center text-slate-400 italic text-xs border border-slate-100 shadow-sm">
                    Đang cập nhật bác sĩ cho chuyên khoa này.
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php 
require_once 'views/footer.php'; 
?>