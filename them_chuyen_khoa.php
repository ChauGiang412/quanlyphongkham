<div class="max-w-2xl mx-auto px-4 py-10">
    <!-- Nút quay lại trang Admin -->
    <div class="mb-6">
        <a href="index.php?act=admin" class="inline-flex items-center space-x-2 text-xs font-semibold text-slate-600 hover:text-brand-600 bg-white px-4 py-2 rounded-2xl shadow-sm border border-slate-100 transition">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Quay lại trang quản trị</span>
        </a>
    </div>


    <div class="bg-white p-8 rounded-3xl shadow-xl border border-slate-100">
        <div class="text-center mb-8">
            <div class="w-12 h-12 bg-teal-100 text-brand-600 rounded-2xl flex items-center justify-center text-xl mx-auto mb-3">
                <i class="fa-solid fa-hospital-user"></i>
            </div>
            <h2 class="text-2xl font-bold text-slate-900">Thêm Chuyên Khoa Mới</h2>
            <p class="text-xs text-slate-500 mt-1">Cập nhật thông tin, hình ảnh và bài viết giới thiệu chi tiết cho chuyên khoa</p>
        </div>


        <?php if (!empty($error)): ?>
            <div class="bg-rose-50 text-rose-700 p-3.5 rounded-2xl mb-5 text-xs font-medium text-center border border-rose-100">
                <?= $error; ?>
            </div>
        <?php endif; ?>


        <!-- BẮT BUỘC BỔ SUNG enctype="multipart/form-data" ĐỂ HỖ TRỢ UPLOAD FILE -->
        <form action="index.php?act=them-chuyen-khoa-exec" method="POST" enctype="multipart/form-data" class="space-y-5">
           
            <!-- Tên chuyên khoa -->
            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase mb-1.5">
                    Tên chuyên khoa <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="ten_chuyen_khoa" required placeholder="Ví dụ: Khoa Tim Mạch Quốc Tế" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 transition">
            </div>


            <!-- Mô tả ngắn (hiển thị ngoài Card trang chủ) -->
            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase mb-1.5">
                    Mô tả ngắn <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="mo_ta" required placeholder="Nhập 1-2 câu tóm tắt hiển thị ngoài trang chủ..." class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 transition">
            </div>


            <!-- Bài viết giới thiệu chi tiết riêng từng khoa -->
            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase mb-1.5">
                    Bài viết giới thiệu chi tiết <span class="text-rose-500">*</span>
                </label>
                <textarea name="gioi_thieu_chi_tiet" rows="6" required placeholder="Nhập bài viết giới thiệu chi tiết về cơ sở vật chất, thế mạnh, công nghệ điều trị riêng của khoa này..." class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 transition"></textarea>
            </div>


            <!-- Tải ảnh minh họa lên từ máy tính -->
            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase mb-1.5">
                    Hình ảnh chuyên khoa <span class="text-rose-500">*</span>
                </label>
                <input type="file" name="hinh_anh" accept="image/*" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-2.5 text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-teal-50 file:text-brand-700 hover:file:bg-teal-100 transition">
            </div>


            <!-- Nút gửi form -->
            <button type="submit" class="w-full bg-brand-600 hover:bg-brand-700 text-white font-semibold py-3.5 rounded-2xl shadow-lg shadow-teal-600/20 transition text-sm">
                Lưu Và Hiển Thị Chuyên Khoa
            </button>
        </form>
    </div>
</div>

