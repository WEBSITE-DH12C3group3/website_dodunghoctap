<!doctype html>
<html lang="vi" class="h-full antialiased">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'PEAK VL')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    {{-- favicon + icons --}}
    <link rel="apple-touch-icon" sizes="57x57" href="{{ asset('icons/apple-icon-57x57.png') }}">
    <link rel="apple-touch-icon" sizes="60x60" href="{{ asset('icons/apple-icon-60x60.png') }}">
    <link rel="apple-touch-icon" sizes="72x72" href="{{ asset('icons/apple-icon-72x72.png') }}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('icons/apple-icon-76x76.png') }}">
    <link rel="apple-touch-icon" sizes="114x114" href="{{ asset('icons/apple-icon-114x114.png') }}">
    <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('icons/apple-icon-120x120.png') }}">
    <link rel="apple-touch-icon" sizes="144x144" href="{{ asset('icons/apple-icon-144x144.png') }}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('icons/apple-icon-152x152.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('icons/apple-icon-180x180.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('icons/android-icon-192x192.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('icons/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('icons/favicon-96x96.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('icons/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('icons/manifest.json') }}">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="{{ asset('icons/ms-icon-144x144.png') }}">
    <meta name="theme-color" content="#ffffff">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'ui-sans-serif', 'system-ui'] },
                    colors: {
                        brand: { 600: '#4f46e5', 700: '#4338ca' }
                    }
                }
            }
        }
        const theme = localStorage.getItem('theme');
        if (theme === 'dark' || (!theme && matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>

<body class="font-sans antialiased">
    <div class="sticky top-0 z-40">
        @include('store.partials.header', [
            'categories' => $categories ?? [],
            'cartCount' => $cartCount ?? 0,
            'activeCategoryId' => $activeCategoryId ?? null,
        ])
    </div>

    @yield('content')

    @include('store.partials.footer')
</body>

{{-- Chatbot popup --}}
<style>
#chat-toggle {
  position: fixed;
  bottom: 20px;
  right: 20px;
  background: #6635c0ff;
  color: white;
  border-radius: 50%;
  width: 60px;
  height: 60px;
  text-align: center;
  font-size: 28px;
  cursor: pointer;
  box-shadow: 0 4px 8px rgba(0,0,0,0.2);
  z-index: 9999;
  display: flex;
  justify-content: center;
  align-items: center;
}
#chatbox {
  position: fixed;
  bottom: 90px;
  right: 20px;
  width: 320px;
  height: 420px;
  border: 1px solid #ccc;
  background: white;
  border-radius: 10px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.3);
  display: none;
  flex-direction: column;
  z-index: 10000;
}
#chat-header {
  background: #4a27c9ff;
  color: white;
  padding: 10px;
  border-radius: 10px 10px 0 0;
  font-weight: bold;
}
#messages {
  flex: 1;
  padding: 10px;
  overflow-y: auto;
}
#chat-input {
  display: flex;
  border-top: 1px solid #ccc;
}
#chat-input input {
  flex: 1;
  border: none;
  padding: 10px;
}
#chat-input button {
  background: #29099cff;
  border: none;
  color: white;
  padding: 10px 15px;
  cursor: pointer;
}
</style>

<div id="chat-toggle">💬</div>

<div id="chatbox">
  <div id="chat-header">ChatBot</div>
  <div id="messages"></div>
  <div id="chat-input">
    <input type="text" id="messageInput" placeholder="Nhập tin nhắn...">
    <button onclick="sendMessage()">➤</button>
  </div>
</div>

<script>
document.getElementById('chat-toggle').onclick = function() {
  let chatbox = document.getElementById('chatbox');
  chatbox.style.display = chatbox.style.display === 'flex' ? 'none' : 'flex';
};

async function sendMessage() {
    const msg = document.getElementById('messageInput').value;
    if (!msg) return;

    let messagesDiv = document.getElementById('messages');
    messagesDiv.innerHTML += `<div><b>Bạn:</b> ${msg}</div>`;

    document.getElementById('messageInput').value = '';

    const res = await fetch("{{ url('/chat') }}", {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}' 
        },
        body: JSON.stringify({ message: msg })
    });

    const data = await res.json();
    messagesDiv.innerHTML += `<div><b>AI:</b> ${data.reply}</div>`;
    messagesDiv.scrollTop = messagesDiv.scrollHeight;
}
</script>

</html>
