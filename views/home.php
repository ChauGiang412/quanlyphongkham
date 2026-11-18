
<?php
require_once 'views/header.php';

if (!function_exists('e')) {
    function e($value): string {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}
?>

<style>
    [x-cloak] {
        display: none !important;
    }

    html {
        scroll-behavior: smooth;
        scroll-padding-top: 100px;
    }

    .clinic-section {
        scroll-margin-top: 100px;
    }

    .clinic-card {
        transition: transform .3s ease, box-shadow .3s ease, border-color .3s ease;
    }

    .clinic-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 20px 45px rgba(15, 118, 110, .10);
        border-color: #99f6e4;
    }

    .clinic-image {
        transition: transform .7s ease;
    }

    .clinic-card:hover .clinic-image {
        transform: scale(1.06);
    }

    .clinic-gradient {
        background: linear-gradient(135deg, #0f766e 0%, #115e59 55%, #134e4a 100%);
    }

    .clinic-soft-bg {
        background: linear-gradient(135deg, #f0fdfa 0%, #ffffff 65%, #ecfeff 100%);
    }

    .clinic-label {
        letter-spacing: .16em;
    }

    /* ===== Slider lướt ngang (chuyên khoa / bác sĩ / gói khám) ===== */
    .hs { position: relative; }
    .hs-track {
        --hs-gap: 1.5rem;
        display: flex;
        gap: var(--hs-gap);
        overflow-x: auto;
        scroll-snap-type: x mandatory;
        scroll-behavior: smooth;
        -webkit-overflow-scrolling: touch;
        padding: 14px 4px 22px;
        margin: -14px -4px -22px;
        scrollbar-width: none;
    }
    .hs-track::-webkit-scrollbar { display: none; }
    .hs-track > * {
        flex: 0 0 86%;
        min-width: 0;
        scroll-snap-align: start;
    }
    @media (min-width: 640px) {
        .hs-track > * { flex-basis: calc((100% - var(--hs-gap)) / 2); }
    }
    @media (min-width: 1280px) {
        .hs-track > * { flex-basis: calc((100% - 2 * var(--hs-gap)) / 3); }
    }
    .hs-track > .col-span-full { flex-basis: 100%; }
    .hs-btn {
        position: absolute;
        top: 50%;
        z-index: 10;
        width: 46px;
        height: 46px;
        margin-top: -23px;
        border-radius: 9999px;
        border: 1px solid #e2e8f0;
        background: #fff;
        color: #0f766e;
        box-shadow: 0 10px 25px rgba(15, 23, 42, .15);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: background .2s, color .2s, opacity .2s, transform .2s;
    }
    .hs-btn:hover:not(:disabled) { background: #0d9488; color: #fff; transform: scale(1.08); }
    .hs-btn:disabled { opacity: 0; pointer-events: none; }
    .hs-prev { left: -14px; }
    .hs-next { right: -14px; }
    .hs-dots {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 8px;
        margin-top: 18px;
    }
    .hs-dot {
        width: 9px;
        height: 9px;
        padding: 0;
        border: 0;
        border-radius: 9999px;
        background: #cbd5e1;
        cursor: pointer;
        transition: width .25s ease, background .25s ease;
    }
    .hs-dot:hover { background: #5eead4; }
    .hs-dot.is-active { width: 26px; background: #0d9488; }
    @media (max-width: 639px) {
        .hs-btn { width: 38px; height: 38px; margin-top: -19px; }
        .hs-prev { left: -6px; }
        .hs-next { right: -6px; }
    }
</style>

<main
    x-data="{ sidebarOpen: false }"
    @toggle-home-sidebar.window="sidebarOpen = !sidebarOpen"
    class="w-full max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-7 md:py-10"
>
    <div
        class="grid grid-cols-1 gap-7 items-start"
        :class="sidebarOpen ? 'lg:grid-cols-12' : 'lg:grid-cols-1'"
    >

        <!-- =====================================================
             SIDEBAR ĐIỀU HƯỚNG
        ====================================================== -->
        <aside
            x-cloak
            x-show="sidebarOpen"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 -translate-x-4"
            x-transition:enter-end="opacity-100 translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-x-0"
            x-transition:leave-end="opacity-0 -translate-x-4"
            class="lg:col-span-3 space-y-5"
        >
            <div class="bg-white p-5 md:p-6 rounded-3xl border border-slate-200/70 shadow-sm sticky top-24">

                <div class="flex items-center gap-3 pb-5 border-b border-slate-100">
                    <div class="w-10 h-10 rounded-xl clinic-gradient text-white flex items-center justify-center shadow-md">
                        <i class="fa-solid fa-compass"></i>
                    </div>

                    <div>
                        <h3 class="font-extrabold text-slate-900 text-sm">
                            Điều hướng
                        </h3>
                        <p class="text-[11px] text-slate-400 mt-1">
                            Khám phá Elite Clinic
                        </p>
                    </div>
                </div>

                <nav class="space-y-1.5 mt-5">

                    <?php
                    $menuItems = [
                        ['hero', 'fa-house', 'Trang chủ'],
                        ['video-gioi-thieu', 'fa-circle-play', 'Giới thiệu'],
                        ['co-so-vat-chat', 'fa-hospital', 'Cơ sở vật chất'],
                        ['chuyen-khoa', 'fa-stethoscope', 'Chuyên khoa'],
                        ['bac-si', 'fa-user-doctor', 'Đội ngũ bác sĩ'],
                        ['goi-kham', 'fa-notes-medical', 'Gói khám'],
                        ['danh-gia', 'fa-star', 'Đánh giá'],
                        ['ban-do', 'fa-map-location-dot', 'Bản đồ']
                    ];
                    ?>

                    <?php foreach ($menuItems as $item): ?>
                        <a
                            href="#<?= e($item[0]); ?>"
                            class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:bg-teal-50 hover:text-teal-700 transition group"
                        >
                            <i class="fa-solid <?= e($item[1]); ?> w-5 text-center text-slate-400 group-hover:text-teal-600"></i>
                            <span class="text-xs font-semibold"><?= e($item[2]); ?></span>
                            <i class="fa-solid fa-chevron-right text-[9px] ml-auto opacity-0 group-hover:opacity-100 transition"></i>
                        </a>
                    <?php endforeach; ?>

                </nav>

                <div class="mt-5 pt-5 border-t border-slate-100">
                    <a
                        href="index.php?act=chon-loai-lich"
                        class="clinic-gradient w-full text-white font-bold py-3.5 rounded-xl shadow-lg shadow-teal-900/15 hover:shadow-xl transition flex items-center justify-center gap-2 text-xs"
                    >
                        <i class="fa-solid fa-calendar-check"></i>
                        Đặt lịch khám ngay
                    </a>
                </div>

            </div>
        </aside>

        <!-- =====================================================
             NỘI DUNG CHÍNH
        ====================================================== -->
        <div
            class="min-w-0 space-y-16 md:space-y-20"
            :class="sidebarOpen ? 'lg:col-span-9' : 'lg:col-span-1'"
        >

            <!-- =================================================
                 1. HERO BANNER
            ================================================== -->
            <section id="hero" class="clinic-section relative min-h-[480px] md:min-h-[560px] rounded-[2rem] overflow-hidden shadow-xl bg-teal-950">

                <img
                    src="https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?auto=format&fit=crop&w=2000&q=85"
                    alt="Không gian bệnh viện hiện đại"
                    class="absolute inset-0 w-full h-full object-cover"
                    fetchpriority="high"
                >

                <div class="absolute inset-0 bg-gradient-to-r from-teal-950/95 via-teal-900/80 to-teal-900/20"></div>

                <div class="absolute -right-24 -top-24 w-96 h-96 border border-white/10 rounded-full"></div>
                <div class="absolute -right-12 -top-12 w-72 h-72 border border-white/10 rounded-full"></div>

                <div class="relative z-10 min-h-[480px] md:min-h-[560px] flex items-center px-7 py-14 md:px-14 md:py-20">

                    <div class="max-w-3xl space-y-7">

                        <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-md border border-white/20 text-teal-100 px-4 py-2 rounded-full text-[10px] md:text-xs font-bold uppercase clinic-label">
                            <i class="fa-solid fa-heart-pulse"></i>
                            Chào mừng đến với Elite Clinic
                        </div>

                        <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-extrabold text-white leading-[1.15] tracking-tight">
                            Chăm Sóc Sức Khỏe
                            <span class="text-teal-300">Toàn Diện</span>
                            Cùng Chuyên Gia
                        </h1>

                        <p class="text-sm md:text-base text-teal-50/90 leading-8 max-w-2xl">
                            Trải nghiệm dịch vụ chăm sóc sức khỏe chuyên nghiệp với đội ngũ bác sĩ giàu kinh nghiệm, quy trình đặt lịch thuận tiện và không gian khám chữa bệnh hiện đại.
                        </p>

                        <div class="flex flex-wrap gap-3 pt-2">

                            <a
                                href="index.php?act=chon-loai-lich"
                                class="bg-white text-teal-800 font-extrabold px-6 py-4 rounded-xl shadow-xl hover:bg-teal-50 hover:-translate-y-1 transition flex items-center gap-3 text-xs md:text-sm"
                            >
                                <i class="fa-solid fa-calendar-check text-teal-600"></i>
                                Đặt lịch khám ngay
                                <i class="fa-solid fa-arrow-right text-xs"></i>
                            </a>

                            <a
                                href="#chuyen-khoa"
                                class="border border-white/40 bg-white/10 backdrop-blur-sm text-white font-bold px-6 py-4 rounded-xl hover:bg-white/20 transition flex items-center gap-2 text-xs md:text-sm"
                            >
                                Khám phá dịch vụ
                            </a>

                        </div>

                        <div class="flex flex-wrap gap-5 md:gap-8 pt-5 text-teal-50/90 text-xs">

                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-user-doctor text-teal-300"></i>
                                Đội ngũ bác sĩ
                            </div>

                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-hospital text-teal-300"></i>
                                Không gian hiện đại
                            </div>

                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-calendar-check text-teal-300"></i>
                                Đặt lịch tiện lợi
                            </div>

                        </div>

                    </div>
                </div>
            </section>

            <!-- =================================================
                 2. GIỚI THIỆU
            ================================================== -->
            <section id="video-gioi-thieu" class="clinic-section grid grid-cols-1 lg:grid-cols-2 gap-9 lg:gap-14 items-center">

                <div class="space-y-6">

                    <div>
                        <span class="text-teal-700 font-extrabold text-[10px] uppercase clinic-label">
                            Về chúng tôi
                        </span>

                        <h2 class="text-2xl md:text-4xl font-extrabold text-slate-900 leading-tight mt-3">
                            Sức Khỏe Của Bạn
                            <span class="text-teal-700">Là Ưu Tiên</span>
                            Của Chúng Tôi
                        </h2>
                    </div>

                    <p class="text-sm text-slate-500 leading-8">
                        Elite Clinic hướng tới trải nghiệm chăm sóc sức khỏe chuyên nghiệp, thuận tiện và tận tâm. Hệ thống giúp người bệnh dễ dàng tìm hiểu chuyên khoa, lựa chọn bác sĩ và chủ động đăng ký lịch khám.
                    </p>

                    <div class="space-y-5">

                        <div class="flex gap-4">
                            <div class="w-12 h-12 shrink-0 rounded-2xl bg-teal-50 text-teal-700 flex items-center justify-center text-lg">
                                <i class="fa-solid fa-user-doctor"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-sm text-slate-900">Đội ngũ bác sĩ chuyên môn</h3>
                                <p class="text-xs text-slate-500 mt-1 leading-6">
                                    Cung cấp thông tin bác sĩ và chuyên khoa để bệnh nhân dễ dàng lựa chọn.
                                </p>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <div class="w-12 h-12 shrink-0 rounded-2xl bg-cyan-50 text-cyan-700 flex items-center justify-center text-lg">
                                <i class="fa-solid fa-calendar-check"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-sm text-slate-900">Đăng ký khám thuận tiện</h3>
                                <p class="text-xs text-slate-500 mt-1 leading-6">
                                    Chủ động lựa chọn dịch vụ và thời gian khám phù hợp.
                                </p>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <div class="w-12 h-12 shrink-0 rounded-2xl bg-blue-50 text-blue-700 flex items-center justify-center text-lg">
                                <i class="fa-solid fa-shield-heart"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-sm text-slate-900">Theo dõi thông tin khám</h3>
                                <p class="text-xs text-slate-500 mt-1 leading-6">
                                    Hỗ trợ tra cứu và theo dõi thông tin lịch khám trên hệ thống.
                                </p>
                            </div>
                        </div>

                    </div>

                    <a
                        href="index.php?act=chon-loai-lich"
                        class="inline-flex items-center gap-3 clinic-gradient text-white font-bold px-6 py-3.5 rounded-xl shadow-lg hover:-translate-y-1 transition text-xs"
                    >
                        Đăng ký khám ngay
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>

                </div>

                <div class="relative">

                    <div class="rounded-[2rem] overflow-hidden shadow-xl h-[320px] md:h-[440px]">
                        <img
                            src="https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?auto=format&fit=crop&w=1200&q=85"
                            alt="Bác sĩ tư vấn bệnh nhân"
                            class="w-full h-full object-cover"
                            loading="lazy"
                        >
                    </div>

                    <div class="absolute -bottom-5 -left-3 md:-left-7 bg-white rounded-2xl shadow-xl border border-slate-100 p-5 max-w-[230px]">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-xl clinic-gradient text-white flex items-center justify-center">
                                <i class="fa-solid fa-heart-pulse text-lg"></i>
                            </div>
                            <div>
                                <p class="font-extrabold text-slate-900 text-sm">Elite Clinic</p>
                                <p class="text-[10px] text-slate-500 mt-1">Đồng hành vì sức khỏe</p>
                            </div>
                        </div>
                    </div>

                    <div class="absolute -top-4 -right-3 w-24 h-24 rounded-full bg-teal-100/70 -z-10"></div>

                </div>

            </section>

            <!-- =================================================
                 3. CƠ SỞ VẬT CHẤT
            ================================================== -->
            <section id="co-so-vat-chat" class="clinic-section space-y-8">

                <div class="text-center max-w-2xl mx-auto">
                    <span class="text-teal-700 font-extrabold text-[10px] uppercase clinic-label">
                        Không gian khám chữa bệnh
                    </span>

                    <h2 class="text-2xl md:text-3xl font-extrabold text-slate-900 mt-3">
                        Cơ Sở Vật Chất Hiện Đại
                    </h2>

                    <p class="text-xs md:text-sm text-slate-500 mt-3 leading-7">
                        Không gian khám chữa bệnh được thiết kế hướng tới sự tiện nghi, sạch sẽ và thoải mái.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

                    <div class="clinic-card relative h-64 md:h-80 rounded-3xl overflow-hidden shadow-sm group">
                        <img
                            src="https://images.unsplash.com/photo-1516549655169-df83a0774514?auto=format&fit=crop&w=900&q=85"
                            alt="Không gian bệnh viện"
                            class="clinic-image w-full h-full object-cover"
                            loading="lazy"
                        >
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/85 via-transparent to-transparent"></div>
                        <div class="absolute bottom-0 left-0 p-6 text-white">
                            <div class="w-10 h-10 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center mb-3">
                                <i class="fa-solid fa-hospital"></i>
                            </div>
                            <h3 class="font-extrabold text-lg">Không gian bệnh viện</h3>
                            <p class="text-xs text-white/80 mt-2">Không gian khám chữa bệnh tiện nghi</p>
                        </div>
                    </div>

                    <div class="clinic-card relative h-64 md:h-80 rounded-3xl overflow-hidden shadow-sm group">
                        <img
                            src="https://images.unsplash.com/photo-1584982751601-97dcc096659c?auto=format&fit=crop&w=900&q=85"
                            alt="Thiết bị y tế"
                            class="clinic-image w-full h-full object-cover"
                            loading="lazy"
                        >
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/85 via-transparent to-transparent"></div>
                        <div class="absolute bottom-0 left-0 p-6 text-white">
                            <div class="w-10 h-10 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center mb-3">
                                <i class="fa-solid fa-microscope"></i>
                            </div>
                            <h3 class="font-extrabold text-lg">Trang thiết bị y tế</h3>
                            <p class="text-xs text-white/80 mt-2">Hỗ trợ quá trình thăm khám</p>
                        </div>
                    </div>

                    <div class="clinic-card relative h-64 md:h-80 rounded-3xl overflow-hidden shadow-sm group">
                        <img
                            src="https://images.unsplash.com/photo-1551076805-e1869033e561?auto=format&fit=crop&w=900&q=85"
                            alt="Bác sĩ thăm khám"
                            class="clinic-image w-full h-full object-cover"
                            loading="lazy"
                        >
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/85 via-transparent to-transparent"></div>
                        <div class="absolute bottom-0 left-0 p-6 text-white">
                            <div class="w-10 h-10 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center mb-3">
                                <i class="fa-solid fa-user-nurse"></i>
                            </div>
                            <h3 class="font-extrabold text-lg">Chăm sóc bệnh nhân</h3>
                            <p class="text-xs text-white/80 mt-2">Tận tâm trong từng quy trình khám</p>
                        </div>
                    </div>

                </div>

            </section>

            <!-- =================================================
                 4. CHUYÊN KHOA - HÌNH ẢNH DỊCH VỤ LỚN
            ================================================== -->
            <section id="chuyen-khoa" class="clinic-section space-y-8">

                <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">

                    <div>
                        <span class="inline-flex items-center gap-2 text-teal-700 font-extrabold text-[10px] uppercase clinic-label mb-3">
                            <span class="w-8 h-[2px] bg-teal-600 rounded-full"></span>
                            Danh mục dịch vụ
                        </span>

                        <h2 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight">
                            Chuyên Khoa Y Tế
                        </h2>

                        <p class="text-sm text-slate-500 mt-3 leading-7">
                            Khám phá các chuyên khoa và lựa chọn dịch vụ chăm sóc sức khỏe phù hợp.
                        </p>
                    </div>

                    <div class="flex items-center gap-2 text-xs font-semibold text-slate-500">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        Chăm sóc sức khỏe toàn diện
                    </div>

                </div>

                <div class="hs" data-hs>
                    <button type="button" class="hs-btn hs-prev" data-hs-prev aria-label="Xem mục trước"><i class="fa-solid fa-chevron-left"></i></button>
                    <button type="button" class="hs-btn hs-next" data-hs-next aria-label="Xem mục tiếp theo"><i class="fa-solid fa-chevron-right"></i></button>
                <div class="hs-track" data-hs-track>

                    <?php if (!empty($danhSachChuyenKhoa)): ?>

                        <?php foreach ($danhSachChuyenKhoa as $ck): ?>

                            <a
                                href="index.php?act=chi-tiet-chuyen-khoa&id=<?= urlencode($ck['ma_chuyen_khoa']); ?>"
                                class="clinic-card group bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-sm flex flex-col"
                            >

                                <!-- ẢNH CHUYÊN KHOA LỚN -->
                                <div class="relative h-[220px] md:h-[240px] overflow-hidden bg-gradient-to-br from-teal-50 to-cyan-100">

                                    <?php if (!empty($ck['hinh_anh'])): ?>

                                        <img
                                            src="<?= e($ck['hinh_anh']); ?>"
                                            alt="<?= e($ck['ten_chuyen_khoa'] ?? 'Chuyên khoa'); ?>"
                                            class="clinic-image w-full h-full object-cover"
                                            loading="lazy"
                                        >

                                    <?php else: ?>

                                        <div class="w-full h-full flex items-center justify-center">
                                            <div class="w-24 h-24 rounded-3xl bg-white/80 flex items-center justify-center shadow-sm">
                                                <i class="fa-solid fa-stethoscope text-5xl text-teal-600"></i>
                                            </div>
                                        </div>

                                    <?php endif; ?>

                                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/40 via-transparent to-transparent"></div>

                                    <span class="absolute top-4 left-4 text-[10px] font-extrabold uppercase tracking-wider text-teal-800 bg-white/95 backdrop-blur-sm px-4 py-2 rounded-full shadow-sm">
                                        <i class="fa-solid fa-heart-pulse mr-1"></i>
                                        Chuyên khoa
                                    </span>

                                    <div class="absolute bottom-4 right-4 w-11 h-11 rounded-full bg-white text-teal-700 flex items-center justify-center shadow-lg group-hover:bg-teal-600 group-hover:text-white transition">
                                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                    </div>

                                </div>

                                <!-- NỘI DUNG CHUYÊN KHOA -->
                                <div class="p-6 flex flex-col flex-1">

                                    <h3 class="font-extrabold text-slate-900 text-lg group-hover:text-teal-700 transition-colors">
                                        <?= e($ck['ten_chuyen_khoa'] ?? ''); ?>
                                    </h3>

                                    <p class="text-sm text-slate-500 leading-7 mt-3 line-clamp-3 flex-1">
                                        <?= e($ck['mo_ta'] ?? 'Dịch vụ chăm sóc sức khỏe và thăm khám chuyên sâu.'); ?>
                                    </p>

                                    <div class="mt-5 pt-4 border-t border-slate-100 flex items-center justify-between">

                                        <span class="text-xs font-bold text-teal-700">
                                            Khám phá chuyên khoa
                                        </span>

                                        <span class="text-teal-600 text-sm group-hover:translate-x-1 transition-transform">
                                            <i class="fa-solid fa-arrow-right"></i>
                                        </span>

                                    </div>

                                </div>

                            </a>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <div class="col-span-full bg-white border border-slate-200 rounded-3xl p-12 text-center">
                            <div class="w-16 h-16 mx-auto rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center text-2xl mb-4">
                                <i class="fa-solid fa-stethoscope"></i>
                            </div>
                            <h3 class="font-bold text-slate-700">Chưa có chuyên khoa</h3>
                            <p class="text-xs text-slate-400 mt-2">
                                Thông tin chuyên khoa sẽ được cập nhật trong thời gian tới.
                            </p>
                        </div>

                    <?php endif; ?>

                </div>
                </div>
                <div class="hs-dots" data-hs-dots aria-label="Chọn vị trí hiển thị"></div>

            </section>

            <!-- =================================================
                 5. ĐỘI NGŨ BÁC SĨ
            ================================================== -->
            <section id="bac-si" class="clinic-section space-y-8">

                <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">

                    <div>
                        <span class="text-teal-700 font-extrabold text-[10px] uppercase clinic-label">
                            Đội ngũ chuyên môn
                        </span>

                        <h2 class="text-2xl md:text-3xl font-extrabold text-slate-900 mt-3">
                            Bác Sĩ Chuyên Sâu
                        </h2>

                        <p class="text-sm text-slate-500 mt-3">
                            Tìm hiểu thông tin và hồ sơ đội ngũ bác sĩ tại Elite Clinic.
                        </p>
                    </div>

                    <div class="hidden md:flex items-center gap-2 text-xs font-semibold text-teal-700 bg-teal-50 px-4 py-3 rounded-xl">
                        <i class="fa-solid fa-user-doctor"></i>
                        Đội ngũ chuyên môn
                    </div>

                </div>

                <div class="hs" data-hs>
                    <button type="button" class="hs-btn hs-prev" data-hs-prev aria-label="Xem mục trước"><i class="fa-solid fa-chevron-left"></i></button>
                    <button type="button" class="hs-btn hs-next" data-hs-next aria-label="Xem mục tiếp theo"><i class="fa-solid fa-chevron-right"></i></button>
                <div class="hs-track" data-hs-track>

                    <?php if (!empty($danhSachBacSi)): ?>

                        <?php foreach ($danhSachBacSi as $bs): ?>

                            <a
                                href="index.php?act=chi-tiet-bac-si&id=<?= urlencode($bs['ma_bac_si']); ?>"
                                class="clinic-card bg-white rounded-3xl border border-slate-200/80 p-6 text-center shadow-sm block"
                            >

                                <div class="relative w-28 h-28 mx-auto mb-5">

                                    <?php if (!empty($bs['hinh_anh'])): ?>

                                        <img
                                            src="<?= e($bs['hinh_anh']); ?>"
                                            alt="<?= e($bs['ten_bac_si'] ?? 'Bác sĩ'); ?>"
                                            class="w-full h-full object-cover rounded-full border-4 border-teal-50 shadow-md"
                                            loading="lazy"
                                        >

                                    <?php else: ?>

                                        <img
                                            src="https://images.unsplash.com/photo-1537368910025-700350fe46c7?auto=format&fit=crop&q=80&w=300"
                                            alt="Bác sĩ"
                                            class="w-full h-full object-cover rounded-full border-4 border-teal-50 shadow-md"
                                            loading="lazy"
                                        >

                                    <?php endif; ?>

                                    <span class="absolute bottom-1 right-1 w-5 h-5 bg-emerald-500 border-4 border-white rounded-full"></span>

                                </div>

                                <h3 class="font-extrabold text-slate-900 text-base">
                                    <?= e($bs['ten_bac_si'] ?? ''); ?>
                                </h3>

                                <p class="text-xs text-teal-700 font-bold mt-2">
                                    <?= e($bs['ten_chuyen_khoa'] ?? 'Bác sĩ chuyên khoa'); ?>
                                </p>

                                <div class="my-5 border-t border-slate-100"></div>

                                <p class="text-xs text-slate-500">
                                    <i class="fa-solid fa-graduation-cap text-teal-600 mr-1"></i>
                                    Bằng cấp:
                                    <?= e($bs['bang_cap'] ?? 'Chuyên khoa'); ?>
                                </p>

                                <div class="mt-5 inline-flex items-center gap-2 text-xs font-bold text-teal-700">
                                    Xem hồ sơ bác sĩ
                                    <i class="fa-solid fa-arrow-right"></i>
                                </div>

                            </a>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <div class="col-span-full bg-white rounded-3xl border border-slate-200 p-10 text-center">
                            <i class="fa-solid fa-user-doctor text-3xl text-slate-300"></i>
                            <p class="text-sm text-slate-500 mt-4">
                                Hiện chưa có thông tin bác sĩ.
                            </p>
                        </div>

                    <?php endif; ?>

                </div>
                </div>
                <div class="hs-dots" data-hs-dots aria-label="Chọn vị trí hiển thị"></div>

            </section>

            <!-- =================================================
                 6. GÓI KHÁM
            ================================================== -->
            <section id="goi-kham" class="clinic-section space-y-8">

                <div>
                    <span class="text-teal-700 font-extrabold text-[10px] uppercase clinic-label">
                        Dịch vụ chăm sóc
                    </span>

                    <h2 class="text-2xl md:text-3xl font-extrabold text-slate-900 mt-3">
                        Gói Khám Sức Khỏe
                    </h2>

                    <p class="text-sm text-slate-500 mt-3 leading-7">
                        Lựa chọn gói khám phù hợp với nhu cầu chăm sóc và theo dõi sức khỏe.
                    </p>
                </div>

                <div class="hs" data-hs>
                    <button type="button" class="hs-btn hs-prev" data-hs-prev aria-label="Xem mục trước"><i class="fa-solid fa-chevron-left"></i></button>
                    <button type="button" class="hs-btn hs-next" data-hs-next aria-label="Xem mục tiếp theo"><i class="fa-solid fa-chevron-right"></i></button>
                <div class="hs-track" data-hs-track>

                    <?php if (!empty($danhSachGoiKham)): ?>

                        <?php foreach ($danhSachGoiKham as $gk):

                            $tenGoi = $gk['ten_goi_kham']
                                ?? $gk['ten_goi']
                                ?? $gk['tieu_de']
                                ?? 'Gói khám dịch vụ';

                        ?>

                            <div class="clinic-card bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-sm flex flex-col">

                                <div class="clinic-gradient p-6 relative overflow-hidden">

                                    <div class="absolute -right-7 -top-7 w-32 h-32 rounded-full border border-white/10"></div>
                                    <div class="absolute -right-2 -top-2 w-20 h-20 rounded-full border border-white/10"></div>

                                    <div class="relative z-10 flex items-center justify-between">
                                        <div class="w-12 h-12 bg-white/15 rounded-2xl flex items-center justify-center text-white text-xl">
                                            <i class="fa-solid fa-notes-medical"></i>
                                        </div>

                                        <span class="bg-white/15 border border-white/20 text-white text-[9px] font-extrabold uppercase px-3 py-2 rounded-full">
                                            Trọn gói
                                        </span>
                                    </div>

                                    <h3 class="relative z-10 text-white text-lg font-extrabold mt-5">
                                        <?= e($tenGoi); ?>
                                    </h3>

                                </div>

                                <div class="p-6 flex flex-col flex-1">

                                    <p class="text-xs text-slate-500 leading-7 line-clamp-3 flex-1">
                                        <?= e($gk['mo_ta'] ?? 'Gói khám sức khỏe với các dịch vụ được thiết kế phù hợp với nhu cầu của bạn.'); ?>
                                    </p>

                                    <div class="border-t border-slate-100 mt-5 pt-5">

                                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">
                                            Chi phí dịch vụ
                                        </p>

                                        <div class="text-xl font-extrabold text-teal-700 mt-2">
                                            <?= number_format((float)($gk['gia'] ?? 0), 0, ',', '.'); ?>
                                            <span class="text-xs">đ</span>
                                        </div>

                                        <div class="grid grid-cols-2 gap-3 mt-5">

                                            <a
                                                href="index.php?act=chi-tiet-goi-kham&id=<?= urlencode($gk['ma_goi_kham']); ?>"
                                                class="text-center bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs py-3 rounded-xl transition"
                                            >
                                                Chi tiết
                                            </a>

                                            <a
                                                href="index.php?act=dat-goi-kham&id=<?= urlencode($gk['ma_goi_kham']); ?>"
                                                class="text-center clinic-gradient text-white font-bold text-xs py-3 rounded-xl shadow-md hover:shadow-lg transition"
                                            >
                                                Đặt gói
                                            </a>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <div class="col-span-full bg-white rounded-3xl border border-slate-200 p-10 text-center">
                            <i class="fa-solid fa-notes-medical text-3xl text-slate-300"></i>
                            <p class="text-sm text-slate-500 mt-4">
                                Hiện chưa có gói khám nào.
                            </p>
                        </div>

                    <?php endif; ?>

                </div>
                </div>
                <div class="hs-dots" data-hs-dots aria-label="Chọn vị trí hiển thị"></div>

            </section>

            <!-- =================================================
                 7. ĐÁNH GIÁ BỆNH NHÂN
            ================================================== -->
            <section id="danh-gia" class="clinic-section relative rounded-[2rem] clinic-soft-bg border border-teal-100/80 p-7 md:p-12 overflow-hidden">

                <div class="absolute -right-20 -top-20 w-64 h-64 rounded-full bg-teal-100/50"></div>
                <div class="absolute -left-16 -bottom-20 w-56 h-56 rounded-full bg-cyan-100/40"></div>

                <div class="relative z-10 space-y-8">

                    <div class="text-center max-w-xl mx-auto">
                        <span class="text-teal-700 font-extrabold text-[10px] uppercase clinic-label">
                            Phản hồi khách hàng
                        </span>

                        <h2 class="text-2xl md:text-3xl font-extrabold text-slate-900 mt-3">
                            Bệnh Nhân Nói Gì Về Chúng Tôi?
                        </h2>

                        <p class="text-xs text-slate-500 mt-3 leading-7">
                            Những chia sẻ về trải nghiệm dịch vụ tại Elite Clinic.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                        <div class="bg-white/90 backdrop-blur-sm p-7 rounded-3xl border border-white shadow-sm">
                            <div class="flex text-amber-400 text-xs gap-1 mb-5">
                                <?php for ($i = 0; $i < 5; $i++): ?>
                                    <i class="fa-solid fa-star"></i>
                                <?php endfor; ?>
                            </div>

                            <i class="fa-solid fa-quote-left text-3xl text-teal-100"></i>

                            <p class="text-sm text-slate-600 leading-7 mt-3">
                                "Dịch vụ khám rất nhanh chóng, bác sĩ tư vấn tận tình và nhẹ nhàng. Tôi hoàn toàn hài lòng."
                            </p>

                            <div class="flex items-center gap-3 mt-6 pt-5 border-t border-slate-100">
                                <div class="w-11 h-11 rounded-full bg-teal-50 text-teal-700 flex items-center justify-center font-bold">
                                    A
                                </div>
                                <div>
                                    <p class="font-extrabold text-slate-900 text-xs">Nguyễn Văn An</p>
                                    <p class="text-[10px] text-slate-400 mt-1">Khách hàng</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white/90 backdrop-blur-sm p-7 rounded-3xl border border-white shadow-sm">
                            <div class="flex text-amber-400 text-xs gap-1 mb-5">
                                <?php for ($i = 0; $i < 5; $i++): ?>
                                    <i class="fa-solid fa-star"></i>
                                <?php endfor; ?>
                            </div>

                            <i class="fa-solid fa-quote-left text-3xl text-teal-100"></i>

                            <p class="text-sm text-slate-600 leading-7 mt-3">
                                "Không phải chờ đợi lâu, hệ thống tra cứu thông tin sau khi khám xong rất tiện lợi."
                            </p>

                            <div class="flex items-center gap-3 mt-6 pt-5 border-t border-slate-100">
                                <div class="w-11 h-11 rounded-full bg-cyan-50 text-cyan-700 flex items-center justify-center font-bold">
                                    M
                                </div>
                                <div>
                                    <p class="font-extrabold text-slate-900 text-xs">Trần Thị Mai</p>
                                    <p class="text-[10px] text-slate-400 mt-1">Khách hàng</p>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>

            </section>

            <!-- =================================================
                 8. BẢN ĐỒ
            ================================================== -->
            <section id="ban-do" class="clinic-section space-y-7">

                <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">

                    <div>
                        <span class="text-teal-700 font-extrabold text-[10px] uppercase clinic-label">
                            Vị trí của chúng tôi
                        </span>

                        <h2 class="text-2xl md:text-3xl font-extrabold text-slate-900 mt-3">
                            Tìm Đường Đến Elite Clinic
                        </h2>

                        <p class="text-sm text-slate-500 mt-3">
                            Xem vị trí trên bản đồ để thuận tiện di chuyển.
                        </p>
                    </div>

                    <div class="inline-flex items-center gap-2 text-xs font-bold text-teal-700 bg-teal-50 px-4 py-3 rounded-xl">
                        <i class="fa-solid fa-location-dot"></i>
                        Bản đồ chỉ đường
                    </div>

                </div>

                <div class="rounded-3xl overflow-hidden border border-slate-200 shadow-lg bg-slate-100 h-[320px] md:h-[430px]">

                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3723.863855881425!2d105.78165707503175!3d21.034120380614533!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3135ab868b505c9f%3A0x80852e92c235583b!2sVHLGsOG7nW5nIMSQ4bqhaSBo4buNYyBUaMawxqFuZyBt4bqhaQ!5e0!3m2!1svi!2s!4v1700000000000!5m2!1svi!2s"
                        width="100%"
                        height="100%"
                        style="border:0;"
                        allowfullscreen
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>

                </div>

            </section>

            <!-- =================================================
                 9. CTA CUỐI TRANG
            ================================================== -->
            <section class="relative clinic-gradient rounded-[2rem] p-8 md:p-12 overflow-hidden shadow-xl">

                <div class="absolute -right-16 -top-24 w-72 h-72 border border-white/10 rounded-full"></div>
                <div class="absolute -right-4 -top-12 w-52 h-52 border border-white/10 rounded-full"></div>

                <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-7">

                    <div class="max-w-2xl">
                        <span class="text-teal-200 font-bold text-[10px] uppercase clinic-label">
                            Bắt đầu chăm sóc sức khỏe
                        </span>

                        <h2 class="text-2xl md:text-3xl font-extrabold text-white mt-3 leading-tight">
                            Chủ Động Bảo Vệ Sức Khỏe Của Bạn Ngay Hôm Nay
                        </h2>

                        <p class="text-xs md:text-sm text-teal-100/80 mt-4 leading-7">
                            Lựa chọn chuyên khoa, tìm bác sĩ phù hợp và đăng ký lịch khám ngay trên Elite Clinic.
                        </p>
                    </div>

                    <a
                        href="index.php?act=chon-loai-lich"
                        class="shrink-0 inline-flex items-center justify-center gap-3 bg-white text-teal-800 font-extrabold px-7 py-4 rounded-xl shadow-lg hover:bg-teal-50 hover:-translate-y-1 transition text-xs md:text-sm"
                    >
                        <i class="fa-solid fa-calendar-check"></i>
                        Đặt lịch khám ngay
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>

                </div>

            </section>

        </div>
    </div>
</main>

<?php
require_once 'views/footer.php';
?>

<script>
(function () {
    document.querySelectorAll('[data-hs]').forEach(function (box) {
        var track = box.querySelector('[data-hs-track]');
        var prev = box.querySelector('[data-hs-prev]');
        var next = box.querySelector('[data-hs-next]');
        if (!track || !prev || !next) return;

        function step() {
            var first = track.firstElementChild;
            var gap = parseFloat(getComputedStyle(track).columnGap) || 0;
            return first ? first.getBoundingClientRect().width + gap : track.clientWidth * 0.9;
        }

        var dotsBox = box.nextElementSibling;
        if (dotsBox && !dotsBox.hasAttribute('data-hs-dots')) dotsBox = null;
        var dots = [];

        function maxScroll() {
            return Math.max(0, track.scrollWidth - track.clientWidth);
        }

        function pageCount() {
            var max = maxScroll();
            if (max <= 2) return 1;
            return Math.ceil((max - 2) / step()) + 1;
        }

        function buildDots() {
            if (!dotsBox) return;
            var n = pageCount();
            if (dots.length === n) return;
            dotsBox.innerHTML = '';
            dots = [];
            dotsBox.style.display = n > 1 ? '' : 'none';
            for (var i = 0; i < n; i++) {
                (function (idx) {
                    var d = document.createElement('button');
                    d.type = 'button';
                    d.className = 'hs-dot';
                    d.setAttribute('aria-label', 'Đến vị trí ' + (idx + 1));
                    d.addEventListener('click', function () {
                        var target = idx === n - 1 ? maxScroll() : idx * step();
                        track.scrollTo({ left: target, behavior: 'smooth' });
                    });
                    dotsBox.appendChild(d);
                    dots.push(d);
                })(i);
            }
        }

        function update() {
            var max = maxScroll() - 2;
            prev.disabled = track.scrollLeft <= 2;
            next.disabled = track.scrollLeft >= max;
            buildDots();
            if (dots.length) {
                var idx = track.scrollLeft >= max
                    ? dots.length - 1
                    : Math.min(dots.length - 1, Math.round(track.scrollLeft / step()));
                dots.forEach(function (d, i) { d.classList.toggle('is-active', i === idx); });
            }
        }

        prev.addEventListener('click', function () { track.scrollBy({ left: -step(), behavior: 'smooth' }); });
        next.addEventListener('click', function () { track.scrollBy({ left: step(), behavior: 'smooth' }); });
        track.addEventListener('scroll', update, { passive: true });
        window.addEventListener('resize', update);
        window.addEventListener('load', update);
        update();

        // Kéo chuột để lướt trên máy tính
        var down = false, moved = false, startX = 0, startLeft = 0;
        track.addEventListener('mousedown', function (e) {
            down = true; moved = false; startX = e.pageX; startLeft = track.scrollLeft;
        });
        window.addEventListener('mouseup', function () {
            if (!down) return;
            down = false;
            track.style.scrollSnapType = '';
            track.style.scrollBehavior = '';
        });
        window.addEventListener('mousemove', function (e) {
            if (!down) return;
            var dx = e.pageX - startX;
            if (Math.abs(dx) > 5) {
                moved = true;
                track.style.scrollSnapType = 'none';
                track.style.scrollBehavior = 'auto';
                track.scrollLeft = startLeft - dx;
            }
        });
        // Chặn click nhầm vào thẻ sau khi vừa kéo
        track.addEventListener('click', function (e) {
            if (moved) { e.preventDefault(); e.stopPropagation(); moved = false; }
        }, true);
        track.addEventListener('dragstart', function (e) { e.preventDefault(); });
    });
})();
</script>
