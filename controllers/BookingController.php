<?php
require_once __DIR__ . '/../config/Database.php';

class BookingController {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function chonLoaiLich() {
        if (file_exists('views/chon_loai_lich.php')) {
            include_once 'views/chon_loai_lich.php';
        } else {
            header('Location: index.php?act=home');
            exit();
        }
    }

    // Luồng đặt lịch chuyên khoa: Chọn chuyên khoa -> Hiện bác sĩ -> Chọn ngày -> Hiện giờ trống
    public function formDatLichChuyenKhoa() {
        $danhSachChuyenKhoa = $this->db->query("SELECT * FROM chuyen_khoa")->fetchAll(PDO::FETCH_ASSOC);
        
        $maChuyenKhoa = $_GET['ma_chuyen_khoa'] ?? null;
        $danhSachBacSiTheoCK = [];
        
        if ($maChuyenKhoa) {
            $stmt = $this->db->prepare("SELECT * FROM bac_si WHERE ma_chuyen_khoa = :ck");
            $stmt->execute([':ck' => $maChuyenKhoa]);
            $danhSachBacSiTheoCK = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        include_once 'views/header.php';
        echo '<main class="max-w-4xl mx-auto px-4 py-10 space-y-6">
                <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-sm space-y-6">
                    <h2 class="text-xl font-extrabold text-slate-950">Đặt Lịch Khám Theo Chuyên Khoa</h2>
                    
                    <!-- Bước 1: Chọn Chuyên Khoa -->
                    <form method="GET" action="index.php" class="space-y-4">
                        <input type="hidden" name="act" value="dat-lich-chuyen-khoa">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">1. Chọn Chuyên Khoa <span class="text-red-500">*</span></label>
                            <select name="ma_chuyen_khoa" onchange="this.form.submit()" required class="w-full p-3.5 border rounded-2xl text-xs bg-slate-50 font-semibold text-slate-800">
                                <option value="">-- Chọn chuyên khoa y tế --</option>';
                                foreach($danhSachChuyenKhoa as $ck) {
                                    $selected = ($maChuyenKhoa == $ck['ma_chuyen_khoa']) ? 'selected' : '';
                                    echo '<option value="'.$ck['ma_chuyen_khoa'].'" '.$selected.'>'.$ck['ten_chuyen_khoa'].'</option>';
                                }
        echo '</select>
                        </div>
                    </form>';

                    // Bước 2, 3, 4: Chỉ hiển thị tiếp khi đã chọn chuyên khoa
                    if ($maChuyenKhoa) {
                        echo '<form action="index.php?act=xac-nhan-dat-lich" method="POST" class="space-y-6 pt-4 border-t border-slate-100">
                                <input type="hidden" name="ma_chuyen_khoa" value="'.$maChuyenKhoa.'">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-2">2. Chọn Bác Sĩ Chuyên Khoa <span class="text-red-500">*</span></label>
                                    <select name="ma_bac_si" required class="w-full p-3.5 border rounded-2xl text-xs bg-slate-50 font-semibold text-slate-800">
                                        <option value="">-- Chọn bác sĩ thuộc chuyên khoa --</option>';
                                        foreach($danhSachBacSiTheoCK as $bs) {
                                            echo '<option value="'.$bs['ma_bac_si'].'">'.$bs['ten_bac_si'].' ('.($bs['bang_cap'] ?? 'BS').')</option>';
                                        }
                        echo '</select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-2">3. Chọn Ngày Khám <span class="text-red-500">*</span></label>
                                    <input type="date" id="ngay_kham" name="ngay_kham" min="'.date('Y-m-d').'" required onchange="hienThiKhungGio()" class="w-full p-3.5 border rounded-2xl text-xs bg-slate-50 font-semibold text-slate-800">
                                </div>

                                <!-- Bước 4: Khung giờ trống (Ẩn đi cho đến khi chọn ngày khám) -->
                                <div id="khung-gio-container" style="display: none;" class="space-y-2">
                                    <label class="block text-xs font-bold text-slate-700 mb-2">4. Chọn Khung Giờ Trống <span class="text-red-500">*</span></label>
                                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                        <label class="border p-3 rounded-xl text-center text-xs font-bold cursor-pointer hover:bg-brand-50 hover:border-brand-600 transition"><input type="radio" name="gio_kham" value="08:00 - 09:00" required class="hidden peer"> <span class="peer-checked:text-brand-600">08:00 - 09:00</span></label>
                                        <label class="border p-3 rounded-xl text-center text-xs font-bold cursor-pointer hover:bg-brand-50 hover:border-brand-600 transition"><input type="radio" name="gio_kham" value="09:00 - 10:00" required class="hidden peer"> <span class="peer-checked:text-brand-600">09:00 - 10:00</span></label>
                                        <label class="border p-3 rounded-xl text-center text-xs font-bold cursor-pointer hover:bg-brand-50 hover:border-brand-600 transition"><input type="radio" name="gio_kham" value="10:00 - 11:00" required class="hidden peer"> <span class="peer-checked:text-brand-600">10:00 - 11:00</span></label>
                                        <label class="border p-3 rounded-xl text-center text-xs font-bold cursor-pointer hover:bg-brand-50 hover:border-brand-600 transition"><input type="radio" name="gio_kham" value="14:00 - 15:00" required class="hidden peer"> <span class="peer-checked:text-brand-600">14:00 - 15:00</span></label>
                                    </div>
                                </div>

                                <button type="submit" class="w-full bg-brand-600 hover:bg-brand-700 text-white font-bold py-4 rounded-2xl text-xs shadow-md uppercase tracking-wider transition">Xác Nhận Đặt Lịch Khám</button>
                              </form>

                              <script>
                              function hienThiKhungGio() {
                                  var ngay = document.getElementById("ngay_kham").value;
                                  var container = document.getElementById("khung-gio-container");
                                  if (ngay) {
                                      container.style.display = "block";
                                  } else {
                                      container.style.display = "none";
                                  }
                              }
                              </script>';
                    } else {
                        echo '<p class="text-xs text-slate-400 italic text-center py-4 bg-slate-50 rounded-2xl">Vui lòng chọn chuyên khoa ở trên để tiếp tục hiển thị danh sách bác sĩ.</p>';
                    }

        echo '</div></main>';
        include_once 'views/footer.php';
    }

    // Luồng đặt lịch gói khám: Chọn gói -> Chọn ngày -> Hiện giờ trống
    public function formDatLichGoiKham() {
        $danhSachGoiKham = $this->db->query("SELECT * FROM goi_kham")->fetchAll(PDO::FETCH_ASSOC);

        include_once 'views/header.php';
        echo '<main class="max-w-4xl mx-auto px-4 py-10 space-y-6">
                <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-sm space-y-6">
                    <h2 class="text-xl font-extrabold text-slate-950">Đặt Lịch Gói Khám Sức Khỏe</h2>
                    <form action="index.php?act=xac-nhan-dat-goi" method="POST" class="space-y-6">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">Chọn Gói Khám Dịch Vụ <span class="text-red-500">*</span></label>
                            <select name="ma_goi_kham" required class="w-full p-3.5 border rounded-2xl text-xs bg-slate-50 font-semibold text-slate-800">
                                <option value="">-- Chọn gói tầm soát sức khỏe chuẩn 5 sao --</option>';
                                foreach($danhSachGoiKham as $gk) {
                                    $tenGoi = $gk['ten_goi_kham'] ?? $gk['ten_goi'] ?? $gk['tieu_de'] ?? 'Gói khám';
                                    echo '<option value="'.$gk['ma_goi_kham'].'">'.$tenGoi.' - '.number_format($gk['gia'] ?? 0, 0, ',', '.').' đ</option>';
                                }
        echo '</select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">Chọn Ngày Thực Hiện <span class="text-red-500">*</span></label>
                            <input type="date" id="ngay_goi" name="ngay_kham" min="'.date('Y-m-d').'" required onchange="hienThiGioGoi()" class="w-full p-3.5 border rounded-2xl text-xs bg-slate-50 font-semibold text-slate-800">
                        </div>

                        <!-- Khung giờ trống (Ẩn đi cho đến khi chọn ngày) -->
                        <div id="gio-goi-container" style="display: none;" class="space-y-2">
                            <label class="block text-xs font-bold text-slate-700 mb-2">Chọn Khung Giờ Trống <span class="text-red-500">*</span></label>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                <label class="border p-3 rounded-xl text-center text-xs font-bold cursor-pointer hover:bg-brand-50 hover:border-brand-600 transition"><input type="radio" name="gio_kham" value="08:00 - 09:00" required class="hidden peer"> <span class="peer-checked:text-brand-600">08:00 - 09:00</span></label>
                                <label class="border p-3 rounded-xl text-center text-xs font-bold cursor-pointer hover:bg-brand-50 hover:border-brand-600 transition"><input type="radio" name="gio_kham" value="09:00 - 10:00" required class="hidden peer"> <span class="peer-checked:text-brand-600">09:00 - 10:00</span></label>
                                <label class="border p-3 rounded-xl text-center text-xs font-bold cursor-pointer hover:bg-brand-50 hover:border-brand-600 transition"><input type="radio" name="gio_kham" value="10:00 - 11:00" required class="hidden peer"> <span class="peer-checked:text-brand-600">10:00 - 11:00</span></label>
                                <label class="border p-3 rounded-xl text-center text-xs font-bold cursor-pointer hover:bg-brand-50 hover:border-brand-600 transition"><input type="radio" name="gio_kham" value="14:00 - 15:00" required class="hidden peer"> <span class="peer-checked:text-brand-600">14:00 - 15:00</span></label>
                            </div>
                        </div>

                        <button type="submit" class="w-full bg-brand-600 hover:bg-brand-700 text-white font-bold py-4 rounded-2xl text-xs shadow-md uppercase tracking-wider transition">Xác Nhận Đặt Gói Khám</button>
                    </form>
                </div>
              </main>

              <script>
              function hienThiGioGoi() {
                  var ngay = document.getElementById("ngay_goi").value;
                  var container = document.getElementById("gio-goi-container");
                  if (ngay) {
                      container.style.display = "block";
                  } else {
                      container.style.display = "none";
                  }
              }
              </script>';
        include_once 'views/footer.php';
    }
}