import axios from "axios";

/* ===== axios defaults ===== */
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.headers.common['X-CSRF-TOKEN'] =
  document.querySelector('meta[name="csrf-token"]')?.content || '';

/* ===== Base URLs lấy từ meta (đúng cả khi có /public) ===== */
const ADMIN_CHAT_BASE =
  document.querySelector('meta[name="admin-chat-base"]')?.content || '/admin/chat';

const urls = {
  messages: (sid) => `${ADMIN_CHAT_BASE}/${sid}/messages`,
  send:     (sid) => `${ADMIN_CHAT_BASE}/${sid}/send`,
  assign:   (sid) => `${ADMIN_CHAT_BASE}/${sid}/assign`,
  unassign: (sid) => `${ADMIN_CHAT_BASE}/${sid}/unassign`,
  markRead: (sid) => `${ADMIN_CHAT_BASE}/${sid}/mark-read`,
};

const S = {
  meId: Number(window.AuthUserId ?? -1),
  current: null,
  channel: null,
};

function esc(s){return String(s??"").replace(/[&<>"']/g,m=>({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;' }[m]))}

function renderMsg(m){
  const box = document.getElementById('chat-box');
  const mine = Number(m.sender_id) === S.meId;
  const wrap = document.createElement('div');
  wrap.className = 'flex ' + (mine ? 'justify-end' : 'justify-start');
  const bubble = document.createElement('div');
  bubble.className = 'max-w-[70%] rounded-2xl px-3 py-2 shadow-sm ' + (mine ? 'bg-pink-600 text-white rounded-br-sm' : 'bg-white text-gray-800 rounded-bl-sm');
  bubble.innerHTML = `
    <div class="text-[11px] opacity-70 mb-0.5">${esc(m.sender_name ?? '')} ${m.created_at_label ? '— '+esc(m.created_at_label) : ''}</div>
    ${esc(m.content)}
  `;
  wrap.appendChild(bubble);
  box.appendChild(wrap);
  box.scrollTop = box.scrollHeight;
}

async function loadSession(sessionId){
  if(!sessionId) return;
  S.current = sessionId;

  document.getElementById('chat-empty')?.remove();
  const box = document.getElementById('chat-box');
  box.innerHTML = '';

  try {
    const { data } = await axios.get(urls.messages(sessionId));

    if (data.session) {
      const customer = data.session.user?.full_name ?? 'Khách hàng';
      document.getElementById('chat-avatar').textContent = (customer[0]||'?').toUpperCase();
      document.getElementById('chat-customer').textContent = customer;
      document.getElementById('chat-meta').textContent =
        `#${data.session.session_id} • ${data.session.assigned_admin ? 'Đã phân công' : 'Chưa phân công'}`;

      const assignForm = document.getElementById('assign-form');
      const unassignForm = document.getElementById('unassign-form');
      assignForm.classList.toggle('hidden', !!data.session.assigned_admin);
      unassignForm.classList.toggle('hidden', !data.session.assigned_admin);
      assignForm.setAttribute('action', urls.assign(sessionId));
      unassignForm.setAttribute('action', urls.unassign(sessionId));

      (data.messages||[]).forEach(renderMsg);
    } else {
      (data.messages||[]).forEach(renderMsg);
      document.getElementById('chat-avatar').textContent = '?';
      document.getElementById('chat-customer').textContent = `#${sessionId}`;
      document.getElementById('chat-meta').textContent = '';
      document.getElementById('assign-form')?.classList.add('hidden');
      document.getElementById('unassign-form')?.classList.add('hidden');
    }
  } catch (e) {
    console.error('[admin-messenger] messages failed', e);
    return;
  }

  // Realtime
  try {
    if (S.channel) S.channel.stopListening('.chat.message.created');
    S.channel = window.Echo?.private(`chat.session.${sessionId}`);
    S.channel?.listen('.chat.message.created', (e) => {
      renderMsg(e.message);
    });
  } catch {}
}

function setupThreadClicks(){
  const wrap = document.getElementById('thread-list');
  if (!wrap) return;
  wrap.addEventListener('click', (ev) => {
    const btn = ev.target.closest('.thread-item');
    if (!btn) return;
    wrap.querySelectorAll('.thread-item').forEach(b=>b.classList.remove('bg-gray-100'));
    btn.classList.add('bg-gray-100');
    loadSession(btn.getAttribute('data-session'));
  }, false);
}

function setupSend(){
  const form = document.getElementById('chat-send-form');
  const input = document.getElementById('chat-input');

  form?.addEventListener('submit', async (ev)=>{
    ev.preventDefault();
    const text = input.value.trim();
    if(!text || !S.current) return;

    try {
      await axios.post(urls.send(S.current), { content: text });
      renderMsg({ sender_id: S.meId, sender_name: 'Tôi', content: text, created_at_label: new Date().toLocaleTimeString() });
      input.value=''; input.style.height='auto';
    } catch(e){
      console.error('[admin-messenger] send failed', e);
      alert('Gửi tin nhắn thất bại');
    }
  });

  input?.addEventListener('input', ()=>{
    input.style.height='auto';
    input.style.height = Math.min(input.scrollHeight, 200)+'px';
  });
  input?.addEventListener('keydown',(e)=>{
    if(e.key==='Enter' && !e.shiftKey){ e.preventDefault(); form.requestSubmit(); }
  });
}

document.addEventListener('DOMContentLoaded', ()=>{
  setupThreadClicks();
  setupSend();
});
console.log('[admin-messenger] loaded');
