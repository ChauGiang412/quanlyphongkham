
<div class="bg-white border border-slate-100 rounded-2xl shadow-sm overflow-hidden">

    <div class="px-5 py-5 border-b border-slate-100">

        <h2 class="font-extrabold text-lg">
            Danh sách bệnh nhân (<?= (int)($pagination['total'] ?? count($danhSachBenhNhan)) ?>)
        </h2>

        <p class="text-sm text-slate-400 mt-1">
            Những bệnh nhân đã từng đặt lịch khám với bạn.
        </p>

    </div>

    <?php if (empty($danhSachBenhNhan)): ?>

        <div class="p-10 text-center text-slate-400">

            <i class="fa-solid fa-users text-4xl mb-3"></i>

            <p>Chưa có dữ liệu bệnh nhân.</p>

        </div>

    <?php else: ?>

        <div class="overflow-x-auto">

            <table class="w-full text-sm text-left">

                <thead class="bg-slate-50 text-slate-500 text-xs uppercase">

                    <tr>
                        <th class="px-5 py-4">Họ tên</th>
                        <th class="px-5 py-4">Email</th>
                        <th class="px-5 py-4">Số điện thoại</th>
                        <th class="px-5 py-4">Số lần khám</th>
                        <th class="px-5 py-4">Lần khám gần nhất</th>
                    </tr>

                </thead>

                <tbody class="divide-y divide-slate-100">

                    <?php foreach ($danhSachBenhNhan as $bn): ?>

                        <tr class="hover:bg-slate-50">

                            <td class="px-5 py-4 font-bold">
                                <?= htmlspecialchars($bn['ho_ten']) ?>
                            </td>

                            <td class="px-5 py-4">
                                <?= htmlspecialchars($bn['email'] ?? '') ?>
                            </td>

                            <td class="px-5 py-4">
                                <?= htmlspecialchars($bn['so_dien_thoai'] ?? '') ?>
                            </td>

                            <td class="px-5 py-4">

                                <span class="px-3 py-1 bg-teal-50 text-teal-700 rounded-full font-bold">
                                    <?= (int)$bn['tong_lan_kham'] ?>
                                </span>

                            </td>

                            <td class="px-5 py-4">

                                <?= !empty($bn['lan_kham_gan_nhat'])
                                    ? date('d/m/Y', strtotime($bn['lan_kham_gan_nhat']))
                                    : '—' ?>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    <?php endif; ?>

</div>

<!-- PHÂN TRANG -->
<?php
$currentPage = (int)($pagination['page'] ?? 1);
$totalPages = (int)($pagination['totalPages'] ?? 1);
$totalRecords = (int)($pagination['total'] ?? count($danhSachBenhNhan));

$currentPage = max(1, $currentPage);
$totalPages = max(1, $totalPages);

$makePageUrl = function ($page) {
    return 'index.php?' . http_build_query([
        'act' => 'bac-si-benh-nhan',
        'page' => $page
    ]);
};
?>

<?php if ($totalPages > 1): ?>

    <div class="flex flex-col items-center justify-center gap-3 mt-7 mb-5">

        <div class="flex items-center justify-center flex-wrap gap-2">

            <!-- TRANG TRƯỚC -->
            <?php if ($currentPage > 1): ?>

                <a href="<?= htmlspecialchars($makePageUrl($currentPage - 1)) ?>"
                   class="w-10 h-10 rounded-full flex items-center justify-center border border-slate-200 bg-white text-slate-600 hover:bg-teal-50 hover:text-teal-700 hover:border-teal-300 transition">

                    <i class="fa-solid fa-chevron-left"></i>

                </a>

            <?php else: ?>

                <span class="w-10 h-10 rounded-full flex items-center justify-center border border-slate-100 bg-slate-50 text-slate-300">

                    <i class="fa-solid fa-chevron-left"></i>

                </span>

            <?php endif; ?>

            <!-- SỐ TRANG -->
            <?php
            $startPage = max(1, $currentPage - 2);
            $endPage = min($totalPages, $currentPage + 2);
            ?>

            <?php for ($i = $startPage; $i <= $endPage; $i++): ?>

                <a href="<?= htmlspecialchars($makePageUrl($i)) ?>"
                   class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm transition
                   <?= $i === $currentPage
                       ? 'bg-teal-600 text-white shadow-md shadow-teal-100'
                       : 'bg-white border border-slate-200 text-slate-600 hover:bg-teal-50 hover:text-teal-700 hover:border-teal-300' ?>">

                    <?= $i ?>

                </a>

            <?php endfor; ?>

            <!-- TRANG SAU -->
            <?php if ($currentPage < $totalPages): ?>

                <a href="<?= htmlspecialchars($makePageUrl($currentPage + 1)) ?>"
                   class="w-10 h-10 rounded-full flex items-center justify-center border border-slate-200 bg-white text-slate-600 hover:bg-teal-50 hover:text-teal-700 hover:border-teal-300 transition">

                    <i class="fa-solid fa-chevron-right"></i>

                </a>

            <?php else: ?>

                <span class="w-10 h-10 rounded-full flex items-center justify-center border border-slate-100 bg-slate-50 text-slate-300">

                    <i class="fa-solid fa-chevron-right"></i>

                </span>

            <?php endif; ?>

        </div>

        <p class="text-xs text-slate-400">
            Trang <?= $currentPage ?> / <?= $totalPages ?>
            · <?= $totalRecords ?> bệnh nhân
        </p>

    </div>

<?php endif; ?>
