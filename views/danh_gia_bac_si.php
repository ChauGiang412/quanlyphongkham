
<?php
$tongDanhGia = (int)($thongKeDanhGia['tong'] ?? 0);
$diemTrungBinh = (float)($thongKeDanhGia['trung_binh'] ?? 0);
$namSao = (int)($thongKeDanhGia['nam_sao'] ?? 0);

$currentPage = max(1, (int)($pagination['page'] ?? 1));
$totalPages = max(1, (int)($pagination['totalPages'] ?? 1));
$totalRecords = (int)($pagination['total'] ?? count($danhSachDanhGia));

$makePageUrl = function ($page) {
    return 'index.php?' . http_build_query([
        'act' => 'bac-si-danh-gia',
        'page' => $page
    ]);
};
?>

<!-- THỐNG KÊ ĐÁNH GIÁ -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">

    <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm">

        <p class="text-sm text-slate-500">
            Tổng lượt đánh giá
        </p>

        <p class="text-3xl font-extrabold mt-2">
            <?= $tongDanhGia ?>
        </p>

        <p class="text-xs text-slate-400 mt-1">
            Lượt nhận xét từ bệnh nhân
        </p>

    </div>

    <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm">

        <p class="text-sm text-slate-500">
            Điểm trung bình
        </p>

        <p class="text-3xl font-extrabold mt-2">

            <?= number_format($diemTrungBinh, 1) ?>

            <span class="text-amber-500 text-xl">★</span>

        </p>

        <p class="text-xs text-slate-400 mt-1">
            Trên thang điểm 5
        </p>

    </div>

    <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm">

        <p class="text-sm text-slate-500">
            Đánh giá 5 sao
        </p>

        <p class="text-3xl font-extrabold mt-2">
            <?= $namSao ?>
        </p>

        <p class="text-xs text-slate-400 mt-1">
            Lượt đánh giá cao nhất
        </p>

    </div>

</div>

<!-- CHI TIẾT ĐÁNH GIÁ -->
<div class="mb-5">

    <h2 class="font-extrabold text-lg">
        Nhận xét từ bệnh nhân
    </h2>

    <p class="text-sm text-slate-400 mt-1">
        Danh sách đánh giá dành cho bạn.
    </p>

</div>

<div class="space-y-4">

    <?php if (empty($danhSachDanhGia)): ?>

        <div class="bg-white border border-slate-100 rounded-2xl p-10 text-center text-slate-400">

            <i class="fa-regular fa-star text-4xl mb-3"></i>

            <p>Bạn chưa có đánh giá nào.</p>

        </div>

    <?php else: ?>

        <?php foreach ($danhSachDanhGia as $dg): ?>

            <?php
            $soSao = max(0, min(5, (int)$dg['so_sao']));
            ?>

            <article class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm">

                <div class="flex flex-wrap items-start justify-between gap-3">

                    <div>

                        <p class="font-extrabold text-slate-900">
                            <?= htmlspecialchars($dg['ten_benh_nhan']) ?>
                        </p>

                        <p class="text-xs text-slate-400 mt-1">

                            Lịch khám:

                            <?= !empty($dg['ngay_kham'])
                                ? date('d/m/Y', strtotime($dg['ngay_kham']))
                                : 'Không có thông tin' ?>

                        </p>

                    </div>

                    <div class="text-amber-500 tracking-widest text-lg">

                        <?= str_repeat('★', $soSao) ?>

                        <span class="text-slate-200">
                            <?= str_repeat('★', 5 - $soSao) ?>
                        </span>

                    </div>

                </div>

                <div class="mt-4 bg-slate-50 rounded-xl p-4">

                    <p class="text-sm text-slate-600 whitespace-pre-line">

                        <?= htmlspecialchars(
                            $dg['noi_dung'] ?: 'Bệnh nhân không để lại nhận xét.'
                        ) ?>

                    </p>

                </div>

            </article>

        <?php endforeach; ?>

    <?php endif; ?>

</div>

<!-- PHÂN TRANG -->
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
            · <?= $totalRecords ?> đánh giá
        </p>

    </div>

<?php endif; ?>
