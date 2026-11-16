
<!-- ================= FOOTER ================= -->
<footer class="bg-slate-900 text-slate-400 pt-16 pb-10 border-t border-slate-800">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 grid grid-cols-1 md:grid-cols-4 gap-8 mb-12">

        <!-- Cột 1: Thương hiệu -->
        <div>
            <div class="flex items-center space-x-2 text-white font-bold text-lg mb-4">
                <i class="fa-solid fa-hospital text-brand-500"></i>
                <span>Elite Clinic</span>
            </div>

            <p class="text-xs text-slate-400 leading-relaxed mb-4">
                Hệ thống y tế chuẩn quốc tế 5 sao cung cấp dịch vụ chăm sóc sức khỏe toàn diện, an toàn và tận tâm.
            </p>

            <div class="flex space-x-3 text-sm text-white">
                <a href="#" class="w-8 h-8 rounded-full bg-slate-800 flex items-center justify-center hover:bg-brand-600 transition">
                    <i class="fa-brands fa-facebook-f"></i>
                </a>

                <a href="#" class="w-8 h-8 rounded-full bg-slate-800 flex items-center justify-center hover:bg-brand-600 transition">
                    <i class="fa-brands fa-youtube"></i>
                </a>

                <a href="#" class="w-8 h-8 rounded-full bg-slate-800 flex items-center justify-center hover:bg-brand-600 transition">
                    <i class="fa-brands fa-linkedin-in"></i>
                </a>
            </div>
        </div>

        <!-- Cột 2: Chuyên khoa -->
        <div>
            <h4 class="text-white text-xs font-bold uppercase tracking-wider mb-4">
                Chuyên Khoa Mũi Nhọn
            </h4>

            <ul class="space-y-2.5 text-xs">
                <li><a href="index.php?act=home#chuyen-khoa" class="hover:text-white transition">Khoa Tim Mạch Quốc Tế</a></li>
                <li><a href="index.php?act=home#chuyen-khoa" class="hover:text-white transition">Khoa Nhi & Tiêm Chủng</a></li>
                <li><a href="index.php?act=home#chuyen-khoa" class="hover:text-white transition">Khoa Răng Hàm Mặt</a></li>
                <li><a href="index.php?act=home#chuyen-khoa" class="hover:text-white transition">Tầm Soát Ung Thư Sớm</a></li>
            </ul>
        </div>

        <!-- Cột 3: Thời gian -->
        <div>
            <h4 class="text-white text-xs font-bold uppercase tracking-wider mb-4">
                Thời Gian Làm Việc
            </h4>

            <ul class="space-y-2.5 text-xs">
                <li class="flex justify-between gap-2">
                    <span>Thứ Hai - Thứ Sáu:</span>
                    <span class="text-white">08:00 - 20:00</span>
                </li>

                <li class="flex justify-between gap-2">
                    <span>Thứ Bảy - Chủ Nhật:</span>
                    <span class="text-white">08:00 - 17:00</span>
                </li>

                <li class="pt-2 text-brand-400 font-semibold">
                    <i class="fa-solid fa-headset mr-1"></i>
                    Cấp cứu: 24/7
                </li>
            </ul>
        </div>

        <!-- Cột 4: Liên hệ -->
        <div>
            <h4 class="text-white text-xs font-bold uppercase tracking-wider mb-4">
                Liên Hệ Phòng Khám
            </h4>

            <ul class="space-y-2.5 text-xs">
                <li class="flex items-start space-x-2">
                    <i class="fa-solid fa-location-dot text-brand-500 mt-0.5"></i>
                    <span>Trường Đại Học Thương Mại, Cầu Giấy, Hà Nội</span>
                </li>

                <li class="flex items-center space-x-2">
                    <i class="fa-solid fa-phone text-brand-500"></i>
                    <span>Hotline: 1900 8888</span>
                </li>

                <li class="flex items-center space-x-2">
                    <i class="fa-solid fa-envelope text-brand-500"></i>
                    <span>support@eliteclinic.vn</span>
                </li>
            </ul>
        </div>
    </div>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 pt-8 border-t border-slate-800 text-center text-[11px] text-slate-500">
        &copy; 2026 Elite Clinic Management System. All rights reserved.
        Phát triển chuyên sâu chuẩn MVC OOP PHP.
    </div>
</footer>


<!-- ================= CHATBOT ================= -->
<div class="fixed bottom-24 right-6 z-50">

    <!-- Nút mở chatbot -->
    <button
        type="button"
        id="toggle-chat"
        class="w-12 h-12 bg-slate-900 hover:bg-brand-600 text-white rounded-full shadow-2xl flex items-center justify-center transition duration-300">

        <i class="fa-solid fa-comments text-lg"></i>
    </button>

    <!-- Khung chat -->
    <div
        id="chat-box"
        class="hidden absolute bottom-16 right-0 w-[calc(100vw-32px)] sm:w-96 max-w-[400px] bg-white rounded-3xl shadow-2xl border border-slate-100 overflow-hidden flex flex-col h-[500px]">

        <!-- Header -->
        <div class="bg-slate-900 text-white p-4 flex items-center justify-between shrink-0">

            <div class="flex items-center space-x-2">
                <div class="w-9 h-9 rounded-full bg-brand-600 flex items-center justify-center">
                    <i class="fa-solid fa-robot"></i>
                </div>

                <div>
                    <h4 class="text-xs font-bold">Elite Clinic Assistant</h4>
                    <span class="text-[9px] text-teal-400 block">
                        Sẵn sàng hỗ trợ 24/7
                    </span>
                </div>
            </div>

            <button
                type="button"
                id="close-chat"
                class="text-slate-400 hover:text-white text-lg">

                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <!-- Nội dung chat -->
        <div
            id="chat-messages"
            class="flex-1 p-4 overflow-y-auto space-y-3 bg-slate-50 text-xs">

            <div class="bg-white p-3 rounded-2xl border border-slate-100 text-slate-700 max-w-[90%] shadow-sm">
                Xin chào! Tôi là Elite Clinic Assistant.

                Tôi có thể hỗ trợ bạn tìm hiểu thông tin về chuyên khoa, bác sĩ, gói khám, giờ làm việc và địa chỉ phòng khám.
            </div>
        </div>

        <!-- Câu hỏi gợi ý -->
        <div class="px-3 py-3 bg-white border-t border-slate-100 flex flex-wrap gap-1.5 shrink-0">

            <button type="button"
                class="suggestion-btn bg-teal-50 hover:bg-teal-100 text-brand-700 text-[10px] font-semibold px-2.5 py-2 rounded-xl transition border border-teal-100">
                🏥 Danh sách chuyên khoa
            </button>

            <button type="button"
                class="suggestion-btn bg-teal-50 hover:bg-teal-100 text-brand-700 text-[10px] font-semibold px-2.5 py-2 rounded-xl transition border border-teal-100">
                📋 Bảng giá gói khám
            </button>

            <button type="button"
                class="suggestion-btn bg-teal-50 hover:bg-teal-100 text-brand-700 text-[10px] font-semibold px-2.5 py-2 rounded-xl transition border border-teal-100">
                👨‍⚕️ Đội ngũ bác sĩ
            </button>

            <button type="button"
                class="suggestion-btn bg-teal-50 hover:bg-teal-100 text-brand-700 text-[10px] font-semibold px-2.5 py-2 rounded-xl transition border border-teal-100">
                ⏰ Giờ làm việc & Địa chỉ
            </button>
        </div>

        <!-- Ô nhập tin nhắn -->
        <form
            id="chat-form"
            class="p-3 bg-white border-t border-slate-100 flex items-center space-x-2 shrink-0">

            <input
                type="text"
                id="chat-input"
                autocomplete="off"
                placeholder="Nhập câu hỏi của bạn..."
                class="flex-1 min-w-0 bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-xs text-slate-700 focus:outline-none focus:border-brand-500">

            <button
                type="submit"
                class="bg-brand-600 text-white w-10 h-10 rounded-xl text-xs hover:bg-brand-700 transition shrink-0">

                <i class="fa-solid fa-paper-plane"></i>
            </button>
        </form>
    </div>
</div>


<!-- ================= JAVASCRIPT CHATBOT ================= -->
<script>
document.addEventListener('DOMContentLoaded', function () {

    const toggleBtn = document.getElementById('toggle-chat');
    const closeBtn = document.getElementById('close-chat');
    const chatBox = document.getElementById('chat-box');
    const chatForm = document.getElementById('chat-form');
    const chatInput = document.getElementById('chat-input');
    const chatMessages = document.getElementById('chat-messages');

    const suggestionBtns = document.querySelectorAll('.suggestion-btn');

    if (!toggleBtn || !closeBtn || !chatBox || !chatForm || !chatInput || !chatMessages) {
        return;
    }

    // Mở chatbot
    toggleBtn.addEventListener('click', function () {
        chatBox.classList.toggle('hidden');
    });

    // Đóng chatbot
    closeBtn.addEventListener('click', function () {
        chatBox.classList.add('hidden');
    });

    // Cuộn xuống tin nhắn mới nhất
    function scrollToBottom() {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    // Tạo tin nhắn an toàn
    function createMessage(content, isUser = false) {
        const message = document.createElement('div');

        message.className = isUser
            ? 'ml-auto bg-brand-600 text-white p-3 rounded-2xl max-w-[85%] shadow-sm whitespace-pre-line break-words'
            : 'bg-white p-3 rounded-2xl border border-slate-100 text-slate-700 max-w-[90%] shadow-sm whitespace-pre-line break-words';

        message.textContent = content;

        chatMessages.appendChild(message);
        scrollToBottom();

        return message;
    }

    // Đọc dữ liệu theo nhiều tên cột có thể có
    function getField(item, fields) {
        for (const field of fields) {
            const value = item[field];

            if (value !== undefined && value !== null && value !== '') {
                return value;
            }
        }

        return '';
    }

    // Tạo hình ảnh
    function createImage(imagePath) {
        if (!imagePath) return null;

        const img = document.createElement('img');

        img.src = imagePath;
        img.alt = 'Hình ảnh';
        img.className = 'w-full h-32 object-cover';

        img.onerror = function () {
            this.remove();
        };

        return img;
    }

    // Tạo dòng tiêu đề
    function createTitle(text) {
        const title = document.createElement('h5');

        title.className = 'font-bold text-sm text-slate-800';
        title.textContent = text || 'Thông tin đang cập nhật';

        return title;
    }

    // Hiển thị các thẻ dữ liệu
    function renderItems(type, items) {

        if (!Array.isArray(items) || items.length === 0) {
            createMessage('Hiện tại chưa có dữ liệu để hiển thị.');
            return;
        }

        items.forEach(function (item) {

            const card = document.createElement('div');

            card.className = 'bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden max-w-[95%]';

            const image = getField(item, [
                'hinh_anh',
                'anh_dai_dien',
                'avatar',
                'hinh'
            ]);

            const img = createImage(image);

            if (img) {
                card.appendChild(img);
            }

            const content = document.createElement('div');
            content.className = 'p-3 space-y-2';

            let title = '';
            let description = '';

            // CHUYÊN KHOA
            if (type === 'specialties') {

                title = getField(item, [
                    'ten_chuyen_khoa',
                    'ten_khoa',
                    'ten'
                ]);

                description = getField(item, [
                    'mo_ta',
                    'mo_ta_chuyen_khoa',
                    'gioi_thieu',
                    'mo_ta_ngan'
                ]);
            }

            // GÓI KHÁM
            else if (type === 'packages') {

                title = getField(item, [
                    'ten_goi',
                    'ten_goi_kham',
                    'ten'
                ]);

                description = getField(item, [
                    'mo_ta',
                    'mo_ta_goi',
                    'mo_ta_ngan'
                ]);

                const price = getField(item, [
                    'gia',
                    'gia_goi',
                    'don_gia'
                ]);

                if (price !== '') {

                    const priceElement = document.createElement('p');

                    priceElement.className = 'text-brand-600 font-extrabold text-sm';

                    const numericPrice = Number(price);

                    priceElement.textContent = Number.isFinite(numericPrice)
                        ? numericPrice.toLocaleString('vi-VN') + ' đ'
                        : String(price);

                    content.appendChild(priceElement);
                }
            }

            // BÁC SĨ
            else if (type === 'doctors') {

                title = getField(item, [
                    'ho_ten',
                    'ten_bac_si',
                    'ten',
                    'full_name'
                ]);

                description = getField(item, [
                    'chuyen_mon',
                    'ten_chuyen_khoa',
                    'mo_ta',
                    'kinh_nghiem'
                ]);
            }

            content.appendChild(createTitle(title));

            if (description) {

                const desc = document.createElement('p');

                desc.className = 'text-[11px] text-slate-500 leading-relaxed';
                desc.textContent = description;

                content.appendChild(desc);
            }

            card.appendChild(content);
            chatMessages.appendChild(card);
        });

        scrollToBottom();
    }

    // Hiển thị trạng thái đang xử lý
    function showLoading() {
        const loading = document.createElement('div');

        loading.className = 'chat-loading bg-white p-3 rounded-2xl border border-slate-100 text-slate-500 max-w-[85%]';

        loading.textContent = 'Elite Clinic Assistant đang trả lời...';

        chatMessages.appendChild(loading);
        scrollToBottom();

        return loading;
    }

    // Gửi tin nhắn đến ChatController
    async function sendUserMessage(message) {

        message = message.trim();

        if (!message) return;

        createMessage(message, true);

        const loading = showLoading();

        const formData = new FormData();

        formData.append('message', message);

        try {

            const response = await fetch('index.php?act=chat-reply', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                throw new Error('Máy chủ phản hồi lỗi HTTP ' + response.status);
            }

            const data = await response.json();

            loading.remove();

            // Hiển thị nội dung trả lời
            createMessage(
                data.reply || 'Xin lỗi, hiện tại tôi chưa có câu trả lời.'
            );

            // Hiển thị danh sách lấy từ database
            if (data.items && data.type) {
                renderItems(data.type, data.items);
            }

        } catch (error) {

            loading.remove();

            console.error('Lỗi chatbot:', error);

            createMessage(
                'Xin lỗi, hệ thống đang gặp sự cố. Bạn vui lòng thử lại sau nhé!'
            );
        }
    }

    // Nút gợi ý nhanh
    suggestionBtns.forEach(function (button) {

        button.addEventListener('click', function () {

            const message = this.textContent
                .replace(/^[^\p{L}\p{N}]+/u, '')
                .trim();

            sendUserMessage(message);
        });
    });

    // Gửi tin nhắn bằng form
    chatForm.addEventListener('submit', function (event) {

        event.preventDefault();

        const message = chatInput.value;

        if (!message.trim()) return;

        sendUserMessage(message);

        chatInput.value = '';
        chatInput.focus();
    });

});
</script>

</body>
</html>
