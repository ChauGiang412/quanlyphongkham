<?php
require_once 'views/header.php';
?>


<main class="max-w-6xl mx-auto px-4 py-10 space-y-6">
    <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-sm space-y-6">
        <div class="flex justify-between items-center">
            <div>
                <span class="text-brand-600 font-bold text-xs uppercase tracking-wider">Hệ Thống Quản Trị</span>
                <h1 class="text-xl font-extrabold text-slate-950">Quản Lý Lịch Hẹn Khám Bệnh</h1>
            </div>
            <a href="index.php?act=admin-dashboard" class="text-xs font-bold text-slate-600 bg-slate-100 px-4 py-2 rounded-xl hover:bg-slate-200 transition">Quay lại Dashboard</a>
        </div>


        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-400 uppercase">
                    <tr>
                        <th class="p-4 font-bold">Mã Lịch</th>
                        <th class="p-4 font-bold">Bệnh Nhân</th>
                        <th class="p-4 font-bold">Dịch Vụ / Bác Sĩ</th>
                        <th class="p-4 font-bold">Thời Gian</th>
                        <th class="p-4 font-bold">Trạng Thái</th>
                        <th class="p-4 font-bold text-center">Hành Động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-semibold text-slate-700">
                    <?php if (empty($danhSachLich)): ?>
                        <tr><td colspan="6" class="p-6 text-center text-slate-400 italic">Chưa có lịch hẹn nào trong hệ thống.</td></tr>
                    <?php else: ?>
                        <?php foreach ($danhSachLich as $lich): ?>
                        <tr>
                            <td class="p-4">#<?= $lich['ma_lich_kham'] ?? $lich['id']; ?></td>
                            <td class="p-4"><?= htmlspecialchars($lich['ho_ten'] ?? 'Khách hàng'); ?><br><span class="text-slate-400 font-normal"><?= $lich['so_dien_thoai'] ?? ''; ?></span></td>
                            <td class="p-4"><?= htmlspecialchars($lich['ten_chuyen_khoa'] ?? 'Gói khám / Chuyên khoa'); ?><br><span class="text-brand-600 font-normal"><?= htmlspecialchars($lich['ten_bac_si'] ?? ''); ?></span></td>
                            <td class="p-4"><?= $lich['ngay_kham']; ?><br><span class="text-slate-400 font-normal"><?= $lich['gio_kham'] ?? ''; ?></span></td>
                            <td class="p-4">
                                <?php
                                $stt = $lich['trang_thai'] ?? 'Chờ duyệt';
                                $bg = ($stt == 'Đã xác nhận') ? 'bg-emerald-50 text-emerald-600' : (($stt == 'Đã hủy') ? 'bg-red-50 text-red-600' : 'bg-amber-50 text-amber-600');
                                ?>
                                <span class="px-3 py-1 rounded-full font-bold uppercase <?= $bg; ?>"><?= $stt; ?></span>
                            </td>
                            <td class="p-4 text-center space-x-2">
                                <a href="index.php?act=admin-duyet-lich&id=<?= $lich['ma_lich_kham'] ?? $lich['id']; ?>" class="inline-block bg-emerald-50 text-emerald-600 px-3 py-1.5 rounded-xl font-bold hover:bg-emerald-100 transition">Duyệt</a>
                                <a href="index.php?act=admin-huy-lich&id=<?= $lich['ma_lich_kham'] ?? $lich['id']; ?>" class="inline-block bg-red-50 text-red-600 px-3 py-1.5 rounded-xl font-bold hover:bg-red-100 transition">Hủy</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>


<?php
require_once 'views/footer.php';
?>

