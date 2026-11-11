
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user = $_SESSION['user'] ?? null;
$role = $user['vai_tro'] ?? '';
$name = $user['ho_ten'] ?? 'Tài khoản';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Elite Clinic - Hệ Thống Y Tế</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#f0fdfa',
                            100: '#ccfbf1',
                            600: '#0d9488',
                            700: '#0f766e',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Font Awesome -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Alpine JS -->
    <script defer
            src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] {
            display: none !important;
        }

        html {
            scroll-behavior: smooth;
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-800 font-sans antialiased min-h-screen">

<header class="bg-white/95 backdrop-blur-md sticky top-0 z-50 border-b border-slate-100 shadow-sm">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 min-h-20 flex items-center justify-between gap-4">

        <!-- LOGO -->
        <a href="index.php?act=home"
           class="flex items-center space-x-3 group shrink-0">

            <div class="w-11 h-11 rounded-2xl bg-brand-600 text-white flex items-center justify-center text-xl shadow-md group-hover:bg-brand-700 transition">
                <i class="fa-solid fa-hospital"></i>
            </div>

            <div>
                <span class="text-lg font-extrabold text-slate-900 tracking-tight block">
                    Elite Clinic
                </span>

                <span class="text-[10px] text-brand-600 font-bold uppercase tracking-widest block">
                    Hệ Thống Y Tế 5 Sao
                </span>
            </div>
        </a>

        <!-- MENU -->
        <nav class="hidden lg:flex items-center gap-6 text-xs font-bold text-slate-600">

            <a href="index.php?act=home"
               class="hover:text-brand-600 transition">
                Trang Chủ
            </a>

            <a href="index.php?act=home#chuyen-khoa"
               class="hover:text-brand-600 transition">
                Chuyên Khoa
            </a>

            <a href="index.php?act=home#bac-si"
               class="hover:text-brand-600 transition">
                Bác Sĩ
            </a>

            <a href="index.php?act=home#goi-kham"
               class="hover:text-brand-600 transition">
                Gói Khám
            </a>

            <?php if (!$user || $role === 'benh_nhan'): ?>
                <a href="index.php?act=chon-loai-lich"
                   class="bg-brand-600 hover:bg-brand-700 text-white px-5 py-3 rounded-xl shadow-md transition">
                    <i class="fa-solid fa-calendar-check mr-1"></i>
                    Đặt Lịch Khám
                </a>
            <?php endif; ?>

        </nav>

        <!-- TÀI KHOẢN -->
        <div class="flex items-center gap-3">

            <?php if ($user): ?>

                <?php if ($role === 'bac_si'): ?>

                    <a href="index.php?act=bac-si-dashboard"
                       class="hidden sm:flex items-center gap-2 bg-teal-50 hover:bg-teal-100 text-teal-700 font-bold text-xs px-4 py-3 rounded-xl border border-teal-100 transition">

                        <i class="fa-solid fa-house-medical"></i>
                        Dashboard bác sĩ
                    </a>

                <?php elseif ($role === 'quan_tri'): ?>

                    <a href="index.php?act=admin-dashboard"
                       class="hidden sm:flex items-center gap-2 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs px-4 py-3 rounded-xl transition">

                        <i class="fa-solid fa-shield-halved"></i>
                        Trang Quản Trị
                    </a>

                <?php endif; ?>

                <!-- DROPDOWN -->
                <div class="relative" x-data="{ open: false }">

                    <button type="button"
                            @click="open = !open"
                            class="flex items-center gap-2 bg-slate-100 hover:bg-slate-200 px-3 py-2.5 rounded-xl transition text-xs font-bold text-slate-700">

                        <i class="fa-solid fa-circle-user text-brand-600 text-lg"></i>

                        <span class="max-w-[110px] truncate">
                            <?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>
                        </span>

                        <i class="fa-solid fa-chevron-down text-[10px]"></i>
                    </button>

                    <div x-cloak
                         x-show="open"
                         @click.outside="open = false"
                         class="absolute right-0 mt-2 w-56 bg-white border border-slate-100 rounded-2xl shadow-xl py-2 text-xs font-semibold text-slate-700 z-50">

                        <?php if ($role === 'bac_si'): ?>

                            <a href="index.php?act=bac-si-dashboard"
                               class="block px-4 py-3 hover:bg-brand-50 hover:text-brand-600">
                                <i class="fa-solid fa-chart-line mr-2"></i>
                                Dashboard bác sĩ
                            </a>

                            <a href="index.php?act=bac-si-lich-kham"
                               class="block px-4 py-3 hover:bg-brand-50 hover:text-brand-600">
                                <i class="fa-solid fa-calendar-check mr-2"></i>
                                Quản lý lịch khám
                            </a>

                            <a href="index.php?act=bac-si-benh-nhan"
                               class="block px-4 py-3 hover:bg-brand-50 hover:text-brand-600">
                                <i class="fa-solid fa-users mr-2"></i>
                                Bệnh nhân
                            </a>

                            <a href="index.php?act=bac-si-danh-gia"
                               class="block px-4 py-3 hover:bg-brand-50 hover:text-brand-600">
                                <i class="fa-solid fa-star mr-2"></i>
                                Đánh giá
                            </a>

                            <a href="index.php?act=bac-si-ho-so"
                               class="block px-4 py-3 hover:bg-brand-50 hover:text-brand-600">
                                <i class="fa-solid fa-user-doctor mr-2"></i>
                                Hồ sơ bác sĩ
                            </a>

                        <?php elseif ($role === 'quan_tri'): ?>

                            <a href="index.php?act=admin-dashboard"
                               class="block px-4 py-3 hover:bg-brand-50 hover:text-brand-600">
                                <i class="fa-solid fa-gauge-high mr-2"></i>
                                Dashboard quản trị
                            </a>

                            <a href="index.php?act=admin-lich-kham"
                               class="block px-4 py-3 hover:bg-brand-50 hover:text-brand-600">
                                <i class="fa-solid fa-calendar-days mr-2"></i>
                                Quản lý lịch khám
                            </a>

                        <?php else: ?>

                            <a href="index.php?act=ho-so"
                               class="block px-4 py-3 hover:bg-brand-50 hover:text-brand-600">
                                <i class="fa-solid fa-user-pen mr-2"></i>
                                Hồ sơ cá nhân
                            </a>

                            <a href="index.php?act=lich-su"
                               class="block px-4 py-3 hover:bg-brand-50 hover:text-brand-600">
                                <i class="fa-solid fa-calendar-days mr-2"></i>
                                Lịch sử khám
                            </a>

                        <?php endif; ?>

                        <div class="border-t border-slate-100 my-1"></div>

                        <a href="index.php?act=logout"
                           class="block px-4 py-3 text-red-600 hover:bg-red-50">
                            <i class="fa-solid fa-right-from-bracket mr-2"></i>
                            Đăng Xuất
                        </a>

                    </div>
                </div>

            <?php else: ?>

                <div class="flex items-center gap-2">

                    <a href="index.php?act=login"
                       class="text-xs font-bold text-slate-700 hover:text-brand-600 px-3 py-2">
                        Đăng Nhập
                    </a>

                    <a href="index.php?act=register"
                       class="bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold px-4 py-3 rounded-xl shadow transition">
                        Đăng Ký
                    </a>

                </div>

            <?php endif; ?>

        </div>
    </div>

    <!-- MENU BÁC SĨ TRÊN ĐIỆN THOẠI -->
    <?php if ($user && $role === 'bac_si'): ?>

        <div class="sm:hidden border-t border-slate-100 px-4 py-3 flex gap-5 overflow-x-auto text-xs font-bold text-slate-600">

            <a href="index.php?act=bac-si-dashboard"
               class="whitespace-nowrap text-teal-700">
                Dashboard
            </a>

            <a href="index.php?act=bac-si-lich-kham"
               class="whitespace-nowrap">
                Lịch khám
            </a>

            <a href="index.php?act=bac-si-benh-nhan"
               class="whitespace-nowrap">
                Bệnh nhân
            </a>

            <a href="index.php?act=bac-si-danh-gia"
               class="whitespace-nowrap">
                Đánh giá
            </a>

            <a href="index.php?act=bac-si-ho-so"
               class="whitespace-nowrap">
                Hồ sơ
            </a>

        </div>

    <?php endif; ?>

</header>
