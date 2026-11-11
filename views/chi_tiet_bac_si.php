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

    <!-- Thông tin chi tiết bác sĩ -->
    <?php if (!empty($chiTiet)): ?>
        <div class="bg-white p-8 md:p-10 rounded-3xl border border-slate-100 shadow-sm space-y-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                <!-- Cột hình ảnh bác sĩ -->
                <div class="lg:col-span-4 flex justify-center">
                    <?php if (!empty($chiTiet['hinh_anh'])): ?>
                        <img src="<?= htmlspecialchars($chiTiet['hinh_anh']); ?>" alt="<?= htmlspecialchars($chiTiet['ten_bac_si']); ?>" class="w-56 h-56 lg:w-64 lg:h-64 object-cover rounded-full border-4 border-brand-50 shadow-md">
                    <?php else: ?>
                        <img src="https://images.unsplash.com/photo-1537368910025-700350fe46c7?auto=format&fit=crop&q=80&w=300" alt="Bác sĩ" class="w-56 h-56 lg:w-64 lg:h-64 object-cover rounded-full border-4 border-brand-50 shadow-md">
                    <?php endif; ?>
                </div>

                <!-- Cột thông tin cá nhân và nút đặt lịch -->
                <div class="lg:col-span-8 space-y-4">
                    <span class="bg-brand-50 text-brand-600 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider inline-block">
                        <?= htmlspecialchars($chiTiet['ten_chuyen_khoa'] ?? 'Bác sĩ chuyên khoa'); ?>
                    </span>
                    <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900"><?= htmlspecialchars($chiTiet['ten_bac_si']); ?></h1>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 space-y-1">
                            <span class="text-slate-400 text-[11px] font-semibold uppercase tracking-wider block">Bằng Cấp / Học Vị</span>
                            <span class="text-slate-800 font-bold text-xs"><?= htmlspecialchars($chiTiet['bang_cap'] ?? 'Đang cập nhật'); ?></span>
                        </div>
                        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 space-y-1">
                            <span class="text-slate-400 text-[11px] font-semibold uppercase tracking-wider block">Kinh Nghiệm</span>
                            <span class="text-brand-600 font-bold text-xs"><?= htmlspecialchars($chiTiet['so_nam_kinh_nghiem'] ?? 0); ?> năm kinh nghiệm</span>
                        </div>
                    </div>

                    <div class="pt-4 flex flex-wrap gap-4">
                        <a href="index.php?act=chon-loai-lich" class="inline-flex items-center space-x-2 bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs px-6 py-3.5 rounded-2xl shadow transition uppercase tracking-wider">
                            <i class="fa-solid fa-calendar-check"></i>
                            <span>Đặt Lịch Khám Với Bác Sĩ Này</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Khung tiểu sử / giới thiệu chuyên sâu mở rộng -->
            <div class="pt-6 border-t border-slate-100 space-y-4">
                <h3 class="font-bold text-slate-900 text-sm uppercase tracking-wide flex items-center space-x-2">
                    <i class="fa-solid fa-user-doctor text-brand-600"></i>
                    <span>Tiểu Sử & Quá Trình Công Tác</span>
                </h3>
                <div class="text-xs md:text-sm text-slate-600 leading-relaxed space-y-3 bg-slate-50/60 p-6 rounded-2xl border border-slate-100">
                    <?= nl2br(htmlspecialchars($chiTiet['gioi_thieu'] ?? 'Chưa có thông tin giới thiệu chi tiết cho bác sĩ này.')); ?>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="bg-white p-8 rounded-3xl text-center text-slate-400 italic text-xs">Không tìm thấy thông tin bác sĩ.</div>
    <?php endif; ?>
</main>

<?php 
require_once 'views/footer.php'; 
?>