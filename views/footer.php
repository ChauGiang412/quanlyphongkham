<!-- Chân trang Footer -->
    <footer class="bg-slate-900 text-slate-400 pt-16 pb-10 border-t border-slate-800">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 grid grid-cols-1 md:grid-cols-4 gap-8 mb-12">
            <!-- Cột 1: Thông tin thương hiệu -->
            <div class="md:col-span-1">
                <div class="flex items-center space-x-2 text-white font-bold text-lg mb-4">
                    <i class="fa-solid fa-hospital text-brand-500"></i>
                    <span>Elite Clinic</span>
                </div>
                <p class="text-xs text-slate-400 leading-relaxed mb-4">
                    Hệ thống y tế chuẩn quốc tế 5 sao cung cấp dịch vụ chăm sóc sức khỏe toàn diện, an toàn và tận tâm.
                </p>
                <div class="flex space-x-3 text-sm text-white">
                    <a href="#" class="w-8 h-8 rounded-full bg-slate-800 flex items-center justify-center hover:bg-brand-600 transition"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" class="w-8 h-8 rounded-full bg-slate-800 flex items-center justify-center hover:bg-brand-600 transition"><i class="fa-brands fa-youtube"></i></a>
                    <a href="#" class="w-8 h-8 rounded-full bg-slate-800 flex items-center justify-center hover:bg-brand-600 transition"><i class="fa-brands fa-linkedin-in"></i></a>
                </div>
            </div>

            <!-- Cột 2: Liên kết nhanh -->
            <div>
                <h4 class="text-white text-xs font-bold uppercase tracking-wider mb-4">Chuyên Khoa Mũi Nhọn</h4>
                <ul class="space-y-2.5 text-xs">
                    <li><a href="index.php?act=home#chuyen-khoa" class="hover:text-white transition">Khoa Tim Mạch Quốc Tế</a></li>
                    <li><a href="index.php?act=home#chuyen-khoa" class="hover:text-white transition">Khoa Nhi & Tiêm Chủng</a></li>
                    <li><a href="index.php?act=home#chuyen-khoa" class="hover:text-white transition">Khoa Răng Hàm Mặt</a></li>
                    <li><a href="index.php?act=home#chuyen-khoa" class="hover:text-white transition">Tầm Soát Ung Thư Sớm</a></li>
                </ul>
            </div>

            <!-- Cột 3: Thời gian làm việc -->
            <div>
                <h4 class="text-white text-xs font-bold uppercase tracking-wider mb-4">Thời Gian Làm Việc</h4>
                <ul class="space-y-2.5 text-xs">
                    <li class="flex justify-between"><span>Thứ Hai - Thứ Sáu:</span> <span class="text-white">08:00 - 20:00</span></li>
                    <li class="flex justify-between"><span>Thứ Bảy - Chủ Nhật:</span> <span class="text-white">08:00 - 17:00</span></li>
                    <li class="pt-2 text-brand-400 font-semibold"><i class="fa-solid fa-headset mr-1"></i> Cấp cứu: 24/7</li>
                </ul>
            </div>

            <!-- Cột 4: Liên hệ -->
            <div>
                <h4 class="text-white text-xs font-bold uppercase tracking-wider mb-4">Liên Hệ Phòng Khám</h4>
                <ul class="space-y-2.5 text-xs">
                    <li class="flex items-start space-x-2"><i class="fa-solid fa-location-dot text-brand-500 mt-0.5"></i> <span>Trường Đại Học Thương Mại, Cầu Giấy, Hà Nội</span></li>
                    <li class="flex items-center space-x-2"><i class="fa-solid fa-phone text-brand-500"></i> <span>Hotline: 1900 8888</span></li>
                    <li class="flex items-center space-x-2"><i class="fa-solid fa-envelope text-brand-500"></i> <span>support@eliteclinic.vn</span></li>
                </ul>
            </div>
        </div>
        
        <div class="max-w-6xl mx-auto px-4 sm:px-6 pt-8 border-t border-slate-800 text-center text-[11px] text-slate-500">
            &copy; 2026 Elite Clinic Management System. All rights reserved. Phát triển chuyên sâu chuẩn MVC OOP PHP.
        </div>
    </footer>

    <!-- Widget Chatbot Tư Vấn Thông Minh -->
    <div class="fixed bottom-24 right-6 z-50">
        <!-- Nút Mở Chatbot -->
        <button id="toggle-chat" class="w-12 h-12 bg-slate-900 hover:bg-brand-600 text-white rounded-full shadow-2xl flex items-center justify-center transition duration-300">
            <i class="fa-solid fa-comments text-lg"></i>
        </button>

        <!-- Khung Cửa Sổ Chat -->
        <div id="chat-box" class="hidden absolute bottom-16 right-0 w-80 sm:w-96 bg-white rounded-3xl shadow-2xl border border-slate-100 overflow-hidden flex flex-col h-[460px]">
            <!-- Header Chat -->
            <div class="bg-slate-900 text-white p-4 flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded-full bg-brand-600 flex items-center justify-center text-xs">
                        <i class="fa-solid fa-robot"></i>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold">Elite Clinic Assistant</h4>
                        <span class="text-[9px] text-teal-400 block">Sẵn sàng hỗ trợ 24/7</span>
                    </div>
                </div>
                <button id="close-chat" class="text-slate-400 hover:text-white text-sm"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <!-- Nội dung tin nhắn -->
            <div id="chat-messages" class="flex-1 p-4 overflow-y-auto space-y-3 bg-slate-50/50 text-xs">
                <div class="bg-white p-3 rounded-2xl border border-slate-100 text-slate-700 max-w-[85%] shadow-sm">
                    Xin chào! Tôi có thể tư vấn thông tin về Chuyên khoa, Bác sĩ, Gói khám hoặc Giờ làm việc của Elite Clinic cho bạn!
                </div>
            </div>

            <!-- Các nút câu hỏi gợi ý nhanh -->
            <div class="px-3 py-2 bg-white border-t border-slate-100 flex flex-wrap gap-1.5">
                <button type="button" class="suggestion-btn bg-teal-50 hover:bg-teal-100 text-brand-700 text-[10px] font-semibold px-2.5 py-1.5 rounded-xl transition border border-teal-100">
                    🏥 Danh sách chuyên khoa
                </button>
                <button type="button" class="suggestion-btn bg-teal-50 hover:bg-teal-100 text-brand-700 text-[10px] font-semibold px-2.5 py-1.5 rounded-xl transition border border-teal-100">
                    📋 Bảng giá gói khám
                </button>
                <button type="button" class="suggestion-btn bg-teal-50 hover:bg-teal-100 text-brand-700 text-[10px] font-semibold px-2.5 py-1.5 rounded-xl transition border border-teal-100">
                    👨‍⚕️ Đội ngũ bác sĩ
                </button>
                <button type="button" class="suggestion-btn bg-teal-50 hover:bg-teal-100 text-brand-700 text-[10px] font-semibold px-2.5 py-1.5 rounded-xl transition border border-teal-100">
                    ⏰ Giờ làm việc & Địa chỉ
                </button>
            </div>

            <!-- Input gửi tin nhắn -->
            <form id="chat-form" class="p-3 bg-white border-t border-slate-100 flex items-center space-x-2">
                <input type="text" id="chat-input" placeholder="Nhập câu hỏi của bạn..." class="flex-1 bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-700 focus:outline-none focus:border-brand-500">
                <button type="submit" class="bg-brand-600 text-white p-2 rounded-xl text-xs hover:bg-brand-700 transition">
                    <i class="fa-solid fa-paper-plane"></i>
                </button>
            </form>
        </div>
    </div>

    <!-- Script Xử Lý Ajax Gửi/Nhận Tin Nhắn & Gợi Ý -->
    <script>
        const toggleBtn = document.getElementById('toggle-chat');
        const closeBtn = document.getElementById('close-chat');
        const chatBox = document.getElementById('chat-box');
        const chatForm = document.getElementById('chat-form');
        const chatInput = document.getElementById('chat-input');
        const chatMessages = document.getElementById('chat-messages');
        const suggestionBtns = document.querySelectorAll('.suggestion-btn');

        toggleBtn.addEventListener('click', () => chatBox.classList.toggle('hidden'));
        closeBtn.addEventListener('click', () => chatBox.classList.add('hidden'));

        // Hàm gửi tin nhắn chung
        function sendUserMessage(msg) {
            if (!msg.trim()) return;

            // Hiển thị tin nhắn người dùng
            chatMessages.innerHTML += `<div class="ml-auto bg-brand-600 text-white p-3 rounded-2xl max-w-[85%] shadow-sm">${msg}</div>`;
            chatMessages.scrollTop = chatMessages.scrollHeight;

            // Gửi Ajax lên Controller
            const formData = new FormData();
            formData.append('message', msg);

            fetch('index.php?act=chat-reply', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                chatMessages.innerHTML += `<div class="bg-white p-3 rounded-2xl border border-slate-100 text-slate-700 max-w-[85%] shadow-sm whitespace-pre-line">${data.reply}</div>`;
                chatMessages.scrollTop = chatMessages.scrollHeight;
            });
        }

        // Bắt sự kiện bấm nút Gợi ý nhanh
        suggestionBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const text = this.textContent.trim().replace(/^[^\w\sà-ỹ]+/u, '').trim();
                sendUserMessage(text);
            });
        });

        // Bắt sự kiện gõ phím gửi form
        chatForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const msg = chatInput.value;
            sendUserMessage(msg);
            chatInput.value = '';
        });
    </script>
</body>
</html>