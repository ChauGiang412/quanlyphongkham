<?php 
require_once 'views/header.php'; 
?>

<main class="max-w-md mx-auto px-4 py-12">
    <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-sm space-y-6">
        <div class="text-center space-y-2">
            <span class="bg-brand-50 text-brand-600 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">Elite Clinic</span>
            <h1 class="text-2xl font-extrabold text-slate-900">Đăng Nhập Hệ Thống</h1>
            <p class="text-xs text-slate-500">Chào mừng bạn quay trở lại với hệ thống y tế chuẩn 5 sao.</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="bg-red-50 text-red-600 text-xs p-4 rounded-2xl border border-red-100 font-semibold text-center">
                <?= htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form action="index.php?act=login" method="POST" class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Địa Chỉ Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" required class="w-full p-3.5 border rounded-2xl text-xs bg-slate-50 font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-600" placeholder="name@example.com">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Mật Khẩu <span class="text-red-500">*</span></label>
                <input type="password" name="mat_khau" required class="w-full p-3.5 border rounded-2xl text-xs bg-slate-50 font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-600" placeholder="••••••••">
            </div>

            <button type="submit" class="w-full bg-brand-600 hover:bg-brand-700 text-white font-bold py-3.5 rounded-2xl text-xs shadow-md uppercase tracking-wider transition">
                Đăng Nhập Ngay
            </button>
        </form>

        <div class="text-center pt-2 border-t border-slate-100">
            <p class="text-xs text-slate-500">
                Chưa có tài khoản? 
                <a href="index.php?act=register" class="font-bold text-brand-600 hover:underline">Đăng ký ngay</a>
            </p>
        </div>
    </div>
</main>

<?php 
require_once 'views/footer.php'; 
?>