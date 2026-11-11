<div class="space-y-8">
    <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div class="space-y-1">
            <span class="text-brand-600 font-bold text-xs uppercase tracking-wider">Hệ Thống Y Tế Elite Clinic</span>
            <h1 class="text-2xl font-extrabold text-slate-950">Bảng Điều Hướng Tổng Quan Quản Trị</h1>
            <p class="text-xs text-slate-500">Xin chào quản trị viên, chúc bạn một ngày làm việc hiệu quả.</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="index.php?act=admin-quan-ly-lich" class="bg-brand-600 hover:bg-brand-700 text-white font-bold px-5 py-3 rounded-2xl text-xs shadow-md transition flex items-center space-x-2">
                <i class="fa-solid fa-calendar-check"></i>
                <span>Quản Lý Lịch Khám</span>
            </a>
        </div>
    </div>

    <!-- Thẻ Thống Kê Tổng Quan -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm space-y-3">
            <div class="w-12 h-12 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center text-lg font-bold">
                <i class="fa-solid fa-calendar-days"></i>
            </div>
            <div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Tổng Lịch Hẹn</span>
                <div class="text-2xl font-black text-slate-900 mt-1"><?= $tongLichKham ?? 0; ?></div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm space-y-3">
            <div class="w-12 h-12 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center text-lg font-bold">
                <i class="fa-solid fa-user-doctor"></i>
            </div>
            <div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Tổng Bác Sĩ</span>
                <div class="text-2xl font-black text-brand-600 mt-1"><?= $tongBacSi ?? 0; ?></div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm space-y-3">
            <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-700 flex items-center justify-center text-lg font-bold">
                <i class="fa-solid fa-hospital"></i>
            </div>
            <div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Chuyên Khoa Y Tế</span>
                <div class="text-2xl font-black text-slate-900 mt-1"><?= $tongChuyenKhoa ?? 0; ?></div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm space-y-3">
            <div class="w-12 h-12 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center text-lg font-bold">
                <i class="fa-solid fa-notes-medical"></i>
            </div>
            <div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Gói Khám Sức Khỏe</span>
                <div class="text-2xl font-black text-brand-600 mt-1"><?= $tongGoiKham ?? 0; ?></div>
            </div>
        </div>
    </div>
</div>