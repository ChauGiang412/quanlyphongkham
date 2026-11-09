<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hệ Thống Quản Trị - Elite Clinic</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { brand: { 50: '#ecfdf5', 600: '#059669', 700: '#047857' } }
                }
            }
        }
    </script>
</head>
<body class="bg-slate-50 font-sans antialiased text-slate-800">
    <div class="min-h-screen flex">
        <!-- Sidebar Quản Trị -->
        <aside class="w-64 bg-slate-900 text-slate-300 flex flex-col justify-between hidden md:flex">
            <div class="p-6 space-y-6">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-brand-600 text-white flex items-center justify-center font-bold text-lg">EC</div>
                    <span class="text-white font-extrabold text-base tracking-wider">Elite Admin</span>
                </div>
                <nav class="space-y-1 text-xs font-bold">
                    <!-- Nút truy cập Dashboard chính thức -->
                    <a href="index.php?act=admin_dashboard" class="flex items-center space-x-3 px-4 py-3 rounded-xl bg-brand-600 text-white transition shadow-sm">
                        <i class="fa-solid fa-chart-pie w-5"></i>
                        <span>Dashboard Tổng Quan</span>
                    </a>
                    <a href="index.php?act=admin-quan-ly-lich" class="flex items-center space-x-3 px-4 py-3 rounded-xl hover:bg-slate-800 hover:text-white transition">
                        <i class="fa-solid fa-calendar-check w-5"></i>
                        <span>Quản Lý Lịch Khám</span>
                    </a>
                    <a href="index.php?act=admin-chuyen-khoa" class="flex items-center space-x-3 px-4 py-3 rounded-xl hover:bg-slate-800 hover:text-white transition">
                        <i class="fa-solid fa-hospital w-5"></i>
                        <span>Quản Lý Chuyên Khoa</span>
                    </a>
                </nav>
            </div>
            <div class="p-6 border-t border-slate-800 space-y-3">
                <a href="index.php?act=home" class="flex items-center space-x-2 text-xs font-bold text-slate-400 hover:text-white transition">
                    <i class="fa-solid fa-globe"></i>
                    <span>Xem Trang Chủ</span>
                </a>
                <a href="index.php?act=dang-xuat" class="flex items-center space-x-2 text-xs font-bold text-red-400 hover:text-red-300 transition">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    <span>Đăng Xuất</span>
                </a>
            </div>
        </aside>


        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0">
            <header class="bg-white border-b border-slate-100 h-16 flex items-center justify-between px-6 shadow-sm">
                <div class="flex items-center space-x-4">
                    <span class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">Hệ Thống Quản Trị Elite Clinic</span>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="index.php?act=admin-dashboard" class="md:hidden text-xs font-bold text-white bg-brand-600 px-3 py-1.5 rounded-xl">Dashboard</a>
                    <a href="index.php?act=dang-xuat" class="text-xs font-bold text-red-600 bg-red-50 px-3 py-1.5 rounded-xl hover:bg-red-100 transition">Đăng Xuất</a>
                </div>
            </header>
            <main class="flex-1 p-6 md:p-8 space-y-6">

