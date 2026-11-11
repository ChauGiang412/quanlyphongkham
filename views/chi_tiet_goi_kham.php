<?php 
require_once 'views/header.php'; 
$tenGoi = $chiTiet['ten_goi_kham'] ?? $chiTiet['ten_goi'] ?? $chiTiet['tieu_de'] ?? 'Gói khám dịch vụ';
?>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    <!-- Nút quay lại -->
    <div>
        <a href="index.php?act=home" class="inline-flex items-center space-x-2 text-xs font-bold text-slate-600 hover:text-brand-600 transition">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Quay lại trang chủ</span>
        </a>
    </div>

    <!-- Thông tin chi tiết gói khám -->
    <?php if (!empty($chiTiet)): ?>
        <div class="bg-white p-8 md:p-10 rounded-3xl border border-slate-100 shadow-sm space-y-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                <div class="lg:col-span-5 space-y-4">
                    <span class="bg-brand-600 text-white text-xs font-bold px-3.5 py-1.5 rounded-full uppercase tracking-wider inline-block shadow-sm">Gói Trọn Gói</span>
                    <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900"><?= htmlspecialchars($tenGoi); ?></h1>
                    <div class="text-xl md:text-2xl font-extrabold text-brand-700">
                        <?= number_format($chiTiet['gia'] ?? 0, 0, ',', '.'); ?> đ
                    </div>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Tầm soát sức khỏe toàn diện với hệ thống trang thiết bị y tế hiện đại, chẩn đoán chính xác và tư vấn phác đồ tối ưu từ chuyên gia.
                    </p>
                    <div class="pt-4 flex flex-wrap gap-4">
                        <a href="index.php?act=chon-loai-lich" class="inline-flex items-center space-x-2 bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs px-6 py-3.5 rounded-2xl shadow transition uppercase tracking-wider">
                            <i class="fa-solid fa-calendar-check"></i>
                            <span>Đặt Gói Khám Ngay</span>
                        </a>
                    </div>
                </div>

                <div class="lg:col-span-7 bg-brand-50/50 p-6 rounded-2xl border border-brand-100 space-y-4">
                    <h3 class="font-bold text-slate-900 text-sm uppercase tracking-wide flex items-center space-x-2">
                        <i class="fa-solid fa-notes-medical text-brand-600"></i>
                        <span>Quyền Lợi & Danh Mục Khám</span>
                    </h3>
                    <ul class="space-y-2 text-xs text-slate-600 list-disc list-inside leading-relaxed">
                        <li>Khám tổng quát lâm sàng với bác sĩ chuyên khoa đầu ngành.</li>
                        <li>Được hỗ trợ ưu tiên không phải chờ đợi lâu khi đến viện.</li>
                        <li>Trang thiết bị chẩn đoán hình ảnh và xét nghiệm công nghệ cao.</li>
                        <li>Hồ sơ kết quả lưu trữ điện tử tiện lợi, bảo mật tuyệt đối.</li>
                    </ul>
                </div>
            </div>

            <!-- Mô tả chi tiết gói khám -->
            <div class="pt-6 border-t border-slate-100 space-y-4">
                <h3 class="font-bold text-slate-900 text-sm uppercase tracking-wide flex items-center space-x-2">
                    <i class="fa-solid fa-circle-info text-brand-600"></i>
                    <span>Chi Tiết Nội Dung Gói Khám</span>
                </h3>
                <div class="text-xs md:text-sm text-slate-600 leading-relaxed space-y-3 bg-slate-50/60 p-6 rounded-2xl border border-slate-100">
                    <?= nl2br(htmlspecialchars($chiTiet['mo_ta'] ?? 'Chưa có thông tin mô tả chi tiết cho gói khám này.')); ?>
                </div>
            </div>

            <!-- PHẦN BỔ SUNG: QUY TRÌNH KHÁM SỨC KHỎE -->
            <div class="pt-6 border-t border-slate-100 space-y-6">
                <h3 class="font-bold text-slate-900 text-sm uppercase tracking-wide flex items-center space-x-2">
                    <i class="fa-solid fa-list-check text-brand-600"></i>
                    <span>Quy Trình Thực Hiện Gói Khám</span>
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="bg-slate-50 p-5 rounded-2xl border border-slate-100 space-y-2 relative">
                        <span class="w-8 h-8 rounded-full bg-brand-600 text-white font-bold text-xs flex items-center justify-center shadow-sm">1</span>
                        <h4 class="font-bold text-slate-900 text-xs uppercase">Đăng Ký & Tiếp Nhận</h4>
                        <p class="text-[11px] text-slate-500 leading-relaxed">Làm thủ tục nhanh tại quầy lễ tân hoặc xác nhận mã lịch hẹn đặt trực tuyến.</p>
                    </div>

                    <div class="bg-slate-50 p-5 rounded-2xl border border-slate-100 space-y-2 relative">
                        <span class="w-8 h-8 rounded-full bg-brand-600 text-white font-bold text-xs flex items-center justify-center shadow-sm">2</span>
                        <h4 class="font-bold text-slate-900 text-xs uppercase">Khám Lâm Sàng</h4>
                        <p class="text-[11px] text-slate-500 leading-relaxed">Bác sĩ chuyên khoa thăm khám tổng quát, tư vấn các hạng mục trong gói.</p>
                    </div>

                    <div class="bg-slate-50 p-5 rounded-2xl border border-slate-100 space-y-2 relative">
                        <span class="w-8 h-8 rounded-full bg-brand-600 text-white font-bold text-xs flex items-center justify-center shadow-sm">3</span>
                        <h4 class="font-bold text-slate-900 text-xs uppercase">Cận Lâm Sàng</h4>
                        <p class="text-[11px] text-slate-500 leading-relaxed">Thực hiện các bước xét nghiệm máu, siêu âm, X-quang hoặc nội soi theo chỉ định.</p>
                    </div>

                    <div class="bg-slate-50 p-5 rounded-2xl border border-slate-100 space-y-2 relative">
                        <span class="w-8 h-8 rounded-full bg-brand-600 text-white font-bold text-xs flex items-center justify-center shadow-sm">4</span>
                        <h4 class="font-bold text-slate-900 text-xs uppercase">Kết Luận & Tư Vấn</h4>
                        <p class="text-[11px] text-slate-500 leading-relaxed">Nhận kết quả tổng hợp, bác sĩ đọc kết quả, kê đơn và hướng dẫn chăm sóc sức khỏe.</p>
                    </div>
                </div>
            </div>

        </div>
    <?php else: ?>
        <div class="bg-white p-8 rounded-3xl text-center text-slate-400 italic text-xs">Không tìm thấy thông tin gói khám.</div>
    <?php endif; ?>
</main>

<?php 
require_once 'views/footer.php'; 
?>