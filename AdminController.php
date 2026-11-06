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

        class=(flash['type'] ?? '') === 'error'
            ? 'bg-red-50 text-red-700 border-red-200'
            : 'bg-emerald-50 text-emerald-700 border-emerald-200';

        return '<div class="mb-5 border rounded-xl px-4 py-3 '
            . $class . '">'
            . this->e(flash['message'])
            . '</div>';
    }

    private function csrfField(): string
    {
        return '<input type="hidden" name="csrf_token" value="'
            . this->e(_SESSION['admin_csrf'])
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
        return '<input type="' . this->e(type)
            . '" name="' . this->e(name)
            . '" value="' . this->e(value)
            . '" placeholder="' . this->e(placeholder)
            . '" ' . ($required ? 'required' : '')
            . ' class="w-full p-3 border border-slate-200 rounded-xl text-sm">';
    }

    private function textarea(
        string $name,
        string $placeholder,
        string $value = ''
    ): string {
        return '<textarea name="' . this->e(name)
            . '" placeholder="' . this->e(placeholder)
            . '" rows="3"
            class="w-full p-3 border border-slate-200 rounded-xl text-sm">'
            . this->e(value)
            . '</textarea>';
    }

    private function button(string $label): string
    {
        return '<button type="submit"
            class="bg-emerald-600 hover:bg-emerald-700 text-white
            font-bold px-5 py-3 rounded-xl text-sm">'
            . this->e(label)
            . '</button>';
    }

    private function imageHtml(?string $path): string
    {
        if (!$path) {
            return '<span class="text-slate-400">Chưa có ảnh</span>';
        }

        return '<img src="' . this->e(path)
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

        mime=mimecontenttype(file['tmp_name']);

        $types = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp'
        ];

        if (!isset(types[mime])) {
            throw new RuntimeException(
                'Chỉ chấp nhận ảnh JPG, PNG hoặc WEBP.'
            );
        }

        $dir = $this->rootPath . '/uploads/' . $folder;

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $filename = bin2hex(random_bytes(16))
            . '.' . types[mime];

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
        if (!path||strpos(path, 'uploads/') !== 0) {
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
            . this->e(label)
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
            'admin-danh-gia' => '⭐ Quản lý đánh giá',
            'admin-thong-bao' => '🔔 Quản lý thông báo'
        ];

        echo '<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>' . this->e(title) . ' - Elite Clinic</title>
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
            isActive=(_GET['act'] ?? '') === $baseAct;
            if (baseAct==='admin-benh-nhan'&&isset(parts[1])) {
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
            . this->e(title)
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
        $queries = [
            'Tổng bệnh nhân' =>
                "SELECT COUNT(*) FROM nguoi_dung WHERE vai_tro='benh_nhan'",

            'Tổng bác sĩ' =>
                "SELECT COUNT(*) FROM bac_si",

            'Tổng chuyên khoa' =>
                "SELECT COUNT(*) FROM chuyen_khoa",

            'Tổng gói khám' =>
                "SELECT COUNT(*) FROM goi_kham",

            'Tổng lịch khám' =>
                "SELECT COUNT(*) FROM lich_kham",

            'Lịch chờ xác nhận' =>
                "SELECT COUNT(*) FROM lich_kham WHERE trang_thai='cho_xac_nhan'"
        ];

        $content = '<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">';

        foreach ($queries as $label => $sql) {
            number=(int)this->db->query($sql)->fetchColumn();

            $content .= '
<div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
<p class="text-sm text-slate-500 font-semibold">'
                . this->e(label)
                . '</p>
<p class="text-3xl font-black text-emerald-600 mt-3">'
                . number_format($number)
                . '</p>
</div>';
        }

        $content .= '</div>';

        $recent = $this->db->query(
            "SELECT lk.ma_lich_kham,lk.ngay_kham,lk.gio_kham,
                    lk.trang_thai,nd.ho_ten,bs.ten_bac_si
             FROM lich_kham lk
             LEFT JOIN nguoi_dung nd
                ON nd.ma_nguoi_dung=lk.ma_nguoi_dung
             LEFT JOIN bac_si bs
                ON bs.ma_bac_si=lk.ma_bac_si
             ORDER BY lk.ma_lich_kham DESC
             LIMIT 8"
        )->fetchAll(PDO::FETCH_ASSOC);

        $content .= '
<div class="bg-white rounded-2xl border border-slate-100
shadow-sm mt-7 overflow-x-auto">

<div class="p-5 font-bold text-slate-900">
Lịch khám mới nhất
</div>

<table class="w-full text-sm text-left">
<thead class="bg-slate-50">
<tr>
<th class="p-4">Mã lịch</th>
<th class="p-4">Bệnh nhân</th>
<th class="p-4">Bác sĩ</th>
<th class="p-4">Ngày khám</th>
<th class="p-4">Trạng thái</th>
</tr>
</thead>
<tbody>';

        foreach ($recent as $r) {
            $content .= '<tr class="border-t">
<td class="p-4">#' . (int)$r['ma_lich_kham'] . '</td>
<td class="p-4">' . this->e(r['ho_ten'] ?? '') . '</td>
<td class="p-4">' . this->e(r['ten_bac_si'] ?? '') . '</td>
<td class="p-4">' . this->e(r['ngay_kham']) . ' '
                . this->e(r['gio_kham']) . '</td>
<td class="p-4">' . this->e(r['trang_thai']) . '</td>
</tr>';
        }

        $content .= '</tbody></table></div>';

        $this->render('Dashboard tổng quan', $content);
    }

    /* =====================================================
       2. QUẢN LÝ CHUYÊN KHOA
    ===================================================== */

    public function quanLyChuyenKhoa(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->verifyPost();

            try {
                id=(int)(_POST['id'] ?? 0);
                name=trim(_POST['ten_chuyen_khoa'] ?? '');

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
                    q->execute([id]);
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

                    q->execute(arraymerge(values, [$id]));

                    if ($old['hinh_anh'] !== $image) {
                        this->deleteImage(old['hinh_anh']);
                    }

                    $this->flash('Cập nhật chuyên khoa thành công.');
                } else {
                    $q = $this->db->prepare(
                        "INSERT INTO chuyen_khoa
                         (ten_chuyen_khoa,mo_ta,gioi_thieu_chi_tiet,hinh_anh)
                         VALUES (?,?,?,?)"
                    );

                    q->execute(values);

                    $this->flash('Thêm chuyên khoa thành công.');
                }
            } catch (Throwable $e) {
                this->flash(e->getMessage(), 'error');
            }

            $this->redirect('admin-chuyen-khoa');
        }

        $edit = null;

        if (isset($_GET['edit'])) {
            $q = $this->db->prepare(
                "SELECT * FROM chuyen_khoa WHERE ma_chuyen_khoa=?"
            );
            q->execute([(int)_GET['edit']]);
            $edit = $q->fetch(PDO::FETCH_ASSOC) ?: null;
        }

        search=trim(_GET['q'] ?? '');

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

        $content .= '
<form method="POST" enctype="multipart/form-data"
class="bg-white p-6 rounded-2xl border border-slate-100
shadow-sm space-y-4 mb-7">'
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

        $content .= '</form>
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
            id=(int)r['ma_chuyen_khoa'];

            $content .= '<tr class="border-t">
<td class="p-4">' . this->imageHtml(r['hinh_anh']) . '</td>
<td class="p-4 font-bold">'
                . this->e(r['ten_chuyen_khoa']) . '</td>
<td class="p-4">' . this->e(r['mo_ta']) . '</td>
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

        $content .= '</tbody></table></div>';

        $this->render('Quản lý chuyên khoa', $content);
    }

    public function xoaChuyenKhoa(): void
    {
        $this->verifyPost();

        id=(int)(_POST['id'] ?? 0);

        try {
            foreach ([
                'bac_si' => 'bác sĩ',
                'goi_kham' => 'gói khám'
            ] as $table => $label) {
                $q = $this->db->prepare(
                    "SELECT COUNT(*) FROM {$table} WHERE ma_chuyen_khoa=?"
                );
                q->execute([id]);

                if ((int)$q->fetchColumn() > 0) {
                    throw new RuntimeException(
                        "Không thể xóa vì chuyên khoa vẫn có {$label}."
                    );
                }
            }

            $q = $this->db->prepare(
                "SELECT hinh_anh FROM chuyen_khoa WHERE ma_chuyen_khoa=?"
            );
            q->execute([id]);
            $old = $q->fetch(PDO::FETCH_ASSOC);

            $q = $this->db->prepare(
                "DELETE FROM chuyen_khoa WHERE ma_chuyen_khoa=?"
            );
            q->execute([id]);

            if ($old) {
                this->deleteImage(old['hinh_anh']);
            }

            $this->flash('Đã xóa chuyên khoa.');
        } catch (Throwable $e) {
            this->flash(e->getMessage(), 'error');
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
                id=(int)(_POST['id'] ?? 0);
                name=trim(_POST['ten_bac_si'] ?? '');
                email=trim(_POST['email'] ?? '');
                phone=trim(_POST['so_dien_thoai'] ?? '');
                specialty=(int)(_POST['ma_chuyen_khoa'] ?? 0);
                degree=trim(_POST['bang_cap'] ?? '');
                experience=(int)(_POST['so_nam_kinh_nghiem'] ?? 0);
                intro=trim(_POST['gioi_thieu'] ?? '');
                fee=(float)(_POST['phi_kham'] ?? 0);

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
                q->execute([specialty]);

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
                    q->execute([id]);
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
                    q->execute([email, $old['ma_nguoi_dung']]);

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
                        this->deleteImage(old['hinh_anh']);
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
                    q->execute([email]);

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

                    userId=(int)this->db->lastInsertId();

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

                this->flash(e->getMessage(), 'error');
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
            q->execute([(int)_GET['edit']]);
            $edit = $q->fetch(PDO::FETCH_ASSOC) ?: null;
        }

        $specialties = $this->db->query(
            "SELECT ma_chuyen_khoa,ten_chuyen_khoa
             FROM chuyen_khoa ORDER BY ten_chuyen_khoa"
        )->fetchAll(PDO::FETCH_ASSOC);

        search=trim(_GET['q'] ?? '');

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

        $content .= '<form method="POST" enctype="multipart/form-data"
class="bg-white p-6 rounded-2xl border border-slate-100
shadow-sm space-y-4 mb-7">'
            . $this->csrfField()
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
            selected=(int)(edit['ma_chuyen_khoa'] ?? 0)
                === (int)$sp['ma_chuyen_khoa']
                ? 'selected' : '';

            $content .= '<option value="'
                . (int)$sp['ma_chuyen_khoa'] . '" '
                . $selected . '>'
                . this->e(sp['ten_chuyen_khoa'])
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

        $content .= '</form>
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
            id=(int)r['ma_bac_si'];

            $content .= '<tr class="border-t">
<td class="p-4">' . this->imageHtml(r['hinh_anh']) . '</td>
<td class="p-4 font-bold">' . this->e(r['ten_bac_si']) . '</td>
<td class="p-4">' . this->e(r['ten_chuyen_khoa']) . '</td>
<td class="p-4">' . this->e(r['email']) . '</td>
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

        $content .= '</tbody></table></div>';

        $this->render('Quản lý bác sĩ', $content);
    }

    public function xoaBacSi(): void
    {
        $this->verifyPost();

        id=(int)(_POST['id'] ?? 0);

        try {
            $q = $this->db->prepare(
                "SELECT ma_nguoi_dung,hinh_anh
                 FROM bac_si WHERE ma_bac_si=?"
            );
            q->execute([id]);
            $doctor = $q->fetch(PDO::FETCH_ASSOC);

            if (!$doctor) {
                throw new RuntimeException('Không tìm thấy bác sĩ.');
            }

            $q = $this->db->prepare(
                "SELECT COUNT(*) FROM lich_kham WHERE ma_bac_si=?"
            );
            q->execute([id]);

            if ((int)$q->fetchColumn() > 0) {
                throw new RuntimeException(
                    'Bác sĩ đã có lịch khám, không thể xóa.'
                );
            }

            $this->db->beginTransaction();

            $q = $this->db->prepare(
                "DELETE FROM bac_si WHERE ma_bac_si=?"
            );
            q->execute([id]);

            $q = $this->db->prepare(
                "DELETE FROM nguoi_dung
                 WHERE ma_nguoi_dung=? AND vai_tro='bac_si'"
            );
            q->execute([doctor['ma_nguoi_dung']]);

            $this->db->commit();

            this->deleteImage(doctor['hinh_anh']);

            $this->flash('Đã xóa bác sĩ và tài khoản liên quan.');
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            this->flash(e->getMessage(), 'error');
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
                id=(int)(_POST['id'] ?? 0);
                name=trim(_POST['ten_goi'] ?? '');

                specialty=(_POST['ma_chuyen_khoa'] ?? '') !== ''
                    ? (int)$_POST['ma_chuyen_khoa']
                    : null;

                price=(float)(_POST['gia'] ?? 0);

                duration=(_POST['thoi_gian_du_kien'] ?? '') !== ''
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
                    q->execute([id]);
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

                    q->execute(arraymerge(values, [$id]));

                    if ($old['hinh_anh'] !== $image) {
                        this->deleteImage(old['hinh_anh']);
                    }

                    $this->flash('Cập nhật gói khám thành công.');
                } else {
                    $q = $this->db->prepare(
                        "INSERT INTO goi_kham
                         (ten_goi,ma_chuyen_khoa,mo_ta,quy_trinh,
                          gia,thoi_gian_du_kien,hinh_anh,trang_thai)
                         VALUES (?,?,?,?,?,?,?,?)"
                    );

                    q->execute(values);

                    $this->flash('Thêm gói khám thành công.');
                }
            } catch (Throwable $e) {
                this->flash(e->getMessage(), 'error');
            }

            $this->redirect('admin-goi-kham');
        }

        $edit = null;

        if (isset($_GET['edit'])) {
            $q = $this->db->prepare(
                "SELECT * FROM goi_kham WHERE ma_goi_kham=?"
            );
            q->execute([(int)_GET['edit']]);
            $edit = $q->fetch(PDO::FETCH_ASSOC) ?: null;
        }

        $specialties = $this->db->query(
            "SELECT ma_chuyen_khoa,ten_chuyen_khoa
             FROM chuyen_khoa ORDER BY ten_chuyen_khoa"
        )->fetchAll(PDO::FETCH_ASSOC);

        search=trim(_GET['q'] ?? '');

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

        $content .= '<form method="POST" enctype="multipart/form-data"
class="bg-white p-6 rounded-2xl border border-slate-100
shadow-sm space-y-4 mb-7">'
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
            selected=(int)(edit['ma_chuyen_khoa'] ?? 0)
                === (int)$sp['ma_chuyen_khoa']
                ? 'selected' : '';

            $content .= '<option value="'
                . (int)$sp['ma_chuyen_khoa'] . '" '
                . $selected . '>'
                . this->e(sp['ten_chuyen_khoa'])
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

        $content .= '</form>
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
            id=(int)r['ma_goi_kham'];

            $content .= '<tr class="border-t">
<td class="p-4">' . this->imageHtml(r['hinh_anh']) . '</td>
<td class="p-4 font-bold">' . this->e(r['ten_goi']) . '</td>
<td class="p-4">'
                . this->e(r['ten_chuyen_khoa'] ?? 'Chưa phân loại')
                . '</td>
<td class="p-4">'
                . number_format((float)$r['gia']) . ' đ</td>
<td class="p-4">' . this->e(r['trang_thai']) . '</td>
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

        $content .= '</tbody></table></div>';

        $this->render('Quản lý gói khám', $content);
    }

    public function xoaGoiKham(): void
    {
        $this->verifyPost();

        id=(int)(_POST['id'] ?? 0);

        try {
            $q = $this->db->prepare(
                "SELECT hinh_anh FROM goi_kham WHERE ma_goi_kham=?"
            );
            q->execute([id]);
            $old = $q->fetch(PDO::FETCH_ASSOC);

            if (!$old) {
                throw new RuntimeException(
                    'Không tìm thấy gói khám.'
                );
            }

            $q = $this->db->prepare(
                "DELETE FROM goi_kham WHERE ma_goi_kham=?"
            );
            q->execute([id]);

            this->deleteImage(old['hinh_anh']);

            $this->flash('Đã xóa gói khám.');
        } catch (Throwable $e) {
            this->flash(e->getMessage(), 'error');
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
        search=trim(_GET['q'] ?? '');
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

        $q = this->db->prepare(sql);
        q->execute(params);
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
            $content .= '<option value="' . this->e(s) . '" ' . $selected . '>'
                . this->e(labels[$s]) . '</option>';
        }
        $content .= '</select><button class="bg-slate-800 text-white px-5 rounded-xl">Lọc</button></form>';
        $content .= '<div class="space-y-5">';

        foreach ($rows as $r) {
            id=(int)r['ma_lich_kham'];
            $content .= '<article class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                <div class="space-y-2 text-sm">
                    <h3 class="font-extrabold text-slate-900 text-base">Lịch khám #' . $id . '</h3>
                    <p><b>Bệnh nhân:</b> ' . this->e(r['ho_ten'] ?? '') . '</p>
                    <p><b>Số điện thoại:</b> ' . this->e(r['so_dien_thoai'] ?? '') . '</p>
                    <p><b>Bác sĩ:</b> ' . this->e(r['ten_bac_si'] ?? '') . '</p>
                    <p><b>Chuyên khoa:</b> ' . this->e(r['ten_chuyen_khoa'] ?? '') . '</p>
                    <p><b>Ngày giờ:</b> ' . this->e(r['ngay_kham'] ?? '') . ' - ' . this->e(r['gio_kham'] ?? '') . '</p>
                    <p><b>Lý do khám:</b> ' . this->e(r['ly_do_kham'] ?? '') . '</p>
                    <p><b>Phí khám:</b> ' . number_format((float)($r['phi_kham'] ?? 0)) . ' VNĐ</p>
                    <p><b>Trạng thái:</b> <span class="font-bold">' . this->e(labels[$r['trang_thai']] ?? $r['trang_thai']) . '</span></p>';
            if (!empty($r['ma_phieu_kham'])) {
                content.='<pclass="text-emerald-700font-semibold">Đãcóphiếukhám#'.(int)r['ma_phieu_kham'] . '</p>';
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
                    <textarea name="quy_trinh_kham" rows="4" required class="w-full p-3 border rounded-xl text-sm">1. Tiếp nhận và kiểm tra thông tin đặt lịch.\n2. Làm thủ tục tại quầy tiếp đón.\n3. Gặp bác sĩ chuyên khoa.\n4. Thực hiện xét nghiệm hoặc chẩn đoán hình ảnh nếu bác sĩ chỉ định.\n5. Nhận kết luận và tư vấn sau khám.</textarea>
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
        id=(int)(_POST['id'] ?? 0);
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
            q->execute([id]);
            $lich = $q->fetch(PDO::FETCH_ASSOC);
            if (!$lich) {
                throw new RuntimeException('Không tìm thấy lịch khám.');
            }
            if (!in_array($lich['trang_thai'], transitions[newStatus] ?? [], true)) {
                throw new RuntimeException('Không thể chuyển trạng thái lịch khám này.');
            }

            if ($newStatus === 'da_xac_nhan') {
                huongDan=trim(_POST['huong_dan_chuan_bi'] ?? '');
                quyTrinh=trim(_POST['quy_trinh_kham'] ?? '');
                luuY=trim(_POST['luu_y'] ?? '');
                if ($huongDan === '' || $quyTrinh === '') {
                    throw new RuntimeException('Vui lòng nhập hướng dẫn chuẩn bị và quy trình khám.');
                }

                $q = $this->db->prepare(
                    "UPDATE lich_kham SET trang_thai='da_xac_nhan'
                     WHERE ma_lich_kham=? AND trang_thai='cho_xac_nhan'"
                );
                q->execute([id]);
                if ($q->rowCount() !== 1) {
                    throw new RuntimeException('Lịch khám đã được xử lý bởi thao tác khác.');
                }

                // ma_lich_kham có UNIQUE nên mỗi lịch chỉ có tối đa một phiếu.
                $q = $this->db->prepare(
                    "SELECT ma_phieu_kham FROM phieu_kham_benh WHERE ma_lich_kham=? LIMIT 1"
                );
                q->execute([id]);
                $existing = $q->fetch(PDO::FETCH_ASSOC);

                if ($existing) {
                    $q = $this->db->prepare(
                        "UPDATE phieu_kham_benh
                         SET huong_dan_chuan_bi=?, quy_trinh_kham=?, luu_y=?, trang_thai_phieu='da_gui'
                         WHERE ma_lich_kham=?"
                    );
                    q->execute([huongDan, $quyTrinh, $luuY, $id]);
                } else {
                    $q = $this->db->prepare(
                        "INSERT INTO phieu_kham_benh
                         (ma_lich_kham, chuan_doan, don_thuoc, loi_dan_bac_si,
                          ngay_tai_kham, huong_dan_chuan_bi, quy_trinh_kham,
                          luu_y, trang_thai_phieu)
                         VALUES (?, NULL, NULL, NULL, NULL, ?, ?, ?, 'da_gui')"
                    );
                    q->execute([id, $huongDan, $quyTrinh, $luuY]);
                }

                ngay=!empty(lich['ngay_kham'])
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
                q->execute([newStatus, $id]);
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
                    q->execute([id]);
                }
            }

            $this->db->commit();
            this->flash(newStatus === 'da_xac_nhan'
                ? 'Đã xác nhận lịch, tạo phiếu khám và gửi thông báo cho bệnh nhân.'
                : 'Đã cập nhật trạng thái lịch khám.');
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log('Admin appointment update: ' . $e->getMessage());
            this->flash(e->getMessage(), 'error');
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

        search=trim(_GET['q'] ?? '');

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
        q->execute([like, $like, $like]);

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
                . this->e(r['ho_ten']) . '</td>
<td class="p-4">' . this->e(r['email']) . '</td>
<td class="p-4">' . this->e(r['so_dien_thoai']) . '</td>
<td class="p-4">' . this->e(r['gioi_tinh'] ?? '') . '</td>
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
        id=(int)(_GET['id'] ?? 0);

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
            q->execute([id]);
            $r = $q->fetch(PDO::FETCH_ASSOC);

            if (!$r) {
                $this->flash('Không tìm thấy hồ sơ bệnh án.', 'error');
                header('Location: index.php?act=admin-benh-nhan&view=ho-so-benh-an');
                exit;
            }

            formatDate=staticfunction(date): string {
                return date?date('d/m/Y',strtotime(date)) : 'Chưa cập nhật';
            };
            $field = function (string $label, $value): string {
                value=trim((string)(value ?? ''));
                return '<div class="bg-slate-50 rounded-xl p-4">'
                    . '<p class="text-xs font-semibold text-slate-500 mb-2">'
                    . this->e(label) . '</p>'
                    . '<div class="text-sm text-slate-800 whitespace-pre-line break-words">'
                    . (value!==''?nl2br(this->e($value)) : '<span class="text-slate-400">Chưa có thông tin</span>')
                    . '</div></div>';
            };

            status=(r['trang_thai_phieu'] ?? '') === 'da_kham'
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
                . this->e(formatDate($r['ngay_tao'])) . '</b></div></div>'
                . '<h3 class="font-bold text-slate-900 mb-3">Thông tin bệnh nhân</h3>'
                . '<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">'
                . $field('Họ và tên', $r['ho_ten'])
                . $field('Mã bệnh nhân', '#' . $r['ma_nguoi_dung'])
                . $field('Email', $r['email'])
                . $field('Số điện thoại', $r['so_dien_thoai'])
                . $field('Giới tính', $r['gioi_tinh'])
                . $field('Ngày sinh', formatDate(r['ngay_sinh']))
                . $field('Địa chỉ', $r['dia_chi'])
                . '</div></section>'
                . '<section class="bg-white border border-slate-100 rounded-2xl p-5 md:p-7 shadow-sm mb-6">'
                . '<h3 class="font-bold text-slate-900 mb-4">Thông tin lần khám</h3>'
                . '<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">'
                . $field('Mã lịch khám', '#' . $r['ma_lich_kham'])
                . $field('Ngày khám', formatDate(r['ngay_kham']))
                . $field('Giờ khám', $r['gio_kham'])
                . $field('Bác sĩ phụ trách', $r['ten_bac_si'])
                . $field('Chuyên khoa', $r['ten_chuyen_khoa'])
                . $field('Phí khám', r['phikham']!==null?numberformat((float)r['phi_kham'], 0, ',', '.') . ' đ' : '')
                . '</div></section>'
                . '<section class="bg-white border border-slate-100 rounded-2xl p-5 md:p-7 shadow-sm">'
                . '<h3 class="font-bold text-slate-900 mb-4">Nội dung hồ sơ bệnh án</h3>'
                . '<div class="grid grid-cols-1 md:grid-cols-2 gap-3">'
                . $field('Chẩn đoán của bác sĩ', $r['chuan_doan'])
                . $field('Đơn thuốc', $r['don_thuoc'])
                . $field('Lời dặn bác sĩ', $r['loi_dan_bac_si'])
                . $field('Ngày tái khám', formatDate(r['ngay_tai_kham']))
                . $field('Hướng dẫn chuẩn bị', $r['huong_dan_chuan_bi'])
                . $field('Quy trình khám', $r['quy_trinh_kham'])
                . $field('Lưu ý', $r['luu_y'])
                . '</div><p class="text-xs text-slate-400 mt-5">Thông tin chuyên môn được lưu theo kết quả bác sĩ cập nhật. Admin chỉ xem và tra cứu hồ sơ.</p></section>';

            $this->render('Chi tiết hồ sơ bệnh án', $content);
            return;
        }

        search=trim(_GET['q'] ?? '');
        status=trim(_GET['status'] ?? '');
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
        params=[like, $like, $like, $like, $like];
        if (in_array($status, ['da_gui', 'da_kham'], true)) {
            $sql .= ' AND pk.trang_thai_phieu = ?';
            $params[] = $status;
        }
        $sql .= ' ORDER BY pk.ngay_tao DESC, pk.ma_phieu_kham DESC';

        $q = this->db->prepare(sql);
        q->execute(params);
        $rows = $q->fetchAll(PDO::FETCH_ASSOC);

        $content = '<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">'
            . '<div class="bg-white rounded-2xl border border-slate-100 p-5"><p class="text-xs text-slate-500">Tổng hồ sơ tìm thấy</p><p class="text-3xl font-black text-slate-900 mt-2">'
            . count($rows) . '</p></div>'
            . '<div class="bg-white rounded-2xl border border-slate-100 p-5"><p class="text-xs text-slate-500">Đang chờ khám</p><p class="text-3xl font-black text-amber-600 mt-2">'
            . count(array_filter(rows,staticfn(x) => ($x['trang_thai_phieu'] ?? '') === 'da_gui')) . '</p></div>'
            . '<div class="bg-white rounded-2xl border border-slate-100 p-5"><p class="text-xs text-slate-500">Đã khám</p><p class="text-3xl font-black text-emerald-600 mt-2">'
            . count(array_filter(rows,staticfn(x) => ($x['trang_thai_phieu'] ?? '') === 'da_kham')) . '</p></div>'
            . '</div>'
            . '<form method="GET" class="bg-white border border-slate-100 rounded-2xl p-4 mb-5 flex flex-col md:flex-row gap-3">'
            . '<input type="hidden" name="act" value="admin-benh-nhan"><input type="hidden" name="view" value="ho-so-benh-an">'
            . '<input type="text" name="q" value="' . this->e(search) . '" placeholder="Tìm tên, email, số điện thoại, mã bệnh nhân hoặc mã hồ sơ..." class="flex-1 p-3 border border-slate-200 rounded-xl text-sm">'
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
            done=(r['trang_thai_phieu'] ?? '') === 'da_kham';
            $badge = $done
                ? '<span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 text-xs font-bold">Đã khám</span>'
                : '<span class="px-3 py-1 rounded-full bg-amber-100 text-amber-700 text-xs font-bold">Chờ khám</span>';
            date=!empty(r['ngay_kham']) ? date('d/m/Y', strtotime($r['ngay_kham'])) : '—';
            $content .= '<tr class="border-t hover:bg-slate-50">'
                . '<td class="p-4 font-bold">#' . (int)$r['ma_phieu_kham'] . '</td>'
                . '<td class="p-4"><div class="font-bold">' . this->e(r['ho_ten'] ?? 'Chưa rõ') . '</div><div class="text-xs text-slate-500">' . this->e(r['email'] ?? '') . '</div><div class="text-xs text-slate-500">Mã BN: #' . (int)$r['ma_nguoi_dung'] . '</div></td>'
                . '<td class="p-4">' . this->e(r['ten_bac_si'] ?? 'Chưa phân công') . '</td>'
                . '<td class="p-4">' . this->e(r['ten_chuyen_khoa'] ?? '—') . '</td>'
                . '<td class="p-4">' . this->e(date) . '<div class="text-xs text-slate-500">' . this->e(r['gio_kham'] ?? '') . '</div></td>'
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

        count=count(rows);
        $sum = 0;

        foreach ($rows as $r) {
            sum+=(int)r['so_sao'];
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
                    . this->e(r['ho_ten'] ?? 'Không xác định')
                    . '</td>

<td class="p-4">'
                    . this->e(r['ten_bac_si'] ?? 'Không xác định')
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
                    . nl2br(this->e(r['noi_dung']))
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
                title=trim(_POST['tieu_de'] ?? '');
                body=trim(_POST['noi_dung'] ?? '');
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
                    q->execute([(int)recipient]);
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

                this->flash(e->getMessage(), 'error');
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

        $content = '
<form method="POST"
class="bg-white p-6 rounded-2xl
border border-slate-100 shadow-sm space-y-4 mb-7">'
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
                . this->e(p['ho_ten'])
                . '</option>';
        }

        $content .= '</select>'
            . $this->button('Gửi thông báo')
            . '</form>

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
<td class="p-4">' . this->e(r['ho_ten'] ?? '') . '</td>
<td class="p-4 font-bold">' . this->e(r['tieu_de']) . '</td>
<td class="p-4">' . this->e(r['noi_dung']) . '</td>
<td class="p-4">' . this->e(r['loai_thong_bao']) . '</td>
<td class="p-4">' . this->e(r['trang_thai']) . '</td>
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
}

