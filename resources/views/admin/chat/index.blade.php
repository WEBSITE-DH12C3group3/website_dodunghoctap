@extends('layouts.app')

@section('title', 'Live Chat — Messenger')
@section('page_title', 'Live Chat')

@section('content')
<div class="h-[calc(100vh-160px)] min-h-0 bg-white border rounded-2xl overflow-hidden shadow-sm grid grid-cols-12">

  {{-- LEFT: thread list --}}
<aside class="col-span-4 lg:col-span-3 border-r flex flex-col min-h-0">
    <div class="p-3 border-b">
      <div class="relative">
        <input id="thread-search" type="text" placeholder="Tìm khách hàng..."
               class="w-full rounded-xl border px-4 py-2 pr-9 focus:ring-2 focus:ring-pink-400 outline-none">
        <svg class="absolute right-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400" viewBox="0 0 24 24" fill="currentColor">
          <path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0016 9.5 6.5 6.5 0 109.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79L20 21.5 21.5 20 15.5 14zM9.5 14C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
        </svg>
      </div>
      <div class="mt-2 flex gap-2 text-sm">
        <button data-filter="all" class="tab-filter px-3 py-1 rounded-full bg-pink-600 text-white">Tất cả</button>
        <button data-filter="unassigned" class="tab-filter px-3 py-1 rounded-full bg-gray-100">Chưa phân công</button>
        <button data-filter="mine" class="tab-filter px-3 py-1 rounded-full bg-gray-100">Của tôi</button>
      </div>
    </div>

    <div id="thread-list" class="flex-1 overflow-y-auto divide-y">
      {{-- Server-side render sẵn danh sách để vào là dùng được --}}
      @foreach($sessions as $s)
        <button class="thread-item w-full text-left p-3 hover:bg-gray-50 flex gap-3 items-center"
                data-session="{{ $s->session_id }}">
          <div class="w-10 h-10 rounded-full bg-pink-100 flex items-center justify-center font-semibold text-pink-600">
            {{ Str::upper(Str::substr($s->user?->full_name ?? 'KH', 0, 1)) }}
          </div>
          <div class="flex-1 min-w-0">
            <div class="flex items-center justify-between">
              <p class="font-medium truncate">{{ $s->user?->full_name ?? 'Khách hàng' }}</p>
              <span class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($s->last_message_at)->shortAbsoluteDiffForHumans() }}</span>
            </div>
            <p class="text-sm text-gray-500 truncate">{{ $s->last_message?->content ?? '—' }}</p>
          </div>
          @if(($s->unread_count ?? 0) > 0)
            <span class="ml-2 inline-flex items-center justify-center min-w-6 h-6 text-xs rounded-full bg-red-500 text-white px-1">
              {{ $s->unread_count }}
            </span>
          @endif
        </button>
      @endforeach
    </div>

    <div class="p-3 border-t text-xs text-gray-500">
      Tổng: {{ $sessions->total() }} cuộc hội thoại
    </div>
  </aside>

  {{-- RIGHT: chat panel --}}
<section class="col-span-8 lg:col-span-9 flex flex-col min-h-0">
    {{-- Header --}}
    <header id="chat-header" class="h-16 flex items-center justify-between px-4 border-b">
      <div class="flex items-center gap-3">
        <div id="chat-avatar" class="w-10 h-10 rounded-full bg-pink-100 flex items-center justify-center font-semibold text-pink-600">?</div>
        <div>
          <div id="chat-customer" class="font-semibold">Chọn một cuộc trò chuyện</div>
          <div id="chat-meta" class="text-xs text-gray-500"> </div>
        </div>
      </div>
      <div class="flex items-center gap-2">
        <form id="assign-form" method="post" class="hidden">
          @csrf
          <button class="px-3 py-1 rounded-full bg-emerald-600 text-white text-sm">Assign me</button>
        </form>
        <form id="unassign-form" method="post" class="hidden">
          @csrf
          <button class="px-3 py-1 rounded-full bg-gray-200 text-sm">Bỏ phân công</button>
        </form>
      </div>
    </header>

    {{-- Messages --}}
    <div id="chat-box" class="flex-1 min-h-0 overflow-y-auto p-4 space-y-2 bg-gray-50">
      {{-- khi chưa chọn thread --}}
      <div id="chat-empty" class="h-full grid place-items-center text-gray-400">Hãy chọn một hội thoại ở bên trái</div>
    </div>

    {{-- Composer --}}
    <footer class="p-3 border-t">
      <form id="chat-send-form" class="flex items-end gap-2">
        @csrf
        <textarea id="chat-input" rows="1" placeholder="Aa" class="flex-1 resize-none rounded-2xl border px-4 py-2 focus:ring-2 focus:ring-pink-400 outline-none"></textarea>
        <button id="chat-send-btn" type="submit" class="rounded-full h-10 w-10 grid place-items-center bg-pink-600 text-white">
          <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M2 21l21-9L2 3v7l15 2-15 2z"/></svg>
        </button>
      </form>
      <div id="typing-indicator" class="text-xs text-gray-500 mt-1 hidden">Đang nhập…</div>
    </footer>
  </section>

</div>

{{-- Phân trang threads (nếu cần) --}}
<div class="mt-3">
  {{ $sessions->withQueryString()->links() }}
</div>
<script>
(function () {
  const esc = s => String(s??"").replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m]));
  const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
  let CURRENT_SESSION_ID = null;

  // ✅ SINH URL ĐÚNG GỐC (không hard-code /admin/…)
  window.ADMIN_CHAT = {
    base:      "{{ url('/admin/chat') }}",
    messages:  sid => "{{ url('/admin/chat') }}/" + sid + "/messages",
    send:      sid => "{{ url('/admin/chat') }}/" + sid + "/send",
    markRead:  sid => "{{ url('/admin/chat') }}/" + sid + "/mark-read",
  };

  function renderMsg(m){
    const box=document.getElementById('chat-box');
    const mine=Number(m.sender_id)===Number(window.AuthUserId??-1);
    const wrap=document.createElement('div');
    wrap.className='flex '+(mine?'justify-end':'justify-start');
    const bubble=document.createElement('div');
    bubble.className='max-w-[70%] rounded-2xl px-3 py-2 shadow-sm '+(mine?'bg-pink-600 text-white rounded-br-sm':'bg-white text-gray-800 rounded-bl-sm');
    bubble.innerHTML=`<div class="text-[11px] opacity-70 mb-0.5">${esc(m.sender_name??'')}</div>${esc(m.content)}`;
    wrap.appendChild(bubble); box.appendChild(wrap); box.scrollTop=box.scrollHeight;
  }

  async function loadSession(id){
    if(!id) return;
    CURRENT_SESSION_ID = id;

    document.getElementById('chat-empty')?.remove();
    const box=document.getElementById('chat-box'); box.innerHTML='';

    // ✅ dùng URL đã sinh
    const res = await fetch(window.ADMIN_CHAT.messages(id), {
      headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json'}
    });
    if (!res.ok) {
      console.error('messages failed', res.status);
      return;
    }
    const data = await res.json();

    (data.messages||[]).forEach(renderMsg);
    if (data.session){
      const c = data.session.user?.full_name ?? 'Khách hàng';
      document.getElementById('chat-avatar').textContent=(c[0]||'?').toUpperCase();
      document.getElementById('chat-customer').textContent=c;
      document.getElementById('chat-meta').textContent=`#${data.session.session_id}`;
    }

    document.getElementById('chat-send-form')?.setAttribute('action', window.ADMIN_CHAT.send(id));
  }

  document.querySelectorAll('.thread-item').forEach(b=>{
    b.addEventListener('click',()=>{
      document.querySelectorAll('.thread-item').forEach(x=>x.classList.remove('bg-gray-100'));
      b.classList.add('bg-gray-100');
      loadSession(b.getAttribute('data-session'));
    });
  });

  const form = document.getElementById('chat-send-form');
  const input = document.getElementById('chat-input');

  form?.addEventListener('submit', async (e)=>{
    e.preventDefault();
    const text=(input.value||'').trim();
    if(!text) return;
    if(!CURRENT_SESSION_ID){ alert('Hãy chọn một cuộc hội thoại ở bên trái'); return; }

    const res = await fetch(window.ADMIN_CHAT.send(CURRENT_SESSION_ID), {
      method:'POST',
      headers:{
        'Content-Type':'application/json',
        'X-CSRF-TOKEN': csrf,
        'X-Requested-With':'XMLHttpRequest',
        'Accept':'application/json'
      },
      body: JSON.stringify({ content: text })
    });
    if(!res.ok){
      console.error('send failed', res.status, await res.text());
      alert('Gửi tin nhắn thất bại'); return;
    }
    renderMsg({ sender_id: window.AuthUserId??-1, sender_name:'Tôi', content:text });
    input.value='';
    input.style.height='auto';
  });

  input?.addEventListener('keydown',(e)=>{
    if(e.key==='Enter' && !e.shiftKey){ e.preventDefault(); form.requestSubmit(); }
  });
})();
</script>


<style>
  /* Messages */
  #chat-box::-webkit-scrollbar { width: 8px; }
  #chat-box::-webkit-scrollbar-thumb { background: #ec4899; border-radius: 8px; }
  #chat-box::-webkit-scrollbar-track { background: transparent; }
  #chat-box { scrollbar-width: thin; scrollbar-color: #ec4899 transparent; }

  /* Thread list */
  #thread-list::-webkit-scrollbar { width: 8px; }
  #thread-list::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 8px; }
  #thread-list { scrollbar-width: thin; scrollbar-color: #cbd5e1 transparent; }
</style>

@endsection
