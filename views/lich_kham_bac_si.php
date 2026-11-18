
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

$currentPage = (int)($pagination['page'] ?? 1);
$totalPages = (int)($pagination['totalPages'] ?? 1);
$totalRecords = (int)($pagination['total'] ?? count($danhSachLich));

$currentPage = max(1, $currentPage);
$totalPages = max(1, $totalPages);

$paginationParams = [
    'act' => 'bac-si-lich-kham',
    'trang_thai' => $status ?? '',
    'ngay_kham' => $date ?? ''
];

$makePageUrl = function ($page) use ($paginationParams) {
    return 'index.php?' . http_build_query(
        array_merge($paginationParams, ['page' => $page])
    );
};
?>

<!-- BỘ LỌC -->
<div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm mb-6">

    <form method="GET" action="index.php"
          class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">

        <input type="hidden" name="act" value="bac-si-lich-kham">

        <label class="text-sm font-semibold text-slate-600">
            Trạng thái

            <select name="trang_thai"
                    class="mt-2 w-full border border-slate-200 rounded-xl px-3 py-2.5 bg-white">

                <option value="">Tất cả trạng thái</option>

                <?php foreach ($statusLabels as $key => $label): ?>
                    <option value="<?= htmlspecialchars($key) ?>"
                        <?= ($status ?? '') === $key ? 'selected' : '' ?>>
                        <?= htmlspecialchars($label) ?>
                    </option>
                <?php endforeach; ?>

            </select>
        </label>

        <label class="text-sm font-semibold text-slate-600">
            Ngày khám

            <input type="date"
                   name="ngay_kham"
                   value="<?= htmlspecialchars($date ?? '') ?>"
                   class="mt-2 w-full border border-slate-200 rounded-xl px-3 py-2.5">
        </label>

        <button type="submit"
                class="bg-teal-600 hover:bg-teal-700 text-white rounded-xl px-5 py-3 font-bold">

            <i class="fa-solid fa-filter mr-2"></i>
            Lọc lịch

        </button>

        <a href="index.php?act=bac-si-lich-kham"
           class="text-center border border-slate-200 rounded-xl px-5 py-3 font-bold text-slate-600 hover:bg-slate-50">

            Xóa bộ lọc

        </a>

    </form>

</div>

<!-- DANH SÁCH LỊCH -->
<div class="bg-white border border-slate-100 rounded-2xl shadow-sm overflow-hidden">

    <div class="px-5 py-4 border-b border-slate-100">

        <h2 class="font-extrabold">
            Danh sách lịch khám (<?= $totalRecords ?>)
        </h2>

    </div>

    <?php if (empty($danhSachLich)): ?>

        <div class="p-10 text-center text-slate-400">

            <i class="fa-regular fa-calendar-xmark text-3xl mb-3"></i>

            <p>Không tìm thấy lịch khám phù hợp.</p>

        </div>

    <?php else: ?>

        <div class="overflow-x-auto">

            <table class="w-full text-sm text-left">

                <thead class="bg-slate-50 text-slate-500 text-xs uppercase">

                    <tr>
                        <th class="px-5 py-3">Mã lịch</th>
                        <th class="px-5 py-3">Bệnh nhân</th>
                        <th class="px-5 py-3">Liên hệ</th>
                        <th class="px-5 py-3">Ngày khám</th>
                        <th class="px-5 py-3">Giờ khám</th>
                        <th class="px-5 py-3">Trạng thái</th>
                        <th class="px-5 py-3">Thao tác</th>
                    </tr>

                </thead>

                <tbody class="divide-y divide-slate-100">

                    <?php foreach ($danhSachLich as $row): ?>

                        <tr class="hover:bg-slate-50">

                            <td class="px-5 py-4 font-bold">
                                #<?= (int)$row['ma_lich_kham'] ?>
                            </td>

                            <td class="px-5 py-4 font-semibold">
                                <?= htmlspecialchars($row['ten_benh_nhan']) ?>
                            </td>

                            <td class="px-5 py-4">

                                <p>
                                    <?= htmlspecialchars($row['so_dien_thoai'] ?? '') ?>
                                </p>

                                <p class="text-xs text-slate-400">
                                    <?= htmlspecialchars($row['email'] ?? '') ?>
                                </p>

                            </td>

                            <td class="px-5 py-4 whitespace-nowrap">
                                <?= date('d/m/Y', strtotime($row['ngay_kham'])) ?>
                            </td>

                            <td class="px-5 py-4">
                                <?= htmlspecialchars(substr($row['gio_kham'], 0, 5)) ?>
                            </td>

                            <td class="px-5 py-4">

                                <span class="px-3 py-1 rounded-full text-xs font-bold
                                    <?= $statusColors[$row['trang_thai']] ?? 'bg-slate-100 text-slate-600' ?>">

                                    <?= htmlspecialchars(
                                        $statusLabels[$row['trang_thai']]
                                        ?? $row['trang_thai']
                                    ) ?>

                                </span>

                            </td>

                            <td class="px-5 py-4">

                                <a href="index.php?act=bac-si-chi-tiet-lich&id=<?= (int)$row['ma_lich_kham'] ?>"
                                   class="text-teal-700 font-bold hover:underline">

                                    Xem chi tiết

                                </a>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    <?php endif; ?>

</div>

<!-- PHÂN TRANG -->
<?php if ($totalPages > 1): ?>

    <div class="flex flex-col items-center justify-center gap-3 mt-7 mb-5">

        <div class="flex items-center justify-center flex-wrap gap-2">

            <!-- TRANG TRƯỚC -->
            <?php if ($currentPage > 1): ?>

                <a href="<?= htmlspecialchars($makePageUrl($currentPage - 1)) ?>"
                   class="w-10 h-10 rounded-full flex items-center justify-center
                          border border-slate-200 bg-white text-slate-600
                          hover:bg-teal-50 hover:text-teal-700 hover:border-teal-300 transition">

                    <i class="fa-solid fa-chevron-left"></i>

                </a>

            <?php else: ?>

                <span class="w-10 h-10 rounded-full flex items-center justify-center
                             border border-slate-100 bg-slate-50 text-slate-300">

                    <i class="fa-solid fa-chevron-left"></i>

                </span>

            <?php endif; ?>

            <!-- CÁC SỐ TRANG -->
            <?php
            $startPage = max(1, $currentPage - 2);
            $endPage = min($totalPages, $currentPage + 2);
            ?>

            <?php for ($i = $startPage; $i <= $endPage; $i++): ?>

                <a href="<?= htmlspecialchars($makePageUrl($i)) ?>"
                   class="w-10 h-10 rounded-full flex items-center justify-center
                          font-bold text-sm transition
                          <?= $i === $currentPage
                              ? 'bg-teal-600 text-white shadow-md shadow-teal-100'
                              : 'bg-white border border-slate-200 text-slate-600 hover:bg-teal-50 hover:text-teal-700 hover:border-teal-300' ?>">

                    <?= $i ?>

                </a>

            <?php endfor; ?>

            <!-- TRANG SAU -->
            <?php if ($currentPage < $totalPages): ?>

                <a href="<?= htmlspecialchars($makePageUrl($currentPage + 1)) ?>"
                   class="w-10 h-10 rounded-full flex items-center justify-center
                          border border-slate-200 bg-white text-slate-600
                          hover:bg-teal-50 hover:text-teal-700 hover:border-teal-300 transition">

                    <i class="fa-solid fa-chevron-right"></i>

                </a>

            <?php else: ?>

                <span class="w-10 h-10 rounded-full flex items-center justify-center
                             border border-slate-100 bg-slate-50 text-slate-300">

                    <i class="fa-solid fa-chevron-right"></i>

                </span>

            <?php endif; ?>

        </div>

        <p class="text-xs text-slate-400">
            Trang <?= $currentPage ?> / <?= $totalPages ?>
            · <?= $totalRecords ?> lịch khám
        </p>

    </div>

<?php endif; ?>
