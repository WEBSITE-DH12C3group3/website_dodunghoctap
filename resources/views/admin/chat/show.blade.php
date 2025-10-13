@extends('layouts.app')

@section('content')
<h1 class="text-xl font-semibold mb-3 text-slate-800 dark:text-slate-100">Session #{{ $session->session_id }}</h1>

{{-- ĐÃ SỬA: Container bên ngoài theo phong cách thống nhất --}}
<div class="bg-white/80 dark:bg-slate-900/50 backdrop-blur p-6 rounded-2xl shadow ring-1 ring-slate-900/5 dark:ring-white/10">

    {{-- KHUNG TIN NHẮN (Messages) --}}
    {{-- ĐÃ SỬA: Bỏ border và shadow cũ, dùng nền xám nhẹ để tách biệt --}}
    <div class="p-3 space-y-3 max-w-3xl bg-gray-50 dark:bg-slate-800 rounded-xl overflow-y-auto h-[400px]"
         id="admin-chat-messages" data-chat-session="{{ $session->session_id }}">
        @foreach($session->messages as $m)
            <div class="flex {{ $m->sender_id == auth()->user()->user_id ? 'justify-end' : 'justify-start' }}">
                {{-- Dùng class Tailwind để định kiểu bubble tin nhắn --}}
                <div class="
                    chat-msg 
                    max-w-[80%] p-3 rounded-xl shadow-sm 
                    {{ $m->sender_id == auth()->user()->user_id 
                        ? 'bg-brand-600 text-white rounded-br-md' 
                        : 'bg-white dark:bg-slate-700 text-slate-800 dark:text-slate-100 rounded-bl-md' 
                    }}
                ">
                    {{-- ĐÃ SỬA: Metadata dùng màu xám nhất quán --}}
                    <div class="text-xs text-gray-400 dark:text-slate-500 mb-1">
                        {{ $m->sender?->full_name ?? 'Khách hàng' }} — {{ $m->created_at->format('H:i') }}
                    </div>
                    {{ $m->content }}
                </div>
            </div>
        @endforeach
    </div>

    {{-- FORM GỬI TIN NHẮN (Composer) --}}
    <form method="post" class="mt-4" action="{{ route('admin.chat.send', $session->session_id) }}">
        @csrf
        <div class="flex gap-2 items-center">
            {{-- ĐÃ SỬA: Input theo phong cách chung --}}
            <input type="text" name="content" class="flex-1 rounded-xl px-4 py-2 text-sm text-slate-700 dark:text-slate-200 
                bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 
                focus:outline-none focus:ring-2 focus:ring-brand-500" 
                placeholder="Nhập nội dung...">
            
            {{-- ĐÃ SỬA: Nút Gửi dùng màu Brand nhất quán --}}
            <button class="rounded-xl px-4 py-2 text-sm font-medium text-white bg-brand-600 hover:bg-brand-700 shadow-sm">
                Gửi
            </button>
        </div>
    </form>

    {{-- FORM HÀNH ĐỘNG PHỤ (Unassign) --}}
    <form method="post" class="mt-3" action="{{ route('admin.chat.unassign', $session->session_id) }}">
        @csrf
        {{-- ĐÃ SỬA: Nút phụ dùng màu xám và phong cách chung --}}
        <button class="rounded-xl px-4 py-2 text-sm font-medium bg-gray-200 hover:bg-gray-300 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-800 dark:text-slate-100 shadow-sm">
            Bỏ phân công
        </button>
    </form>

</div>
@endsection