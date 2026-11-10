<?php
$tongDanhGia = count($danhSachDanhGia);


$tongSao = 0;


foreach ($danhSachDanhGia as $dg) {
    $tongSao += (int)$dg['so_sao'];
}


$diemTrungBinh = $tongDanhGia > 0
    ? $tongSao / $tongDanhGia
    : 0;


$namSao = 0;


foreach ($danhSachDanhGia as $dg) {
    if ((int)$dg['so_sao'] === 5) {
        $namSao++;
    }
}
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
