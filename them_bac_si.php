<div class="max-w-xl mx-auto px-4 py-12">
    <div class="bg-white p-8 rounded-3xl shadow-xl border border-slate-100">
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-slate-900">Thêm Bác Sĩ Mới</h2>
            <p class="text-xs text-slate-500 mt-1">Tải lên thông tin và hình ảnh chân dung bác sĩ</p>
        </div>


        <!-- BẮT BUỘC CÓ enctype="multipart/form-data" ĐỂ TẢI FILE LÊN -->
        <form action="index.php?act=them-bac-si-exec" method="POST" enctype="multipart/form-data" class="space-y-4">
            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase mb-1.5">Tên bác sĩ <span class="text-rose-500">*</span></label>
                <input type="text" name="ten_bac_si" required placeholder="BS.CKI Nguyễn Văn A" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
            </div>


            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase mb-1.5">Chọn ảnh chân dung <span class="text-rose-500">*</span></label>
                <input type="file" name="hinh_anh" accept="image/*" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-2.5 text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-teal-50 file:text-brand-700 hover:file:bg-teal-100">
            </div>


            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase mb-1.5">Phí khám (VNĐ) <span class="text-rose-500">*</span></label>
                <input type="number" name="phi_kham" required placeholder="300000" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
            </div>


            <button type="submit" class="w-full bg-brand-600 hover:bg-brand-700 text-white font-semibold py-3.5 rounded-2xl shadow-lg transition text-sm">
                Lưu Thông Tin Bác Sĩ
            </button>
        </form>
    </div>
</div>

