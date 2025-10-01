<div id="chatbox" style="border:1px solid #7036dbff; padding:10px; width:300px;">
  <div id="messages" style="height:200px; overflow-y:auto;"></div>
  <input type="text" id="messageInput" placeholder="Nhập tin nhắn..." style="width:80%;">
  <button onclick="sendMessage()">Gửi</button>
</div>

<script>
async function sendMessage() {
    const msg = document.getElementById('messageInput').value;
    if (!msg) return;

    let messagesDiv = document.getElementById('messages');
    messagesDiv.innerHTML += `<div><b>Bạn:</b> ${msg}</div>`;

    document.getElementById('messageInput').value = '';

    const res = await fetch('/chat', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ message: msg })
    });

    const data = await res.json();
    messagesDiv.innerHTML += `<div><b>AI:</b> ${data.reply}</div>`;
    messagesDiv.scrollTop = messagesDiv.scrollHeight;
}
</script>
