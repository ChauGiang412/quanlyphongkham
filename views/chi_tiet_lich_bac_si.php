<?php
$statusLabels = [
    'cho_xac_nhan' => 'Chờ xác nhận',
    'da_xac_nhan' => 'Đã xác nhận',
    'da_kham' => 'Đã khám',
    'da_huy' => 'Đã hủy'
];

$statusColors = [
    'cho_xac_nhan' => 'bg-amber-50 text-amber-700',
    'da_xac_nhan' => 'bg-teal-50 text-teal-700',
    'da_kham' => 'bg-emerald-50 text-emerald-700',
    'da_huy' => 'bg-rose-50 text-rose-700'
];
?>

<div class="mb-5">

    <a href="index.php?act=bac-si-lich-kham"
       class="text-sm font-bold text-teal-700 hover:underline">

        <i class="fa-solid fa-arrow-left mr-2"></i>
        Quay lại danh sách lịch khám

    </a>

</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    <section class="xl:col-span-2 space-y-6">

        <!-- THÔNG TIN LỊCH KHÁM -->
        <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">

            <div class="flex flex-wrap items-center justify-between gap-3 mb-5">

                <div>

                    <p class="text-xs text-slate-400 uppercase font-bold">
                        Mã lịch khám
                    </p>

                    <h2 class="text-2xl font-extrabold mt-1">
                        #<?= (int)$lich['ma_lich_kham'] ?>
                    </h2>

                </div>

                <span class="px-4 py-2 rounded-full text-xs font-extrabold
                    <?= $statusColors[$lich['trang_thai']] ?? 'bg-slate-100 text-slate-600' ?>">

                    <?= htmlspecialchars(
                        $statusLabels[$lich['trang_thai']]
                        ?? $lich['trang_thai']
                    ) ?>

                </span>

            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 text-sm">

                <div>
                    <p class="text-slate-400 text-xs mb-1">Bệnh nhân</p>
                    <p class="font-bold text-slate-900">
                        <?= htmlspecialchars($lich['ten_benh_nhan']) ?>
                    </p>
                </div>

                <div>
                    <p class="text-slate-400 text-xs mb-1">Số điện thoại</p>
                    <p class="font-semibold">
                        <?= htmlspecialchars($lich['so_dien_thoai'] ?? '') ?>
                    </p>
                </div>

                <div>
                    <p class="text-slate-400 text-xs mb-1">Email</p>
                    <p class="font-semibold">
                        <?= htmlspecialchars($lich['email'] ?? '') ?>
                    </p>
                </div>

                <div>
                    <p class="text-slate-400 text-xs mb-1">Ngày sinh</p>
                    <p class="font-semibold">

                        <?= !empty($lich['ngay_sinh'])
                            ? date('d/m/Y', strtotime($lich['ngay_sinh']))
                            : 'Chưa cập nhật' ?>

                    </p>
                </div>

                <div>
                    <p class="text-slate-400 text-xs mb-1">Giới tính</p>
                    <p class="font-semibold">
                        <?= htmlspecialchars($lich['gioi_tinh'] ?? 'Chưa cập nhật') ?>
                    </p>
                </div>

                <div>
                    <p class="text-slate-400 text-xs mb-1">Ngày khám</p>
                    <p class="font-semibold">
                        <?= date('d/m/Y', strtotime($lich['ngay_kham'])) ?>
                    </p>
                </div>

                <div>
                    <p class="text-slate-400 text-xs mb-1">Giờ khám</p>
                    <p class="font-semibold">
                        <?= htmlspecialchars(substr($lich['gio_kham'], 0, 5)) ?>
                    </p>
                </div>

                <div>
                    <p class="text-slate-400 text-xs mb-1">Chuyên khoa</p>
                    <p class="font-semibold">
                        <?= htmlspecialchars($lich['ten_chuyen_khoa'] ?? '') ?>
                    </p>
                </div>

                <div>
                    <p class="text-slate-400 text-xs mb-1">Phí khám</p>
                    <p class="font-semibold">
                        <?= number_format((float)$lich['phi_kham'], 0, ',', '.') ?> đ
                    </p>
                </div>

            </div>

            <div class="mt-5 p-4 bg-slate-50 rounded-xl">

                <p class="text-xs text-slate-400 mb-1">
                    Lý do khám
                </p>

                <p class="text-sm">
                    <?= nl2br(htmlspecialchars(
                        $lich['ly_do_kham'] ?: 'Bệnh nhân chưa cung cấp lý do.'
                    )) ?>
                </p>

            </div>

        </div>

        <!-- KẾT QUẢ KHÁM -->
        <?php if (
            in_array(
                $lich['trang_thai'],
                ['da_xac_nhan', 'da_kham'],
                true
            )
        ): ?>

            <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">

                <h2 class="font-extrabold text-lg">
                    Kết quả khám bệnh
                </h2>

                <p class="text-sm text-slate-400 mt-1 mb-5">
                    Nhập chẩn đoán và hướng dẫn điều trị cho bệnh nhân.
                </p>

                <form action="index.php?act=bac-si-luu-ket-qua"
                      method="POST"
                      class="space-y-4">

                    <input type="hidden"
                           name="ma_lich_kham"
                           value="<?= (int)$lich['ma_lich_kham'] ?>">

                    <label class="block text-sm font-bold">

                        Chẩn đoán <span class="text-rose-500">*</span>

                        <textarea name="chuan_doan"
                                  required
                                  rows="3"
                                  class="mt-2 w-full border border-slate-200 rounded-xl p-3 font-normal focus:ring-2 focus:ring-teal-500 focus:outline-none"
                                  placeholder="Nhập kết quả chẩn đoán..."><?= htmlspecialchars($phieuKham['chuan_doan'] ?? '') ?></textarea>

                    </label>

                    <label class="block text-sm font-bold">

                        Đơn thuốc

                        <textarea name="don_thuoc"
                                  rows="3"
                                  class="mt-2 w-full border border-slate-200 rounded-xl p-3 font-normal focus:ring-2 focus:ring-teal-500 focus:outline-none"
                                  placeholder="Tên thuốc, liều dùng, cách sử dụng..."><?= htmlspecialchars($phieuKham['don_thuoc'] ?? '') ?></textarea>

                    </label>

                    <label class="block text-sm font-bold">

                        Lời dặn bác sĩ

                        <textarea name="loi_dan_bac_si"
                                  rows="3"
                                  class="mt-2 w-full border border-slate-200 rounded-xl p-3 font-normal focus:ring-2 focus:ring-teal-500 focus:outline-none"
                                  placeholder="Lời dặn và hướng dẫn chăm sóc..."><?= htmlspecialchars($phieuKham['loi_dan_bac_si'] ?? '') ?></textarea>

                    </label>

                    <label class="block text-sm font-bold">

                        Ngày tái khám

                        <input type="date"
                               name="ngay_tai_kham"
                               value="<?= htmlspecialchars($phieuKham['ngay_tai_kham'] ?? '') ?>"
                               class="mt-2 w-full md:w-1/2 border border-slate-200 rounded-xl p-3 font-normal">

                    </label>

                    <button type="submit"
                            class="bg-teal-600 hover:bg-teal-700 text-white rounded-xl px-6 py-3 font-bold">

                        <i class="fa-solid fa-floppy-disk mr-2"></i>

                        Lưu kết quả khám

                    </button>

                </form>

            </div>

        <?php elseif ($lich['trang_thai'] === 'cho_xac_nhan'): ?>

            <div class="bg-amber-50 border border-amber-100 rounded-2xl p-5 text-amber-800 text-sm">

                Bạn cần xác nhận lịch khám trước khi nhập kết quả khám bệnh.

            </div>

        <?php else: ?>

            <div class="bg-rose-50 border border-rose-100 rounded-2xl p-5 text-rose-700 text-sm">

                Lịch khám đã bị hủy nên không thể nhập kết quả.

            </div>

        <?php endif; ?>

    </section>

    <!-- THAO TÁC -->
    <aside class="space-y-5">

        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm">

            <h3 class="font-extrabold mb-4">
                Thao tác lịch khám
            </h3>

            <?php if ($lich['trang_thai'] === 'cho_xac_nhan'): ?>

                <form action="index.php?act=bac-si-cap-nhat-trang-thai"
                      method="POST"
                      class="space-y-3">

                    <input type="hidden"
                           name="ma_lich_kham"
                           value="<?= (int)$lich['ma_lich_kham'] ?>">

                    <button type="submit"
                            name="trang_thai"
                            value="da_xac_nhan"
                            class="w-full bg-teal-600 hover:bg-teal-700 text-white rounded-xl py-3 font-bold">

                        <i class="fa-solid fa-check mr-2"></i>
                        Xác nhận lịch

                    </button>

                    <button type="submit"
                            name="trang_thai"
                            value="da_huy"
                            onclick="return confirm('Bạn có chắc muốn hủy lịch khám này?')"
                            class="w-full bg-rose-50 hover:bg-rose-100 text-rose-700 rounded-xl py-3 font-bold">

                        <i class="fa-solid fa-xmark mr-2"></i>
                        Hủy lịch

                    </button>

                </form>

            <?php elseif ($lich['trang_thai'] === 'da_xac_nhan'): ?>

                <form action="index.php?act=bac-si-cap-nhat-trang-thai"
                      method="POST">

                    <input type="hidden"
                           name="ma_lich_kham"
                           value="<?= (int)$lich['ma_lich_kham'] ?>">

                    <button type="submit"
                            name="trang_thai"
                            value="da_huy"
                            onclick="return confirm('Bạn có chắc muốn hủy lịch khám này?')"
                            class="w-full bg-rose-50 hover:bg-rose-100 text-rose-700 rounded-xl py-3 font-bold">

                        Hủy lịch khám

                    </button>

                </form>

            <?php else: ?>

                <p class="text-sm text-slate-500">
                    Không còn thao tác trạng thái nào cho lịch khám này.
                </p>

            <?php endif; ?>

        </div>

        <div class="bg-teal-50 border border-teal-100 rounded-2xl p-5">

            <i class="fa-solid fa-shield-heart text-teal-700 text-xl mb-2"></i>

            <p class="font-bold text-teal-900">
                Bảo mật thông tin
            </p>

            <p class="text-xs text-teal-800 mt-1">
                Chỉ bác sĩ được phân công cho lịch khám này mới có thể xem và cập nhật thông tin.
            </p>

        </div>

    </aside>

</div>