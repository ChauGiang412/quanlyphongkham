<?php require_once 'views/header.php'; ?>

<main class="max-w-4xl mx-auto px-4 py-12" x-data="datGoiKhamApp()">
    <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-sm space-y-6">
        <div>
            <span class="text-brand-600 font-bold text-xs uppercase tracking-wider block mb-1">Dịch Vụ Trọn Gói</span>
            <h1 class="text-2xl md:text-3xl font-bold text-slate-900">Đặt Lịch Gói Khám Sức Khỏe Riêng</h1>
            <p class="text-xs text-slate-500 mt-1">Chọn gói dịch vụ, bác sĩ phụ trách và ngày khám để hiển thị danh sách các khung giờ trống.</p>
        </div>

        <form action="index.php?act=dat-goi-kham-exec" method="POST" class="space-y-6">
            
            <!-- Chọn Gói Khám Dịch Vụ -->
            <div class="space-y-2">
                <label class="block text-xs font-bold text-slate-700">Chọn Gói Khám Sức Khỏe <span class="text-red-500">*</span></label>
                <select name="ma_goi_kham" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl p-3.5 text-xs text-slate-700 focus:outline-none focus:border-brand-600">
                    <option value="">-- Chọn gói khám dịch vụ --</option>
                    <?php foreach ($danhSachGoiKham as $gk): ?>
                        <option value="<?= $gk['ma_goi_kham']; ?>" <?= (isset($goiHienTai) && $goiHienTai['ma_goi_kham'] == $gk['ma_goi_kham']) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($gk['ten_goi']); ?> - <?= number_format($gk['gia'], 0, ',', '.'); ?> đ
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Chọn Bác Sĩ Phụ Trách Gói -->
            <div class="space-y-2">
                <label class="block text-xs font-bold text-slate-700">Chọn Bác Sĩ Phụ Trách Gói <span class="text-red-500">*</span></label>
                <select name="ma_bac_si" x-model="maBacSi" @change="fetchGioDaDat()" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl p-3.5 text-xs text-slate-700 focus:outline-none focus:border-brand-600">
                    <option value="">-- Chọn bác sĩ phụ trách --</option>
                    <?php foreach ($danhSachBacSi as $bs): ?>
                        <option value="<?= $bs['ma_bac_si']; ?>"><?= htmlspecialchars($bs['ten_bac_si']); ?> (<?= htmlspecialchars($bs['ten_chuyen_khoa'] ?? 'Chuyên khoa'); ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Chọn Ngày Khám -->
            <div class="space-y-2">
                <label class="block text-xs font-bold text-slate-700">Ngày Thực Hiện Gói Khám <span class="text-red-500">*</span></label>
                <input type="date" name="ngay_kham" x-model="ngayKham" @change="fetchGioDaDat()" required min="<?= date('Y-m-d'); ?>" class="w-full bg-slate-50 border border-slate-200 rounded-2xl p-3.5 text-xs text-slate-700 focus:outline-none focus:border-brand-600">
            </div>

            <!-- Khung Giờ Trống Thực Hiện Gói (Bắt buộc chọn Bác sĩ & Ngày mới hiện) -->
            <div class="space-y-2">
                <label class="block text-xs font-bold text-slate-700">Chọn Khung Giờ Trống <span class="text-red-500">*</span></label>
                <input type="hidden" name="gio_kham" x-model="selectedTime" required>
                
                <!-- Cảnh báo nếu chưa chọn đủ điều kiện -->
                <div x-show="!maBacSi || !ngayKham" class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-700 text-xs flex items-center space-x-2">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span>Vui lòng chọn <strong>Bác sĩ phụ trách</strong> và <strong>Ngày khám</strong> để hiển thị khung giờ trống của gói này.</span>
                </div>

                <!-- Danh sách khung giờ trống -->
                <div x-show="maBacSi && ngayKham" class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-1">
                    <template x-for="gio in danhSachGio" :key="gio">
                        <button type="button" 
                                @click="if (!isDaDat(gio)) { selectedTime = gio; }"
                                :disabled="isDaDat(gio)"
                                :class="{
                                    'bg-brand-600 text-white border-brand-600 shadow-md': selectedTime === gio,
                                    'bg-slate-100 text-slate-400 border-slate-200 cursor-not-allowed opacity-60': isDaDat(gio),
                                    'bg-slate-50 text-slate-700 border-slate-200 hover:border-brand-400': !isDaDat(gio) && selectedTime !== gio
                                }"
                                class="py-3 px-2 rounded-2xl border text-xs font-bold transition text-center flex flex-col items-center justify-center">
                            <span class="flex items-center"><i class="fa-regular fa-clock mr-1"></i> <span x-text="gio"></span></span>
                            <span class="text-[10px] mt-0.5 font-normal" x-text="isDaDat(gio) ? 'Đã có lịch' : 'Còn trống'"></span>
                        </button>
                    </template>
                </div>
            </div>

            <!-- Ghi Chú Riêng -->
            <div class="space-y-2">
                <label class="block text-xs font-bold text-slate-700">Ghi Chú Cho Gói Khám</label>
                <textarea name="ghi_chu" rows="3" placeholder="Nhập yêu cầu đặc biệt khi thực hiện gói..." class="w-full bg-slate-50 border border-slate-200 rounded-2xl p-3.5 text-xs text-slate-700 focus:outline-none focus:border-brand-600"></textarea>
            </div>

            <button type="submit" class="w-full bg-brand-600 hover:bg-brand-700 text-white font-bold py-4 rounded-2xl shadow-lg transition text-xs uppercase tracking-wider">
                Xác Nhận Đặt Lịch Gói Khám Riêng
            </button>
        </form>
    </div>
</main>

<script>
function datGoiKhamApp() {
    return {
        maBacSi: '',
        ngayKham: '',
        selectedTime: '',
        danhSachGio: ['08:00', '09:00', '10:00', '11:00', '13:30', '14:30', '15:30', '16:30'],
        gioDaDat: [],

        fetchGioDaDat() {
            if (this.maBacSi && this.ngayKham) {
                fetch(`index.php?act=api-gio-da-dat&ma_bac_si=${this.maBacSi}&ngay_kham=${this.ngayKham}`)
                    .then(res => res.json())
                    .then(data => {
                        this.gioDaDat = data;
                        if (this.gioDaDat.includes(this.selectedTime)) {
                            this.selectedTime = '';
                        }
                    })
                    .catch(err => console.error('Lỗi tải khung giờ:', err));
            } else {
                this.gioDaDat = [];
            }
        },

        isDaDat(gio) {
            return this.gioDaDat.includes(gio);
        }
    }
}
<?php require_once 'views/header.php'; ?>

<main class="max-w-4xl mx-auto px-4 py-12"
      x-data="datGoiKhamApp()"
      x-init="$nextTick(() => locBacSiTheoGoi())">

    <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-sm space-y-6">

        <div>
            <span class="text-brand-600 font-bold text-xs uppercase tracking-wider block mb-1">
                Dịch Vụ Trọn Gói
            </span>

            <h1 class="text-2xl md:text-3xl font-bold text-slate-900">
                Đặt Lịch Gói Khám Sức Khỏe Riêng
            </h1>

            <p class="text-xs text-slate-500 mt-1">
                Chọn gói dịch vụ, bác sĩ phụ trách và ngày khám để hiển thị danh sách các khung giờ trống.
            </p>
        </div>

        <form action="index.php?act=dat-goi-kham-exec"
              method="POST"
              class="space-y-6"
              @submit="if (!selectedTime) {
                  alert('Vui lòng chọn khung giờ khám!');
                  return false;
              }">

            <!-- Chọn gói khám -->
            <div class="space-y-2">

                <label class="block text-xs font-bold text-slate-700">
                    Chọn Gói Khám Sức Khỏe
                    <span class="text-red-500">*</span>
                </label>

                <select name="ma_goi_kham"
                        x-model="maGoiKham"
                        @change="locBacSiTheoGoi(); selectedTime = ''; fetchGioDaDat()"
                        required
                        class="w-full bg-slate-50 border border-slate-200 rounded-2xl p-3.5 text-xs text-slate-700 focus:outline-none focus:border-brand-600">

                    <option value="">-- Chọn gói khám dịch vụ --</option>

                    <?php foreach ($danhSachGoiKham as $gk): ?>

                        <option
                            value="<?= htmlspecialchars($gk['ma_goi_kham']); ?>"
                            data-chuyen-khoa="<?= htmlspecialchars($gk['ma_chuyen_khoa'] ?? ''); ?>"
                            <?= (
                                isset($goiHienTai) &&
                                $goiHienTai &&
                                $goiHienTai['ma_goi_kham'] == $gk['ma_goi_kham']
                            ) ? 'selected' : ''; ?>>

                            <?= htmlspecialchars($gk['ten_goi']); ?>
                            - <?= number_format($gk['gia'], 0, ',', '.'); ?> đ

                        </option>

                    <?php endforeach; ?>

                </select>

            </div>

            <!-- Chọn bác sĩ -->
            <div class="space-y-2">

                <label class="block text-xs font-bold text-slate-700">
                    Chọn Bác Sĩ Phụ Trách Gói
                    <span class="text-red-500">*</span>
                </label>

                <select name="ma_bac_si"
                        x-ref="bacSiSelect"
                        x-model="maBacSi"
                        @change="selectedTime = ''; fetchGioDaDat()"
                        required
                        :disabled="!maGoiKham"
                        class="w-full bg-slate-50 border border-slate-200 rounded-2xl p-3.5 text-xs text-slate-700 focus:outline-none focus:border-brand-600 disabled:opacity-50">

                    <option value="">-- Chọn bác sĩ phụ trách --</option>

                    <?php foreach ($danhSachBacSi as $bs): ?>

                        <option
                            value="<?= htmlspecialchars($bs['ma_bac_si']); ?>"
                            data-chuyen-khoa="<?= htmlspecialchars($bs['ma_chuyen_khoa'] ?? ''); ?>">

                            <?= htmlspecialchars($bs['ten_bac_si']); ?>
                            (<?= htmlspecialchars($bs['ten_chuyen_khoa'] ?? 'Chuyên khoa'); ?>)

                        </option>

                    <?php endforeach; ?>

                </select>

                <p class="text-[11px] text-slate-500"
                   x-show="maGoiKham && maChuyenKhoaGoi">

                    Danh sách bác sĩ được lọc theo chuyên khoa của gói khám.

                </p>

            </div>

            <!-- Chọn ngày khám -->
            <div class="space-y-2">

                <label class="block text-xs font-bold text-slate-700">
                    Ngày Thực Hiện Gói Khám
                    <span class="text-red-500">*</span>
                </label>

                <input type="date"
                       name="ngay_kham"
                       x-model="ngayKham"
                       @change="selectedTime = ''; fetchGioDaDat()"
                       required
                       min="<?= date('Y-m-d'); ?>"
                       class="w-full bg-slate-50 border border-slate-200 rounded-2xl p-3.5 text-xs text-slate-700 focus:outline-none focus:border-brand-600">

            </div>

            <!-- Chọn giờ khám -->
            <div class="space-y-2">

                <label class="block text-xs font-bold text-slate-700">
                    Chọn Khung Giờ Trống
                    <span class="text-red-500">*</span>
                </label>

                <input type="hidden"
                       name="gio_kham"
                       x-model="selectedTime"
                       required>

                <!-- Chưa chọn đủ thông tin -->
                <div x-show="!maGoiKham || !maBacSi || !ngayKham"
                     class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-700 text-xs flex items-center space-x-2">

                    <i class="fa-solid fa-triangle-exclamation"></i>

                    <span>
                        Vui lòng chọn gói khám, bác sĩ phụ trách và ngày khám để hiển thị khung giờ trống.
                    </span>

                </div>

                <!-- Đang tải -->
                <div x-show="dangTai"
                     class="p-4 rounded-2xl bg-blue-50 border border-blue-200 text-blue-700 text-xs">

                    Đang kiểm tra các khung giờ đã đặt...

                </div>

                <!-- Lỗi tải dữ liệu -->
                <div x-show="loiTaiDuLieu"
                     class="p-4 rounded-2xl bg-red-50 border border-red-200 text-red-700 text-xs">

                    Không thể tải danh sách khung giờ. Vui lòng thử lại.

                    <button type="button"
                            @click="fetchGioDaDat()"
                            class="font-bold underline ml-1">

                        Thử lại

                    </button>

                </div>

                <!-- Danh sách giờ -->
                <div x-show="maGoiKham && maBacSi && ngayKham && !dangTai && !loiTaiDuLieu"
                     class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-1">

                    <template x-for="gio in danhSachGio" :key="gio">

                        <button type="button"
                                @click="if (!isDaDat(gio)) selectedTime = gio"
                                :disabled="isDaDat(gio)"
                                :class="{
                                    'bg-brand-600 text-white border-brand-600 shadow-md': selectedTime === gio,

                                    'bg-slate-100 text-slate-400 border-slate-200 cursor-not-allowed opacity-60': isDaDat(gio),

                                    'bg-slate-50 text-slate-700 border-slate-200 hover:border-brand-400': !isDaDat(gio) && selectedTime !== gio
                                }"
                                class="py-3 px-2 rounded-2xl border text-xs font-bold transition text-center flex flex-col items-center justify-center">

                            <span class="flex items-center">
                                <i class="fa-regular fa-clock mr-1"></i>
                                <span x-text="gio"></span>
                            </span>

                            <span class="text-[10px] mt-0.5 font-normal"
                                  x-text="isDaDat(gio) ? 'Đã có lịch' : 'Còn trống'">
                            </span>

                        </button>

                    </template>

                </div>

                <!-- Thông báo không có giờ -->
                <div x-show="maGoiKham && maBacSi && ngayKham && !dangTai && !loiTaiDuLieu && danhSachGio.length === 0"
                     class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-700 text-xs">

                    Không có khung giờ khám phù hợp.

                </div>

            </div>

            <!-- Ghi chú -->
            <div class="space-y-2">

                <label class="block text-xs font-bold text-slate-700">
                    Ghi Chú Cho Gói Khám
                </label>

                <textarea name="ghi_chu"
                          rows="3"
                          placeholder="Nhập yêu cầu đặc biệt khi thực hiện gói..."
                          class="w-full bg-slate-50 border border-slate-200 rounded-2xl p-3.5 text-xs text-slate-700 focus:outline-none focus:border-brand-600"></textarea>

            </div>

            <!-- Nút xác nhận -->
            <button type="submit"
                    :disabled="!maGoiKham || !maBacSi || !ngayKham || !selectedTime || dangTai || loiTaiDuLieu"
                    class="w-full bg-brand-600 hover:bg-brand-700 disabled:opacity-50 disabled:cursor-not-allowed text-white font-bold py-4 rounded-2xl shadow-lg transition text-xs uppercase tracking-wider">

                Xác Nhận Đặt Lịch Gói Khám Riêng

            </button>

        </form>

    </div>

</main>

<script>
function datGoiKhamApp() {
    return {
        maGoiKham: '',
        maChuyenKhoaGoi: '',
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
        requestId: 0,

        // Lọc bác sĩ theo chuyên khoa của gói khám
        locBacSiTheoGoi() {
            const selectGoi = document.querySelector(
                'select[name="ma_goi_kham"]'
            );

            const optionGoi = selectGoi.options[selectGoi.selectedIndex];

            this.maChuyenKhoaGoi =
                optionGoi ? optionGoi.dataset.chuyenKhoa || '' : '';

            const selectBacSi = this.$refs.bacSiSelect;

            let bacSiHopLe = false;

            Array.from(selectBacSi.options).forEach((option, index) => {

                if (index === 0) {
                    option.hidden = false;
                    return;
                }

                const maChuyenKhoaBacSi =
                    option.dataset.chuyenKhoa || '';

                const hopLe =
                    !this.maChuyenKhoaGoi ||
                    maChuyenKhoaBacSi === this.maChuyenKhoaGoi;

                option.hidden = !hopLe;

                if (option.value === this.maBacSi && hopLe) {
                    bacSiHopLe = true;
                }

            });

            if (!bacSiHopLe) {
                this.maBacSi = '';
                this.selectedTime = '';
                this.gioDaDat = [];
            }
        },

        // Lấy danh sách giờ đã đặt từ máy chủ
        fetchGioDaDat() {

            const currentRequest = ++this.requestId;

            this.gioDaDat = [];
            this.loiTaiDuLieu = false;

            if (!this.maGoiKham || !this.maBacSi || !this.ngayKham) {
                this.dangTai = false;
                return;
            }

            this.dangTai = true;

            const params = new URLSearchParams({
                ma_bac_si: this.maBacSi,
                ngay_kham: this.ngayKham
            });

            fetch(`index.php?act=api-gio-da-dat&${params.toString()}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Không thể tải dữ liệu.');
                    }

                    return response.json();
                })
                .then(data => {

                    if (currentRequest !== this.requestId) {
                        return;
                    }

                    this.gioDaDat = Array.isArray(data)
                        ? data.map(gio => String(gio).substring(0, 5))
                        : [];

                    if (this.gioDaDat.includes(this.selectedTime)) {
                        this.selectedTime = '';
                    }

                })
                .catch(error => {

                    if (currentRequest !== this.requestId) {
                        return;
                    }

                    console.error('Lỗi tải khung giờ:', error);

                    this.gioDaDat = [];
                    this.selectedTime = '';
                    this.loiTaiDuLieu = true;

                })
                .finally(() => {

                    if (currentRequest === this.requestId) {
                        this.dangTai = false;
                    }

                });
        },

        // Kiểm tra giờ đã được đặt
        isDaDat(gio) {
            return this.gioDaDat.includes(gio);
        }
    }
}
</script>

<?php require_once 'views/footer.php'; ?>

</script>

<?php require_once 'views/footer.php'; ?>