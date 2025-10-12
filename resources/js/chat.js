(function () {
  'use strict';

  /* ========== Debug helpers ========== */
  const DBG = true;
  const log = (...args) => { if (DBG) console.log('[chat]', ...args); };
  const err = (...args) => console.error('[chat]', ...args);

  /* ========== Shortcuts & meta ========== */
  const q = (sel) => document.querySelector(sel);
  const getMeta = (name) => q(`meta[name="${name}"]`)?.content || '';

  // URL lấy từ <meta> (cách B)
  const OPEN_URL     = getMeta('chat-open-url');
  const HISTORY_TPL  = getMeta('chat-history-url');   // có placeholder :id
  const SEND_URL     = getMeta('chat-send-url');
  const MARKREAD_URL = getMeta('chat-markread-url');

  // CSRF
  const getCsrf = () =>
    (q('meta[name="csrf-token"]')?.content || q('input[name="_token"]')?.value || '');

  const baseHeaders = () => ({
    'X-CSRF-TOKEN': getCsrf(),
    'X-Requested-With': 'XMLHttpRequest',
    'Accept': 'application/json'
  });

  const esc = (s) =>
    String(s ?? '').replace(/[&<>"']/g, m =>
      ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m])
    );

  /* ========== State ========== */
  const CHAT = { sessionId: null, unreadCount: 0, panelOpen: false, channel: null };

  /* ========== DOM refs ========== */
  let els = {};
  function captureEls() {
    els = {
      bubble:   q('#chat-bubble'),
      panel:    q('#chat-panel'),
      badge:    q('#chat-unread'),
      msgBox:   q('#chat-messages'),
      form:     q('#chat-form'),
      input:    q('#chat-input'),
      minimize: q('#chat-minimize'),
      close:    q('#chat-close'),
      typing:   q('#chat-typing') // optional
    };
    // expose for quick debug in console
    window.__chatDebug = { ...els, CHAT };

    // chỉ cần các phần tử bắt buộc
    const required = ['bubble','panel','badge','msgBox','form','input'];
    return required.every(k => !!els[k]);
  }

  /* ========== Render ========== */
  function scrollToEnd() {
    els.msgBox?.scrollTo({ top: els.msgBox.scrollHeight, behavior: 'smooth' });
  }

  function renderMessage(m) {
    const meId = Number(window.AuthUserId ?? -1);
    const mine = Number(m.sender_id) === meId;
    const wrap = document.createElement('div');
    wrap.className = 'chat-msg ' + (mine ? 'me' : 'other');
    wrap.innerHTML = `<div class="bubble">${esc(m.content)}</div>`;
    els.msgBox.appendChild(wrap);
    scrollToEnd();
  }

  function renderMessages(list) {
    els.msgBox.innerHTML = '';
    (list || []).forEach(renderMessage);
    scrollToEnd();
  }

  function setUnread(n) {
    CHAT.unreadCount = n;
    if (!els.badge) return;
    if (n > 0) {
      els.badge.textContent = String(n);
      els.badge.classList.remove('hidden');
    } else {
      els.badge.textContent = '0';
      els.badge.classList.add('hidden');
    }
  }

  function togglePanel(show) {
    CHAT.panelOpen = (show === undefined ? !CHAT.panelOpen : show);
    els.panel.classList.toggle('hidden', !CHAT.panelOpen);
    if (CHAT.panelOpen) {
      setUnread(0);
      markRead().catch(() => {});
      scrollToEnd();
    }
  }

  /* ========== API calls ========== */
  async function openSession() {
    if (!OPEN_URL) throw new Error('Missing meta: chat-open-url');

    const res = await fetch(OPEN_URL, { method: 'POST', headers: baseHeaders() });
    if (!res.ok) throw new Error(`open failed ${res.status}`);

    const { session_id } = await res.json();
    CHAT.sessionId = session_id;

    await loadHistory(session_id);
    await markRead();
    subscribeRealtime(session_id);

    log('session opened', session_id);
  }

  async function loadHistory(sessionId) {
    if (!HISTORY_TPL) throw new Error('Missing meta: chat-history-url');

    const url = HISTORY_TPL.replace(':id', String(sessionId));
    const res = await fetch(url, { headers: baseHeaders() });
    if (!res.ok) throw new Error(`history failed ${res.status}`);

    const data = await res.json();
    renderMessages(data.messages || []);
  }

  async function markRead() {
    if (!CHAT.sessionId || !MARKREAD_URL) return;
    try {
      await fetch(MARKREAD_URL, {
        method: 'POST',
        headers: { ...baseHeaders(), 'Content-Type': 'application/json' },
        body: JSON.stringify({ session_id: CHAT.sessionId })
      });
    } catch (e) {
      err('markRead error', e);
    }
  }

  async function sendMessage(text) {
    if (!SEND_URL) throw new Error('Missing meta: chat-send-url');

    // optimistic UI
    renderMessage({ sender_id: window.AuthUserId || 0, content: text });

    try {
      const res = await fetch(SEND_URL, {
        method: 'POST',
        headers: { ...baseHeaders(), 'Content-Type': 'application/json' },
        body: JSON.stringify({ session_id: CHAT.sessionId, content: text })
      });
      if (!res.ok) err('send failed', res.status);
    } catch (e) {
      err('send error', e);
    }
  }

  /* ========== Realtime ========== */
  function subscribeRealtime(sessionId) {
    if (!window.Echo) {
      log('Echo not found — skip realtime');
      return;
    }
    if (CHAT.channel) {
      try { CHAT.channel.stopListening('.chat.message.created'); } catch {}
    }
    CHAT.channel = window.Echo.private(`chat.session.${sessionId}`);
    CHAT.channel.listen('.chat.message.created', (e) => {
      const m = e?.message; if (!m) return;
      renderMessage(m);

      const meId = Number(window.AuthUserId ?? -1);
      const isMine = Number(m.sender_id) === meId;

      if (!CHAT.panelOpen && !isMine) setUnread(CHAT.unreadCount + 1);
      else if (!isMine) markRead().catch(() => {});
    });
  }

  /* ========== Events ========== */
  function bindEvents() {
    els.bubble.addEventListener('click', async () => {
      // bắt buộc đăng nhập? nếu có thì redirect
      if (!window.AuthUserId) { window.location.href = '/login'; return; }

      if (!CHAT.sessionId) {
        try { await openSession(); }
        catch (e) { err('openSession error', e); return; }
      }
      togglePanel(true);
    });

    els.form.addEventListener('submit', async (ev) => {
      ev.preventDefault();
      const text = (els.input.value || '').trim();
      if (!text) return;

      if (!CHAT.sessionId) {
        try { await openSession(); }
        catch (e) { err('open before send', e); return; }
      }

      els.input.value = '';
      sendMessage(text);
    });

    els.minimize?.addEventListener('click', () => togglePanel(false));
    els.close?.addEventListener('click', () => togglePanel(false));

    // drag bubble (optional)
    let dragging = false, offsetX = 0, offsetY = 0;
    els.bubble.addEventListener('mousedown', (e) => {
      dragging = true;
      els.bubble.classList.add('dragging');
      const r = els.bubble.getBoundingClientRect();
      offsetX = e.clientX - r.left;
      offsetY = e.clientY - r.top;
    });
    document.addEventListener('mousemove', (e) => {
      if (!dragging) return;
      Object.assign(els.bubble.style, {
        left: (e.clientX - offsetX) + 'px',
        top:  (e.clientY - offsetY) + 'px',
        right: 'auto',
        bottom: 'auto',
        position: 'fixed'
      });
    });
    document.addEventListener('mouseup', () => {
      dragging = false;
      els.bubble.classList.remove('dragging');
    });

    // quay lại tab -> mark read
    document.addEventListener('visibilitychange', () => {
      if (!document.hidden && CHAT.panelOpen) markRead().catch(()=>{});
    });
  }

  /* ========== Bootstrap ========== */
  const ready = (fn) =>
    (document.readyState === 'loading')
      ? document.addEventListener('DOMContentLoaded', fn)
      : fn();

  ready(() => {
    if (!captureEls()) {
      const retry = setInterval(() => {
        if (captureEls()) {
          clearInterval(retry);
          bindEvents();
          log('widget initialized (late)');
        }
      }, 120);
      setTimeout(() => clearInterval(retry), 5000);
      return;
    }
    bindEvents();
    log('widget initialized');
  });
})();
