@extends('layouts.app')

@section('title', 'Live Chat — Messenger')
@section('page_title', 'Live Chat')

@section('content')
{{-- ĐÃ SỬA: Container chính theo phong cách Admin Panel --}}
<div class="bg-white/80 dark:bg-slate-900/50 backdrop-blur p-6 rounded-2xl shadow ring-1 ring-slate-900/5 dark:ring-white/10">

    {{-- KHUNG ỨNG DỤNG CHAT BÊN TRONG --}}
    {{-- Giữ nguyên kích thước và grid layout nhưng bỏ box shadow/border trùng lặp --}}
    <div class="h-[calc(100vh-210px)] min-h-0 bg-white dark:bg-slate-900 rounded-xl overflow-hidden grid grid-cols-12 border border-slate-200 dark:border-slate-700">

        {{-- LEFT: thread list --}}
        <aside class="col-span-4 lg:col-span-3 border-r dark:border-slate-700 flex flex-col min-h-0">
            <div class="p-3 border-b dark:border-slate-700">
                <div class="relative">
                    <input id="thread-search" type="text" placeholder="Tìm khách hàng..."
                        {{-- ĐÃ SỬA: Input theo phong cách chung --}}
                        class="w-full rounded-xl border px-4 py-2 pr-9 dark:bg-slate-800 dark:border-slate-700 focus:ring-2 focus:ring-brand-500 outline-none">
                    <svg class="absolute right-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0016 9.5 6.5 6.5 0 109.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79L20 21.5 21.5 20 15.5 14zM9.5 14C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z" />
                    </svg>
                </div>
                <div class="mt-2 flex gap-2 text-sm">
                    {{-- ĐÃ SỬA: Màu brand --}}
                    <button data-filter="all" class="tab-filter px-3 py-1 rounded-full bg-brand-600 text-white shadow-sm">Tất cả</button>
                    <button data-filter="unassigned" class="tab-filter px-3 py-1 rounded-full bg-gray-100 dark:bg-slate-800 dark:text-slate-300 hover:bg-gray-200 dark:hover:bg-slate-700">Chưa phân công</button>
                    <button data-filter="mine" class="tab-filter px-3 py-1 rounded-full bg-gray-100 dark:bg-slate-800 dark:text-slate-300 hover:bg-gray-200 dark:hover:bg-slate-700">Của tôi</button>
                </div>
            </div>

            <div id="thread-list" class="flex-1 overflow-y-auto divide-y dark:divide-slate-700">
                @foreach($sessions as $s)
                {{-- ĐÃ SỬA: Màu brand cho avatar --}}
                <button class="thread-item w-full text-left p-3 hover:bg-gray-50 dark:hover:bg-slate-800 flex gap-3 items-center"
                    data-session="{{ $s->session_id }}">
                    <div class="w-10 h-10 rounded-full bg-brand-100 flex items-center justify-center font-semibold text-brand-600">
                        {{ Str::upper(Str::substr($s->user?->full_name ?? 'KH', 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <p class="font-medium truncate text-slate-800 dark:text-slate-100">{{ $s->user?->full_name ?? 'Khách hàng' }}</p>
                            <span class="text-xs text-gray-400 dark:text-slate-500">{{ \Carbon\Carbon::parse($s->last_message_at)->shortAbsoluteDiffForHumans() }}</span>
                        </div>
                        <p class="text-sm text-gray-500 dark:text-slate-400 truncate">{{ $s->last_message?->content ?? '—' }}</p>
                    </div>
                    @if(($s->unread_count ?? 0) > 0)
                    <span class="ml-2 inline-flex items-center justify-center min-w-6 h-6 text-xs rounded-full bg-red-500 text-white px-1">
                        {{ $s->unread_count }}
                    </span>
                    @endif
                </button>
                @endforeach
            </div>

            <div class="p-3 border-t dark:border-slate-700 text-xs text-gray-500 dark:text-slate-400">
                Tổng: {{ $sessions->total() }} cuộc hội thoại
            </div>
        </aside>

        {{-- RIGHT: chat panel --}}
        <section class="col-span-8 lg:col-span-9 flex flex-col min-h-0">
            {{-- Header --}}
            <header id="chat-header" class="h-16 flex items-center justify-between px-4 border-b dark:border-slate-700">
                <div class="flex items-center gap-3">
                    {{-- ĐÃ SỬA: Màu brand cho avatar --}}
                    <div id="chat-avatar" class="w-10 h-10 rounded-full bg-brand-100 flex items-center justify-center font-semibold text-brand-600">?</div>
                    <div>
                        <div id="chat-customer" class="font-semibold text-slate-800 dark:text-slate-100">Chọn một cuộc trò chuyện</div>
                        <div id="chat-meta" class="text-xs text-gray-500 dark:text-slate-400"> </div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <form id="assign-form" method="post" class="hidden">
                        @csrf
                        {{-- ĐÃ SỬA: Màu thương hiệu chung --}}
                        <button class="px-3 py-1 rounded-full bg-brand-600 hover:bg-brand-700 text-white text-sm shadow-sm">Assign me</button>
                    </form>
                    <form id="unassign-form" method="post" class="hidden">
                        @csrf
                        {{-- Giữ màu xám cho hành động phụ --}}
                        <button class="px-3 py-1 rounded-full bg-gray-200 dark:bg-slate-700 dark:text-slate-200 text-sm hover:bg-gray-300 dark:hover:bg-slate-600">Bỏ phân công</button>
                    </form>
                </div>
            </header>

            {{-- Messages --}}
            <div id="chat-box" class="flex-1 min-h-0 overflow-y-auto p-4 space-y-2 bg-gray-50 dark:bg-slate-800">
                {{-- khi chưa chọn thread --}}
                <div id="chat-empty" class="h-full grid place-items-center text-gray-400 dark:text-slate-600">Hãy chọn một hội thoại ở bên trái</div>
            </div>

            {{-- Composer --}}
            <footer class="p-3 border-t dark:border-slate-700 bg-white dark:bg-slate-900">
                <form id="chat-send-form" class="flex items-end gap-2">
                    @csrf
                    {{-- ĐÃ SỬA: Textarea theo phong cách chung --}}
                    <textarea id="chat-input" rows="1" placeholder="Aa" class="flex-1 resize-none rounded-2xl border px-4 py-2 dark:bg-slate-800 dark:border-slate-700 focus:ring-2 focus:ring-brand-500 outline-none"></textarea>
                    {{-- ĐÃ SỬA: Màu brand cho nút gửi --}}
                    <button id="chat-send-btn" type="submit" class="rounded-full h-10 w-10 grid place-items-center bg-brand-600 hover:bg-brand-700 text-white shadow-sm">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M2 21l21-9L2 3v7l15 2-15 2z" />
                        </svg>
                    </button>
                </form>
                <div id="typing-indicator" class="text-xs text-gray-500 dark:text-slate-400 mt-1 hidden">Đang nhập…</div>
            </footer>
        </section>

    </div> {{-- End: Ứng dụng chat --}}
</div> {{-- End: Container chính --}}

{{-- Phân trang threads --}}
<div class="mt-3">
    {{ $sessions->withQueryString()->links() }}
</div>
<script>
    (function() {
        document.querySelectorAll('.thread-item').forEach(b => {
            b.addEventListener('click', () => {
                // gỡ chọn tất cả
                document.querySelectorAll('.thread-item.is-selected')
                    .forEach(x => x.classList.remove('is-selected'));

                // chọn item hiện tại
                b.classList.add('is-selected');

                loadSession(b.getAttribute('data-session'));
            });
        });
    })();
</script>

<script>
    (function() {
        const esc = s => String(s ?? "").replace(/[&<>"']/g, m => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        } [m]));
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
        let CURRENT_SESSION_ID = null;

        // ✅ SINH URL ĐÚNG GỐC (không hard-code /admin/…)
        window.ADMIN_CHAT = {
            base: "{{ url('/admin/chat') }}",
            messages: sid => "{{ url('/admin/chat') }}/" + sid + "/messages",
            send: sid => "{{ url('/admin/chat') }}/" + sid + "/send",
            markRead: sid => "{{ url('/admin/chat') }}/" + sid + "/mark-read",
        };

        function renderMsg(m) {
            const box = document.getElementById('chat-box');
            const mine = Number(m.sender_id) === Number(window.AuthUserId ?? -1);
            const wrap = document.createElement('div');
            wrap.className = 'flex ' + (mine ? 'justify-end' : 'justify-start');
            const bubble = document.createElement('div');
            bubble.className = 'max-w-[70%] rounded-2xl px-3 py-2 shadow-sm ' + (mine ? 'bg-pink-600 text-white rounded-br-sm' : 'bg-white text-gray-800 rounded-bl-sm');
            bubble.innerHTML = `<div class="text-[11px] opacity-70 mb-0.5">${esc(m.sender_name??'')}</div>${esc(m.content)}`;
            wrap.appendChild(bubble);
            box.appendChild(wrap);
            box.scrollTop = box.scrollHeight;
        }

        async function loadSession(id) {
            if (!id) return;
            CURRENT_SESSION_ID = id;

            document.getElementById('chat-empty')?.remove();
            const box = document.getElementById('chat-box');
            box.innerHTML = '';

            // ✅ dùng URL đã sinh
            const res = await fetch(window.ADMIN_CHAT.messages(id), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            if (!res.ok) {
                console.error('messages failed', res.status);
                return;
            }
            const data = await res.json();

            (data.messages || []).forEach(renderMsg);
            if (data.session) {
                const c = data.session.user?.full_name ?? 'Khách hàng';
                document.getElementById('chat-avatar').textContent = (c[0] || '?').toUpperCase();
                document.getElementById('chat-customer').textContent = c;
                document.getElementById('chat-meta').textContent = `#${data.session.session_id}`;
            }

            document.getElementById('chat-send-form')?.setAttribute('action', window.ADMIN_CHAT.send(id));
        }

        document.querySelectorAll('.thread-item').forEach(b => {
            b.addEventListener('click', () => {
                document.querySelectorAll('.thread-item').forEach(x => x.classList.remove('bg-gray-100'));
                b.classList.add('bg-gray-100');
                loadSession(b.getAttribute('data-session'));
            });
        });

        const form = document.getElementById('chat-send-form');
        const input = document.getElementById('chat-input');

        form?.addEventListener('submit', async (e) => {
            e.preventDefault();
            const text = (input.value || '').trim();
            if (!text) return;
            if (!CURRENT_SESSION_ID) {
                alert('Hãy chọn một cuộc hội thoại ở bên trái');
                return;
            }

            const res = await fetch(window.ADMIN_CHAT.send(CURRENT_SESSION_ID), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    content: text
                })
            });
            if (!res.ok) {
                console.error('send failed', res.status, await res.text());
                alert('Gửi tin nhắn thất bại');
                return;
            }
            renderMsg({
                sender_id: window.AuthUserId ?? -1,
                sender_name: 'Tôi',
                content: text
            });
            input.value = '';
            input.style.height = 'auto';
        });

        input?.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                form.requestSubmit();
            }
        });
    })();
</script>

<style>
    /* Messages */
    #chat-box::-webkit-scrollbar {
        width: 8px;
    }

    /* ĐÃ SỬA: Thay màu hồng (#ec4899) bằng màu thương hiệu chung (ví dụ: blue-500) */
    #chat-box::-webkit-scrollbar-thumb {
        background: #3b82f6;
        border-radius: 8px;
    }

    #chat-box::-webkit-scrollbar-track {
        background: transparent;
    }

    #chat-box {
        scrollbar-width: thin;
        scrollbar-color: #3b82f6 transparent;
    }

    /* Thread list */
    #thread-list::-webkit-scrollbar {
        width: 8px;
    }

    /* Giữ màu xám cho thread list */
    #thread-list::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 8px;
    }

    #thread-list {
        scrollbar-width: thin;
        scrollbar-color: #cbd5e1 transparent;
    }

    .thread-item.is-selected {
        background: #1E293B;
        /* slate-800 */
        color: #fff;
        box-shadow: inset 0 0 0 1px rgba(59, 130, 246, .45);
        /* viền nhẹ theo brand nếu muốn */
    }
</style>

@endsection