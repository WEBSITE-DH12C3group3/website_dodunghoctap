document.addEventListener('DOMContentLoaded', () => {
  if (!window.Echo) return
  const el = document.querySelector('[data-chat-session]')
  if (!el) return
  const sessionId = el.getAttribute('data-chat-session')
  const meId = window.AuthUserId

  const box = document.getElementById('admin-chat-messages')
  const render = (m) => {
    const wrap = document.createElement('div')
    wrap.className = 'flex ' + (Number(m.sender_id) === Number(meId) ? 'justify-end' : 'justify-start')
    const bubble = document.createElement('div')
    bubble.className = 'chat-msg ' + (Number(m.sender_id) === Number(meId) ? 'me' : 'other')
    bubble.textContent = m.content
    wrap.appendChild(bubble)
    box.appendChild(wrap)
    box.scrollTop = box.scrollHeight
  }

  window.Echo.private(`chat.session.${sessionId}`)
    .listen('.chat.message.created', (e) => render(e.message))
})
