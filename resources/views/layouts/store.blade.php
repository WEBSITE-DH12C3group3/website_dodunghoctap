<!DOCTYPE html>
<html lang="vi" class="h-full antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'PEAKVL')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Favicon + Icons -->
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

    <!-- Tailwind Config -->
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
        // Theme dark/light
        const theme = localStorage.getItem('theme');
        if (theme === 'dark' || (!theme && matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="font-sans antialiased bg-gray-100 dark:bg-slate-900">
    <!-- Header -->
    <div class="sticky top-0 z-40">
        @include('store.partials.header', [
            'categories' => $categories ?? [],
            'cartCount' => $cartCount ?? 0,
            'activeCategoryId' => $activeCategoryId ?? null,
        ])
    </div>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    @include('store.partials.footer')

    <!-- Chatbot Styles -->
    <style>
        /* Chatbot Toggle Button */
        #chat-toggle {
            position: fixed;
            bottom: 20px;
            right: 20px; /* Giữ vị trí gốc bên phải */
            background: #6635c0;
            color: white;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            text-align: center;
            font-size: 28px;
            cursor: pointer;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            z-index: 10001;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Chatbot Box */
        #chatbox {
            position: fixed;
            bottom: 90px;
            right: 20px; /* Giữ vị trí gốc bên phải */
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
            background: #4a27c9;
            color: white;
            padding: 10px;
            border-radius: 10px 10px 0 0;
            font-weight: bold;
        }

        #messages {
            flex: 1;
            padding: 10px;
            overflow-y: auto;
            background: #f9f9f9;
        }

        #chat-input {
            display: flex;
            border-top: 1px solid #ccc;
        }

        #chat-input input {
            flex: 1;
            border: none;
            padding: 10px;
            outline: none;
        }

        #chat-input button {
            background: #29099c;
            border: none;
            color: white;
            padding: 10px 15px;
            cursor: pointer;
        }

        /* Welcome Notification */
        .welcome-notification {
            position: absolute;
            bottom: calc(100% + 10px);
            right: calc(50% + 60px); /* Dịch sang trái 50px từ tâm nút */
            transform: translateX(50%);
            background-color: #333;
            color: white;
            padding: 10px 15px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            z-index: 10002;
            white-space: nowrap;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
        }

        .welcome-notification::after {
            content: '';
            position: absolute;
            top: 100%;
            left: calc(50% + 55px); /* Dịch mũi tên sang phải 50px */
            transform: translateX(-50%);
            border: 7px solid transparent;
            border-top-color: #333;
        }

        /* Dark Mode Adjustments */
        .dark #chatbox {
            background: #1f2937;
            border-color: #4b5563;
        }

        .dark #chat-header {
            background: #312e81;
        }

        .dark #messages {
            background: #111827;
            color: #e5e7eb;
        }

        .dark #chat-input {
            border-top-color: #4b5563;
        }

        .dark #chat-input input {
            background: #1f2937;
            color: #e5e7eb;
        }
    </style>

    <!-- Chatbot HTML -->
    <div id="chat-toggle">🤖</div>
    <div id="chatbox">
        <div id="chat-header">ChatBot</div>
        <div id="messages"></div>
        <div id="chat-input">
            <input type="text" id="messageInput" placeholder="Nhập tin nhắn...">
            <button onclick="sendMessage()">➤</button>
        </div>
    </div>

    <!-- Chatbot JavaScript -->
    <script>
        let notificationInterval;

        // Toggle Chatbox
        document.getElementById('chat-toggle').addEventListener('click', function() {
            console.log('Chat toggle clicked');
            const chatbox = document.getElementById('chatbox');
            const isOpen = chatbox.style.display === 'flex';
            chatbox.style.display = isOpen ? 'none' : 'flex';
            
            // Dừng hoặc khởi động lại thông báo khi toggle
            if (isOpen) {
                console.log('Chatbox closed, restarting notification interval');
                notificationInterval = setInterval(showWelcomeNotification, 5000);
            } else {
                console.log('Chatbox opened, stopping notification interval');
                clearInterval(notificationInterval);
                // Xóa thông báo hiện tại nếu có
                const existingNotification = document.querySelector('.welcome-notification');
                if (existingNotification) {
                    existingNotification.remove();
                }
            }
        });

        // Send Message
        async function sendMessage() {
            const input = document.getElementById('messageInput');
            const message = input.value.trim();
            if (!message) {
                console.warn('Empty message, not sending');
                return;
            }

            const messagesDiv = document.getElementById('messages');
            messagesDiv.innerHTML += `<div class="text-gray-800 dark:text-gray-200"><b>Bạn:</b> ${message}</div>`;
            input.value = '';

            try {
                const res = await fetch("{{ url('/chat') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ message })
                });

                if (!res.ok) {
                    throw new Error(`HTTP error! Status: ${res.status}`);
                }

                const data = await res.json();
                messagesDiv.innerHTML += `<div class="text-gray-800 dark:text-gray-200"><b>AI:</b> ${data.reply || 'Không có phản hồi'}</div>`;
                messagesDiv.scrollTop = messagesDiv.scrollHeight;
            } catch (error) {
                console.error('Error sending message:', error);
                messagesDiv.innerHTML += `<div class="text-red-500"><b>Lỗi:</b> Không thể gửi tin nhắn. Vui lòng thử lại.</div>`;
                messagesDiv.scrollTop = messagesDiv.scrollHeight;
            }
        }

        // Show Welcome Notification
        function showWelcomeNotification() {
            const chatbox = document.getElementById('chatbox');
            if (chatbox.style.display === 'flex') {
                console.log('Chatbox is open, skipping notification');
                return;
            }

            console.log('Showing welcome notification');
            const chatToggle = document.getElementById('chat-toggle');
            if (chatToggle.querySelector('.welcome-notification')) {
                console.log('Notification already exists');
                return;
            }

            const notificationDiv = document.createElement('div');
            notificationDiv.className = 'welcome-notification';
            notificationDiv.textContent = 'Chào mừng bạn đến với PeakVL.';
            chatToggle.appendChild(notificationDiv);

            setTimeout(() => {
                if (notificationDiv.parentNode) {
                    notificationDiv.remove();
                }
            }, 3000);
        }

        // Initialize Notification
        document.addEventListener('DOMContentLoaded', () => {
            showWelcomeNotification();
            notificationInterval = setInterval(showWelcomeNotification, 5000);
        });
    </script>
</body>
</html>