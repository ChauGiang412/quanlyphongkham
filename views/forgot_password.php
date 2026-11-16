
<?php
require_once __DIR__ . '/header.php';
?>

<main class="max-w-md mx-auto px-4 py-12">
    <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-sm space-y-6">

        <!-- Tiêu đề -->
        <div class="text-center space-y-2">
            <span class="bg-brand-50 text-brand-600 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                Elite Clinic
            </span>

            <h1 class="text-2xl font-extrabold text-slate-900">
                Đặt Lại Mật Khẩu
            </h1>

            <p class="text-xs text-slate-500">
                Nhập email tài khoản và mật khẩu mới của bạn.
            </p>
        </div>

        <!-- Thông báo lỗi -->
        <?php if (!empty($error)): ?>
            <div class="bg-red-50 text-red-600 text-xs p-4 rounded-2xl border border-red-100 font-semibold text-center">
                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <!-- Thông báo thành công -->
        <?php if (!empty($success)): ?>
            <div class="bg-green-50 text-green-700 text-xs p-4 rounded-2xl border border-green-100 font-semibold text-center">
                <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?>

                <div class="mt-3">
                    <a
                        href="index.php?act=login"
                        class="font-bold text-brand-600 hover:underline"
                    >
                        Đăng nhập ngay
                    </a>
                </div>
            </div>
        <?php endif; ?>

        <?php if (empty($success)): ?>
            <form action="index.php?act=forgot-password" method="POST" class="space-y-4">

                <!-- CSRF -->
                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= htmlspecialchars($csrfToken ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                >

                <!-- Email -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Địa Chỉ Email <span class="text-red-500">*</span>
                    </label>

                    <input
                        type="email"
                        name="email"
                        required
                        autocomplete="email"
                        value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                        class="w-full p-3.5 border rounded-2xl text-xs bg-slate-50 font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-600"
                        placeholder="name@example.com"
                    >
                </div>

                <!-- Mật khẩu mới -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Mật Khẩu Mới <span class="text-red-500">*</span>
                    </label>

                    <input
                        type="password"
                        name="mat_khau"
                        required
                        minlength="8"
                        autocomplete="new-password"
                        class="w-full p-3.5 border rounded-2xl text-xs bg-slate-50 font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-600"
                        placeholder="Nhập mật khẩu mới"
                    >

                    <p class="text-[10px] text-slate-400 mt-1">
                        Mật khẩu phải có ít nhất 8 ký tự.
                    </p>
                </div>

                <!-- Xác nhận mật khẩu -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Xác Nhận Mật Khẩu <span class="text-red-500">*</span>
                    </label>

                    <input
                        type="password"
                        name="xac_nhan_mat_khau"
                        required
                        minlength="8"
                        autocomplete="new-password"
                        class="w-full p-3.5 border rounded-2xl text-xs bg-slate-50 font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-600"
                        placeholder="Nhập lại mật khẩu mới"
                    >
                </div>

                <!-- Nút đổi mật khẩu -->
                <button
                    type="submit"
                    class="w-full bg-brand-600 hover:bg-brand-700 text-white font-bold py-3.5 rounded-2xl text-xs shadow-md uppercase tracking-wider transition"
                >
                    Đổi Mật Khẩu
                </button>

            </form>
        <?php endif; ?>

        <!-- Quay lại đăng nhập -->
        <div class="text-center pt-2 border-t border-slate-100">
            <a
                href="index.php?act=login"
                class="text-xs font-bold text-brand-600 hover:underline"
            >
                <i class="fas fa-arrow-left mr-1"></i>
                Quay lại đăng nhập
            </a>
        </div>

    </div>
</main>

<?php
require_once __DIR__ . '/footer.php';
?>
