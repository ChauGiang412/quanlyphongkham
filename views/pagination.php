
<?php
// Thành phần phân trang dùng chung cho các giao diện.

if (!empty($pagination)):
    $currentPage = (int)($pagination['page'] ?? 1);
    $totalPages = (int)($pagination['totalPages'] ?? 1);

    $params = $paginationParams ?? $_GET;
    $pageParam = $paginationPageParam ?? 'page';

    unset($params[$pageParam]);

    $anchor = $paginationAnchor ?? '';

    $makeUrl = static function (int $p) use (
        $params,
        $anchor,
        $pageParam
    ): string {
        $query = $params;
        $query[$pageParam] = $p;

        return 'index.php?' .
            htmlspecialchars(
                http_build_query($query),
                ENT_QUOTES,
                'UTF-8'
            ) . $anchor;
    };
?>

<nav class="flex flex-wrap items-center justify-center gap-2 py-6"
     aria-label="Phân trang">

    <a href="<?= $makeUrl(1) ?>"
       class="px-3 py-2 rounded-lg border text-sm <?= $currentPage <= 1 ? 'pointer-events-none opacity-40' : 'hover:bg-slate-50' ?>">
        Đầu
    </a>

    <a href="<?= $makeUrl(max(1, $currentPage - 1)) ?>"
       class="px-3 py-2 rounded-lg border text-sm <?= $currentPage <= 1 ? 'pointer-events-none opacity-40' : 'hover:bg-slate-50' ?>">
        Trước
    </a>

    <?php
    $start = max(1, $currentPage - 2);
    $end = min($totalPages, $currentPage + 2);

    for ($p = $start; $p <= $end; $p++):
    ?>

        <a href="<?= $makeUrl($p) ?>"
           class="min-w-10 text-center px-3 py-2 rounded-lg border text-sm <?= $p === $currentPage ? 'bg-teal-600 border-teal-600 text-white font-bold' : 'hover:bg-slate-50' ?>">
            <?= $p ?>
        </a>

    <?php endfor; ?>

    <a href="<?= $makeUrl(min($totalPages, $currentPage + 1)) ?>"
       class="px-3 py-2 rounded-lg border text-sm <?= $currentPage >= $totalPages ? 'pointer-events-none opacity-40' : 'hover:bg-slate-50' ?>">
        Sau
    </a>

    <a href="<?= $makeUrl($totalPages) ?>"
       class="px-3 py-2 rounded-lg border text-sm <?= $currentPage >= $totalPages ? 'pointer-events-none opacity-40' : 'hover:bg-slate-50' ?>">
        Cuối
    </a>

    <span class="w-full text-center text-xs text-slate-400">
        Trang <?= $currentPage ?>/<?= $totalPages ?>
        · <?= (int)($pagination['total'] ?? 0) ?> bản ghi
    </span>
</nav>

<?php endif; ?>
