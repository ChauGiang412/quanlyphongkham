<?php 
require_once 'views/header.php'; 
?>

<main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-8">
    <!-- Tiêu đề -->
    <div class="text-center space-y-2">
        <span class="text-brand-600 font-bold text-xs uppercase tracking-wider block">Hệ Thống Y Tế Elite Clinic</span>
        <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900">Lựa Chọn Hình Thức Đặt Lịch Khám</h1>
        <p class="text-xs text-slate-500 max-w-lg mx-auto">Vui lòng chọn loại dịch vụ bạn muốn đăng ký để tiếp tục hệ thống chọn chuyên khoa, bác sĩ và giờ khám.</p>
    </div>

    <!-- 2 Thẻ lựa chọn hình thức -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-4">
        
        <!-- 1. Đặt lịch chuyên khoa -->
        <div class="bg-white p-8 rounded-3xl border border-brand-100 shadow-sm hover:shadow-md transition flex flex-col justify-between space-y-6 group">
            <div class="space-y-4">
                <div class="w-14 h-14 rounded-2xl bg-brand-600 text-white flex items-center justify-center text-xl shadow-md group-hover:scale-105 transition">
                    <i class="fa-solid fa-stethoscope"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-900">Đặt Lịch Chuyên Khoa</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Khám bệnh theo chuyên khoa, lựa chọn trực tiếp bác sĩ chuyên sâu và triệu chứng riêng theo nhu cầu của bạn.
                </p>
            </div>
            <div>
                <a href="index.php?act=dat-lich-chuyen-khoa" class="inline-flex items-center space-x-2 text-xs font-bold text-brand-600 hover:text-brand-700 transition group-hover:translate-x-1 duration-200">
                    <span>Chọn hình thức này</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </div>

        <!-- 2. Đặt lịch gói khám (Đã trỏ đúng act=dat-goi-kham) -->
        <div class="bg-white p-8 rounded-3xl border border-brand-100 shadow-sm hover:shadow-md transition flex flex-col justify-between space-y-6 group">
            <div class="space-y-4">
                <div class="w-14 h-14 rounded-2xl bg-brand-600 text-white flex items-center justify-center text-xl shadow-md group-hover:scale-105 transition">
                    <i class="fa-solid fa-notes-medical"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-900">Đặt Lịch Gói Khám</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Đăng ký các gói tầm soát sức khỏe tổng quát, tim mạch, nha khoa trọn gói chuẩn 5 sao nhanh chóng.
                </p>
            </div>
            <div>
                <a href="index.php?act=dat-goi-kham" class="inline-flex items-center space-x-2 text-xs font-bold text-brand-600 hover:text-brand-700 transition group-hover:translate-x-1 duration-200">
                    <span>Chọn hình thức này</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </div>

    </div>

    <!-- Nút quay lại trang chủ -->
    <div class="text-center pt-6">
        <a href="index.php?act=home" class="inline-flex items-center space-x-2 text-xs font-bold text-slate-600 hover:text-brand-600 transition">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Quay lại trang chủ</span>
        </a>
    </div>
</main>

<?php 
require_once 'views/footer.php'; 
?>