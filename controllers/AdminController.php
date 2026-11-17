<?php

require_once __DIR__ . '/../config/Database.php';

class AdminController
{
    private PDO $db;
    private string $rootPath;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $role = $_SESSION['vai_tro']
            ?? $_SESSION['user']['vai_tro']
            ?? '';

        if ($role !== 'quan_tri') {
            header('Location: index.php?act=login');
            exit;
        }

        $database = new Database();
        $this->db = $database->getConnection();
        $this->rootPath = dirname(__DIR__);

        if (!$this->db) {
            die('Không thể kết nối cơ sở dữ liệu.');
        }

        if (empty($_SESSION['admin_csrf'])) {
            $_SESSION['admin_csrf'] = bin2hex(random_bytes(32));
        }
    }

    /* =====================================================
       HÀM HỖ TRỢ
    ===================================================== */

    private function e($value): string
    {
        return htmlspecialchars(
            (string)$value,
            ENT_QUOTES,
            'UTF-8'
        );
    }

    private function redirect(string $act): void
    {
        header('Location: index.php?act=' . urlencode($act));
        exit;
    }

    private function flash(string $message, string $type = 'success'): void
    {
        $_SESSION['admin_flash'] = [
            'message' => $message,
            'type' => $type
        ];
    }

    private function showFlash(): string
    {
        if (empty($_SESSION['admin_flash'])) {
            return '';
        }

        $flash = $_SESSION['admin_flash'];
        unset($_SESSION['admin_flash']);

        $class = ($flash['type'] ?? '') === 'error'
            ? 'bg-red-50 text-red-700 border-red-200'
            : 'bg-emerald-50 text-emerald-700 border-emerald-200';

        return '<div class="mb-5 border rounded-xl px-4 py-3 '
            . $class . '">'
            . $this->e($flash['message'])
            . '</div>';
    }

    private function csrfField(): string
    {
        return '<input type="hidden" name="csrf_token" value="'
            . $this->e($_SESSION['admin_csrf'])
            . '">';
    }

    private function verifyPost(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Phương thức không được phép.');
        }

        $token = $_POST['csrf_token'] ?? '';

        if (
            empty($_SESSION['admin_csrf']) ||
            !hash_equals($_SESSION['admin_csrf'], (string)$token)
        ) {
            http_response_code(403);
            exit('Yêu cầu không hợp lệ. Vui lòng tải lại trang.');
        }
    }

    private function input(
        string $name,
        string $placeholder,
        string $value = '',
        string $type = 'text',
        bool $required = false
    ): string {
        return '<input type="' . $this->e($type)
            . '" name="' . $this->e($name)
            . '" value="' . $this->e($value)
            . '" placeholder="' . $this->e($placeholder)
            . '" ' . ($required ? 'required' : '')
            . ' class="w-full p-3 border border-slate-200 rounded-xl text-sm">';
    }

    private function textarea(
        string $name,
        string $placeholder,
        string $value = ''
    ): string {
        return '<textarea name="' . $this->e($name)
            . '" placeholder="' . $this->e($placeholder)
            . '" rows="3"
            class="w-full p-3 border border-slate-200 rounded-xl text-sm">'
            . $this->e($value)
            . '</textarea>';
    }

    private function button(string $label): string
    {
        return '<button type="submit"
            class="bg-emerald-600 hover:bg-emerald-700 text-white
            font-bold px-5 py-3 rounded-xl text-sm">'
            . $this->e($label)
            . '</button>';
    }

    private function imageHtml(?string $path): string
    {
        if (!$path) {
            return '<span class="text-slate-400">Chưa có ảnh</span>';
        }

        return '<img src="' . $this->e($path)
            . '" class="w-14 h-14 object-cover rounded-xl" alt="Ảnh">';
    }

    private function uploadImage(
        string $field,
        string $folder,
        ?string $old = null
    ): ?string {
        if (
            !isset($_FILES[$field]) ||
            $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE
        ) {
            return $old;
        }

        $file = $_FILES[$field];

        if (
            $file['error'] !== UPLOAD_ERR_OK ||
            $file['size'] > 5 * 1024 * 1024
        ) {
            throw new RuntimeException(
                'Ảnh không hợp lệ hoặc vượt quá 5MB.'
            );
        }

        $mime = mime_content_type($file['tmp_name']);

        $types = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp'
        ];

        if (!isset($types[$mime])) {
            throw new RuntimeException(
                'Chỉ chấp nhận ảnh JPG, PNG hoặc WEBP.'
            );
        }

        $dir = $this->rootPath . '/uploads/' . $folder;

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $filename = bin2hex(random_bytes(16))
            . '.' . $types[$mime];

        if (!move_uploaded_file(
            $file['tmp_name'],
            $dir . '/' . $filename
        )) {
            throw new RuntimeException('Không thể lưu ảnh.');
        }

        return 'uploads/' . $folder . '/' . $filename;
    }

    private function deleteImage(?string $path): void
    {
        if (!$path || strpos($path, 'uploads/') !== 0) {
            return;
        }

        $fullPath = $this->rootPath . '/' . $path;

        if (is_file($fullPath)) {
            @unlink($fullPath);
        }
    }

    private function statusForm(
        string $act,
        int $id,
        string $label,
        string $color
    ): string {
        return '<form method="POST" action="index.php?act=' . $act
            . '" class="inline-block mr-2">'
            . $this->csrfField()
            . '<input type="hidden" name="id" value="' . $id . '">'
            . '<button class="' . $color . ' font-bold text-xs">'
            . $this->e($label)
            . '</button></form>';
    }

    /* =====================================================
       GIAO DIỆN QUẢN TRỊ
    ===================================================== */

    private function render(string $title, string $content): void
    {
        $menu = [
            'admin-dashboard' => '📊 Dashboard tổng quan',
            'admin-chuyen-khoa' => '🏥 Quản lý chuyên khoa',
            'admin-bac-si' => '👨‍⚕️ Quản lý bác sĩ',
            'admin-goi-kham' => '📋 Quản lý gói khám',
            'admin-lich-kham' => '📅 Quản lý lịch khám',
            'admin-benh-nhan' => '👥 Quản lý bệnh nhân',
            'admin-benh-nhan&view=ho-so-benh-an' => '🗂️ Quản lý hồ sơ bệnh án',
            'admin-nguoi-dung' => '👤 Quản lý người dùng',
            'admin-danh-gia' => '⭐ Quản lý đánh giá',
        ];

        echo '<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>' . $this->e($title) . ' - Elite Clinic</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-slate-50 font-sans text-slate-800">

<div class="min-h-screen flex flex-col md:flex-row">

<aside class="w-full md:w-64 bg-slate-900 text-slate-300
flex flex-col justify-between p-6 shrink-0">

<div>
<div class="flex items-center gap-3 mb-8">
<div class="w-10 h-10 rounded-xl bg-emerald-600
text-white flex items-center justify-center font-bold">
EC
</div>
<span class="text-white font-extrabold">Elite Admin</span>
</div>

<nav class="space-y-1 text-xs font-bold">';

        foreach ($menu as $act => $label) {
            $parts = explode('&', $act, 2);
            $baseAct = $parts[0];
            $isActive = ($_GET['act'] ?? '') === $baseAct;
            if ($baseAct === 'admin-benh-nhan' && isset($parts[1])) {
                $isActive = $isActive
                    && ($_GET['view'] ?? '') === 'ho-so-benh-an';
            } elseif ($baseAct === 'admin-benh-nhan') {
                $isActive = $isActive
                    && ($_GET['view'] ?? '') !== 'ho-so-benh-an';
            }
            $active = $isActive
                ? 'bg-emerald-600 text-white'
                : 'hover:bg-slate-800 text-slate-300';

            echo '<a href="index.php?act=' . $act
                . '" class="block px-4 py-3 rounded-xl '
                . $active . '">'
                . $label
                . '</a>';
        }

        echo '</nav>
</div>

<div class="pt-6 mt-6 border-t border-slate-800 space-y-3">
<a href="index.php?act=home"
class="block text-xs font-bold text-slate-400 hover:text-white">
🌐 Xem trang chủ
</a>

<a href="index.php?act=dang-xuat"
class="block text-xs font-bold text-red-400 hover:text-red-300">
🚪 Đăng xuất
</a>
</div>

</aside>

<main class="flex-1 min-w-0 p-5 md:p-8">
<div class="max-w-7xl mx-auto">

<div class="mb-7">
<p class="text-xs text-emerald-600 font-bold uppercase">
Elite Clinic
</p>
<h1 class="text-2xl font-extrabold text-slate-900 mt-1">'
            . $this->e($title)
            . '</h1>
</div>'

            . $this->showFlash()
            . $content

            . '</div>
</main>
</div>
</body>
</html>';
    }

    /* =====================================================
       1. DASHBOARD
    ===================================================== */

    public function dashboard(): void
    {
        try {
            // 1. Thống kê tổng quan
            $statistics = [
                [
                    'label' => 'Tổng bệnh nhân',
                    'value' => (int)$this->db->query("SELECT COUNT(*) FROM nguoi_dung WHERE vai_tro='benh_nhan'")->fetchColumn(),
                    'icon' => 'fa-users',
                    'color' => 'blue',
                    'description' => 'Tài khoản bệnh nhân'
                ],
                [
                    'label' => 'Tổng bác sĩ',
                    'value' => (int)$this->db->query("SELECT COUNT(*) FROM bac_si")->fetchColumn(),
                    'icon' => 'fa-user-doctor',
                    'color' => 'emerald',
                    'description' => 'Đội ngũ bác sĩ'
                ],
                [
                    'label' => 'Tổng lịch khám',
                    'value' => (int)$this->db->query("SELECT COUNT(*) FROM lich_kham")->fetchColumn(),
                    'icon' => 'fa-calendar-check',
                    'color' => 'orange',
                    'description' => 'Tất cả lịch hẹn'
                ],
                [
                    'label' => 'Chờ xác nhận',
                    'value' => (int)$this->db->query("SELECT COUNT(*) FROM lich_kham WHERE trang_thai='cho_xac_nhan'")->fetchColumn(),
                    'icon' => 'fa-clock',
                    'color' => 'red',
                    'description' => 'Lịch khám cần xử lý'
                ]
            ];

            $tongChuyenKhoa = (int)$this->db->query("SELECT COUNT(*) FROM chuyen_khoa")->fetchColumn();
            $tongGoiKham = (int)$this->db->query("SELECT COUNT(*) FROM goi_kham")->fetchColumn();

            // 2. Lịch khám theo tháng trong năm hiện tại
            $currentYear = (int)date('Y');
            $monthlyQuery = $this->db->prepare(
                "SELECT MONTH(ngay_kham) AS thang, COUNT(*) AS tong
                 FROM lich_kham
                 WHERE YEAR(ngay_kham)=?
                 GROUP BY MONTH(ngay_kham)
                 ORDER BY MONTH(ngay_kham)"
            );
            $monthlyQuery->execute([$currentYear]);
            $monthlyData = array_fill(1, 12, 0);
            foreach ($monthlyQuery->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $month = (int)$row['thang'];
                if ($month >= 1 && $month <= 12) {
                    $monthlyData[$month] = (int)$row['tong'];
                }
            }

            // 3. Tỷ lệ trạng thái lịch khám
            $statusRows = $this->db->query(
                "SELECT trang_thai, COUNT(*) AS tong
                 FROM lich_kham
                 GROUP BY trang_thai"
            )->fetchAll(PDO::FETCH_ASSOC);

            $statusLabelsMap = [
                'cho_xac_nhan' => 'Chờ xác nhận',
                'da_xac_nhan' => 'Đã xác nhận',
                'da_kham' => 'Đã khám',
                'da_huy' => 'Đã hủy'
            ];
            $statusColorsMap = [
                'cho_xac_nhan' => '#F59E0B',
                'da_xac_nhan' => '#3B82F6',
                'da_kham' => '#10B981',
                'da_huy' => '#EF4444'
            ];
            $statusLabels = [];
            $statusValues = [];
            $statusColors = [];
            $statusTotals = [];
            foreach ($statusRows as $row) {
                $key = (string)$row['trang_thai'];
                $statusLabels[] = $statusLabelsMap[$key] ?? $key;
                $statusValues[] = (int)$row['tong'];
                $statusColors[] = $statusColorsMap[$key] ?? '#94A3B8';
                $statusTotals[$key] = (int)$row['tong'];
            }

            // 4. Lượt khám theo chuyên khoa (dùng khóa ma_chuyen_khoa của lich_kham)
            $specialtyRows = $this->db->query(
                "SELECT ck.ten_chuyen_khoa, COUNT(lk.ma_lich_kham) AS tong
                 FROM chuyen_khoa ck
                 LEFT JOIN lich_kham lk
                    ON lk.ma_chuyen_khoa=ck.ma_chuyen_khoa
                    AND lk.trang_thai <> 'da_huy'
                 GROUP BY ck.ma_chuyen_khoa, ck.ten_chuyen_khoa
                 ORDER BY tong DESC
                 LIMIT 10"
            )->fetchAll(PDO::FETCH_ASSOC);
            $specialtyLabels = [];
            $specialtyValues = [];
            foreach ($specialtyRows as $row) {
                $specialtyLabels[] = (string)$row['ten_chuyen_khoa'];
                $specialtyValues[] = (int)$row['tong'];
            }

            // 5. Lịch khám mới nhất
            $recentRows = $this->db->query(
                "SELECT lk.ma_lich_kham, lk.ngay_kham, lk.gio_kham,
                        lk.trang_thai, nd.ho_ten, bs.ten_bac_si,
                        ck.ten_chuyen_khoa
                 FROM lich_kham lk
                 LEFT JOIN nguoi_dung nd ON nd.ma_nguoi_dung=lk.ma_nguoi_dung
                 LEFT JOIN bac_si bs ON bs.ma_bac_si=lk.ma_bac_si
                 LEFT JOIN chuyen_khoa ck ON ck.ma_chuyen_khoa=lk.ma_chuyen_khoa
                 ORDER BY lk.ma_lich_kham DESC
                 LIMIT 8"
            )->fetchAll(PDO::FETCH_ASSOC);

            $colorClasses = [
                'blue' => ['icon' => 'bg-blue-50 text-blue-600', 'number' => 'text-blue-700'],
                'emerald' => ['icon' => 'bg-emerald-50 text-emerald-600', 'number' => 'text-emerald-700'],
                'orange' => ['icon' => 'bg-orange-50 text-orange-600', 'number' => 'text-orange-600'],
                'red' => ['icon' => 'bg-red-50 text-red-600', 'number' => 'text-red-600']
            ];

            $content = '
<div class="space-y-6">
    <section class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-wider text-emerald-600">Hệ thống y tế Elite Clinic</p>
            <h2 class="text-2xl font-extrabold text-slate-900 mt-2">Tổng quan hoạt động</h2>
            <p class="text-sm text-slate-500 mt-2">Theo dõi tình hình hoạt động và quản lý phòng khám.</p>
        </div>
        <a href="index.php?act=admin-lich-kham" class="inline-flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm px-5 py-3 rounded-xl transition">
            <i class="fa-solid fa-calendar-check"></i> Quản lý lịch khám
        </a>
    </section>

    <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">';

            foreach ($statistics as $item) {
                $color = $colorClasses[$item['color']];
                $content .= '
        <article class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-500">' . $this->e($item['label']) . '</p>
                    <p class="text-3xl font-black mt-3 ' . $color['number'] . '">' . number_format($item['value']) . '</p>
                    <p class="text-xs text-slate-400 mt-2">' . $this->e($item['description']) . '</p>
                </div>
                <div class="w-12 h-12 rounded-xl flex items-center justify-center text-lg ' . $color['icon'] . '">
                    <i class="fa-solid ' . $this->e($item['icon']) . '"></i>
                </div>
            </div>
        </article>';
            }

            $content .= '
    </section>

    <section class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <article class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm flex items-center justify-between">
            <div><p class="text-xs font-bold text-slate-500 uppercase">Chuyên khoa</p><p class="text-2xl font-black text-slate-900 mt-2">' . number_format($tongChuyenKhoa) . '</p></div>
            <div class="w-11 h-11 rounded-xl bg-violet-50 text-violet-600 flex items-center justify-center text-lg"><i class="fa-solid fa-hospital"></i></div>
        </article>
        <article class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm flex items-center justify-between">
            <div><p class="text-xs font-bold text-slate-500 uppercase">Gói khám sức khỏe</p><p class="text-2xl font-black text-slate-900 mt-2">' . number_format($tongGoiKham) . '</p></div>
            <div class="w-11 h-11 rounded-xl bg-cyan-50 text-cyan-600 flex items-center justify-center text-lg"><i class="fa-solid fa-notes-medical"></i></div>
        </article>
    </section>

    <section class="grid grid-cols-1 xl:grid-cols-3 gap-5">
        <article class="xl:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
            <div class="flex items-center justify-between mb-5"><div><h3 class="font-extrabold text-slate-800">Thống kê lịch khám theo tháng</h3><p class="text-xs text-slate-400 mt-1">Số lượng lịch khám trong năm ' . $currentYear . '</p></div><span class="text-xs font-bold text-blue-600 bg-blue-50 px-3 py-2 rounded-lg">' . $currentYear . '</span></div>
            <div class="relative h-72"><canvas id="adminMonthlyChart"></canvas></div>
        </article>
        <article class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
            <h3 class="font-extrabold text-slate-800">Trạng thái lịch khám</h3><p class="text-xs text-slate-400 mt-1 mb-5">Phân loại theo tình trạng xử lý</p>
            <div class="relative h-64"><canvas id="adminStatusChart"></canvas></div>
        </article>
    </section>

    <section class="grid grid-cols-1 xl:grid-cols-2 gap-5">
        <article class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
            <h3 class="font-extrabold text-slate-800">Lượt khám theo chuyên khoa</h3><p class="text-xs text-slate-400 mt-1 mb-5">Không tính các lịch đã hủy</p>
            <div class="relative h-80"><canvas id="adminSpecialtyChart"></canvas></div>
        </article>
        <article class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
            <h3 class="font-extrabold text-slate-800">Tình hình lịch khám</h3><p class="text-xs text-slate-400 mt-1">Tỷ lệ theo trạng thái hiện tại</p>
            <div class="space-y-5 mt-7">';

            $totalStatus = array_sum($statusValues);
            $statusBarColors = [
                'cho_xac_nhan' => 'bg-amber-500',
                'da_xac_nhan' => 'bg-blue-500',
                'da_kham' => 'bg-emerald-500',
                'da_huy' => 'bg-red-500'
            ];
            foreach ($statusLabelsMap as $key => $label) {
                $count = $statusTotals[$key] ?? 0;
                $percentage = $totalStatus > 0 ? round($count / $totalStatus * 100, 1) : 0;
                $barColor = $statusBarColors[$key];
                $content .= '
                <div>
                    <div class="flex justify-between items-center mb-2"><span class="text-sm font-semibold text-slate-600">' . $this->e($label) . '</span><span class="text-sm font-bold text-slate-800">' . number_format($count) . ' (' . $percentage . '%)</span></div>
                    <div class="w-full h-2.5 rounded-full bg-slate-100 overflow-hidden"><div class="h-full rounded-full ' . $barColor . '" style="width:' . $percentage . '%"></div></div>
                </div>';
            }

            $content .= '
            </div>
            <div class="mt-8 pt-5 border-t border-slate-100 grid grid-cols-2 gap-4">
                <div><p class="text-xs text-slate-400">Lịch khám năm ' . $currentYear . '</p><p class="text-xl font-black text-slate-800 mt-1">' . number_format(array_sum($monthlyData)) . '</p></div>
                <div><p class="text-xs text-slate-400">Tổng lịch khám</p><p class="text-xl font-black text-slate-800 mt-1">' . number_format($totalStatus) . '</p></div>
            </div>
        </article>
    </section>

    <section class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 flex flex-col sm:flex-row justify-between sm:items-center gap-3"><div><h3 class="font-extrabold text-slate-800">Lịch khám mới nhất</h3><p class="text-xs text-slate-400 mt-1">Các lịch hẹn được tạo gần đây</p></div><a href="index.php?act=admin-lich-kham" class="text-sm font-bold text-emerald-600 hover:text-emerald-700">Xem tất cả <i class="fa-solid fa-arrow-right ml-1"></i></a></div>
        <div class="overflow-x-auto"><table class="w-full text-sm text-left whitespace-nowrap">
            <thead class="bg-slate-50 text-slate-500"><tr><th class="px-5 py-4">Mã lịch</th><th class="px-5 py-4">Bệnh nhân</th><th class="px-5 py-4">Bác sĩ</th><th class="px-5 py-4">Chuyên khoa</th><th class="px-5 py-4">Ngày khám</th><th class="px-5 py-4">Trạng thái</th></tr></thead><tbody>';

            $statusBadge = [
                'cho_xac_nhan' => 'bg-amber-50 text-amber-700',
                'da_xac_nhan' => 'bg-blue-50 text-blue-700',
                'da_kham' => 'bg-emerald-50 text-emerald-700',
                'da_huy' => 'bg-red-50 text-red-700'
            ];
            foreach ($recentRows as $row) {
                $status = (string)$row['trang_thai'];
                $badgeClass = $statusBadge[$status] ?? 'bg-slate-100 text-slate-600';
                $statusText = $statusLabelsMap[$status] ?? $status;
                $date = !empty($row['ngay_kham']) ? date('d/m/Y', strtotime($row['ngay_kham'])) : '—';
                $content .= '
                <tr class="border-t border-slate-100 hover:bg-slate-50 transition">
                    <td class="px-5 py-4 font-bold text-slate-700">#' . (int)$row['ma_lich_kham'] . '</td>
                    <td class="px-5 py-4 font-semibold text-slate-800">' . $this->e($row['ho_ten'] ?? 'Chưa có') . '</td>
                    <td class="px-5 py-4 text-slate-600">' . $this->e($row['ten_bac_si'] ?? 'Chưa phân công') . '</td>
                    <td class="px-5 py-4 text-slate-600">' . $this->e($row['ten_chuyen_khoa'] ?? 'Chưa có') . '</td>
                    <td class="px-5 py-4 text-slate-600">' . $this->e($date) . ' - ' . $this->e($row['gio_kham'] ?? '') . '</td>
                    <td class="px-5 py-4"><span class="inline-flex px-3 py-1.5 rounded-full text-xs font-bold ' . $badgeClass . '">' . $this->e($statusText) . '</span></td>
                </tr>';
            }
            if (!$recentRows) {
                $content .= '<tr><td colspan="6" class="px-5 py-10 text-center text-slate-400">Chưa có lịch khám nào.</td></tr>';
            }

            $jsonFlags = JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT;
            $monthlyJson = json_encode(array_values($monthlyData), $jsonFlags);
            $statusLabelsJson = json_encode($statusLabels, $jsonFlags);
            $statusValuesJson = json_encode($statusValues, $jsonFlags);
            $statusColorsJson = json_encode($statusColors, $jsonFlags);
            $specialtyLabelsJson = json_encode($specialtyLabels, $jsonFlags);
            $specialtyValuesJson = json_encode($specialtyValues, $jsonFlags);

            $content .= '
            </tbody></table></div>
    </section>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const monthlyData = ' . $monthlyJson . ';
    const statusLabels = ' . $statusLabelsJson . ';
    const statusValues = ' . $statusValuesJson . ';
    const statusColors = ' . $statusColorsJson . ';
    const specialtyLabels = ' . $specialtyLabelsJson . ';
    const specialtyValues = ' . $specialtyValuesJson . ';

    if (typeof Chart === "undefined") return;

    const monthlyCanvas = document.getElementById("adminMonthlyChart");
    if (monthlyCanvas) {
        new Chart(monthlyCanvas, {
            type: "bar",
            data: {
                labels: ["Tháng 1", "Tháng 2", "Tháng 3", "Tháng 4", "Tháng 5", "Tháng 6", "Tháng 7", "Tháng 8", "Tháng 9", "Tháng 10", "Tháng 11", "Tháng 12"],
                datasets: [{ label: "Số lịch khám", data: monthlyData, backgroundColor: "#3B82F6", borderRadius: 7, maxBarThickness: 36 }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: "#F1F5F9" } },
                    x: { grid: { display: false } }
                }
            }
        });
    }

    const statusCanvas = document.getElementById("adminStatusChart");
    if (statusCanvas) {
        new Chart(statusCanvas, {
            type: "doughnut",
            data: { labels: statusLabels, datasets: [{ data: statusValues, backgroundColor: statusColors, borderWidth: 3, borderColor: "#FFFFFF", hoverOffset: 7 }] },
            options: {
                responsive: true, maintainAspectRatio: false, cutout: "68%",
                plugins: { legend: { position: "bottom", labels: { usePointStyle: true, padding: 16, font: { size: 11 } } } }
            }
        });
    }

    const specialtyCanvas = document.getElementById("adminSpecialtyChart");
    if (specialtyCanvas) {
        new Chart(specialtyCanvas, {
            type: "bar",
            data: { labels: specialtyLabels, datasets: [{ label: "Lượt khám", data: specialtyValues, backgroundColor: "#10B981", borderRadius: 6, maxBarThickness: 34 }] },
            options: {
                indexAxis: "y", responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: "#F1F5F9" } },
                    y: { grid: { display: false } }
                }
            }
        });
    }
});
</script>';

            $this->render('Dashboard tổng quan', $content);
        } catch (Throwable $e) {
            error_log('Admin dashboard error: ' . $e->getMessage());
            $content = '<div class="bg-white border border-red-200 rounded-2xl p-6"><h2 class="text-lg font-bold text-red-600">Không thể tải Dashboard</h2><p class="text-sm text-slate-600 mt-2">Đã xảy ra lỗi khi lấy dữ liệu thống kê. Vui lòng kiểm tra cấu trúc database và nhật ký lỗi PHP.</p></div>';
            $this->render('Dashboard tổng quan', $content);
        }
    }

    /* =====================================================
       2. QUẢN LÝ CHUYÊN KHOA
    ===================================================== */

    public function quanLyChuyenKhoa(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->verifyPost();

            try {
                $id = (int)($_POST['id'] ?? 0);
                $name = trim($_POST['ten_chuyen_khoa'] ?? '');

                if ($name === '') {
                    throw new RuntimeException(
                        'Vui lòng nhập tên chuyên khoa.'
                    );
                }

                $old = null;

                if ($id) {
                    $q = $this->db->prepare(
                        "SELECT * FROM chuyen_khoa WHERE ma_chuyen_khoa=?"
                    );
                    $q->execute([$id]);
                    $old = $q->fetch(PDO::FETCH_ASSOC);

                    if (!$old) {
                        throw new RuntimeException(
                            'Không tìm thấy chuyên khoa.'
                        );
                    }
                }

                $image = $this->uploadImage(
                    'hinh_anh',
                    'chuyen_khoa',
                    $old['hinh_anh'] ?? null
                );

                $values = [
                    $name,
                    trim($_POST['mo_ta'] ?? ''),
                    trim($_POST['gioi_thieu_chi_tiet'] ?? ''),
                    $image
                ];

                if ($id) {
                    $q = $this->db->prepare(
                        "UPDATE chuyen_khoa
                         SET ten_chuyen_khoa=?,mo_ta=?,
                             gioi_thieu_chi_tiet=?,hinh_anh=?
                         WHERE ma_chuyen_khoa=?"
                    );

                    $q->execute(array_merge($values, [$id]));

                    if ($old['hinh_anh'] !== $image) {
                        $this->deleteImage($old['hinh_anh']);
                    }

                    $this->flash('Cập nhật chuyên khoa thành công.');
                } else {
                    $q = $this->db->prepare(
                        "INSERT INTO chuyen_khoa
                         (ten_chuyen_khoa,mo_ta,gioi_thieu_chi_tiet,hinh_anh)
                         VALUES (?,?,?,?)"
                    );

                    $q->execute($values);

                    $this->flash('Thêm chuyên khoa thành công.');
                }
            } catch (Throwable $e) {
                $this->flash($e->getMessage(), 'error');
            }

            $this->redirect('admin-chuyen-khoa');
        }

        $edit = null;

        if (isset($_GET['edit'])) {
            $q = $this->db->prepare(
                "SELECT * FROM chuyen_khoa WHERE ma_chuyen_khoa=?"
            );
            $q->execute([(int)$_GET['edit']]);
            $edit = $q->fetch(PDO::FETCH_ASSOC) ?: null;
        }

        $search = trim($_GET['q'] ?? '');

        $q = $this->db->prepare(
            "SELECT * FROM chuyen_khoa
             WHERE ten_chuyen_khoa LIKE ?
             ORDER BY ma_chuyen_khoa DESC"
        );

        $q->execute(['%' . $search . '%']);
        $rows = $q->fetchAll(PDO::FETCH_ASSOC);

        $content = '
<form method="GET" class="mb-5 flex gap-2">
<input type="hidden" name="act" value="admin-chuyen-khoa">'
            . $this->input('q', 'Tìm tên chuyên khoa...', $search)
            . '<button class="bg-slate-800 text-white px-5 rounded-xl">
Tìm
</button>
</form>';

        $content .= '<div class="flex justify-end mb-4"><a href="index.php?act=admin-chuyen-khoa&add=1" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-5 py-3 rounded-xl shadow-sm">+ Thêm chuyên khoa</a></div>';

        $content .= '
<dialog id="specialtyDialog" class="w-full max-w-2xl rounded-2xl p-0 backdrop:bg-slate-900/50 shadow-2xl"><div class="flex justify-end px-6 pt-4"><button type="button" onclick="document.getElementById(\'specialtyDialog\').close()" class="text-slate-400 hover:text-slate-700 text-2xl">&times;</button></div>';

        $content .= '
<form method="POST" enctype="multipart/form-data"
class="bg-white p-6 rounded-2xl space-y-4">'
            . $this->csrfField()
            . '<input type="hidden" name="id" value="'
            . (int)($edit['ma_chuyen_khoa'] ?? 0) . '">

<h2 class="font-bold">'
            . ($edit ? 'Chỉnh sửa chuyên khoa' : 'Thêm chuyên khoa mới')
            . '</h2>'
            . $this->input(
                'ten_chuyen_khoa',
                'Tên chuyên khoa',
                $edit['ten_chuyen_khoa'] ?? '',
                'text',
                true
            )
            . $this->textarea(
                'mo_ta',
                'Mô tả ngắn',
                $edit['mo_ta'] ?? ''
            )
            . $this->textarea(
                'gioi_thieu_chi_tiet',
                'Giới thiệu chi tiết',
                $edit['gioi_thieu_chi_tiet'] ?? ''
            )
            . '<div>
<label class="block text-sm mb-2">Ảnh chuyên khoa</label>
<input type="file" name="hinh_anh"
accept="image/jpeg,image/png,image/webp">
</div>'
            . $this->button(
                $edit ? 'Lưu thay đổi' : 'Thêm chuyên khoa'
            );

        if ($edit) {
            $content .= '
<a href="index.php?act=admin-chuyen-khoa"
class="text-slate-500 text-sm ml-3">Hủy sửa</a>';
        }

        $content .= '</form></dialog>
<div class="bg-white rounded-2xl border border-slate-100
shadow-sm overflow-x-auto">
<table class="w-full text-sm text-left">
<thead class="bg-slate-50">
<tr>
<th class="p-4">Ảnh</th>
<th class="p-4">Tên chuyên khoa</th>
<th class="p-4">Mô tả</th>
<th class="p-4">Thao tác</th>
</tr>
</thead>
<tbody>';

        foreach ($rows as $r) {
            $id = (int)$r['ma_chuyen_khoa'];

            $content .= '<tr class="border-t">
<td class="p-4">' . $this->imageHtml($r['hinh_anh']) . '</td>
<td class="p-4 font-bold">'
                . $this->e($r['ten_chuyen_khoa']) . '</td>
<td class="p-4">' . $this->e($r['mo_ta']) . '</td>
<td class="p-4 whitespace-nowrap">

<a class="text-blue-600 font-bold mr-3"
href="index.php?act=admin-chuyen-khoa&edit=' . $id . '">
Sửa
</a>

<form method="POST"
action="index.php?act=admin-xoa-chuyen-khoa"
class="inline"
onsubmit="return confirm(\'Bạn chắc chắn muốn xóa?\')">'
                . $this->csrfField()
                . '<input type="hidden" name="id" value="' . $id . '">
<button class="text-red-600 font-bold">Xóa</button>
</form>

</td></tr>';
        }

        $content .= '</tbody></table></div><script>const specialtyParams = new URLSearchParams(window.location.search); if (specialtyParams.has("edit") || specialtyParams.has("add")) { const dialog = document.getElementById("specialtyDialog"); if (dialog && !dialog.open) dialog.showModal(); }</script>';

        $this->render('Quản lý chuyên khoa', $content);
    }

    public function xoaChuyenKhoa(): void
    {
        $this->verifyPost();

        $id = (int)($_POST['id'] ?? 0);

        try {
            foreach ([
                'bac_si' => 'bác sĩ',
                'goi_kham' => 'gói khám'
            ] as $table => $label) {
                $q = $this->db->prepare(
                    "SELECT COUNT(*) FROM {$table} WHERE ma_chuyen_khoa=?"
                );
                $q->execute([$id]);

                if ((int)$q->fetchColumn() > 0) {
                    throw new RuntimeException(
                        "Không thể xóa vì chuyên khoa vẫn có {$label}."
                    );
                }
            }

            $q = $this->db->prepare(
                "SELECT hinh_anh FROM chuyen_khoa WHERE ma_chuyen_khoa=?"
            );
            $q->execute([$id]);
            $old = $q->fetch(PDO::FETCH_ASSOC);

            $q = $this->db->prepare(
                "DELETE FROM chuyen_khoa WHERE ma_chuyen_khoa=?"
            );
            $q->execute([$id]);

            if ($old) {
                $this->deleteImage($old['hinh_anh']);
            }

            $this->flash('Đã xóa chuyên khoa.');
        } catch (Throwable $e) {
            $this->flash($e->getMessage(), 'error');
        }

        $this->redirect('admin-chuyen-khoa');
    }

    /* =====================================================
       3. QUẢN LÝ BÁC SĨ
    ===================================================== */

    public function quanLyBacSi(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->verifyPost();

            try {
                $id = (int)($_POST['id'] ?? 0);
                $name = trim($_POST['ten_bac_si'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $phone = trim($_POST['so_dien_thoai'] ?? '');
                $specialty = (int)($_POST['ma_chuyen_khoa'] ?? 0);
                $degree = trim($_POST['bang_cap'] ?? '');
                $experience = (int)($_POST['so_nam_kinh_nghiem'] ?? 0);
                $intro = trim($_POST['gioi_thieu'] ?? '');
                $fee = (float)($_POST['phi_kham'] ?? 0);

                if (
                    $name === '' ||
                    !filter_var($email, FILTER_VALIDATE_EMAIL) ||
                    $specialty < 1 ||
                    $experience < 0 ||
                    $fee < 0
                ) {
                    throw new RuntimeException(
                        'Thông tin bác sĩ chưa hợp lệ.'
                    );
                }

                $q = $this->db->prepare(
                    "SELECT COUNT(*) FROM chuyen_khoa WHERE ma_chuyen_khoa=?"
                );
                $q->execute([$specialty]);

                if (!(int)$q->fetchColumn()) {
                    throw new RuntimeException(
                        'Chuyên khoa không tồn tại.'
                    );
                }

                if ($id) {
                    $q = $this->db->prepare(
                        "SELECT bs.*,nd.ma_nguoi_dung
                         FROM bac_si bs
                         JOIN nguoi_dung nd
                         ON nd.ma_nguoi_dung=bs.ma_nguoi_dung
                         WHERE bs.ma_bac_si=?"
                    );
                    $q->execute([$id]);
                    $old = $q->fetch(PDO::FETCH_ASSOC);

                    if (!$old) {
                        throw new RuntimeException(
                            'Không tìm thấy bác sĩ.'
                        );
                    }

                    $q = $this->db->prepare(
                        "SELECT COUNT(*) FROM nguoi_dung
                         WHERE email=? AND ma_nguoi_dung<>?"
                    );
                    $q->execute([$email, $old['ma_nguoi_dung']]);

                    if ((int)$q->fetchColumn()) {
                        throw new RuntimeException(
                            'Email đã được sử dụng.'
                        );
                    }

                    $image = $this->uploadImage(
                        'hinh_anh',
                        'bac_si',
                        $old['hinh_anh']
                    );

                    $this->db->beginTransaction();

                    $q = $this->db->prepare(
                        "UPDATE nguoi_dung
                         SET ho_ten=?,email=?,so_dien_thoai=?
                         WHERE ma_nguoi_dung=?"
                    );
                    $q->execute([
                        $name,
                        $email,
                        $phone,
                        $old['ma_nguoi_dung']
                    ]);

                    $q = $this->db->prepare(
                        "UPDATE bac_si
                         SET ma_chuyen_khoa=?,ten_bac_si=?,bang_cap=?,
                             so_nam_kinh_nghiem=?,gioi_thieu=?,
                             phi_kham=?,hinh_anh=?
                         WHERE ma_bac_si=?"
                    );

                    $q->execute([
                        $specialty,
                        $name,
                        $degree,
                        $experience,
                        $intro,
                        $fee,
                        $image,
                        $id
                    ]);

                    $this->db->commit();

                    if ($old['hinh_anh'] !== $image) {
                        $this->deleteImage($old['hinh_anh']);
                    }

                    $this->flash('Cập nhật bác sĩ thành công.');
                } else {
                    $password = $_POST['mat_khau'] ?? '';

                    if (strlen($password) < 6) {
                        throw new RuntimeException(
                            'Mật khẩu phải có ít nhất 6 ký tự.'
                        );
                    }

                    $q = $this->db->prepare(
                        "SELECT COUNT(*) FROM nguoi_dung WHERE email=?"
                    );
                    $q->execute([$email]);

                    if ((int)$q->fetchColumn()) {
                        throw new RuntimeException(
                            'Email đã tồn tại.'
                        );
                    }

                    $image = $this->uploadImage('hinh_anh', 'bac_si');

                    $this->db->beginTransaction();

                    $q = $this->db->prepare(
                        "INSERT INTO nguoi_dung
                         (ho_ten,email,mat_khau,so_dien_thoai,vai_tro)
                         VALUES (?,?,?,?, 'bac_si')"
                    );

                    $q->execute([
                        $name,
                        $email,
                        password_hash($password, PASSWORD_DEFAULT),
                        $phone
                    ]);

                    $userId = (int)$this->db->lastInsertId();

                    $q = $this->db->prepare(
                        "INSERT INTO bac_si
                         (ma_nguoi_dung,ma_chuyen_khoa,ten_bac_si,
                          bang_cap,so_nam_kinh_nghiem,gioi_thieu,
                          phi_kham,hinh_anh)
                         VALUES (?,?,?,?,?,?,?,?)"
                    );

                    $q->execute([
                        $userId,
                        $specialty,
                        $name,
                        $degree,
                        $experience,
                        $intro,
                        $fee,
                        $image
                    ]);

                    $this->db->commit();

                    $this->flash(
                        'Thêm bác sĩ và tài khoản thành công.'
                    );
                }
            } catch (Throwable $e) {
                if ($this->db->inTransaction()) {
                    $this->db->rollBack();
                }

                $this->flash($e->getMessage(), 'error');
            }

            $this->redirect('admin-bac-si');
        }

        $edit = null;

        if (isset($_GET['edit'])) {
            $q = $this->db->prepare(
                "SELECT bs.*,nd.email,nd.so_dien_thoai
                 FROM bac_si bs
                 JOIN nguoi_dung nd
                 ON nd.ma_nguoi_dung=bs.ma_nguoi_dung
                 WHERE bs.ma_bac_si=?"
            );
            $q->execute([(int)$_GET['edit']]);
            $edit = $q->fetch(PDO::FETCH_ASSOC) ?: null;
        }

        $specialties = $this->db->query(
            "SELECT ma_chuyen_khoa,ten_chuyen_khoa
             FROM chuyen_khoa ORDER BY ten_chuyen_khoa"
        )->fetchAll(PDO::FETCH_ASSOC);

        $search = trim($_GET['q'] ?? '');

        $q = $this->db->prepare(
            "SELECT bs.*,ck.ten_chuyen_khoa,
                    nd.email,nd.so_dien_thoai
             FROM bac_si bs
             JOIN chuyen_khoa ck
                ON ck.ma_chuyen_khoa=bs.ma_chuyen_khoa
             JOIN nguoi_dung nd
                ON nd.ma_nguoi_dung=bs.ma_nguoi_dung
             WHERE bs.ten_bac_si LIKE ?
             ORDER BY bs.ma_bac_si DESC"
        );

        $q->execute(['%' . $search . '%']);
        $rows = $q->fetchAll(PDO::FETCH_ASSOC);

        $content = '<form method="GET" class="mb-5 flex gap-2">
<input type="hidden" name="act" value="admin-bac-si">'
            . $this->input('q', 'Tìm tên bác sĩ...', $search)
            . '<button class="bg-slate-800 text-white px-5 rounded-xl">
Tìm
</button></form>';

        $content .= '<div class="flex justify-end mb-4"><a href="index.php?act=admin-bac-si&add=1" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-5 py-3 rounded-xl shadow-sm">+ Thêm bác sĩ</a></div>';

        $content .= '<dialog id="doctorDialog" class="w-full max-w-2xl rounded-2xl p-0 backdrop:bg-slate-900/50 shadow-2xl"><div class="flex justify-end px-6 pt-4"><button type="button" onclick="document.getElementById(\'doctorDialog\').close()" class="text-slate-400 hover:text-slate-700 text-2xl">&times;</button></div>';

        $content .= '<form method="POST" enctype="multipart/form-data"
autocomplete="off" class="bg-white p-6 rounded-2xl space-y-4">'
            . $this->csrfField()
            . '<input type="text" name="fake_username" autocomplete="username" tabindex="-1" aria-hidden="true" style="position:absolute;left:-9999px;width:1px;height:1px;opacity:0;">'
            . '<input type="password" name="fake_password" autocomplete="current-password" tabindex="-1" aria-hidden="true" style="position:absolute;left:-9999px;width:1px;height:1px;opacity:0;">'
            . '<input type="hidden" name="id" value="'
            . (int)($edit['ma_bac_si'] ?? 0) . '">'
            . '<h2 class="font-bold">'
            . ($edit ? 'Chỉnh sửa bác sĩ' : 'Thêm bác sĩ mới')
            . '</h2>'
            . $this->input(
                'ten_bac_si',
                'Họ tên bác sĩ',
                $edit['ten_bac_si'] ?? '',
                'text',
                true
            )
            . $this->input(
                'email',
                'Email đăng nhập',
                $edit['email'] ?? '',
                'email',
                true
            )
            . $this->input(
                'so_dien_thoai',
                'Số điện thoại',
                $edit['so_dien_thoai'] ?? ''
            )
            . ($edit ? '' : $this->input(
                'mat_khau',
                'Mật khẩu đăng nhập',
                '',
                'password',
                true
            ))
            . '<select name="ma_chuyen_khoa" required
class="w-full p-3 border rounded-xl text-sm">
<option value="">-- Chọn chuyên khoa --</option>';

        foreach ($specialties as $sp) {
            $selected = (int)($edit['ma_chuyen_khoa'] ?? 0)
                === (int)$sp['ma_chuyen_khoa']
                ? 'selected' : '';

            $content .= '<option value="'
                . (int)$sp['ma_chuyen_khoa'] . '" '
                . $selected . '>'
                . $this->e($sp['ten_chuyen_khoa'])
                . '</option>';
        }

        $content .= '</select>'
            . $this->input(
                'bang_cap',
                'Bằng cấp',
                $edit['bang_cap'] ?? ''
            )
            . $this->input(
                'so_nam_kinh_nghiem',
                'Số năm kinh nghiệm',
                (string)($edit['so_nam_kinh_nghiem'] ?? '0'),
                'number'
            )
            . $this->input(
                'phi_kham',
                'Phí khám (VNĐ)',
                (string)($edit['phi_kham'] ?? '0'),
                'number'
            )
            . $this->textarea(
                'gioi_thieu',
                'Giới thiệu bác sĩ',
                $edit['gioi_thieu'] ?? ''
            )
            . '<div>
<label class="block text-sm mb-2">Ảnh bác sĩ</label>
<input type="file" name="hinh_anh"
accept="image/jpeg,image/png,image/webp">
</div>'
            . $this->button(
                $edit ? 'Lưu thay đổi' : 'Thêm bác sĩ'
            );

        if ($edit) {
            $content .= '
<a href="index.php?act=admin-bac-si"
class="text-slate-500 text-sm ml-3">Hủy sửa</a>';
        }

        $content .= '</form></dialog>
<div class="bg-white rounded-2xl border border-slate-100
shadow-sm overflow-x-auto">
<table class="w-full text-sm text-left">
<thead class="bg-slate-50">
<tr>
<th class="p-4">Ảnh</th>
<th class="p-4">Bác sĩ</th>
<th class="p-4">Chuyên khoa</th>
<th class="p-4">Email</th>
<th class="p-4">Phí khám</th>
<th class="p-4">Thao tác</th>
</tr>
</thead>
<tbody>';

        foreach ($rows as $r) {
            $id = (int)$r['ma_bac_si'];

            $content .= '<tr class="border-t">
<td class="p-4">' . $this->imageHtml($r['hinh_anh']) . '</td>
<td class="p-4 font-bold">' . $this->e($r['ten_bac_si']) . '</td>
<td class="p-4">' . $this->e($r['ten_chuyen_khoa']) . '</td>
<td class="p-4">' . $this->e($r['email']) . '</td>
<td class="p-4">' . number_format((float)$r['phi_kham']) . ' đ</td>
<td class="p-4 whitespace-nowrap">
<a class="text-blue-600 font-bold mr-3"
href="index.php?act=admin-bac-si&edit=' . $id . '">Sửa</a>

<form method="POST" action="index.php?act=admin-xoa-bac-si"
class="inline"
onsubmit="return confirm(\'Xóa bác sĩ này?\')">'
                . $this->csrfField()
                . '<input type="hidden" name="id" value="' . $id . '">
<button class="text-red-600 font-bold">Xóa</button>
</form>
</td></tr>';
        }

        $content .= '</tbody></table></div><script>
(function () {
    const params = new URLSearchParams(window.location.search);
    const dialog = document.getElementById("doctorDialog");
    if ((params.has("edit") || params.has("add")) && dialog && !dialog.open) {
        dialog.showModal();
    }

    const form = dialog ? dialog.querySelector("form") : null;
    if (!form) return;

    form.setAttribute("autocomplete", "off");
    const email = form.querySelector("input[name=\"email\"]");
    const password = form.querySelector("input[name=\"mat_khau\"]");

    if (email) {
        email.setAttribute("autocomplete", "new-email");
        email.setAttribute("readonly", "readonly");
        email.addEventListener("focus", function () {
            this.removeAttribute("readonly");
        }, { once: true });
    }

    if (password) {
        password.setAttribute("autocomplete", "new-password");
        password.setAttribute("readonly", "readonly");
        password.addEventListener("focus", function () {
            this.removeAttribute("readonly");
        }, { once: true });
    }
})();
</script>';

        $this->render('Quản lý bác sĩ', $content);
    }

    public function xoaBacSi(): void
    {
        $this->verifyPost();

        $id = (int)($_POST['id'] ?? 0);

        try {
            $q = $this->db->prepare(
                "SELECT ma_nguoi_dung,hinh_anh
                 FROM bac_si WHERE ma_bac_si=?"
            );
            $q->execute([$id]);
            $doctor = $q->fetch(PDO::FETCH_ASSOC);

            if (!$doctor) {
                throw new RuntimeException('Không tìm thấy bác sĩ.');
            }

            $q = $this->db->prepare(
                "SELECT COUNT(*) FROM lich_kham WHERE ma_bac_si=?"
            );
            $q->execute([$id]);

            if ((int)$q->fetchColumn() > 0) {
                throw new RuntimeException(
                    'Bác sĩ đã có lịch khám, không thể xóa.'
                );
            }

            $this->db->beginTransaction();

            $q = $this->db->prepare(
                "DELETE FROM bac_si WHERE ma_bac_si=?"
            );
            $q->execute([$id]);

            $q = $this->db->prepare(
                "DELETE FROM nguoi_dung
                 WHERE ma_nguoi_dung=? AND vai_tro='bac_si'"
            );
            $q->execute([$doctor['ma_nguoi_dung']]);

            $this->db->commit();

            $this->deleteImage($doctor['hinh_anh']);

            $this->flash('Đã xóa bác sĩ và tài khoản liên quan.');
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            $this->flash($e->getMessage(), 'error');
        }

        $this->redirect('admin-bac-si');
    }

    /* =====================================================
       4. QUẢN LÝ GÓI KHÁM
    ===================================================== */

    public function quanLyGoiKham(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->verifyPost();

            try {
                $id = (int)($_POST['id'] ?? 0);
                $name = trim($_POST['ten_goi'] ?? '');

                $specialty = ($_POST['ma_chuyen_khoa'] ?? '') !== ''
                    ? (int)$_POST['ma_chuyen_khoa']
                    : null;

                $price = (float)($_POST['gia'] ?? 0);

                $duration = ($_POST['thoi_gian_du_kien'] ?? '') !== ''
                    ? (int)$_POST['thoi_gian_du_kien']
                    : null;

                $status = $_POST['trang_thai'] ?? 'dang_hoat_dong';

                if (
                    $name === '' ||
                    $price < 0 ||
                    !in_array(
                        $status,
                        ['dang_hoat_dong', 'ngung_hoat_dong'],
                        true
                    )
                ) {
                    throw new RuntimeException(
                        'Thông tin gói khám không hợp lệ.'
                    );
                }

                $old = null;

                if ($id) {
                    $q = $this->db->prepare(
                        "SELECT * FROM goi_kham WHERE ma_goi_kham=?"
                    );
                    $q->execute([$id]);
                    $old = $q->fetch(PDO::FETCH_ASSOC);

                    if (!$old) {
                        throw new RuntimeException(
                            'Không tìm thấy gói khám.'
                        );
                    }
                }

                $image = $this->uploadImage(
                    'hinh_anh',
                    'goi_kham',
                    $old['hinh_anh'] ?? null
                );

                $values = [
                    $name,
                    $specialty,
                    trim($_POST['mo_ta'] ?? ''),
                    trim($_POST['quy_trinh'] ?? ''),
                    $price,
                    $duration,
                    $image,
                    $status
                ];

                if ($id) {
                    $q = $this->db->prepare(
                        "UPDATE goi_kham
                         SET ten_goi=?,ma_chuyen_khoa=?,mo_ta=?,
                             quy_trinh=?,gia=?,thoi_gian_du_kien=?,
                             hinh_anh=?,trang_thai=?
                         WHERE ma_goi_kham=?"
                    );

                    $q->execute(array_merge($values, [$id]));

                    if ($old['hinh_anh'] !== $image) {
                        $this->deleteImage($old['hinh_anh']);
                    }

                    $this->flash('Cập nhật gói khám thành công.');
                } else {
                    $q = $this->db->prepare(
                        "INSERT INTO goi_kham
                         (ten_goi,ma_chuyen_khoa,mo_ta,quy_trinh,
                          gia,thoi_gian_du_kien,hinh_anh,trang_thai)
                         VALUES (?,?,?,?,?,?,?,?)"
                    );

                    $q->execute($values);

                    $this->flash('Thêm gói khám thành công.');
                }
            } catch (Throwable $e) {
                $this->flash($e->getMessage(), 'error');
            }

            $this->redirect('admin-goi-kham');
        }

        $edit = null;

        if (isset($_GET['edit'])) {
            $q = $this->db->prepare(
                "SELECT * FROM goi_kham WHERE ma_goi_kham=?"
            );
            $q->execute([(int)$_GET['edit']]);
            $edit = $q->fetch(PDO::FETCH_ASSOC) ?: null;
        }

        $specialties = $this->db->query(
            "SELECT ma_chuyen_khoa,ten_chuyen_khoa
             FROM chuyen_khoa ORDER BY ten_chuyen_khoa"
        )->fetchAll(PDO::FETCH_ASSOC);

        $search = trim($_GET['q'] ?? '');

        $q = $this->db->prepare(
            "SELECT g.*,c.ten_chuyen_khoa
             FROM goi_kham g
             LEFT JOIN chuyen_khoa c
             ON c.ma_chuyen_khoa=g.ma_chuyen_khoa
             WHERE g.ten_goi LIKE ?
             ORDER BY g.ma_goi_kham DESC"
        );
        $q->execute(['%' . $search . '%']);
        $rows = $q->fetchAll(PDO::FETCH_ASSOC);

        $content = '<form method="GET" class="mb-5 flex gap-2">
<input type="hidden" name="act" value="admin-goi-kham">'
            . $this->input('q', 'Tìm tên gói khám...', $search)
            . '<button class="bg-slate-800 text-white px-5 rounded-xl">
Tìm
</button></form>';

        $content .= '<div class="flex justify-end mb-4"><a href="index.php?act=admin-goi-kham&add=1" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-5 py-3 rounded-xl shadow-sm">+ Thêm gói khám</a></div>';

        $content .= '<dialog id="packageDialog" class="w-full max-w-2xl rounded-2xl p-0 backdrop:bg-slate-900/50 shadow-2xl"><div class="flex justify-end px-6 pt-4"><button type="button" onclick="document.getElementById(\'packageDialog\').close()" class="text-slate-400 hover:text-slate-700 text-2xl">&times;</button></div>';

        $content .= '<form method="POST" enctype="multipart/form-data"
class="bg-white p-6 rounded-2xl space-y-4">'
            . $this->csrfField()
            . '<input type="hidden" name="id" value="'
            . (int)($edit['ma_goi_kham'] ?? 0) . '">'
            . '<h2 class="font-bold">'
            . ($edit ? 'Chỉnh sửa gói khám' : 'Thêm gói khám mới')
            . '</h2>'
            . $this->input(
                'ten_goi',
                'Tên gói khám',
                $edit['ten_goi'] ?? '',
                'text',
                true
            )
            . '<select name="ma_chuyen_khoa"
class="w-full p-3 border rounded-xl text-sm">
<option value="">-- Không chọn chuyên khoa --</option>';

        foreach ($specialties as $sp) {
            $selected = (int)($edit['ma_chuyen_khoa'] ?? 0)
                === (int)$sp['ma_chuyen_khoa']
                ? 'selected' : '';

            $content .= '<option value="'
                . (int)$sp['ma_chuyen_khoa'] . '" '
                . $selected . '>'
                . $this->e($sp['ten_chuyen_khoa'])
                . '</option>';
        }

        $content .= '</select>'
            . $this->input(
                'gia',
                'Giá gói khám (VNĐ)',
                (string)($edit['gia'] ?? ''),
                'number',
                true
            )
            . $this->input(
                'thoi_gian_du_kien',
                'Thời gian dự kiến (phút)',
                (string)($edit['thoi_gian_du_kien'] ?? ''),
                'number'
            )
            . $this->textarea(
                'mo_ta',
                'Mô tả gói khám',
                $edit['mo_ta'] ?? ''
            )
            . $this->textarea(
                'quy_trinh',
                'Quy trình khám',
                $edit['quy_trinh'] ?? ''
            )
            . '<select name="trang_thai"
class="w-full p-3 border rounded-xl text-sm">
<option value="dang_hoat_dong">Đang hoạt động</option>
<option value="ngung_hoat_dong" '
            . (($edit['trang_thai'] ?? '') === 'ngung_hoat_dong'
                ? 'selected' : '')
            . '>Ngừng hoạt động</option>
</select>

<div>
<label class="block text-sm mb-2">Ảnh gói khám</label>
<input type="file" name="hinh_anh"
accept="image/jpeg,image/png,image/webp">
</div>'
            . $this->button(
                $edit ? 'Lưu thay đổi' : 'Thêm gói khám'
            );

        if ($edit) {
            $content .= '
<a href="index.php?act=admin-goi-kham"
class="text-slate-500 text-sm ml-3">Hủy sửa</a>';
        }

        $content .= '</form></dialog>
<div class="bg-white rounded-2xl border border-slate-100
shadow-sm overflow-x-auto">
<table class="w-full text-sm text-left">
<thead class="bg-slate-50">
<tr>
<th class="p-4">Ảnh</th>
<th class="p-4">Tên gói</th>
<th class="p-4">Chuyên khoa</th>
<th class="p-4">Giá</th>
<th class="p-4">Trạng thái</th>
<th class="p-4">Thao tác</th>
</tr>
</thead>
<tbody>';

        foreach ($rows as $r) {
            $id = (int)$r['ma_goi_kham'];

            $content .= '<tr class="border-t">
<td class="p-4">' . $this->imageHtml($r['hinh_anh']) . '</td>
<td class="p-4 font-bold">' . $this->e($r['ten_goi']) . '</td>
<td class="p-4">'
                . $this->e($r['ten_chuyen_khoa'] ?? 'Chưa phân loại')
                . '</td>
<td class="p-4">'
                . number_format((float)$r['gia']) . ' đ</td>
<td class="p-4">' . $this->e($r['trang_thai']) . '</td>
<td class="p-4 whitespace-nowrap">
<a class="text-blue-600 font-bold mr-3"
href="index.php?act=admin-goi-kham&edit=' . $id . '">Sửa</a>

<form method="POST" action="index.php?act=admin-xoa-goi-kham"
class="inline"
onsubmit="return confirm(\'Xóa gói khám này?\')">'
                . $this->csrfField()
                . '<input type="hidden" name="id" value="' . $id . '">
<button class="text-red-600 font-bold">Xóa</button>
</form>
</td></tr>';
        }

        $content .= '</tbody></table></div><script>const packageParams = new URLSearchParams(window.location.search); if (packageParams.has("edit") || packageParams.has("add")) { const dialog = document.getElementById("packageDialog"); if (dialog && !dialog.open) dialog.showModal(); }</script>';

        $this->render('Quản lý gói khám', $content);
    }

    public function xoaGoiKham(): void
    {
        $this->verifyPost();

        $id = (int)($_POST['id'] ?? 0);

        try {
            $q = $this->db->prepare(
                "SELECT hinh_anh FROM goi_kham WHERE ma_goi_kham=?"
            );
            $q->execute([$id]);
            $old = $q->fetch(PDO::FETCH_ASSOC);

            if (!$old) {
                throw new RuntimeException(
                    'Không tìm thấy gói khám.'
                );
            }

            $q = $this->db->prepare(
                "DELETE FROM goi_kham WHERE ma_goi_kham=?"
            );
            $q->execute([$id]);

            $this->deleteImage($old['hinh_anh']);

            $this->flash('Đã xóa gói khám.');
        } catch (Throwable $e) {
            $this->flash($e->getMessage(), 'error');
        }

        $this->redirect('admin-goi-kham');
    }

    /* =====================================================
       5. QUẢN LÝ LỊCH KHÁM
    ===================================================== */

    public function quanLyLich(): void
    {
        $this->quanLyLichKham();
    }

    public function quanLyLichKham(): void
    {
        $search = trim($_GET['q'] ?? '');
        $status = $_GET['status'] ?? '';

        $valid = ['cho_xac_nhan', 'da_xac_nhan', 'da_kham', 'da_huy'];
        $sql = "SELECT lk.*, nd.ho_ten, nd.so_dien_thoai,
                       bs.ten_bac_si, ck.ten_chuyen_khoa,
                       pk.ma_phieu_kham
                FROM lich_kham lk
                LEFT JOIN nguoi_dung nd ON nd.ma_nguoi_dung = lk.ma_nguoi_dung
                LEFT JOIN bac_si bs ON bs.ma_bac_si = lk.ma_bac_si
                LEFT JOIN chuyen_khoa ck ON ck.ma_chuyen_khoa = lk.ma_chuyen_khoa
                LEFT JOIN phieu_kham_benh pk ON pk.ma_lich_kham = lk.ma_lich_kham
                WHERE (nd.ho_ten LIKE ? OR bs.ten_bac_si LIKE ?)";
        $params = ['%' . $search . '%', '%' . $search . '%'];

        if (in_array($status, $valid, true)) {
            $sql .= " AND lk.trang_thai = ?";
            $params[] = $status;
        }
        $sql .= " ORDER BY lk.ngay_kham DESC, lk.gio_kham DESC";

        $q = $this->db->prepare($sql);
        $q->execute($params);
        $rows = $q->fetchAll(PDO::FETCH_ASSOC);

        $labels = [
            'cho_xac_nhan' => 'Chờ xác nhận',
            'da_xac_nhan' => 'Đã xác nhận',
            'da_kham' => 'Đã khám',
            'da_huy' => 'Đã hủy'
        ];

        $content = $this->showFlash();
        $content .= '<form method="GET" class="bg-white p-4 rounded-2xl border border-slate-100 mb-5 flex flex-col md:flex-row gap-3">
            <input type="hidden" name="act" value="admin-lich-kham">'
            . $this->input('q', 'Tìm bệnh nhân hoặc bác sĩ...', $search)
            . '<select name="status" class="p-3 border rounded-xl text-sm">
                <option value="">Tất cả trạng thái</option>';
        foreach ($valid as $s) {
            $selected = $status === $s ? 'selected' : '';
            $content .= '<option value="' . $this->e($s) . '" ' . $selected . '>'
                . $this->e($labels[$s]) . '</option>';
        }
        $content .= '</select><button class="bg-slate-800 text-white px-5 rounded-xl">Lọc</button></form>';
        $content .= '<div class="space-y-5">';

        foreach ($rows as $r) {
            $id = (int)$r['ma_lich_kham'];
            $content .= '<article class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                <div class="space-y-2 text-sm">
                    <h3 class="font-extrabold text-slate-900 text-base">Lịch khám #' . $id . '</h3>
                    <p><b>Bệnh nhân:</b> ' . $this->e($r['ho_ten'] ?? '') . '</p>
                    <p><b>Số điện thoại:</b> ' . $this->e($r['so_dien_thoai'] ?? '') . '</p>
                    <p><b>Bác sĩ:</b> ' . $this->e($r['ten_bac_si'] ?? '') . '</p>
                    <p><b>Chuyên khoa:</b> ' . $this->e($r['ten_chuyen_khoa'] ?? '') . '</p>
                    <p><b>Ngày giờ:</b> ' . $this->e($r['ngay_kham'] ?? '') . ' - ' . $this->e($r['gio_kham'] ?? '') . '</p>
                    <p><b>Lý do khám:</b> ' . $this->e($r['ly_do_kham'] ?? '') . '</p>
                    <p><b>Phí khám:</b> ' . number_format((float)($r['phi_kham'] ?? 0)) . ' VNĐ</p>
                    <p><b>Trạng thái:</b> <span class="font-bold">' . $this->e($labels[$r['trang_thai']] ?? $r['trang_thai']) . '</span></p>';
            if (!empty($r['ma_phieu_kham'])) {
                $content .= '<p class="text-emerald-700 font-semibold">Đã có phiếu khám #' . (int)$r['ma_phieu_kham'] . '</p>';
            }
            $content .= '</div>';

            if ($r['trang_thai'] === 'cho_xac_nhan') {
                $content .= '<form method="POST" action="index.php?act=admin-duyet-lich" class="bg-slate-50 rounded-xl p-4 space-y-3">'
                    . $this->csrfField()
                    . '<input type="hidden" name="id" value="' . $id . '">
                    <h4 class="font-bold text-emerald-700">Xác nhận lịch và tạo phiếu khám</h4>
                    <label class="block text-xs font-semibold">Hướng dẫn chuẩn bị</label>
                    <textarea name="huong_dan_chuan_bi" rows="3" required class="w-full p-3 border rounded-xl text-sm">Vui lòng mang theo giấy tờ tùy thân, hồ sơ bệnh án, kết quả xét nghiệm và đơn thuốc cũ (nếu có). Có mặt trước giờ hẹn 15 phút.</textarea>
                    <label class="block text-xs font-semibold">Quy trình khám</label>
                    <textarea name="quy_trinh_kham" rows="4" required class="w-full p-3 border rounded-xl text-sm">
                    1. Tiếp nhận và kiểm tra thông tin đặt lịch.
                    2. Làm thủ tục tại quầy tiếp đón.
                    3. Gặp bác sĩ chuyên khoa.
                    4. Thực hiện xét nghiệm hoặc chẩn đoán hình ảnh nếu bác sĩ chỉ định.
                    5. Nhận kết luận và tư vấn sau khám.</textarea>
                    <label class="block text-xs font-semibold">Lưu ý riêng</label>
                    <textarea name="luu_y" rows="2" class="w-full p-3 border rounded-xl text-sm" placeholder="Nhập lưu ý riêng cho bệnh nhân nếu có..."></textarea>
                    <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 rounded-xl">Xác nhận lịch và gửi phiếu khám</button>
                    </form>';
            } elseif ($r['trang_thai'] === 'da_xac_nhan') {
                $content .= '<div class="space-y-3"><p class="text-emerald-700 font-semibold">Lịch đã xác nhận. Phiếu khám đã được tạo.</p>'
                    . $this->statusForm('admin-da-kham', $id, 'Đánh dấu đã khám', 'text-blue-600')
                    . $this->statusForm('admin-huy-lich', $id, 'Hủy lịch', 'text-red-600')
                    . '</div>';
            }
            if ($r['trang_thai'] === 'cho_xac_nhan') {
                $content .= '<div class="lg:col-span-2">' . $this->statusForm('admin-huy-lich', $id, 'Từ chối / Hủy lịch', 'text-red-600') . '</div>';
            }
            $content .= '</div></article>';
        }
        if (!$rows) {
            $content .= '<div class="bg-white rounded-xl p-8 text-center text-slate-400">Chưa có lịch khám phù hợp.</div>';
        }
        $content .= '</div>';
        $this->render('Quản lý lịch khám', $content);
    }

    private function updateAppointmentStatus(string $newStatus): void
    {
        $this->verifyPost();
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            $this->flash('Mã lịch khám không hợp lệ.', 'error');
            $this->redirect('admin-lich-kham');
        }

        $transitions = [
            'da_xac_nhan' => ['cho_xac_nhan'],
            'da_huy' => ['cho_xac_nhan', 'da_xac_nhan'],
            'da_kham' => ['da_xac_nhan']
        ];

        try {
            $this->db->beginTransaction();
            $q = $this->db->prepare(
                "SELECT lk.*, nd.ho_ten
                 FROM lich_kham lk
                 LEFT JOIN nguoi_dung nd ON nd.ma_nguoi_dung = lk.ma_nguoi_dung
                 WHERE lk.ma_lich_kham = ? FOR UPDATE"
            );
            $q->execute([$id]);
            $lich = $q->fetch(PDO::FETCH_ASSOC);
            if (!$lich) {
                throw new RuntimeException('Không tìm thấy lịch khám.');
            }
            if (!in_array($lich['trang_thai'], $transitions[$newStatus] ?? [], true)) {
                throw new RuntimeException('Không thể chuyển trạng thái lịch khám này.');
            }

            if ($newStatus === 'da_xac_nhan') {
                $huongDan = trim($_POST['huong_dan_chuan_bi'] ?? '');
                $quyTrinh = trim($_POST['quy_trinh_kham'] ?? '');
                $luuY = trim($_POST['luu_y'] ?? '');
                if ($huongDan === '' || $quyTrinh === '') {
                    throw new RuntimeException('Vui lòng nhập hướng dẫn chuẩn bị và quy trình khám.');
                }

                $q = $this->db->prepare(
                    "UPDATE lich_kham SET trang_thai='da_xac_nhan'
                     WHERE ma_lich_kham=? AND trang_thai='cho_xac_nhan'"
                );
                $q->execute([$id]);
                if ($q->rowCount() !== 1) {
                    throw new RuntimeException('Lịch khám đã được xử lý bởi thao tác khác.');
                }

                // ma_lich_kham có UNIQUE nên mỗi lịch chỉ có tối đa một phiếu.
                $q = $this->db->prepare(
                    "SELECT ma_phieu_kham FROM phieu_kham_benh WHERE ma_lich_kham=? LIMIT 1"
                );
                $q->execute([$id]);
                $existing = $q->fetch(PDO::FETCH_ASSOC);

                if ($existing) {
                    $q = $this->db->prepare(
                        "UPDATE phieu_kham_benh
                         SET huong_dan_chuan_bi=?, quy_trinh_kham=?, luu_y=?, trang_thai_phieu='da_gui'
                         WHERE ma_lich_kham=?"
                    );
                    $q->execute([$huongDan, $quyTrinh, $luuY, $id]);
                } else {
                    $q = $this->db->prepare(
                        "INSERT INTO phieu_kham_benh
                         (ma_lich_kham, chuan_doan, don_thuoc, loi_dan_bac_si,
                          ngay_tai_kham, huong_dan_chuan_bi, quy_trinh_kham,
                          luu_y, trang_thai_phieu)
                         VALUES (?, NULL, NULL, NULL, NULL, ?, ?, ?, 'da_gui')"
                    );
                    $q->execute([$id, $huongDan, $quyTrinh, $luuY]);
                }

                $ngay = !empty($lich['ngay_kham'])
                    ? date('d/m/Y', strtotime($lich['ngay_kham'])) : '';
                $noiDung = 'Lịch khám ngày ' . $ngay . ' lúc '
                    . ($lich['gio_kham'] ?? '')
                    . ' đã được xác nhận. Phiếu khám điện tử đã được tạo. '
                    . 'Vui lòng đăng nhập để xem thông tin và hướng dẫn chuẩn bị.';
                $q = $this->db->prepare(
                    "INSERT INTO thong_bao (ma_nguoi_dung, tieu_de, noi_dung, loai_thong_bao)
                     VALUES (?, ?, ?, ?)"
                );
                $q->execute([
                    $lich['ma_nguoi_dung'],
                    'Lịch khám đã được xác nhận',
                    $noiDung,
                    'lich_kham'
                ]);
            } else {
                $q = $this->db->prepare(
                    "UPDATE lich_kham SET trang_thai=? WHERE ma_lich_kham=?"
                );
                $q->execute([$newStatus, $id]);
                if ($newStatus === 'da_huy') {
                    $q = $this->db->prepare(
                        "INSERT INTO thong_bao (ma_nguoi_dung, tieu_de, noi_dung, loai_thong_bao)
                         VALUES (?, ?, ?, ?)"
                    );
                    $q->execute([
                        $lich['ma_nguoi_dung'],
                        'Lịch khám đã bị hủy',
                        'Lịch khám #' . $id . ' đã được hủy. Vui lòng liên hệ phòng khám nếu cần hỗ trợ.',
                        'lich_kham'
                    ]);
                }
                if ($newStatus === 'da_kham') {
                    $q = $this->db->prepare(
                        "UPDATE phieu_kham_benh SET trang_thai_phieu='da_kham' WHERE ma_lich_kham=?"
                    );
                    $q->execute([$id]);
                }
            }

            $this->db->commit();
            $this->flash($newStatus === 'da_xac_nhan'
                ? 'Đã xác nhận lịch, tạo phiếu khám và gửi thông báo cho bệnh nhân.'
                : 'Đã cập nhật trạng thái lịch khám.');
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log('Admin appointment update: ' . $e->getMessage());
            $this->flash($e->getMessage(), 'error');
        }
        $this->redirect('admin-lich-kham');
    }

    public function duyetLich(): void
    {
        $this->updateAppointmentStatus('da_xac_nhan');
    }

    public function huyLich(): void
    {
        $this->updateAppointmentStatus('da_huy');
    }

    public function daKham(): void
    {
        $this->updateAppointmentStatus('da_kham');
    }

    public function bacSiQuanLyLich(): void
    {
        $this->quanLyLichKham();
    }

    public function bacSiDuyetLich(): void
    {
        $this->duyetLich();
    }

    public function bacSiHuyLich(): void
    {
        $this->huyLich();
    }

    /* =====================================================
       6. QUẢN LÝ BỆNH NHÂN
    ===================================================== */

    public function quanLyBenhNhan(): void
    {
        if (($_GET['view'] ?? '') === 'ho-so-benh-an') {
            $this->quanLyHoSoBenhAn();
            return;
        }

        $search = trim($_GET['q'] ?? '');

        $q = $this->db->prepare(
            "SELECT nd.*,
                (SELECT COUNT(*)
                 FROM lich_kham lk
                 WHERE lk.ma_nguoi_dung=nd.ma_nguoi_dung)
                 AS tong_lich
             FROM nguoi_dung nd
             WHERE nd.vai_tro='benh_nhan'
             AND (
                nd.ho_ten LIKE ?
                OR nd.email LIKE ?
                OR nd.so_dien_thoai LIKE ?
             )
             ORDER BY nd.ma_nguoi_dung DESC"
        );

        $like = '%' . $search . '%';
        $q->execute([$like, $like, $like]);

        $rows = $q->fetchAll(PDO::FETCH_ASSOC);

        $content = '<form method="GET" class="mb-5 flex gap-2">
<input type="hidden" name="act" value="admin-benh-nhan">'
            . $this->input(
                'q',
                'Tìm tên, email hoặc số điện thoại...',
                $search
            )
            . '<button class="bg-slate-800 text-white px-5 rounded-xl">
Tìm
</button>
</form>

<div class="bg-white rounded-2xl border border-slate-100
shadow-sm overflow-x-auto">

<table class="w-full text-sm text-left">
<thead class="bg-slate-50">
<tr>
<th class="p-4">Mã</th>
<th class="p-4">Họ tên</th>
<th class="p-4">Email</th>
<th class="p-4">Điện thoại</th>
<th class="p-4">Giới tính</th>
<th class="p-4">Số lịch khám</th>
</tr>
</thead>
<tbody>';

        foreach ($rows as $r) {
            $content .= '<tr class="border-t">
<td class="p-4">#' . (int)$r['ma_nguoi_dung'] . '</td>
<td class="p-4 font-bold">'
                . $this->e($r['ho_ten']) . '</td>
<td class="p-4">' . $this->e($r['email']) . '</td>
<td class="p-4">' . $this->e($r['so_dien_thoai']) . '</td>
<td class="p-4">' . $this->e($r['gioi_tinh'] ?? '') . '</td>
<td class="p-4">' . (int)$r['tong_lich'] . '</td>
</tr>';
        }

        if (!$rows) {
            $content .= '<tr>
<td colspan="6" class="p-8 text-center text-slate-400">
Không tìm thấy bệnh nhân.
</td>
</tr>';
        }

        $content .= '</tbody></table></div>';

        $this->render('Quản lý bệnh nhân', $content);
    }

    /* =====================================================
       6B. QUẢN LÝ HỒ SƠ BỆNH ÁN
       Admin chỉ được tra cứu/xem, không chỉnh sửa kết quả chuyên môn.
    ===================================================== */

    private function quanLyHoSoBenhAn(): void
    {
        $id = (int)($_GET['id'] ?? 0);

        if ($id > 0) {
            $q = $this->db->prepare(
                "SELECT
                    pk.ma_phieu_kham,
                    pk.ma_lich_kham,
                    pk.chuan_doan,
                    pk.don_thuoc,
                    pk.loi_dan_bac_si,
                    pk.ngay_tai_kham,
                    pk.ngay_tao,
                    pk.huong_dan_chuan_bi,
                    pk.quy_trinh_kham,
                    pk.luu_y,
                    pk.trang_thai_phieu,
                    lk.ngay_kham,
                    lk.gio_kham,
                    lk.phi_kham,
                    lk.trang_thai AS trang_thai_lich,
                    nd.ma_nguoi_dung,
                    nd.ho_ten,
                    nd.email,
                    nd.so_dien_thoai,
                    nd.gioi_tinh,
                    nd.ngay_sinh,
                    nd.dia_chi,
                    bs.ten_bac_si,
                    ck.ten_chuyen_khoa
                 FROM phieu_kham_benh pk
                 INNER JOIN lich_kham lk
                    ON lk.ma_lich_kham = pk.ma_lich_kham
                 LEFT JOIN nguoi_dung nd
                    ON nd.ma_nguoi_dung = lk.ma_nguoi_dung
                 LEFT JOIN bac_si bs
                    ON bs.ma_bac_si = lk.ma_bac_si
                 LEFT JOIN chuyen_khoa ck
                    ON ck.ma_chuyen_khoa = lk.ma_chuyen_khoa
                 WHERE pk.ma_phieu_kham = ?
                 LIMIT 1"
            );
            $q->execute([$id]);
            $r = $q->fetch(PDO::FETCH_ASSOC);

            if (!$r) {
                $this->flash('Không tìm thấy hồ sơ bệnh án.', 'error');
                header('Location: index.php?act=admin-benh-nhan&view=ho-so-benh-an');
                exit;
            }

            $formatDate = static function ($date): string {
                return $date ? date('d/m/Y', strtotime($date)) : 'Chưa cập nhật';
            };
            $field = function (string $label, $value): string {
                $value = trim((string)($value ?? ''));
                return '<div class="bg-slate-50 rounded-xl p-4">'
                    . '<p class="text-xs font-semibold text-slate-500 mb-2">'
                    . $this->e($label) . '</p>'
                    . '<div class="text-sm text-slate-800 whitespace-pre-line break-words">'
                    . ($value !== '' ? nl2br($this->e($value)) : '<span class="text-slate-400">Chưa có thông tin</span>')
                    . '</div></div>';
            };

            $status = ($r['trang_thai_phieu'] ?? '') === 'da_kham'
                ? '<span class="inline-flex px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">Đã hoàn thành khám</span>'
                : '<span class="inline-flex px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700">Đã tạo / Chờ khám</span>';

            $content = '<div class="mb-5 flex flex-wrap items-center justify-between gap-3">'
                . '<a href="index.php?act=admin-benh-nhan&view=ho-so-benh-an" class="text-sm font-bold text-emerald-700">← Quay lại danh sách hồ sơ</a>'
                . $status . '</div>'
                . '<section class="bg-white border border-slate-100 rounded-2xl p-5 md:p-7 shadow-sm mb-6">'
                . '<div class="flex flex-wrap justify-between gap-3 mb-5">'
                . '<div><p class="text-xs text-slate-500">Mã hồ sơ</p><h2 class="text-2xl font-black text-slate-900">#'
                . (int)$r['ma_phieu_kham'] . '</h2></div>'
                . '<div class="text-sm text-slate-500">Ngày tạo: <b class="text-slate-800">'
                . $this->e($formatDate($r['ngay_tao'])) . '</b></div></div>'
                . '<h3 class="font-bold text-slate-900 mb-3">Thông tin bệnh nhân</h3>'
                . '<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">'
                . $field('Họ và tên', $r['ho_ten'])
                . $field('Mã bệnh nhân', '#' . $r['ma_nguoi_dung'])
                . $field('Email', $r['email'])
                . $field('Số điện thoại', $r['so_dien_thoai'])
                . $field('Giới tính', $r['gioi_tinh'])
                . $field('Ngày sinh', $formatDate($r['ngay_sinh']))
                . $field('Địa chỉ', $r['dia_chi'])
                . '</div></section>'
                . '<section class="bg-white border border-slate-100 rounded-2xl p-5 md:p-7 shadow-sm mb-6">'
                . '<h3 class="font-bold text-slate-900 mb-4">Thông tin lần khám</h3>'
                . '<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">'
                . $field('Mã lịch khám', '#' . $r['ma_lich_kham'])
                . $field('Ngày khám', $formatDate($r['ngay_kham']))
                . $field('Giờ khám', $r['gio_kham'])
                . $field('Bác sĩ phụ trách', $r['ten_bac_si'])
                . $field('Chuyên khoa', $r['ten_chuyen_khoa'])
                . $field('Phí khám', $r['phi_kham'] !== null ? number_format((float)$r['phi_kham'], 0, ',', '.') . ' đ' : '')
                . '</div></section>'
                . '<section class="bg-white border border-slate-100 rounded-2xl p-5 md:p-7 shadow-sm">'
                . '<h3 class="font-bold text-slate-900 mb-4">Nội dung hồ sơ bệnh án</h3>'
                . '<div class="grid grid-cols-1 md:grid-cols-2 gap-3">'
                . $field('Chẩn đoán của bác sĩ', $r['chuan_doan'])
                . $field('Đơn thuốc', $r['don_thuoc'])
                . $field('Lời dặn bác sĩ', $r['loi_dan_bac_si'])
                . $field('Ngày tái khám', $formatDate($r['ngay_tai_kham']))
                . $field('Hướng dẫn chuẩn bị', $r['huong_dan_chuan_bi'])
                . $field('Quy trình khám', $r['quy_trinh_kham'])
                . $field('Lưu ý', $r['luu_y'])
                . '</div><p class="text-xs text-slate-400 mt-5">Thông tin chuyên môn được lưu theo kết quả bác sĩ cập nhật. Admin chỉ xem và tra cứu hồ sơ.</p></section>';

            $this->render('Chi tiết hồ sơ bệnh án', $content);
            return;
        }

        $search = trim($_GET['q'] ?? '');
        $status = trim($_GET['status'] ?? '');
        $sql = "SELECT
                    pk.ma_phieu_kham,
                    pk.ma_lich_kham,
                    pk.ngay_tao,
                    pk.trang_thai_phieu,
                    lk.ngay_kham,
                    lk.gio_kham,
                    nd.ma_nguoi_dung,
                    nd.ho_ten,
                    nd.email,
                    nd.so_dien_thoai,
                    bs.ten_bac_si,
                    ck.ten_chuyen_khoa
                FROM phieu_kham_benh pk
                INNER JOIN lich_kham lk ON lk.ma_lich_kham=pk.ma_lich_kham
                LEFT JOIN nguoi_dung nd ON nd.ma_nguoi_dung=lk.ma_nguoi_dung
                LEFT JOIN bac_si bs ON bs.ma_bac_si=lk.ma_bac_si
                LEFT JOIN chuyen_khoa ck ON ck.ma_chuyen_khoa=lk.ma_chuyen_khoa
                WHERE (
                    nd.ho_ten LIKE ? OR nd.email LIKE ? OR nd.so_dien_thoai LIKE ?
                    OR CAST(nd.ma_nguoi_dung AS CHAR) LIKE ?
                    OR CAST(pk.ma_phieu_kham AS CHAR) LIKE ?
                )";
        $like = '%' . $search . '%';
        $params = [$like, $like, $like, $like, $like];
        if (in_array($status, ['da_gui', 'da_kham'], true)) {
            $sql .= ' AND pk.trang_thai_phieu = ?';
            $params[] = $status;
        }
        $sql .= ' ORDER BY pk.ngay_tao DESC, pk.ma_phieu_kham DESC';

        $q = $this->db->prepare($sql);
        $q->execute($params);
        $rows = $q->fetchAll(PDO::FETCH_ASSOC);

        $content = '<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">'
            . '<div class="bg-white rounded-2xl border border-slate-100 p-5"><p class="text-xs text-slate-500">Tổng hồ sơ tìm thấy</p><p class="text-3xl font-black text-slate-900 mt-2">'
            . count($rows) . '</p></div>'
            . '<div class="bg-white rounded-2xl border border-slate-100 p-5"><p class="text-xs text-slate-500">Đang chờ khám</p><p class="text-3xl font-black text-amber-600 mt-2">'
            . count(array_filter($rows, static fn($x) => ($x['trang_thai_phieu'] ?? '') === 'da_gui')) . '</p></div>'
            . '<div class="bg-white rounded-2xl border border-slate-100 p-5"><p class="text-xs text-slate-500">Đã khám</p><p class="text-3xl font-black text-emerald-600 mt-2">'
            . count(array_filter($rows, static fn($x) => ($x['trang_thai_phieu'] ?? '') === 'da_kham')) . '</p></div>'
            . '</div>'
            . '<form method="GET" class="bg-white border border-slate-100 rounded-2xl p-4 mb-5 flex flex-col md:flex-row gap-3">'
            . '<input type="hidden" name="act" value="admin-benh-nhan"><input type="hidden" name="view" value="ho-so-benh-an">'
            . '<input type="text" name="q" value="' . $this->e($search) . '" placeholder="Tìm tên, email, số điện thoại, mã bệnh nhân hoặc mã hồ sơ..." class="flex-1 p-3 border border-slate-200 rounded-xl text-sm">'
            . '<select name="status" class="p-3 border border-slate-200 rounded-xl text-sm">'
            . '<option value="">Tất cả trạng thái</option>'
            . '<option value="da_gui" ' . ($status === 'da_gui' ? 'selected' : '') . '>Đã tạo / Chờ khám</option>'
            . '<option value="da_kham" ' . ($status === 'da_kham' ? 'selected' : '') . '>Đã khám</option>'
            . '</select><button class="bg-slate-900 hover:bg-slate-700 text-white font-bold px-5 py-3 rounded-xl text-sm">Tìm kiếm</button>'
            . '</form>'
            . '<div class="bg-white border border-slate-100 rounded-2xl shadow-sm overflow-x-auto">'
            . '<table class="w-full text-sm text-left min-w-[900px]"><thead class="bg-slate-50 text-slate-500"><tr>'
            . '<th class="p-4">Mã hồ sơ</th><th class="p-4">Bệnh nhân</th><th class="p-4">Bác sĩ</th><th class="p-4">Chuyên khoa</th><th class="p-4">Ngày khám</th><th class="p-4">Trạng thái</th><th class="p-4">Thao tác</th>'
            . '</tr></thead><tbody>';

        foreach ($rows as $r) {
            $done = ($r['trang_thai_phieu'] ?? '') === 'da_kham';
            $badge = $done
                ? '<span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 text-xs font-bold">Đã khám</span>'
                : '<span class="px-3 py-1 rounded-full bg-amber-100 text-amber-700 text-xs font-bold">Chờ khám</span>';
            $date = !empty($r['ngay_kham']) ? date('d/m/Y', strtotime($r['ngay_kham'])) : '—';
            $content .= '<tr class="border-t hover:bg-slate-50">'
                . '<td class="p-4 font-bold">#' . (int)$r['ma_phieu_kham'] . '</td>'
                . '<td class="p-4"><div class="font-bold">' . $this->e($r['ho_ten'] ?? 'Chưa rõ') . '</div><div class="text-xs text-slate-500">' . $this->e($r['email'] ?? '') . '</div><div class="text-xs text-slate-500">Mã BN: #' . (int)$r['ma_nguoi_dung'] . '</div></td>'
                . '<td class="p-4">' . $this->e($r['ten_bac_si'] ?? 'Chưa phân công') . '</td>'
                . '<td class="p-4">' . $this->e($r['ten_chuyen_khoa'] ?? '—') . '</td>'
                . '<td class="p-4">' . $this->e($date) . '<div class="text-xs text-slate-500">' . $this->e($r['gio_kham'] ?? '') . '</div></td>'
                . '<td class="p-4">' . $badge . '</td>'
                . '<td class="p-4"><a class="inline-block bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-4 py-2 rounded-lg text-xs" href="index.php?act=admin-benh-nhan&view=ho-so-benh-an&id=' . (int)$r['ma_phieu_kham'] . '">Xem hồ sơ</a></td>'
                . '</tr>';
        }

        if (!$rows) {
            $content .= '<tr><td colspan="7" class="p-10 text-center text-slate-400">Chưa có hồ sơ bệnh án phù hợp. Hồ sơ sẽ xuất hiện sau khi Admin duyệt lịch khám và hệ thống tạo phiếu khám.</td></tr>';
        }
        $content .= '</tbody></table></div>';
        $this->render('Quản lý hồ sơ bệnh án', $content);
    }

    /* =====================================================
       7. QUẢN LÝ ĐÁNH GIÁ
    ===================================================== */

    public function quanLyDanhGia(): void
    {
        /*
         * Bảng danh_gia theo cấu trúc đã cung cấp:
         * ma_danh_gia, ma_nguoi_dung, ma_bac_si,
         * ma_lich_kham, so_sao, noi_dung.
         *
         * Không truy vấn ngay_danh_gia vì bảng không có cột này.
         */

        $rows = $this->db->query(
            "SELECT
                dg.ma_danh_gia,
                dg.ma_lich_kham,
                dg.so_sao,
                dg.noi_dung,
                nd.ho_ten,
                bs.ten_bac_si
             FROM danh_gia dg
             LEFT JOIN nguoi_dung nd
                ON nd.ma_nguoi_dung=dg.ma_nguoi_dung
             LEFT JOIN bac_si bs
                ON bs.ma_bac_si=dg.ma_bac_si
             ORDER BY dg.ma_danh_gia DESC"
        )->fetchAll(PDO::FETCH_ASSOC);

        $count = count($rows);
        $sum = 0;

        foreach ($rows as $r) {
            $sum += (int)$r['so_sao'];
        }

        $average = $count
            ? round($sum / $count, 1)
            : 0;

        $content = '
<div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-7">

<div class="bg-white p-6 rounded-2xl
border border-slate-100 shadow-sm">
<p class="text-sm text-slate-500 font-semibold">
Tổng số đánh giá
</p>
<p class="text-3xl font-black text-emerald-600 mt-3">'
            . $count . '</p>
</div>

<div class="bg-white p-6 rounded-2xl
border border-slate-100 shadow-sm">
<p class="text-sm text-slate-500 font-semibold">
Điểm đánh giá trung bình
</p>
<p class="text-3xl font-black text-amber-500 mt-3">'
            . $average . '/5</p>
</div>

</div>

<div class="bg-white rounded-2xl
border border-slate-100 shadow-sm overflow-x-auto">

<div class="p-5 border-b border-slate-100">
<h2 class="font-bold text-slate-900 text-lg">
Danh sách đánh giá của bệnh nhân
</h2>
<p class="text-sm text-slate-500 mt-1">
Theo dõi đánh giá và mức độ hài lòng của bệnh nhân.
</p>
</div>

<table class="w-full text-sm text-left">
<thead class="bg-slate-50 text-slate-500">
<tr>
<th class="p-4">Mã đánh giá</th>
<th class="p-4">Bệnh nhân</th>
<th class="p-4">Bác sĩ</th>
<th class="p-4">Mã lịch khám</th>
<th class="p-4">Số sao</th>
<th class="p-4">Nội dung đánh giá</th>
</tr>
</thead>
<tbody>';

        if (!$rows) {
            $content .= '
<tr>
<td colspan="6" class="p-10 text-center text-slate-400">
<div class="text-3xl mb-3">⭐</div>
<p class="font-semibold">
Chưa có đánh giá nào trong hệ thống.
</p>
</td>
</tr>';
        } else {
            foreach ($rows as $r) {
                $stars = max(
                    0,
                    min(5, (int)$r['so_sao'])
                );

                $content .= '
<tr class="border-t border-slate-100 hover:bg-slate-50">

<td class="p-4 font-bold">
#' . (int)$r['ma_danh_gia'] . '
</td>

<td class="p-4 font-semibold">'
                    . $this->e($r['ho_ten'] ?? 'Không xác định')
                    . '</td>

<td class="p-4">'
                    . $this->e($r['ten_bac_si'] ?? 'Không xác định')
                    . '</td>

<td class="p-4">
#' . (int)$r['ma_lich_kham'] . '
</td>

<td class="p-4 whitespace-nowrap">
<span class="text-amber-500 font-bold">'
                    . str_repeat('★', $stars)
                    . '</span>
<span class="text-slate-400">'
                    . str_repeat('☆', 5 - $stars)
                    . '</span>
<span class="ml-1 text-slate-500">'
                    . $stars . '/5</span>
</td>

<td class="p-4 min-w-64">'
                    . nl2br($this->e($r['noi_dung']))
                    . '</td>

</tr>';
            }
        }

        $content .= '</tbody></table></div>';

        $this->render('Quản lý đánh giá', $content);
    }

    /* =====================================================
       8. QUẢN LÝ THÔNG BÁO
    ===================================================== */

    public function quanLyThongBao(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->verifyPost();

            try {
                $title = trim($_POST['tieu_de'] ?? '');
                $body = trim($_POST['noi_dung'] ?? '');
                $type = $_POST['loai_thong_bao'] ?? 'he_thong';
                $recipient = $_POST['doi_tuong'] ?? 'tat_ca';

                if ($title === '' || $body === '') {
                    throw new RuntimeException(
                        'Vui lòng nhập tiêu đề và nội dung thông báo.'
                    );
                }

                if (!in_array(
                    $type,
                    ['lich_kham', 'nhac_nho', 'he_thong'],
                    true
                )) {
                    throw new RuntimeException(
                        'Loại thông báo không hợp lệ.'
                    );
                }

                if ($recipient === 'tat_ca') {
                    $users = $this->db->query(
                        "SELECT ma_nguoi_dung
                         FROM nguoi_dung
                         WHERE vai_tro='benh_nhan'"
                    )->fetchAll(PDO::FETCH_COLUMN);
                } else {
                    $q = $this->db->prepare(
                        "SELECT ma_nguoi_dung
                         FROM nguoi_dung
                         WHERE ma_nguoi_dung=?
                         AND vai_tro='benh_nhan'"
                    );
                    $q->execute([(int)$recipient]);
                    $users = $q->fetchAll(PDO::FETCH_COLUMN);
                }

                if (!$users) {
                    throw new RuntimeException(
                        'Không có bệnh nhân phù hợp để gửi thông báo.'
                    );
                }

                $this->db->beginTransaction();

                $q = $this->db->prepare(
                    "INSERT INTO thong_bao
                     (ma_nguoi_dung,tieu_de,noi_dung,loai_thong_bao)
                     VALUES (?,?,?,?)"
                );

                foreach ($users as $userId) {
                    $q->execute([
                        $userId,
                        $title,
                        $body,
                        $type
                    ]);
                }

                $this->db->commit();

                $this->flash('Đã gửi thông báo thành công.');
            } catch (Throwable $e) {
                if ($this->db->inTransaction()) {
                    $this->db->rollBack();
                }

                $this->flash($e->getMessage(), 'error');
            }

            $this->redirect('admin-thong-bao');
        }

        $patients = $this->db->query(
            "SELECT ma_nguoi_dung,ho_ten
             FROM nguoi_dung
             WHERE vai_tro='benh_nhan'
             ORDER BY ho_ten"
        )->fetchAll(PDO::FETCH_ASSOC);

        /*
         * Không sử dụng ngay_tao vì bảng thong_bao
         * trong cấu trúc đã cung cấp không có cột này.
         */

        $rows = $this->db->query(
            "SELECT tb.*,nd.ho_ten
             FROM thong_bao tb
             LEFT JOIN nguoi_dung nd
                ON nd.ma_nguoi_dung=tb.ma_nguoi_dung
             ORDER BY tb.ma_thong_bao DESC
             LIMIT 100"
        )->fetchAll(PDO::FETCH_ASSOC);

        $content = '<div class="flex justify-end mb-4"><button type="button" onclick="document.getElementById(\'notificationDialog\').showModal()" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-5 py-3 rounded-xl shadow-sm">+ Soạn thông báo</button></div>';

        $content .= '<dialog id="notificationDialog" class="w-full max-w-2xl rounded-2xl p-0 backdrop:bg-slate-900/50 shadow-2xl"><div class="flex items-center justify-between px-6 pt-6"><h2 class="text-lg font-bold">Soạn thông báo mới</h2><button type="button" onclick="document.getElementById(\'notificationDialog\').close()" class="text-slate-400 hover:text-slate-700 text-2xl">&times;</button></div>';

        $content .= '
<form method="POST" class="bg-white p-6 rounded-2xl space-y-4">'
            . $this->csrfField()
            . $this->input(
                'tieu_de',
                'Tiêu đề thông báo',
                '',
                'text',
                true
            )
            . $this->textarea(
                'noi_dung',
                'Nội dung thông báo'
            )
            . '<select name="loai_thong_bao"
class="w-full p-3 border rounded-xl text-sm">
<option value="he_thong">Thông báo hệ thống</option>
<option value="lich_kham">Thông báo lịch khám</option>
<option value="nhac_nho">Nhắc nhở</option>
</select>

<select name="doi_tuong"
class="w-full p-3 border rounded-xl text-sm">
<option value="tat_ca">Gửi tất cả bệnh nhân</option>';

        foreach ($patients as $p) {
            $content .= '<option value="'
                . (int)$p['ma_nguoi_dung'] . '">'
                . $this->e($p['ho_ten'])
                . '</option>';
        }

        $content .= '</select>'
            . $this->button('Gửi thông báo')
            . '</form></dialog>

<div class="bg-white rounded-2xl
border border-slate-100 shadow-sm overflow-x-auto">

<table class="w-full text-sm text-left">
<thead class="bg-slate-50">
<tr>
<th class="p-4">Người nhận</th>
<th class="p-4">Tiêu đề</th>
<th class="p-4">Nội dung</th>
<th class="p-4">Loại</th>
<th class="p-4">Trạng thái</th>
<th class="p-4">Mã thông báo</th>
</tr>
</thead>
<tbody>';

        foreach ($rows as $r) {
            $content .= '<tr class="border-t">
<td class="p-4">' . $this->e($r['ho_ten'] ?? '') . '</td>
<td class="p-4 font-bold">' . $this->e($r['tieu_de']) . '</td>
<td class="p-4">' . $this->e($r['noi_dung']) . '</td>
<td class="p-4">' . $this->e($r['loai_thong_bao']) . '</td>
<td class="p-4">' . $this->e($r['trang_thai']) . '</td>
<td class="p-4">#' . (int)$r['ma_thong_bao'] . '</td>
</tr>';
        }

        if (!$rows) {
            $content .= '
<tr>
<td colspan="6" class="p-8 text-center text-slate-400">
Chưa có thông báo.
</td>
</tr>';
        }

        $content .= '</tbody></table></div>';

        $this->render('Quản lý thông báo', $content);
    }

    /* =====================================================
       9. QUẢN LÝ NGƯỜI DÙNG
       Quản trị viên xem / thêm / sửa / khóa / xóa mọi tài khoản.
    ===================================================== */

    private function currentAdminId(): int
    {
        return (int)($_SESSION['user']['ma_nguoi_dung'] ?? 0);
    }

    /**
     * Tự thêm cột trạng thái tài khoản nếu database cũ chưa có.
     * (Có kèm file migrations/ để chạy thủ công nếu muốn.)
     */
    private function ensureAccountStatusColumn(): void
    {
        $q = $this->db->query(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE()
             AND TABLE_NAME = 'nguoi_dung'
             AND COLUMN_NAME = 'trang_thai_tai_khoan'"
        );

        if ((int)$q->fetchColumn() === 0) {
            $this->db->exec(
                "ALTER TABLE nguoi_dung
                 ADD COLUMN trang_thai_tai_khoan
                 ENUM('hoat_dong','bi_khoa') NOT NULL DEFAULT 'hoat_dong'
                 AFTER vai_tro"
            );
        }
    }

    private function countOtherActiveAdmins(int $excludeId): int
    {
        $q = $this->db->prepare(
            "SELECT COUNT(*) FROM nguoi_dung
             WHERE vai_tro='quan_tri'
             AND trang_thai_tai_khoan='hoat_dong'
             AND ma_nguoi_dung<>?"
        );
        $q->execute([$excludeId]);

        return (int)$q->fetchColumn();
    }

    private function userErrorMessage(Throwable $e): string
    {
        // PDOException kế thừa RuntimeException nên phải kiểm tra trước,
        // tránh lộ thông báo lỗi SQL ra giao diện.
        if ($e instanceof PDOException) {
            error_log('Admin user management DB error: ' . $e->getMessage());

            if ((string)$e->getCode() === '23000') {
                return 'Dữ liệu bị trùng hoặc đang được sử dụng ở nơi khác.';
            }

            return 'Đã xảy ra lỗi cơ sở dữ liệu. Vui lòng thử lại.';
        }

        if ($e instanceof RuntimeException) {
            return $e->getMessage();
        }

        error_log('Admin user management error: ' . $e->getMessage());

        return 'Đã xảy ra lỗi hệ thống. Vui lòng thử lại.';
    }

    public function quanLyNguoiDung(): void
    {
        try {
            $this->ensureAccountStatusColumn();
        } catch (Throwable $e) {
            error_log('Admin users migration error: ' . $e->getMessage());
            $this->render(
                'Quản lý người dùng',
                '<div class="bg-white border border-red-200 rounded-2xl p-6 text-sm text-slate-600">'
                . 'Không thể cập nhật cấu trúc bảng nguoi_dung. '
                . 'Hãy chạy file migrations/2026_11_17_trang_thai_tai_khoan.sql rồi tải lại trang.'
                . '</div>'
            );
            return;
        }

        /* ---------- Thêm / sửa ---------- */

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->verifyPost();

            try {
                $id = (int)($_POST['id'] ?? 0);
                $name = trim($_POST['ho_ten'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $phone = trim($_POST['so_dien_thoai'] ?? '');
                $gender = trim($_POST['gioi_tinh'] ?? '');
                $birth = trim($_POST['ngay_sinh'] ?? '');
                $address = trim($_POST['dia_chi'] ?? '');
                $role = (string)($_POST['vai_tro'] ?? 'benh_nhan');
                $password = (string)($_POST['mat_khau'] ?? '');

                if ($name === '' || mb_strlen($name) > 100) {
                    throw new RuntimeException('Họ tên không được để trống và tối đa 100 ký tự.');
                }

                if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 150) {
                    throw new RuntimeException('Email không hợp lệ.');
                }

                if ($phone !== '' && !preg_match('/^[0-9+\-\s.()]{8,20}$/', $phone)) {
                    throw new RuntimeException('Số điện thoại không hợp lệ.');
                }

                if ($gender !== '' && !in_array($gender, ['Nam', 'Nu', 'Khac'], true)) {
                    throw new RuntimeException('Giới tính không hợp lệ.');
                }

                if ($birth !== '') {
                    $date = DateTime::createFromFormat('Y-m-d', $birth);

                    if (
                        !$date ||
                        $date->format('Y-m-d') !== $birth ||
                        $date > new DateTime('today')
                    ) {
                        throw new RuntimeException('Ngày sinh không hợp lệ.');
                    }
                }

                if (mb_strlen($address) > 255) {
                    throw new RuntimeException('Địa chỉ tối đa 255 ký tự.');
                }

                if ($password !== '' && (strlen($password) < 6 || strlen($password) > 72)) {
                    throw new RuntimeException('Mật khẩu phải từ 6 đến 72 ký tự.');
                }

                $q = $this->db->prepare(
                    "SELECT COUNT(*) FROM nguoi_dung
                     WHERE email=? AND ma_nguoi_dung<>?"
                );
                $q->execute([$email, $id]);

                if ((int)$q->fetchColumn() > 0) {
                    throw new RuntimeException('Email đã được sử dụng bởi tài khoản khác.');
                }

                $phone = $phone !== '' ? $phone : null;
                $gender = $gender !== '' ? $gender : null;
                $birth = $birth !== '' ? $birth : null;
                $address = $address !== '' ? $address : null;

                if ($id) {
                    $q = $this->db->prepare(
                        "SELECT * FROM nguoi_dung WHERE ma_nguoi_dung=?"
                    );
                    $q->execute([$id]);
                    $old = $q->fetch(PDO::FETCH_ASSOC);

                    if (!$old) {
                        throw new RuntimeException('Không tìm thấy người dùng.');
                    }

                    // Tài khoản bác sĩ luôn giữ vai trò bác sĩ.
                    if ($old['vai_tro'] === 'bac_si') {
                        $role = 'bac_si';
                    } elseif (!in_array($role, ['benh_nhan', 'quan_tri'], true)) {
                        throw new RuntimeException('Vai trò không hợp lệ.');
                    }

                    if ($old['vai_tro'] === 'quan_tri' && $role !== 'quan_tri') {
                        if ($id === $this->currentAdminId()) {
                            throw new RuntimeException('Bạn không thể tự hạ quyền tài khoản của chính mình.');
                        }

                        if ($this->countOtherActiveAdmins($id) === 0) {
                            throw new RuntimeException('Hệ thống phải còn ít nhất một quản trị viên đang hoạt động.');
                        }
                    }

                    $this->db->beginTransaction();

                    $q = $this->db->prepare(
                        "UPDATE nguoi_dung
                         SET ho_ten=?,email=?,so_dien_thoai=?,
                             gioi_tinh=?,ngay_sinh=?,dia_chi=?,vai_tro=?
                         WHERE ma_nguoi_dung=?"
                    );
                    $q->execute([
                        $name,
                        $email,
                        $phone,
                        $gender,
                        $birth,
                        $address,
                        $role,
                        $id
                    ]);

                    if ($password !== '') {
                        $q = $this->db->prepare(
                            "UPDATE nguoi_dung SET mat_khau=? WHERE ma_nguoi_dung=?"
                        );
                        $q->execute([
                            password_hash($password, PASSWORD_DEFAULT),
                            $id
                        ]);
                    }

                    // Giữ tên hiển thị của bác sĩ đồng bộ với tài khoản.
                    if ($role === 'bac_si') {
                        $q = $this->db->prepare(
                            "UPDATE bac_si SET ten_bac_si=? WHERE ma_nguoi_dung=?"
                        );
                        $q->execute([$name, $id]);
                    }

                    $this->db->commit();

                    if ($id === $this->currentAdminId()) {
                        $_SESSION['user']['ho_ten'] = $name;
                        $_SESSION['user']['email'] = $email;
                    }

                    $this->flash(
                        $password !== ''
                            ? 'Cập nhật người dùng và đặt lại mật khẩu thành công.'
                            : 'Cập nhật người dùng thành công.'
                    );
                } else {
                    if (!in_array($role, ['benh_nhan', 'quan_tri'], true)) {
                        throw new RuntimeException(
                            'Chỉ có thể tạo tài khoản bệnh nhân hoặc quản trị viên tại đây. '
                            . 'Tài khoản bác sĩ được tạo ở mục Quản lý bác sĩ.'
                        );
                    }

                    if ($password === '') {
                        throw new RuntimeException('Vui lòng nhập mật khẩu (tối thiểu 6 ký tự).');
                    }

                    $q = $this->db->prepare(
                        "INSERT INTO nguoi_dung
                         (ho_ten,email,mat_khau,so_dien_thoai,
                          gioi_tinh,ngay_sinh,dia_chi,vai_tro)
                         VALUES (?,?,?,?,?,?,?,?)"
                    );
                    $q->execute([
                        $name,
                        $email,
                        password_hash($password, PASSWORD_DEFAULT),
                        $phone,
                        $gender,
                        $birth,
                        $address,
                        $role
                    ]);

                    $this->flash('Thêm người dùng thành công.');
                }
            } catch (Throwable $e) {
                if ($this->db->inTransaction()) {
                    $this->db->rollBack();
                }

                $this->flash($this->userErrorMessage($e), 'error');
            }

            $this->redirect('admin-nguoi-dung');
        }

        /* ---------- Danh sách ---------- */

        $roleLabels = [
            'benh_nhan' => 'Bệnh nhân',
            'bac_si' => 'Bác sĩ',
            'quan_tri' => 'Quản trị viên'
        ];
        $roleBadges = [
            'benh_nhan' => 'bg-blue-50 text-blue-700',
            'bac_si' => 'bg-emerald-50 text-emerald-700',
            'quan_tri' => 'bg-violet-50 text-violet-700'
        ];

        $search = trim($_GET['q'] ?? '');
        $roleFilter = (string)($_GET['vai_tro'] ?? '');
        $statusFilter = (string)($_GET['trang_thai'] ?? '');

        if (!isset($roleLabels[$roleFilter])) {
            $roleFilter = '';
        }

        if (!in_array($statusFilter, ['hoat_dong', 'bi_khoa'], true)) {
            $statusFilter = '';
        }

        $where = ['1=1'];
        $params = [];

        if ($search !== '') {
            $like = '%' . addcslashes($search, '%_\\') . '%';
            $where[] = '(nd.ho_ten LIKE ? OR nd.email LIKE ? OR nd.so_dien_thoai LIKE ?)';
            array_push($params, $like, $like, $like);
        }

        if ($roleFilter !== '') {
            $where[] = 'nd.vai_tro = ?';
            $params[] = $roleFilter;
        }

        if ($statusFilter !== '') {
            $where[] = 'nd.trang_thai_tai_khoan = ?';
            $params[] = $statusFilter;
        }

        $whereSql = implode(' AND ', $where);

        $q = $this->db->prepare(
            "SELECT COUNT(*) FROM nguoi_dung nd WHERE " . $whereSql
        );
        $q->execute($params);
        $total = (int)$q->fetchColumn();

        $perPage = 15;
        $totalPages = max(1, (int)ceil($total / $perPage));
        $page = min(max(1, (int)($_GET['page'] ?? 1)), $totalPages);
        $offset = ($page - 1) * $perPage;

        $q = $this->db->prepare(
            "SELECT nd.*,
                (SELECT COUNT(*) FROM lich_kham lk
                 WHERE lk.ma_nguoi_dung=nd.ma_nguoi_dung) AS tong_lich
             FROM nguoi_dung nd
             WHERE " . $whereSql . "
             ORDER BY nd.ma_nguoi_dung DESC
             LIMIT " . (int)$perPage . " OFFSET " . (int)$offset
        );
        $q->execute($params);
        $rows = $q->fetchAll(PDO::FETCH_ASSOC);

        $stats = $this->db->query(
            "SELECT COUNT(*) AS tong,
                    SUM(vai_tro='benh_nhan') AS benh_nhan,
                    SUM(vai_tro='bac_si') AS bac_si,
                    SUM(vai_tro='quan_tri') AS quan_tri,
                    SUM(trang_thai_tai_khoan='bi_khoa') AS bi_khoa
             FROM nguoi_dung"
        )->fetch(PDO::FETCH_ASSOC);

        $edit = null;

        if (isset($_GET['edit'])) {
            $q = $this->db->prepare(
                "SELECT * FROM nguoi_dung WHERE ma_nguoi_dung=?"
            );
            $q->execute([(int)$_GET['edit']]);
            $edit = $q->fetch(PDO::FETCH_ASSOC) ?: null;
        }

        $selfId = $this->currentAdminId();

        /* ---------- Thống kê ---------- */

        $cards = [
            ['Tổng tài khoản', (int)$stats['tong'], 'text-slate-900'],
            ['Bệnh nhân', (int)$stats['benh_nhan'], 'text-blue-700'],
            ['Bác sĩ', (int)$stats['bac_si'], 'text-emerald-700'],
            ['Quản trị viên', (int)$stats['quan_tri'], 'text-violet-700'],
            ['Đang bị khóa', (int)$stats['bi_khoa'], 'text-red-600']
        ];

        $content = '<div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">';

        foreach ($cards as $card) {
            $content .= '<div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm">'
                . '<p class="text-xs font-semibold text-slate-500">' . $this->e($card[0]) . '</p>'
                . '<p class="text-3xl font-black mt-2 ' . $card[2] . '">'
                . number_format($card[1]) . '</p></div>';
        }

        $content .= '</div>';

        /* ---------- Bộ lọc ---------- */

        $content .= '<form method="GET" class="bg-white p-4 rounded-2xl border border-slate-100 mb-5 flex flex-col md:flex-row gap-3">'
            . '<input type="hidden" name="act" value="admin-nguoi-dung">'
            . $this->input('q', 'Tìm tên, email hoặc số điện thoại...', $search)
            . '<select name="vai_tro" class="p-3 border border-slate-200 rounded-xl text-sm">'
            . '<option value="">Tất cả vai trò</option>';

        foreach ($roleLabels as $value => $label) {
            $content .= '<option value="' . $this->e($value) . '" '
                . ($roleFilter === $value ? 'selected' : '') . '>'
                . $this->e($label) . '</option>';
        }

        $content .= '</select>'
            . '<select name="trang_thai" class="p-3 border border-slate-200 rounded-xl text-sm">'
            . '<option value="">Tất cả trạng thái</option>'
            . '<option value="hoat_dong" ' . ($statusFilter === 'hoat_dong' ? 'selected' : '') . '>Hoạt động</option>'
            . '<option value="bi_khoa" ' . ($statusFilter === 'bi_khoa' ? 'selected' : '') . '>Đã khóa</option>'
            . '</select>'
            . '<button class="bg-slate-800 text-white px-5 py-3 rounded-xl">Lọc</button>'
            . '</form>';

        $content .= '<div class="flex justify-end mb-4"><a href="index.php?act=admin-nguoi-dung&add=1" '
            . 'class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-5 py-3 rounded-xl shadow-sm">'
            . '+ Thêm người dùng</a></div>';

        /* ---------- Form thêm / sửa ---------- */

        $isDoctor = $edit && ($edit['vai_tro'] ?? '') === 'bac_si';
        $editRole = $edit['vai_tro'] ?? 'benh_nhan';
        $editGender = $edit['gioi_tinh'] ?? '';

        $label = static function (string $text): string {
            return '<label class="block text-xs font-semibold text-slate-500 mb-1">'
                . $text . '</label>';
        };

        $content .= '<dialog id="userDialog" class="w-full max-w-2xl rounded-2xl p-0 backdrop:bg-slate-900/50 shadow-2xl">'
            . '<div class="flex justify-end px-6 pt-4">'
            . '<button type="button" onclick="document.getElementById(\'userDialog\').close()" '
            . 'class="text-slate-400 hover:text-slate-700 text-2xl">&times;</button></div>';

        $content .= '<form method="POST" autocomplete="off" class="bg-white p-6 rounded-2xl space-y-4">'
            . $this->csrfField()
            . '<input type="hidden" name="id" value="' . (int)($edit['ma_nguoi_dung'] ?? 0) . '">'
            . '<h2 class="font-bold">' . ($edit ? 'Chỉnh sửa người dùng' : 'Thêm người dùng mới') . '</h2>'
            . '<div>' . $label('Họ và tên')
            . $this->input('ho_ten', 'Họ và tên', $edit['ho_ten'] ?? '', 'text', true) . '</div>'
            . '<div>' . $label('Email đăng nhập')
            . $this->input('email', 'Email', $edit['email'] ?? '', 'email', true) . '</div>'
            . '<div>' . $label('Số điện thoại')
            . $this->input('so_dien_thoai', 'Số điện thoại', $edit['so_dien_thoai'] ?? '') . '</div>'
            . '<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">'
            . '<div>' . $label('Giới tính')
            . '<select name="gioi_tinh" class="w-full p-3 border border-slate-200 rounded-xl text-sm">'
            . '<option value="">-- Chưa chọn --</option>';

        foreach (['Nam' => 'Nam', 'Nu' => 'Nữ', 'Khac' => 'Khác'] as $value => $text) {
            $content .= '<option value="' . $value . '" '
                . ($editGender === $value ? 'selected' : '') . '>' . $text . '</option>';
        }

        $content .= '</select></div>'
            . '<div>' . $label('Ngày sinh')
            . $this->input('ngay_sinh', 'Ngày sinh', $edit['ngay_sinh'] ?? '', 'date') . '</div>'
            . '</div>'
            . '<div>' . $label('Địa chỉ')
            . $this->input('dia_chi', 'Địa chỉ', $edit['dia_chi'] ?? '') . '</div>';

        if ($isDoctor) {
            $content .= '<div class="p-3 bg-slate-50 rounded-xl text-sm text-slate-600">'
                . 'Vai trò: <b>Bác sĩ</b>. Thông tin chuyên môn của bác sĩ được chỉnh ở mục '
                . '<a class="text-emerald-700 font-bold" href="index.php?act=admin-bac-si">Quản lý bác sĩ</a>.'
                . '</div>';
        } else {
            $content .= '<div>' . $label('Vai trò')
                . '<select name="vai_tro" class="w-full p-3 border border-slate-200 rounded-xl text-sm">'
                . '<option value="benh_nhan" ' . ($editRole === 'benh_nhan' ? 'selected' : '') . '>Bệnh nhân</option>'
                . '<option value="quan_tri" ' . ($editRole === 'quan_tri' ? 'selected' : '') . '>Quản trị viên</option>'
                . '</select></div>';
        }

        $content .= '<div>'
            . $label($edit ? 'Mật khẩu mới (bỏ trống nếu không đổi)' : 'Mật khẩu')
            . '<input type="password" name="mat_khau" autocomplete="new-password" '
            . ($edit ? '' : 'required ')
            . 'placeholder="' . ($edit ? 'Bỏ trống nếu không đổi mật khẩu' : 'Tối thiểu 6 ký tự') . '" '
            . 'class="w-full p-3 border border-slate-200 rounded-xl text-sm"></div>'
            . $this->button($edit ? 'Lưu thay đổi' : 'Thêm người dùng');

        if ($edit) {
            $content .= '<a href="index.php?act=admin-nguoi-dung" class="text-slate-500 text-sm ml-3">Hủy sửa</a>';
        }

        $content .= '</form></dialog>';

        /* ---------- Bảng người dùng ---------- */

        $content .= '<div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-x-auto">'
            . '<table class="w-full text-sm text-left min-w-[900px]">'
            . '<thead class="bg-slate-50 text-slate-500"><tr>'
            . '<th class="p-4">Mã</th>'
            . '<th class="p-4">Người dùng</th>'
            . '<th class="p-4">Vai trò</th>'
            . '<th class="p-4">Trạng thái</th>'
            . '<th class="p-4">Lịch khám</th>'
            . '<th class="p-4">Ngày tạo</th>'
            . '<th class="p-4">Thao tác</th>'
            . '</tr></thead><tbody>';

        foreach ($rows as $r) {
            $uid = (int)$r['ma_nguoi_dung'];
            $isSelf = $uid === $selfId;
            $locked = ($r['trang_thai_tai_khoan'] ?? 'hoat_dong') === 'bi_khoa';
            $role = (string)$r['vai_tro'];

            $statusBadge = $locked
                ? '<span class="inline-flex px-3 py-1 rounded-full text-xs font-bold bg-red-50 text-red-700">Đã khóa</span>'
                : '<span class="inline-flex px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700">Hoạt động</span>';

            $actions = '<a class="text-blue-600 font-bold mr-3" '
                . 'href="index.php?act=admin-nguoi-dung&edit=' . $uid . '">Sửa</a>';

            if (!$isSelf) {
                $actions .= '<form method="POST" action="index.php?act=admin-khoa-nguoi-dung" '
                    . 'class="inline mr-3" onsubmit="return confirm(\''
                    . ($locked
                        ? 'Mở khóa tài khoản này?'
                        : 'Khóa tài khoản này? Người dùng sẽ không thể đăng nhập.')
                    . '\')">'
                    . $this->csrfField()
                    . '<input type="hidden" name="id" value="' . $uid . '">'
                    . '<input type="hidden" name="hanh_dong" value="' . ($locked ? 'mo_khoa' : 'khoa') . '">'
                    . '<button class="' . ($locked ? 'text-emerald-600' : 'text-amber-600') . ' font-bold">'
                    . ($locked ? 'Mở khóa' : 'Khóa') . '</button></form>';

                if ($role === 'bac_si') {
                    $actions .= '<a class="text-slate-400 text-xs" href="index.php?act=admin-bac-si">'
                        . 'Xóa ở mục Bác sĩ</a>';
                } else {
                    $actions .= '<form method="POST" action="index.php?act=admin-xoa-nguoi-dung" '
                        . 'class="inline" onsubmit="return confirm(\'Xóa vĩnh viễn người dùng này?\')">'
                        . $this->csrfField()
                        . '<input type="hidden" name="id" value="' . $uid . '">'
                        . '<button class="text-red-600 font-bold">Xóa</button></form>';
                }
            }

            $created = !empty($r['ngay_tao'])
                ? date('d/m/Y', strtotime($r['ngay_tao']))
                : '—';

            $content .= '<tr class="border-t hover:bg-slate-50' . ($locked ? ' opacity-70' : '') . '">'
                . '<td class="p-4 text-slate-500">#' . $uid . '</td>'
                . '<td class="p-4"><div class="font-bold">' . $this->e($r['ho_ten'])
                . ($isSelf ? ' <span class="text-xs font-semibold text-emerald-600">(Bạn)</span>' : '')
                . '</div><div class="text-xs text-slate-500">' . $this->e($r['email']) . '</div>'
                . '<div class="text-xs text-slate-500">' . $this->e($r['so_dien_thoai'] ?? '') . '</div></td>'
                . '<td class="p-4"><span class="inline-flex px-3 py-1 rounded-full text-xs font-bold '
                . ($roleBadges[$role] ?? 'bg-slate-100 text-slate-600') . '">'
                . $this->e($roleLabels[$role] ?? $role) . '</span></td>'
                . '<td class="p-4">' . $statusBadge . '</td>'
                . '<td class="p-4">' . (int)$r['tong_lich'] . '</td>'
                . '<td class="p-4">' . $this->e($created) . '</td>'
                . '<td class="p-4 whitespace-nowrap">' . $actions . '</td>'
                . '</tr>';
        }

        if (!$rows) {
            $content .= '<tr><td colspan="7" class="p-8 text-center text-slate-400">'
                . 'Không tìm thấy người dùng phù hợp.</td></tr>';
        }

        $content .= '</tbody></table></div>';

        /* ---------- Phân trang ---------- */

        $pageUrl = static function (int $p) use ($search, $roleFilter, $statusFilter): string {
            return 'index.php?' . http_build_query([
                'act' => 'admin-nguoi-dung',
                'q' => $search,
                'vai_tro' => $roleFilter,
                'trang_thai' => $statusFilter,
                'page' => $p
            ]);
        };

        $linkClass = 'px-4 py-2 rounded-xl border border-slate-200 bg-white text-sm font-bold hover:bg-slate-50';
        $disabledClass = 'px-4 py-2 rounded-xl border border-slate-100 bg-slate-50 text-sm font-bold text-slate-300';

        $content .= '<div class="flex flex-col sm:flex-row items-center justify-between gap-3 mt-5">'
            . '<p class="text-sm text-slate-500">Hiển thị ' . count($rows) . ' / '
            . number_format($total) . ' người dùng</p><div class="flex items-center gap-2">'
            . ($page > 1
                ? '<a class="' . $linkClass . '" href="' . $this->e($pageUrl($page - 1)) . '">← Trước</a>'
                : '<span class="' . $disabledClass . '">← Trước</span>')
            . '<span class="text-sm text-slate-600 px-2">Trang ' . $page . ' / ' . $totalPages . '</span>'
            . ($page < $totalPages
                ? '<a class="' . $linkClass . '" href="' . $this->e($pageUrl($page + 1)) . '">Sau →</a>'
                : '<span class="' . $disabledClass . '">Sau →</span>')
            . '</div></div>';

        $content .= '<script>(function () {'
            . 'var p = new URLSearchParams(window.location.search);'
            . 'var d = document.getElementById("userDialog");'
            . 'if ((p.has("edit") || p.has("add")) && d && !d.open) { d.showModal(); }'
            . '})();</script>';

        $this->render('Quản lý người dùng', $content);
    }

    public function khoaNguoiDung(): void
    {
        $this->verifyPost();

        try {
            $this->ensureAccountStatusColumn();

            $id = (int)($_POST['id'] ?? 0);
            $action = (string)($_POST['hanh_dong'] ?? '');

            if (!in_array($action, ['khoa', 'mo_khoa'], true)) {
                throw new RuntimeException('Thao tác không hợp lệ.');
            }

            $q = $this->db->prepare(
                "SELECT ma_nguoi_dung,vai_tro FROM nguoi_dung WHERE ma_nguoi_dung=?"
            );
            $q->execute([$id]);
            $user = $q->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                throw new RuntimeException('Không tìm thấy người dùng.');
            }

            if ($action === 'khoa') {
                if ($id === $this->currentAdminId()) {
                    throw new RuntimeException('Bạn không thể tự khóa tài khoản của chính mình.');
                }

                if (
                    $user['vai_tro'] === 'quan_tri' &&
                    $this->countOtherActiveAdmins($id) === 0
                ) {
                    throw new RuntimeException('Hệ thống phải còn ít nhất một quản trị viên đang hoạt động.');
                }
            }

            $q = $this->db->prepare(
                "UPDATE nguoi_dung SET trang_thai_tai_khoan=? WHERE ma_nguoi_dung=?"
            );
            $q->execute([$action === 'khoa' ? 'bi_khoa' : 'hoat_dong', $id]);

            $this->flash(
                $action === 'khoa'
                    ? 'Đã khóa tài khoản. Người dùng sẽ bị đăng xuất ở lần thao tác tiếp theo.'
                    : 'Đã mở khóa tài khoản.'
            );
        } catch (Throwable $e) {
            $this->flash($this->userErrorMessage($e), 'error');
        }

        $this->redirect('admin-nguoi-dung');
    }

    public function xoaNguoiDung(): void
    {
        $this->verifyPost();

        try {
            $id = (int)($_POST['id'] ?? 0);

            $q = $this->db->prepare(
                "SELECT ma_nguoi_dung,email,vai_tro
                 FROM nguoi_dung WHERE ma_nguoi_dung=?"
            );
            $q->execute([$id]);
            $user = $q->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                throw new RuntimeException('Không tìm thấy người dùng.');
            }

            if ($id === $this->currentAdminId()) {
                throw new RuntimeException('Bạn không thể xóa tài khoản đang đăng nhập.');
            }

            if ($user['vai_tro'] === 'bac_si') {
                throw new RuntimeException('Tài khoản bác sĩ cần được xóa tại mục Quản lý bác sĩ.');
            }

            foreach ([
                'lich_kham' => 'lịch khám',
                'danh_gia' => 'đánh giá'
            ] as $table => $label) {
                $q = $this->db->prepare(
                    "SELECT COUNT(*) FROM {$table} WHERE ma_nguoi_dung=?"
                );
                $q->execute([$id]);

                if ((int)$q->fetchColumn() > 0) {
                    throw new RuntimeException(
                        "Không thể xóa vì người dùng đã có {$label}. "
                        . 'Hãy khóa tài khoản thay vì xóa để giữ nguyên lịch sử.'
                    );
                }
            }

            $this->db->beginTransaction();

            $q = $this->db->prepare("DELETE FROM thong_bao WHERE ma_nguoi_dung=?");
            $q->execute([$id]);

            try {
                $q = $this->db->prepare(
                    "DELETE FROM password_reset_otps WHERE email=?"
                );
                $q->execute([$user['email']]);
            } catch (PDOException $e) {
                // Bảng OTP có thể chưa được tạo trên một số máy.
            }

            $q = $this->db->prepare(
                "DELETE FROM nguoi_dung WHERE ma_nguoi_dung=?"
            );
            $q->execute([$id]);

            $this->db->commit();

            $this->flash('Đã xóa người dùng.');
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            $this->flash($this->userErrorMessage($e), 'error');
        }

        $this->redirect('admin-nguoi-dung');
    }
}