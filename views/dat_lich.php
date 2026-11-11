<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');

require_once __DIR__ . '/header.php';

// Chuẩn bị danh sách bác sĩ để JavaScript lọc theo chuyên khoa
$danhSachBacSiJS = [];

foreach ($danhSachBacSi ?? [] as $bs) {
    $danhSachBacSiJS[] = [
        'ma_bac_si' => (string)($bs['ma_bac_si'] ?? ''),
        'ma_chuyen_khoa' => (string)($bs['ma_chuyen_khoa'] ?? ''),
        'ten_bac_si' => (string)($bs['ten_bac_si'] ?? ''),
        'ten_chuyen_khoa' => (string)($bs['ten_chuyen_khoa'] ?? '')
    ];
}

$bacSiJson = json_encode(
    $danhSachBacSiJS,
    JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS |
    JSON_HEX_QUOT | JSON_HEX_AMP
);

if ($bacSiJson === false) {
    $bacSiJson = '[]';
}
?>

<main class="max-w-4xl mx-auto px-4 py-10"
      x-data="datLichChuyenKhoaApp(<?= htmlspecialchars($bacSiJson, ENT_QUOTES, 'UTF-8') ?>)">

    <div class="bg-white p-6 md:p-8 rounded-3xl border border-slate-100 shadow-sm space-y-7">

        <!-- Tiêu đề -->
        <div>
            <span class="text-brand-600 font-bold text-xs uppercase tracking-wider block mb-2">
                Đặt lịch khám
            </span>

            <h1 class="text-2xl md:text-3xl font-bold text-slate-900">
                Đặt lịch khám theo chuyên khoa
            </h1>

            <p class="text-sm text-slate-500 mt-2">
                Vui lòng chọn chuyên khoa, bác sĩ, ngày khám và khung giờ phù hợp.
            </p>
        </div>

        <form action="index.php?act=dat-lich-exec"
              method="POST"
              class="space-y-6"
              @submit="return kiemTraTruocKhiGui($event)">

            <!-- Chọn chuyên khoa -->
            <div class="space-y-2">
                <label class="block text-sm font-semibold text-slate-700">
                    Chuyên khoa <span class="text-red-500">*</span>
                </label>

                <select name="ma_chuyen_khoa"
                        x-model="maChuyenKhoa"
                        @change="doiChuyenKhoa()"
                        required
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3.5 text-sm text-slate-700 focus:outline-none focus:border-brand-600">

                    <option value="">-- Chọn chuyên khoa --</option>

                    <?php foreach ($danhSachChuyenKhoa ?? [] as $ck): ?>
                        <option value="<?= htmlspecialchars((string)$ck['ma_chuyen_khoa'], ENT_QUOTES, 'UTF-8') ?>">
                            <?= htmlspecialchars($ck['ten_chuyen_khoa'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>

                </select>
            </div>

            <!-- Chọn bác sĩ -->
            <div class="space-y-2">
                <label class="block text-sm font-semibold text-slate-700">
                    Bác sĩ phụ trách <span class="text-red-500">*</span>
                </label>

                <select name="ma_bac_si"
                        x-model="maBacSi"
                        @change="doiBacSi()"
                        required
                        :disabled="!maChuyenKhoa"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3.5 text-sm text-slate-700 focus:outline-none focus:border-brand-600 disabled:opacity-50">

                    <option value="">-- Chọn bác sĩ --</option>

                    <template x-for="bs in bacSiTheoChuyenKhoa()" :key="bs.ma_bac_si">
                        <option :value="bs.ma_bac_si"
                                x-text="bs.ten_bac_si">
                        </option>
                    </template>

                </select>

                <p x-show="maChuyenKhoa && bacSiTheoChuyenKhoa().length === 0"
                   class="text-xs text-amber-600">
                    Chuyên khoa này hiện chưa có bác sĩ.
                </p>

                <p x-show="!maChuyenKhoa"
                   class="text-xs text-slate-400">
                    Vui lòng chọn chuyên khoa trước.
                </p>
            </div>

            <!-- Chọn ngày khám -->
            <div class="space-y-2">
                <label class="block text-sm font-semibold text-slate-700">
                    Ngày khám <span class="text-red-500">*</span>
                </label>

                <input type="date"
                       name="ngay_kham"
                       x-model="ngayKham"
                       @change="doiNgayKham()"
                       min="<?= date('Y-m-d') ?>"
                       required
                       class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3.5 text-sm text-slate-700 focus:outline-none focus:border-brand-600">

                <p class="text-xs text-slate-500">
                    Không thể chọn ngày đã qua.
                </p>
            </div>

            <!-- Chọn giờ khám -->
            <div class="space-y-3">
                <label class="block text-sm font-semibold text-slate-700">
                    Khung giờ khám <span class="text-red-500">*</span>
                </label>

                <input type="hidden"
                       name="gio_kham"
                       x-model="selectedTime"
                       required>

                <!-- Chưa chọn đủ thông tin -->
                <div x-show="!maChuyenKhoa || !maBacSi || !ngayKham"
                     class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-700 text-sm">
                    <i class="fa-solid fa-circle-info mr-1"></i>
                    Vui lòng chọn chuyên khoa, bác sĩ và ngày khám để xem giờ còn trống.
                </div>

                <!-- Đang tải -->
                <div x-show="dangTai"
                     class="p-4 rounded-xl bg-blue-50 border border-blue-200 text-blue-700 text-sm">
                    <i class="fa-solid fa-spinner fa-spin mr-1"></i>
                    Đang kiểm tra lịch khám...
                </div>

                <!-- Lỗi tải dữ liệu -->
                <div x-show="loiTaiDuLieu"
                     class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm">
                    Không thể tải danh sách giờ khám. Vui lòng chọn lại ngày hoặc thử lại.
                    <button type="button"
                            @click="fetchGioDaDat()"
                            class="underline font-semibold ml-1">
                        Thử lại
                    </button>
                </div>

                <!-- Danh sách khung giờ -->
                <div x-show="maBacSi && ngayKham && !dangTai && !loiTaiDuLieu"
                     class="grid grid-cols-2 sm:grid-cols-4 gap-3">

                    <template x-for="gio in danhSachGio" :key="gio">
                        <button type="button"
                                @click="chonGio(gio)"
                                :disabled="khongTheChonGio(gio)"
                                :class="{
                                    'bg-brand-600 text-white border-brand-600 shadow-md':
                                        selectedTime === gio,

                                    'bg-slate-100 text-slate-400 border-slate-200 cursor-not-allowed opacity-60':
                                        khongTheChonGio(gio),

                                    'bg-white text-slate-700 border-slate-200 hover:border-brand-500':
                                        !khongTheChonGio(gio) && selectedTime !== gio
                                }"
                                class="py-3 px-2 rounded-xl border text-sm font-semibold transition text-center">

                            <span class="block">
                                <i class="fa-regular fa-clock mr-1"></i>
                                <span x-text="gio"></span>
                            </span>

                            <span class="block text-[11px] mt-1 font-normal"
                                  x-text="trangThaiGio(gio)">
                            </span>
                        </button>
                    </template>

                </div>

                <!-- Chú thích -->
                <div x-show="maBacSi && ngayKham"
                     class="flex flex-wrap gap-4 text-xs text-slate-500 pt-1">

                    <span>
                        <span class="inline-block w-3 h-3 rounded bg-white border border-slate-300 mr-1"></span>
                        Còn trống
                    </span>

                    <span>
                        <span class="inline-block w-3 h-3 rounded bg-slate-200 mr-1"></span>
                        Đã đặt hoặc đã qua
                    </span>

                    <span>
                        <span class="inline-block w-3 h-3 rounded bg-brand-600 mr-1"></span>
                        Đang chọn
                    </span>
                </div>

                <p x-show="maBacSi && ngayKham && !dangTai && !loiTaiDuLieu && tatCaGioDaHet()"
                   class="text-sm text-amber-600">
                    Ngày này không còn khung giờ phù hợp. Vui lòng chọn ngày khác.
                </p>
            </div>

            <!-- Lý do khám -->
            <div class="space-y-2">
                <label class="block text-sm font-semibold text-slate-700">
                    Lý do khám / Triệu chứng
                </label>

                <textarea name="ly_do_kham"
                          rows="4"
                          placeholder="Mô tả triệu chứng hoặc nhu cầu khám của bạn..."
                          class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3.5 text-sm text-slate-700 focus:outline-none focus:border-brand-600"></textarea>
            </div>

            <!-- Nút xác nhận -->
            <button type="submit"
                    :disabled="!selectedTime || dangTai || loiTaiDuLieu"
                    class="w-full bg-brand-600 hover:bg-brand-700 disabled:opacity-50 disabled:cursor-not-allowed text-white font-bold py-4 rounded-xl shadow-lg transition text-sm uppercase tracking-wider">

                <i class="fa-solid fa-calendar-check mr-2"></i>
                Xác nhận đặt lịch khám
            </button>

        </form>
    </div>
</main>

<script>
function datLichChuyenKhoaApp(danhSachBacSi) {
    return {
        danhSachBacSi: danhSachBacSi || [],

        maChuyenKhoa: '',
        maBacSi: '',
        ngayKham: '',
        selectedTime: '',

        danhSachGio: [
            '08:00',
            '09:00',
            '10:00',
            '11:00',
            '13:30',
            '14:30',
            '15:30',
            '16:30'
        ],

        gioDaDat: [],
        dangTai: false,
        loiTaiDuLieu: false,

        // Lấy bác sĩ thuộc chuyên khoa đang chọn
        bacSiTheoChuyenKhoa() {
            return this.danhSachBacSi.filter(bs =>
                String(bs.ma_chuyen_khoa) === String(this.maChuyenKhoa)
            );
        },

        // Khi thay đổi chuyên khoa
        doiChuyenKhoa() {
            this.maBacSi = '';
            this.ngayKham = '';
            this.selectedTime = '';
            this.gioDaDat = [];
            this.dangTai = false;
            this.loiTaiDuLieu = false;
        },

        // Khi thay đổi bác sĩ
        doiBacSi() {
            this.selectedTime = '';
            this.gioDaDat = [];
            this.loiTaiDuLieu = false;
            this.fetchGioDaDat();
        },

        // Khi thay đổi ngày
        doiNgayKham() {
            this.selectedTime = '';
            this.gioDaDat = [];
            this.loiTaiDuLieu = false;
            this.fetchGioDaDat();
        },

        // Kiểm tra ngày hiện tại theo giờ Việt Nam
        laHomNay() {
            const now = new Date();

            const nam = now.getFullYear();
            const thang = String(now.getMonth() + 1).padStart(2, '0');
            const ngay = String(now.getDate()).padStart(2, '0');

            const homNay = `${nam}-${thang}-${ngay}`;

            return this.ngayKham === homNay;
        },

        // Kiểm tra giờ đã qua trong ngày hôm nay
        laGioDaQua(gio) {
            if (!this.laHomNay()) {
                return false;
            }

            const now = new Date();
            const gioHienTai = now.getHours() * 60 + now.getMinutes();

            const parts = gio.split(':');
            const gioKham = Number(parts[0]) * 60 + Number(parts[1]);

            return gioKham <= gioHienTai;
        },

        // Kiểm tra giờ đã có người đặt
        isDaDat(gio) {
            return this.gioDaDat.includes(gio);
        },

        // Giờ không thể chọn nếu đã qua hoặc đã được đặt
        khongTheChonGio(gio) {
            return this.isDaDat(gio) || this.laGioDaQua(gio);
        },

        // Nội dung trạng thái dưới mỗi khung giờ
        trangThaiGio(gio) {
            if (this.isDaDat(gio)) {
                return 'Đã có lịch';
            }

            if (this.laGioDaQua(gio)) {
                return 'Đã qua';
            }

            return 'Còn trống';
        },

        // Chọn giờ
        chonGio(gio) {
            if (this.khongTheChonGio(gio)) {
                return;
            }

            this.selectedTime = gio;
        },

        // Kiểm tra còn giờ hay không
        tatCaGioDaHet() {
            return this.danhSachGio.every(gio =>
                this.khongTheChonGio(gio)
            );
        },

        // Lấy giờ đã đặt từ máy chủ
        async fetchGioDaDat() {
            this.gioDaDat = [];
            this.loiTaiDuLieu = false;

            if (!this.maBacSi || !this.ngayKham) {
                this.dangTai = false;
                return;
            }

            this.dangTai = true;

            const params = new URLSearchParams({
                ma_bac_si: this.maBacSi,
                ngay_kham: this.ngayKham
            });

            try {
                const response = await fetch(
                    `index.php?act=api-gio-da-dat&${params.toString()}`
                );

                if (!response.ok) {
                    throw new Error('Không thể tải lịch khám.');
                }

                const data = await response.json();

                if (!Array.isArray(data)) {
                    throw new Error('Dữ liệu giờ khám không hợp lệ.');
                }

                this.gioDaDat = data.map(item => {
                    // Hỗ trợ API trả về chuỗi giờ hoặc đối tượng có gio_kham
                    const value = typeof item === 'object' && item !== null
                        ? item.gio_kham
                        : item;

                    return String(value || '').substring(0, 5);
                });

                if (
                    this.selectedTime &&
                    this.khongTheChonGio(this.selectedTime)
                ) {
                    this.selectedTime = '';
                }

            } catch (error) {
                console.error(error);
                this.loiTaiDuLieu = true;
                this.selectedTime = '';
            } finally {
                this.dangTai = false;
            }
        },

        // Kiểm tra lần cuối trước khi gửi form
        kiemTraTruocKhiGui(event) {
            if (!this.maChuyenKhoa || !this.maBacSi || !this.ngayKham) {
                alert('Vui lòng chọn đầy đủ chuyên khoa, bác sĩ và ngày khám.');
                return false;
            }

            if (!this.selectedTime) {
                alert('Vui lòng chọn khung giờ khám.');
                return false;
            }

            if (this.khongTheChonGio(this.selectedTime)) {
                alert('Khung giờ này đã qua hoặc đã có người đặt. Vui lòng chọn giờ khác.');
                this.selectedTime = '';
                this.fetchGioDaDat();
                return false;
            }

            return true;
        }
    };
}
</script>

<?php require_once __DIR__ . '/footer.php'; ?>