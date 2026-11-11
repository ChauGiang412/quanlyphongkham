<?php 
require_once 'views/header.php'; 
?>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- ================= THANH ĐIỀU HƯỚNG DỌC (SIDEBAR) ================= -->
        <aside class="hidden lg:block lg:col-span-3 sticky top-28 space-y-4">
            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm space-y-3">
                <div class="font-bold text-slate-900 text-xs uppercase tracking-wider pb-3 border-b border-slate-100 flex items-center space-x-2">
                    <i class="fa-solid fa-bars text-brand-600"></i>
                    <span>Danh Mục Điều Hướng</span>
                </div>
                
                <nav class="space-y-1 text-xs font-semibold">
                    <a href="#hero" class="flex items-center space-x-3 px-4 py-3 rounded-2xl text-slate-600 hover:bg-brand-50 hover:text-brand-600 transition">
                        <i class="fa-solid fa-house w-5 text-center"></i>
                        <span>Trang Chủ Giới Thiệu</span>
                    </a>
                    <a href="#chuyen-khoa" class="flex items-center space-x-3 px-4 py-3 rounded-2xl text-slate-600 hover:bg-brand-50 hover:text-brand-600 transition">
                        <i class="fa-solid fa-stethoscope w-5 text-center"></i>
                        <span>Chuyên Khoa Y Tế</span>
                    </a>
                    <a href="#bac-si" class="flex items-center space-x-3 px-4 py-3 rounded-2xl text-slate-600 hover:bg-brand-50 hover:text-brand-600 transition">
                        <i class="fa-solid fa-user-doctor w-5 text-center"></i>
                        <span>Đội Ngũ Bác Sĩ</span>
                    </a>
                    <a href="#goi-kham" class="flex items-center space-x-3 px-4 py-3 rounded-2xl text-slate-600 hover:bg-brand-50 hover:text-brand-600 transition">
                        <i class="fa-solid fa-notes-medical w-5 text-center"></i>
                        <span>Gói Khám Sức Khỏe</span>
                    </a>
                    <a href="#danh-gia" class="flex items-center space-x-3 px-4 py-3 rounded-2xl text-slate-600 hover:bg-brand-50 hover:text-brand-600 transition">
                        <i class="fa-solid fa-star w-5 text-center"></i>
                        <span>Đánh Giá Bệnh Nhân</span>
                    </a>
                    <a href="#ban-do" class="flex items-center space-x-3 px-4 py-3 rounded-2xl text-slate-600 hover:bg-brand-50 hover:text-brand-600 transition">
                        <i class="fa-solid fa-map-location-dot w-5 text-center"></i>
                        <span>Bản Đồ Chỉ Đường</span>
                    </a>
                </nav>

                <div class="pt-3 border-t border-slate-100">
                    <a href="index.php?act=chon-loai-lich" class="w-full bg-brand-600 hover:bg-brand-700 text-white font-bold py-3 rounded-2xl shadow-lg transition text-xs uppercase tracking-wider flex items-center justify-center space-x-2">
                        <i class="fa-solid fa-calendar-check"></i>
                        <span>Đặt Lịch Khám Ngay</span>
                    </a>
                </div>
            </div>
        </aside>

        <!-- ================= NỘI DUNG CHÍNH (CỘT PHẢI) ================= -->
        <div class="lg:col-span-9 space-y-12">

            <!-- HERO SECTION -->
            <section id="hero" class="relative bg-gradient-to-r from-teal-900 to-brand-700 text-white py-12 md:py-16 px-8 rounded-3xl shadow-lg overflow-hidden">
                <div class="space-y-6 max-w-xl">
                    <span class="inline-block bg-teal-800/80 text-teal-200 text-xs font-bold px-4 py-1.5 rounded-full uppercase tracking-wider border border-teal-600">
                        Bệnh Viện Quốc Tế 5 Sao
                    </span>
                    <h1 class="text-2xl md:text-4xl font-extrabold tracking-tight leading-tight">
                        Chăm Sóc Sức Khỏe Toàn Diện Cùng Chuyên Gia Hàng Đầu
                    </h1>
                    <p class="text-teal-100 text-xs leading-relaxed">
                        Elite Clinic mang tới quy trình đăng ký khám nhanh chóng, không chờ đợi với đội ngũ bác sĩ chuyên khoa giàu kinh nghiệm.
                    </p>
                    <div class="pt-2 flex flex-wrap gap-4">
                        <a href="index.php?act=chon-loai-lich" class="bg-white text-teal-800 font-bold px-6 py-3 rounded-2xl shadow-xl hover:bg-teal-50 transition text-xs flex items-center space-x-2">
                            <i class="fa-solid fa-calendar-check text-brand-600"></i>
                            <span>Đặt Lịch Khám Ngay</span>
                        </a>
                    </div>
                </div>
            </section>

            <!-- 1. CHUYÊN KHOA -->
            <section id="chuyen-khoa" class="space-y-6">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-brand-600 font-bold text-xs uppercase tracking-wider block mb-1">Danh Mục</span>
                        <h2 class="text-xl md:text-2xl font-bold text-slate-900">Chuyên Khoa Y Tế</h2>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php if (!empty($danhSachChuyenKhoa)): ?>
                        <?php foreach ($danhSachChuyenKhoa as $ck): ?>
                            <!-- Bấm vào để xem chi tiết chuyên khoa -->
                            <a href="index.php?act=chi-tiet-chuyen-khoa&id=<?= $ck['ma_chuyen_khoa']; ?>" class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm hover:shadow-md transition group block space-y-4 flex flex-col justify-between">
                                <div class="space-y-3">
                                    <div class="w-12 h-12 rounded-2xl overflow-hidden bg-brand-50 flex items-center justify-center text-lg border border-slate-100 group-hover:scale-105 transition">
                                        <?php if (!empty($ck['hinh_anh'])): ?>
                                            <img src="<?= htmlspecialchars($ck['hinh_anh']); ?>" alt="<?= htmlspecialchars($ck['ten_chuyen_khoa']); ?>" class="w-full h-full object-cover">
                                        <?php else: ?>
                                            <i class="fa-solid fa-stethoscope text-brand-600"></i>
                                        <?php endif; ?>
                                    </div>
                                    <h3 class="font-bold text-slate-900 text-sm group-hover:text-brand-600 transition"><?= htmlspecialchars($ck['ten_chuyen_khoa']); ?></h3>
                                    <p class="text-xs text-slate-500 leading-relaxed line-clamp-2"><?= htmlspecialchars($ck['mo_ta'] ?? ''); ?></p>
                                </div>
                                <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-brand-600">
                                    <span>Xem chi tiết</span>
                                    <i class="fa-solid fa-arrow-right text-[10px] group-hover:translate-x-1 transition"></i>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>

            <!-- 2. BÁC SĨ -->
            <section id="bac-si" class="bg-white p-8 rounded-3xl border border-slate-100 shadow-sm space-y-6">
                <div>
                    <span class="text-brand-600 font-bold text-xs uppercase tracking-wider block mb-1">Đội Ngũ</span>
                    <h2 class="text-xl md:text-2xl font-bold text-slate-900">Bác Sĩ Chuyên Sâu</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php if (!empty($danhSachBacSi)): ?>
                        <?php foreach ($danhSachBacSi as $bs): ?>
                            <!-- Bấm vào để xem chi tiết bác sĩ -->
                            <a href="index.php?act=chi-tiet-bac-si&id=<?= $bs['ma_bac_si']; ?>" class="bg-slate-50 p-6 rounded-2xl border border-slate-100 text-center space-y-3 hover:shadow-md transition block group flex flex-col justify-between">
                                <div class="space-y-3">
                                    <?php if (!empty($bs['hinh_anh'])): ?>
                                        <img src="<?= htmlspecialchars($bs['hinh_anh']); ?>" alt="Bác sĩ" class="w-20 h-20 object-cover rounded-full mx-auto shadow-inner border group-hover:scale-105 transition">
                                    <?php else: ?>
                                        <img src="https://images.unsplash.com/photo-1537368910025-700350fe46c7?auto=format&fit=crop&q=80&w=300" alt="Bác sĩ" class="w-20 h-20 object-cover rounded-full mx-auto shadow-inner border group-hover:scale-105 transition">
                                    <?php endif; ?>

                                    <h3 class="font-bold text-slate-900 text-sm group-hover:text-brand-600 transition"><?= htmlspecialchars($bs['ten_bac_si']); ?></h3>
                                    <p class="text-xs text-brand-600 font-semibold"><?= htmlspecialchars($bs['ten_chuyen_khoa'] ?? 'Bác sĩ chuyên khoa'); ?></p>
                                    <p class="text-xs text-slate-500">Bằng cấp: <?= htmlspecialchars($bs['bang_cap'] ?? 'Chuyên khoa'); ?></p>
                                </div>
                                <div class="pt-2 text-xs font-bold text-brand-600 group-hover:underline">
                                    Xem hồ sơ chi tiết &rarr;
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>

            <!-- 3. GÓI KHÁM -->
            <section id="goi-kham" class="space-y-6">
                <div>
                    <span class="text-brand-600 font-bold text-xs uppercase tracking-wider block mb-1">Dịch Vụ</span>
                    <h2 class="text-xl md:text-2xl font-bold text-slate-900">Gói Khám Sức Khỏe</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <?php if (!empty($danhSachGoiKham)): ?>
                        <?php foreach ($danhSachGoiKham as $gk): 
                            $tenGoi = $gk['ten_goi_kham'] ?? $gk['ten_goi'] ?? $gk['tieu_de'] ?? 'Gói khám dịch vụ';
                        ?>
                            <div class="bg-gradient-to-br from-brand-50 to-white p-8 rounded-3xl border border-brand-100 shadow-sm flex flex-col justify-between hover:shadow-md transition group">
                                <div>
                                    <span class="bg-brand-600 text-white text-[10px] font-bold px-3 py-1 rounded-full uppercase">Trọn gói</span>
                                    <h3 class="text-lg font-bold text-slate-900 mt-4 mb-2">
                                        <!-- Bấm vào tiêu đề để xem chi tiết gói khám -->
                                        <a href="index.php?act=chi-tiet-goi-kham&id=<?= $gk['ma_goi_kham']; ?>" class="hover:text-brand-600 transition">
                                            <?= htmlspecialchars($tenGoi); ?>
                                        </a>
                                    </h3>
                                    <p class="text-xs text-slate-600 mb-6 line-clamp-2"><?= htmlspecialchars($gk['mo_ta'] ?? ''); ?></p>
                                </div>
                                <div class="flex items-center justify-between pt-4 border-t border-brand-100">
                                    <span class="text-brand-700 font-bold text-sm"><?= number_format($gk['gia'] ?? 0, 0, ',', '.'); ?> đ</span>
                                    <div class="flex space-x-2">
                                        <!-- Nút xem chi tiết gói khám -->
                                        <a href="index.php?act=chi-tiet-goi-kham&id=<?= $gk['ma_goi_kham']; ?>" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs px-3.5 py-2.5 rounded-xl transition">Chi tiết</a>
                                        <!-- Nút đặt gói khám -->
                                        <a href="index.php?act=dat-goi-kham&id=<?= $gk['ma_goi_kham']; ?>" class="bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl shadow transition">Đặt gói</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>

            <!-- 4. ĐÁNH GIÁ -->
            <section id="danh-gia" class="bg-white p-8 rounded-3xl border border-slate-100 shadow-sm space-y-6">
                <div>
                    <span class="text-brand-600 font-bold text-xs uppercase tracking-wider block mb-1">Phản Hồi</span>
                    <h2 class="text-xl md:text-2xl font-bold text-slate-900">Ý Kiến Bệnh Nhân</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100 space-y-3">
                        <div class="flex text-amber-400 text-xs"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></div>
                        <p class="text-xs text-slate-600">"Dịch vụ khám rất nhanh chóng, bác sĩ tư vấn tận tình và nhẹ nhàng. Tôi hoàn toàn hài lòng."</p>
                        <div class="font-bold text-xs text-slate-800">Nguyễn Văn An</div>
                    </div>
                    <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100 space-y-3">
                        <div class="flex text-amber-400 text-xs"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></div>
                        <p class="text-xs text-slate-600">"Không phải chờ đợi lâu, hệ thống tra cứu đơn thuốc sau khi khám xong cực kỳ tiện lợi."</p>
                        <div class="font-bold text-xs text-slate-800">Trần Thị Mai</div>
                    </div>
                </div>
            </section>

            <!-- 5. BẢN ĐỒ -->
            <section id="ban-do" class="space-y-6">
                <div>
                    <span class="text-brand-600 font-bold text-xs uppercase tracking-wider block mb-1">Địa Chỉ</span>
                    <h2 class="text-xl md:text-2xl font-bold text-slate-900">Bản Đồ Chỉ Đường</h2>
                </div>
                <div class="bg-slate-100 h-72 rounded-3xl overflow-hidden border border-slate-200 shadow-sm">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3723.863855881425!2d105.78165707503175!3d21.034120380614533!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3135ab868b505c9f%3A0x80852e92c235583b!2sVHLGsOG7nW5nIMSQ4bqhaSBo4buNYyBUaMawxqFuZyBt4bqhaQ!5e0!3m2!1svi!2s!4v1700000000000!5m2!1svi!2s" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                </div>
            </section>

        </div>

    </div>
</main>

<?php require_once 'views/footer.php'; ?>