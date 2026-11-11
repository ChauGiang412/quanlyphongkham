<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    <!-- FORM CẬP NHẬT -->
    <section class="xl:col-span-2 bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">

        <div class="mb-6">

            <h2 class="font-extrabold text-lg">
                Thông tin bác sĩ
            </h2>

            <p class="text-sm text-slate-400 mt-1">
                Cập nhật thông tin liên hệ và giới thiệu chuyên môn của bạn.
            </p>

        </div>

        <form action="index.php?act=bac-si-cap-nhat-ho-so"
              method="POST"
              class="space-y-5">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <label class="text-sm font-bold">

                    Họ và tên tài khoản
                    <span class="text-rose-500">*</span>

                    <input type="text"
                           name="ho_ten"
                           required
                           value="<?= htmlspecialchars($hoSo['ho_ten'] ?? '') ?>"
                           class="mt-2 w-full border border-slate-200 rounded-xl p-3 font-normal focus:ring-2 focus:ring-teal-500 focus:outline-none">

                </label>

                <label class="text-sm font-bold">

                    Tên hiển thị bác sĩ
                    <span class="text-rose-500">*</span>

                    <input type="text"
                           name="ten_bac_si"
                           required
                           value="<?= htmlspecialchars($hoSo['ten_bac_si'] ?? '') ?>"
                           class="mt-2 w-full border border-slate-200 rounded-xl p-3 font-normal focus:ring-2 focus:ring-teal-500 focus:outline-none">

                </label>

                <label class="text-sm font-bold">

                    Email
                    <span class="text-rose-500">*</span>

                    <input type="email"
                           name="email"
                           required
                           value="<?= htmlspecialchars($hoSo['email'] ?? '') ?>"
                           class="mt-2 w-full border border-slate-200 rounded-xl p-3 font-normal focus:ring-2 focus:ring-teal-500 focus:outline-none">

                </label>

                <label class="text-sm font-bold">

                    Số điện thoại

                    <input type="text"
                           name="so_dien_thoai"
                           value="<?= htmlspecialchars($hoSo['so_dien_thoai'] ?? '') ?>"
                           class="mt-2 w-full border border-slate-200 rounded-xl p-3 font-normal focus:ring-2 focus:ring-teal-500 focus:outline-none">

                </label>

                <label class="text-sm font-bold">

                    Bằng cấp

                    <input type="text"
                           name="bang_cap"
                           value="<?= htmlspecialchars($hoSo['bang_cap'] ?? '') ?>"
                           class="mt-2 w-full border border-slate-200 rounded-xl p-3 font-normal focus:ring-2 focus:ring-teal-500 focus:outline-none">

                </label>

                <label class="text-sm font-bold">

                    Số năm kinh nghiệm

                    <input type="number"
                           name="so_nam_kinh_nghiem"
                           min="0"
                           max="80"
                           value="<?= (int)($hoSo['so_nam_kinh_nghiem'] ?? 0) ?>"
                           class="mt-2 w-full border border-slate-200 rounded-xl p-3 font-normal focus:ring-2 focus:ring-teal-500 focus:outline-none">

                </label>

            </div>

            <label class="block text-sm font-bold">

                Giới thiệu chuyên môn

                <textarea name="gioi_thieu"
                          rows="6"
                          class="mt-2 w-full border border-slate-200 rounded-xl p-3 font-normal focus:ring-2 focus:ring-teal-500 focus:outline-none"
                          placeholder="Giới thiệu về quá trình công tác, chuyên môn và kinh nghiệm của bạn..."><?= htmlspecialchars($hoSo['gioi_thieu'] ?? '') ?></textarea>

            </label>

            <button type="submit"
                    class="bg-teal-600 hover:bg-teal-700 text-white rounded-xl px-6 py-3 font-bold transition">

                <i class="fa-solid fa-floppy-disk mr-2"></i>

                Lưu thay đổi

            </button>

        </form>

    </section>

    <!-- THÔNG TIN TÓM TẮT -->
    <aside class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm h-fit">

        <div class="w-16 h-16 rounded-2xl bg-teal-50 text-teal-700 flex items-center justify-center text-2xl mb-4">

            <i class="fa-solid fa-user-doctor"></i>

        </div>

        <h3 class="font-extrabold text-lg">
            <?= htmlspecialchars($hoSo['ten_bac_si'] ?? '') ?>
        </h3>

        <p class="text-sm text-teal-700 font-semibold mt-1">
            <?= htmlspecialchars($hoSo['ten_chuyen_khoa'] ?? '') ?>
        </p>

        <div class="border-t border-slate-100 mt-5 pt-4 space-y-4">

            <div>

                <p class="text-xs text-slate-400">
                    Mã bác sĩ
                </p>

                <p class="font-bold mt-1">
                    BS<?= str_pad(
                        (string)($hoSo['ma_bac_si'] ?? ''),
                        4,
                        '0',
                        STR_PAD_LEFT
                    ) ?>
                </p>

            </div>

            <div>

                <p class="text-xs text-slate-400">
                    Bằng cấp
                </p>

                <p class="font-bold mt-1">
                    <?= htmlspecialchars($hoSo['bang_cap'] ?? 'Chưa cập nhật') ?>
                </p>

            </div>

            <div>

                <p class="text-xs text-slate-400">
                    Kinh nghiệm
                </p>

                <p class="font-bold mt-1">
                    <?= (int)($hoSo['so_nam_kinh_nghiem'] ?? 0) ?> năm
                </p>

            </div>

            <div>

                <p class="text-xs text-slate-400">
                    Phí khám
                </p>

                <p class="font-bold mt-1 text-teal-700">
                    <?= number_format(
                        (float)($hoSo['phi_kham'] ?? 0),
                        0,
                        ',',
                        '.'
                    ) ?> đ
                </p>

            </div>

        </div>

        <div class="mt-5 p-4 bg-amber-50 rounded-xl">

            <p class="text-xs text-amber-800">

                <i class="fa-solid fa-circle-info mr-1"></i>

                Chuyên khoa và phí khám được quản lý bởi quản trị viên.

            </p>

        </div>

    </aside>

</div>