<?php 
require_once 'views/header.php'; 
?>

<main class="max-w-md mx-auto px-4 py-10">
    <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-sm space-y-6">
        <div class="text-center space-y-2">
            <span class="bg-brand-50 text-brand-600 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">Elite Clinic</span>
            <h1 class="text-2xl font-extrabold text-slate-900">Tạo Tài Khoản Mới</h1>
            <p class="text-xs text-slate-500">Đăng ký tài khoản để dễ dàng quản lý lịch khám và hồ sơ y tế.</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="bg-red-50 text-red-600 text-xs p-4 rounded-2xl border border-red-100 font-semibold text-center">
                <?= htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="bg-emerald-50 text-emerald-600 text-xs p-4 rounded-2xl border border-emerald-100 font-semibold text-center space-y-2">
                <p><?= htmlspecialchars($success); ?></p>
                <a href="index.php?act=login" class="inline-block bg-brand-600 text-white px-4 py-2 rounded-xl font-bold uppercase">Đi đến Đăng nhập</a>
            </div>
        <?php else: ?>
            <form action="index.php?act=register" method="POST" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Họ Và Tên <span class="text-red-500">*</span></label>
                    <input type="text" name="ho_ten" required class="w-full p-3.5 border rounded-2xl text-xs bg-slate-50 font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-600" placeholder="Nguyễn Văn A">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Địa Chỉ Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" required class="w-full p-3.5 border rounded-2xl text-xs bg-slate-50 font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-600" placeholder="name@example.com">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Số Điện Thoại <span class="text-red-500">*</span></label>
                    <input type="text" name="so_dien_thoai" required class="w-full p-3.5 border rounded-2xl text-xs bg-slate-50 font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-600" placeholder="0912345678">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Mật Khẩu <span class="text-red-500">*</span></label>
                    <input type="password" name="mat_khau" required class="w-full p-3.5 border rounded-2xl text-xs bg-slate-50 font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-600" placeholder="••••••••">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nhập Lại Mật Khẩu <span class="text-red-500">*</span></label>
                    <input type="password" name="nhap_lai_mat_khau" required class="w-full p-3.5 border rounded-2xl text-xs bg-slate-50 font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-600" placeholder="••••••••">
                </div>

                <button type="submit" class="w-full bg-brand-600 hover:bg-brand-700 text-white font-bold py-3.5 rounded-2xl text-xs shadow-md uppercase tracking-wider transition">
                    Đăng Ký Tài Khoản
                </button>
            </form>
        <?php endif; ?>

        <div class="text-center pt-2 border-t border-slate-100">
            <p class="text-xs text-slate-500">
                Đã có tài khoản? 
                <a href="index.php?act=login" class="font-bold text-brand-600 hover:underline">Đăng nhập ngay</a>
            </p>
        </div>
    </div>
</main>

<?php 
require_once 'views/footer.php'; 
?>