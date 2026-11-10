<?php
if (!function_exists('bs_e')) {
    function bs_e($value): string
    {
        return htmlspecialchars(
            (string)($value ?? ''),
            ENT_QUOTES,
            'UTF-8'
        );
    }
}


$doctorSession = $_SESSION['bac_si'] ?? [];


$doctorName = $doctorSession['ten_bac_si']
    ?? ($_SESSION['user']['ho_ten'] ?? 'Bác sĩ');


$flash = $_SESSION['bs_flash'] ?? null;
unset($_SESSION['bs_flash']);
?>


<!DOCTYPE html>
<html lang="vi">


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">


    <title><?= bs_e($pageTitle) ?> | Elite Clinic</title>


    <script src="https://cdn.tailwindcss.com"></script>


    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>


<body class="bg-slate-50 text-slate-800">


<div class="min-h-screen md:flex">


    <!-- SIDEBAR -->
    <aside class="w-full md:w-64 bg-slate-950 text-white md:min-h-screen flex-shrink-0">


        <div class="px-6 py-6 border-b border-white/10">


            <a href="index.php?act=bac-si-dashboard"
               class="flex items-center gap-3">


                <div class="w-11 h-11 rounded-2xl bg-teal-500 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-stethoscope"></i>
                </div>


                <div>
                    <h2 class="font-bold text-lg">Elite Clinic</h2>


                    <p class="text-teal-300 text-[10px] uppercase tracking-widest">
                        Doctor Portal
                    </p>
                </div>


            </a>


        </div>


        <!-- THÔNG TIN BÁC SĨ -->
        <div class="px-6 py-5 border-b border-white/10">


            <p class="text-xs text-slate-400">
                Xin chào,
            </p>


            <p class="font-bold mt-1">
                <?= bs_e($doctorName) ?>
            </p>


            <p class="text-xs text-teal-300 mt-1">
                <?= bs_e($doctorSession['ten_chuyen_khoa'] ?? 'Bác sĩ') ?>
            </p>


        </div>


        <!-- MENU -->
        <nav class="p-3 space-y-1 text-sm">


            <?php
            $menus = [
                [
                    'dashboard',
                    'bac-si-dashboard',
                    'Tổng quan',
                    'fa-chart-pie'
                ],
                [
                    'lich-kham',
                    'bac-si-lich-kham',
                    'Lịch khám của tôi',
                    'fa-calendar-check'
                ],
                [
                    'benh-nhan',
                    'bac-si-benh-nhan',
                    'Bệnh nhân',
                    'fa-users'
                ],
                [
                    'danh-gia',
                    'bac-si-danh-gia',
                    'Đánh giá',
                    'fa-star'
                ],
                [
                    'ho-so',
                    'bac-si-ho-so',
                    'Hồ sơ cá nhân',
                    'fa-user-doctor'
                ]
            ];


            foreach ($menus as $menu):
                $isActive = $activeMenu === $menu[0];
            ?>


                <a href="index.php?act=<?= bs_e($menu[1]) ?>"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl transition
                   <?= $isActive
                       ? 'bg-teal-500 text-white'
                       : 'text-slate-300 hover:bg-white/10 hover:text-white' ?>">


                    <i class="fa-solid <?= bs_e($menu[3]) ?> w-5 text-center"></i>


                    <span><?= bs_e($menu[2]) ?></span>


                </a>


            <?php endforeach; ?>


            <div class="pt-4 mt-4 border-t border-white/10">


                <a href="index.php?act=home"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-300 hover:bg-white/10">


                    <i class="fa-solid fa-house w-5 text-center"></i>


                    <span>Về trang chủ</span>


                </a>


                <a href="index.php?act=logout"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-rose-300 hover:bg-rose-500/10">


                    <i class="fa-solid fa-right-from-bracket w-5 text-center"></i>


                    <span>Đăng xuất</span>


                </a>


            </div>


        </nav>


    </aside>


    <!-- MAIN CONTENT -->
    <main class="flex-1 min-w-0">


        <header class="bg-white border-b border-slate-200 px-5 md:px-8 py-5 flex items-center justify-between">


            <div>


                <p class="text-xs text-slate-400 font-semibold uppercase tracking-wider">
                    Khu vực bác sĩ
                </p>


                <h1 class="text-xl md:text-2xl font-extrabold text-slate-900 mt-1">
                    <?= bs_e($pageTitle) ?>
                </h1>


            </div>


            <div class="hidden sm:flex items-center gap-3 text-sm text-slate-500">


                <span class="w-10 h-10 rounded-full bg-teal-50 text-teal-700 flex items-center justify-center">
                    <i class="fa-solid fa-user-doctor"></i>
                </span>


                <span><?= bs_e($doctorName) ?></span>


            </div>


        </header>


        <div class="p-5 md:p-8 max-w-7xl mx-auto">


            <?php if ($flash): ?>


                <div class="mb-6 rounded-xl px-4 py-3 text-sm font-semibold
                    <?= ($flash['type'] ?? '') === 'error'
                        ? 'bg-rose-50 text-rose-700 border border-rose-100'
                        : 'bg-emerald-50 text-emerald-700 border border-emerald-100' ?>">


                    <?= bs_e($flash['message'] ?? '') ?>


                </div>


            <?php endif; ?>


            <?php require $viewFile; ?>


        </div>


    </main>


</div>


</body>
</html>
