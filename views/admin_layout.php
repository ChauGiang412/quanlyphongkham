<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hệ Thống Quản Trị - Admin Panel</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-slate-100 text-slate-800 font-sans antialiased m-0 p-0">

    <div class="flex h-screen overflow-hidden">
        
        <!-- ================= SIDEBAR QUẢN TRỊ RIÊNG BIỆT (BÊN TRÁI) ================= -->
        <aside class="w-64 bg-slate-900 text-slate-300 flex flex-col justify-between p-6 shadow-2xl z-20">
            <div class="space-y-8">
                <!-- Logo Quản Trị -->
                <div class="flex items-center space-x-3 pb-6 border-b border-slate-800">
                    <div class="w-10 h-10 rounded-2xl bg-teal-600 text-white flex items-center justify-center text-lg font-bold shadow-md">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <div>
                        <h2 class="font-extrabold text-white text-sm tracking-wide">Elite Admin</h2>
                        <span class="text-[10px] text-teal-400 font-bold uppercase tracking-widest">Hệ Thống Quản Trị</span>
                    </div>
                </div>

                <!-- Menu Chức Năng Quản Trị -->
                <nav class="space-y-2 text-xs font-semibold">
                    <a href="index.php?act=admin-dashboard" class="flex items-center space-x-3 px-4 py-3 rounded-2xl transition <?= (isset($_GET['act']) && $_GET['act'] == 'admin-dashboard') ? 'bg-teal-600 text-white shadow-lg' : 'hover:bg-slate-800 hover:text-white'; ?>">
                        <i class="fa-solid fa-chart-pie w-5 text-center"></i>
                        <span>Tổng Quan Dashboard</span>
                    </a>
                    <a href="index.php?act=admin-quan-ly-lich" class="flex items-center space-x-3 px-4 py-3 rounded-2xl transition <?= (isset($_GET['act']) && $_GET['act'] == 'admin-quan-ly-lich') ? 'bg-teal-600 text-white shadow-lg' : 'hover:bg-slate-800 hover:text-white'; ?>">
                        <i class="fa-solid fa-calendar-check w-5 text-center"></i>
                        <span>Quản Lý Lịch Hẹn</span>
                    </a>
                </nav>
            </div>

            <!-- Nút Thoát / Về trang chủ / Đăng xuất -->
            <div class="space-y-2 pt-6 border-t border-slate-800 text-xs font-bold">
                <a href="index.php?act=home" class="flex items-center space-x-3 px-4 py-3 rounded-2xl hover:bg-slate-800 hover:text-white transition text-slate-400">
                    <i class="fa-solid fa-globe w-5 text-center"></i>
                    <span>Trang Chủ Website</span>
                </a>
                <a href="index.php?act=dang-xuat" class="flex items-center space-x-3 px-4 py-3 rounded-2xl text-red-400 hover:bg-red-500/10 transition">
                    <i class="fa-solid fa-right-from-bracket w-5 text-center"></i>
                    <span>Đăng Xuất</span>
                </a>
            </div>
        </aside>

        <!-- ================= KHUNG NỘI DUNG CHÍNH (BÊN PHẢI) ================= -->
        <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">
            
            <!-- Top Header Quản Trị -->
            <header class="bg-white border-b border-slate-200 h-20 px-8 flex items-center justify-between shadow-sm sticky top-0 z-10">
                <div class="flex items-center space-x-3">
                    <h1 class="text-sm font-extrabold text-slate-900 uppercase tracking-wide">Khu Vực Quản Trị Hệ Thống Toàn Viện</h1>
                </div>
                <div class="flex items-center space-x-3 bg-slate-50 px-4 py-2 rounded-2xl border border-slate-200 text-xs font-bold text-slate-700">
                    <i class="fa-solid fa-user-shield text-teal-600 text-base"></i>
                    <span>Xin chào, <?= htmlspecialchars($_SESSION['user']['ho_ten'] ?? 'Quản trị viên'); ?></span>
                </div>
            </header>

            <!-- Nội dung thay đổi linh hoạt (được nhúng vào đây) -->
            <div class="p-8 space-y-8 max-w-7xl mx-auto w-full flex-1">
                <?php 
                    // Nhúng nội dung trang con tương ứng (dashboard hoặc quản lý lịch)
                    if (isset($adminViewFile) && file_exists($adminViewFile)) {
                        require_once $adminViewFile;
                    }
                ?>
            </div>

            <!-- Footer Quản Trị -->
            <footer class="bg-white border-t border-slate-200 py-4 px-8 text-center text-xs text-slate-400">
                &copy; <?= date('Y'); ?> Elite Clinic Admin Management System. All rights reserved.
            </footer>

        </div>

    </div>

</body>
</html>