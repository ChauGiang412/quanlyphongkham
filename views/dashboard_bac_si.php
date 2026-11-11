<?php
$cards = [
    [
        'Tổng lịch khám',
        $stats['tong_lich'] ?? 0,
        'fa-calendar-days',
        'bg-blue-50 text-blue-600'
    ],
    [
        'Lịch hôm nay',
        $stats['hom_nay'] ?? 0,
        'fa-calendar-day',
        'bg-violet-50 text-violet-600'
    ],
    [
        'Chờ xác nhận',
        $stats['cho_xac_nhan'] ?? 0,
        'fa-clock',
        'bg-amber-50 text-amber-600'
    ],
    [
        'Đã xác nhận',
        $stats['da_xac_nhan'] ?? 0,
        'fa-calendar-check',
        'bg-teal-50 text-teal-600'
    ],
    [
        'Đã khám',
        $stats['da_kham'] ?? 0,
        'fa-circle-check',
        'bg-emerald-50 text-emerald-600'
    ],
    [
        'Đã hủy',
        $stats['da_huy'] ?? 0,
        'fa-calendar-xmark',
        'bg-rose-50 text-rose-600'
    ]
];

$statusLabels = [
    'cho_xac_nhan' => 'Chờ xác nhận',
    'da_xac_nhan' => 'Đã xác nhận',
    'da_kham' => 'Đã khám',
    'da_huy' => 'Đã hủy'
];
?>

<!-- THỐNG KÊ -->
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 mb-8">

    <?php foreach ($cards as $card): ?>

        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm flex items-center justify-between">

            <div>

                <p class="text-sm text-slate-500">
                    <?= htmlspecialchars($card[0]) ?>
                </p>

                <p class="text-3xl font-extrabold text-slate-900 mt-2">
                    <?= (int)$card[1] ?>
                </p>

            </div>

            <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-xl <?= $card[3] ?>">

                <i class="fa-solid <?= htmlspecialchars($card[2]) ?>"></i>

            </div>

        </div>

    <?php endforeach; ?>

</div>

<!-- LỊCH KHÁM SẮP TỚI -->
<div class="bg-white border border-slate-100 rounded-2xl shadow-sm overflow-hidden">

    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">

        <div>

            <h2 class="font-extrabold text-slate-900">
                Lịch khám sắp tới
            </h2>

            <p class="text-xs text-slate-400 mt-1">
                Danh sách lịch khám đang chờ xử lý
            </p>

        </div>

        <a href="index.php?act=bac-si-lich-kham"
           class="text-sm font-bold text-teal-700 hover:text-teal-900">

            Xem tất cả
            <i class="fa-solid fa-arrow-right ml-1"></i>

        </a>

    </div>

    <?php if (empty($lichSapToi)): ?>

        <div class="p-10 text-center text-slate-400">

            <i class="fa-regular fa-calendar-xmark text-3xl mb-3"></i>

            <p>Hiện chưa có lịch khám sắp tới.</p>

        </div>

    <?php else: ?>

        <div class="overflow-x-auto">

            <table class="w-full text-sm text-left">

                <thead class="bg-slate-50 text-slate-500 text-xs uppercase">

                    <tr>
                        <th class="px-5 py-3">Bệnh nhân</th>
                        <th class="px-5 py-3">Ngày khám</th>
                        <th class="px-5 py-3">Giờ khám</th>
                        <th class="px-5 py-3">Trạng thái</th>
                        <th class="px-5 py-3">Thao tác</th>
                    </tr>

                </thead>

                <tbody class="divide-y divide-slate-100">

                    <?php foreach ($lichSapToi as $row): ?>

                        <tr class="hover:bg-slate-50">

                            <td class="px-5 py-4">

                                <p class="font-bold">
                                    <?= htmlspecialchars($row['ten_benh_nhan']) ?>
                                </p>

                                <p class="text-xs text-slate-400">
                                    <?= htmlspecialchars($row['so_dien_thoai'] ?? '') ?>
                                </p>

                            </td>

                            <td class="px-5 py-4">
                                <?= date('d/m/Y', strtotime($row['ngay_kham'])) ?>
                            </td>

                            <td class="px-5 py-4">
                                <?= htmlspecialchars(substr($row['gio_kham'], 0, 5)) ?>
                            </td>

                            <td class="px-5 py-4">

                                <span class="rounded-full px-3 py-1 text-xs font-bold
                                    <?= $row['trang_thai'] === 'cho_xac_nhan'
                                        ? 'bg-amber-50 text-amber-700'
                                        : 'bg-teal-50 text-teal-700' ?>">

                                    <?= htmlspecialchars(
                                        $statusLabels[$row['trang_thai']]
                                        ?? $row['trang_thai']
                                    ) ?>

                                </span>

                            </td>

                            <td class="px-5 py-4">

                                <a href="index.php?act=bac-si-chi-tiet-lich&id=<?= (int)$row['ma_lich_kham'] ?>"
                                   class="font-bold text-teal-700 hover:underline">

                                    Chi tiết

                                </a>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    <?php endif; ?>

</div>