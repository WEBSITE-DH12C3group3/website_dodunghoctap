<div id="chat-bubble"
     class="fixed bottom-6 right-6 z-50 rounded-full shadow-xl bg-pink-600 hover:bg-pink-500 text-white w-14 h-14 flex items-center justify-center cursor-pointer"
     title="Chat với chúng tôi" aria-label="Open chat">
    <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" viewBox="0 0 24 24" fill="currentColor">
        <path d="M2 5a3 3 0 013-3h14a3 3 0 013 3v9a3 3 0 01-3 3H9l-5 5v-5H5a3 3 0 01-3-3V5z"/>
    </svg>
    <span id="chat-unread"
          class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-semibold rounded-full w-5 h-5 hidden items-center justify-center">0</span>
</div>

<div id="chat-panel" class="hidden fixed bottom-24 right-6 z-50 w-[340px] max-h-[70vh] bg-white shadow-2xl rounded-2xl overflow-hidden">
    <div class="flex items-center justify-between px-4 py-3 bg-pink-600 text-white">
        <div class="font-semibold">Hỗ trợ trực tuyến</div>
        <div class="flex items-center space-x-2">
            <button id="chat-minimize" class="opacity-90 hover:opacity-100" title="Thu gọn">—</button>
            <button id="chat-close" class="opacity-90 hover:opacity-100" title="Đóng">✕</button>
        </div>
    </div>
    <div id="chat-messages" class="p-3 space-y-2 overflow-y-auto" style="max-height: 50vh;">
        <!-- messages render here -->
    </div>
    <form id="chat-form" class="p-3 border-t flex items-center space-x-2">
        @csrf
        <input id="chat-input" type="text" class="flex-1 border rounded-full px-3 py-2 focus:outline-none"
               placeholder="Nhập tin nhắn..." maxlength="5000" autocomplete="off">
        <button type="submit" class="bg-pink-600 hover:bg-pink-500 text-white rounded-full px-4 py-2">Gửi</button>
    </form>
</div>

<style>
#chat-bubble.dragging { opacity: .85; cursor: grabbing; }
.chat-msg { max-width: 80%; padding:.5rem .75rem; border-radius:1rem; }
.chat-msg.me { background:#fce7f3; margin-left:auto; border-bottom-right-radius:.25rem;}
.chat-msg.other { background:#f3f4f6; margin-right:auto; border-bottom-left-radius:.25rem;}
</style>
