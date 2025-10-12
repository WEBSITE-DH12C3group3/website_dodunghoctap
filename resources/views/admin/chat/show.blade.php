@extends('layouts.app')

@section('content')
<h1 class="text-xl font-semibold mb-3">Session #{{ $session->session_id }}</h1>

<div class="border rounded p-3 space-y-2 max-w-3xl bg-white"
  id="admin-chat-messages" data-chat-session="{{ $session->session_id }}">
  @foreach($session->messages as $m)
    <div class="flex {{ $m->sender_id == auth()->user()->user_id ? 'justify-end' : 'justify-start' }}">
      <div class="chat-msg {{ $m->sender_id == auth()->user()->user_id ? 'me' : 'other' }}">
        <div class="text-xs text-gray-500 mb-1">{{ $m->sender?->full_name }} — {{ $m->created_at }}</div>
        {{ $m->content }}
      </div>
    </div>
  @endforeach
</div>

<form method="post" class="mt-3" action="{{ route('admin.chat.send', $session->session_id) }}">
  @csrf
  <div class="flex gap-2">
    <input type="text" name="content" class="flex-1 border rounded px-3 py-2" placeholder="Nhập nội dung...">
    <button class="bg-pink-600 text-white rounded px-4">Gửi</button>
  </div>
</form>

<form method="post" class="mt-3" action="{{ route('admin.chat.unassign', $session->session_id) }}">
  @csrf
  <button class="bg-gray-200 hover:bg-gray-300 rounded px-4 py-2">Bỏ phân công</button>
</form>
@endsection
