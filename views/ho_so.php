<?php require_once 'views/header.php'; ?>

<main class="py-12 bg-slate-50/50 min-h-screen">
    <div class="max-w-xl mx-auto px-4 sm:px-6">
        <div class="text-center mb-8">
            <span class="text-brand-600 font-semibold text-xs uppercase tracking-wider block mb-1">Hồ Sơ Tài Khoản</span>
            <h1 class="text-2xl font-bold text-slate-900">Thông Tin Bệnh Nhân</h1>
        </div>

        <div class="bg-white rounded-3xl shadow-xl border border-slate-100 p-8">
            <form action="index.php?act=cap-nhat-ho-so" method="POST" class="space-y-6">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Họ và Tên</label>
                    <input type="text" name="ho_ten" value="<?= htmlspecialchars($userInfo['ho_ten'] ?? ''); ?>" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-xs text-slate-700 focus:outline-none focus:border-brand-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Số Điện Thoại</label>
                    <input type="text" name="so_dien_thoai" value="<?= htmlspecialchars($userInfo['so_dien_thoai'] ?? ''); ?>" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-xs text-slate-700 focus:outline-none focus:border-brand-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Địa Chỉ Email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($userInfo['email'] ?? ''); ?>" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-xs text-slate-700 focus:outline-none focus:border-brand-500">
                </div>

                <div class="pt-4 flex items-center justify-end space-x-4">
                    <a href="index.php?act=lich-su" class="px-6 py-3 rounded-2xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition">Quay lại lịch sử</a>
                    <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs px-8 py-3.5 rounded-2xl shadow-lg transition">
                        Lưu Thay Đổi
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>

<?php require_once 'views/footer.php'; ?>