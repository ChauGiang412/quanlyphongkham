<?php require_once 'views/header.php'; ?>

<main class="py-10 bg-slate-50/60 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            
            <!-- THANH ĐIỀU HƯỚNG DẠNG SIDEBAR DÀNH CHO BỆNH NHÂN -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6 space-y-6 sticky top-28">
                    <!-- Thông tin ngắn gọn của bệnh nhân -->
                    <div class="flex items-center space-x-3 pb-6 border-b border-slate-100">
                        <div class="w-12 h-12 rounded-2xl bg-brand-100 text-brand-700 flex items-center justify-center font-bold text-base shadow-inner">
                            <?= strtoupper(substr($_SESSION['user']['ho_ten'] ?? 'BN', 0, 1)); ?>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-slate-900"><?= htmlspecialchars($_SESSION['user']['ho_ten'] ?? 'Bệnh nhân'); ?></div>
                            <span class="text-[10px] text-brand-600 font-semibold bg-brand-50 px-2 py-0.5 rounded-full inline-block mt-1">Cổng Bệnh Nhân</span>
                        </div>
                    </div>

                    <!-- Menu điều hướng riêng -->
                    <nav class="space-y-1.5 text-xs font-semibold">
                        <a href="index.php?act=dat-lich" class="flex items-center space-x-3 px-4 py-3 rounded-2xl text-slate-600 hover:bg-slate-50 hover:text-brand-600 transition">
                            <i class="fa-solid fa-calendar-plus w-5 text-slate-400"></i>
                            <span>Đặt Lịch Khám Mới</span>
                        </a>
                        <a href="index.php?act=lich-su" class="flex items-center space-x-3 px-4 py-3 rounded-2xl <?= ($act == 'lich-su' || $act == 'chi-tiet-lich') ? 'bg-brand-50 text-brand-700 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-brand-600'; ?> transition">
                            <i class="fa-solid fa-clock-rotate-left w-5 <?= ($act == 'lich-su' || $act == 'chi-tiet-lich') ? 'text-brand-600' : 'text-slate-400'; ?>"></i>
                            <span>Lịch Sử Khám Bệnh</span>
                        </a>
                        <a href="index.php?act=ho-so" class="flex items-center space-x-3 px-4 py-3 rounded-2xl <?= ($act == 'ho-so') ? 'bg-brand-50 text-brand-700 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-brand-600'; ?> transition">
                            <i class="fa-solid fa-user-gear w-5 <?= ($act == 'ho-so') ? 'text-brand-600' : 'text-slate-400'; ?>"></i>
                            <span>Cập Nhật Hồ Sơ</span>
                        </a>
                        <div class="pt-4 border-t border-slate-100">
                            <a href="index.php?act=dang-xuat" class="flex items-center space-x-3 px-4 py-3 rounded-2xl text-rose-500 hover:bg-rose-50 transition">
                                <i class="fa-solid fa-right-from-bracket w-5"></i>
                                <span>Đăng Xuất Tài Khoản</span>
                            </a>
                        </div>
                    </nav>
                </div>
            </div>

            <!-- NỘI DUNG CHÍNH CỦA TRANG BỆNH NHÂN -->
            <div class="lg:col-span-3">
                <?php 
                    // Nạp nội dung view con tương ứng (được truyền vào từ Controller)
                    if (isset($subView)) {
                        require_once $subView;
                    }
                ?>
            </div>

        </div>
    </div>
</main>

<?php require_once 'views/footer.php'; ?>