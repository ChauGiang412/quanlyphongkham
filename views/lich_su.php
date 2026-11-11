<?php require_once 'views/header.php'; ?>

<main class="py-12 bg-slate-50/50 min-h-screen">
    <div class="max-w-5xl mx-auto px-4 sm:px-6">
        <div class="flex justify-between items-center mb-8">
            <div>
                <span class="text-brand-600 font-semibold text-xs uppercase tracking-wider block mb-1">Hồ Sơ Y Tế Cá Nhân</span>
                <h1 class="text-2xl font-bold text-slate-900">Lịch Sử Đặt Lịch Khám Bệnh</h1>
            </div>
            <a href="index.php?act=dat-lich" class="bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold px-5 py-3 rounded-2xl shadow-md transition">
                <i class="fa-solid fa-plus mr-1.5"></i> Đặt Lịch Mới
            </a>
        </div>

        <div class="bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                            <th class="py-4 px-6">Mã Lịch</th>
                            <th class="py-4 px-6">Chuyên Khoa / Bác Sĩ</th>
                            <th class="py-4 px-6">Thời Gian Khám</th>
                            <th class="py-4 px-6">Trạng Thái</th>
                            <th class="py-4 px-6 text-right">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                        <?php if (!empty($danhSachLich)): ?>
                            <?php foreach ($danhSachLich as $lich): ?>
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="py-4 px-6 font-bold text-slate-900">#<?= $lich['ma_lich_kham']; ?></td>
                                    <td class="py-4 px-6">
                                        <div class="font-bold text-slate-900"><?= htmlspecialchars($lich['ten_chuyen_khoa'] ?? 'Khám tổng quát'); ?></div>
                                        <div class="text-[11px] text-slate-500">BS. <?= htmlspecialchars($lich['ten_bac_si'] ?? 'Chưa phân công'); ?></div>
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="font-semibold"><?= date('d/m/Y', strtotime($lich['ngay_kham'])); ?></div>
                                        <div class="text-[11px] text-slate-500"><?= $lich['gio_kham']; ?></div>
                                    </td>
                                    <td class="py-4 px-6">
                                        <?php 
                                            $badgeClass = "bg-amber-50 text-amber-600 border-amber-200";
                                            $statusText = "Chờ xác nhận";
                                            if($lich['trang_thai'] == 'da_xac_nhan') { $badgeClass = "bg-teal-50 text-teal-600 border-teal-200"; $statusText = "Đã xác nhận"; }
                                            elseif($lich['trang_thai'] == 'da_kham') { $badgeClass = "bg-blue-50 text-blue-600 border-blue-200"; $statusText = "Đã hoàn thành khám"; }
                                            elseif($lich['trang_thai'] == 'da_huy') { $badgeClass = "bg-rose-50 text-rose-600 border-rose-200"; $statusText = "Đã hủy"; }
                                        ?>
                                        <span class="px-3 py-1 rounded-full text-[10px] font-bold border <?= $badgeClass; ?>">
                                            <?= $statusText; ?>
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-right">
                                        <a href="index.php?act=chi-tiet-lich&id=<?= $lich['ma_lich_kham']; ?>" class="inline-flex items-center px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-brand-600 hover:text-white text-slate-700 font-semibold transition text-[11px]">
                                            Xem Phiếu & Đơn Thuốc
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-400">Bạn chưa có lịch hẹn khám nào.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php require_once 'views/footer.php'; ?>