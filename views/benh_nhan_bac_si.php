<div class="bg-white border border-slate-100 rounded-2xl shadow-sm overflow-hidden">

    <div class="px-5 py-5 border-b border-slate-100">

        <h2 class="font-extrabold text-lg">
            Danh sách bệnh nhân
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