<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>FoodShop — Nhắn tin hỗ trợ</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        :root {
            --brand:    #E8501A;
            --brand-dk: #C03E0E;
            --brand-lt: #FFF1EB;
            --ink:      #1C1008;
            --ink-mid:  #6B4C34;
            --ink-lt:   #A07858;
            --surface:  #FFFAF7;
            --card:     #FFFFFF;
            --border:   rgba(232,80,26,0.12);
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--surface);
            color: var(--ink);
            height: 100vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        /* ── TOPBAR ── */
        .topbar {
            height: 64px;
            background: rgba(255,250,247,0.95);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            flex-shrink: 0;
            position: relative;
            z-index: 10;
        }
        .topbar-left { display: flex; align-items: center; gap: 12px; }
        .topbar-back {
            width: 36px; height: 36px;
            background: var(--brand-lt);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            color: var(--brand);
            text-decoration: none;
            font-size: 15px;
            transition: all 0.2s;
        }
        .topbar-back:hover { background: var(--brand); color: white; }
        .shop-avatar {
            width: 40px; height: 40px;
            background: linear-gradient(135deg, var(--brand), var(--brand-dk));
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px;
            box-shadow: 0 4px 12px rgba(232,80,26,0.3);
            position: relative;
        }
        .online-dot {
            position: absolute;
            bottom: -2px; right: -2px;
            width: 11px; height: 11px;
            background: #22C55E;
            border: 2px solid var(--surface);
            border-radius: 50%;
        }
        .shop-info h3 {
            font-size: 15px;
            font-weight: 700;
            color: var(--ink);
            line-height: 1;
        }
        .shop-info p {
            font-size: 12px;
            color: #22C55E;
            font-weight: 500;
            margin-top: 3px;
        }
        .topbar-right { display: flex; align-items: center; gap: 8px; }
        .nav-logo-wrap {
            display: flex; align-items: center; gap: 8px;
            text-decoration: none;
        }
        .nav-logo-icon {
            width: 32px; height: 32px;
            background: var(--brand);
            border-radius: 9px;
            display: flex; align-items: center; justify-content: center;
            font-size: 15px;
        }
        .nav-logo-text {
            font-family: 'Playfair Display', serif;
            font-size: 18px;
            font-weight: 700;
            color: var(--ink);
        }
        .nav-logo-text span { color: var(--brand); }

        /* ── CHAT BODY ── */
        .chat-body {
            flex: 1;
            overflow-y: auto;
            padding: 24px 0;
            display: flex;
            flex-direction: column;
            gap: 0;
            position: relative;
        }
        .chat-body::-webkit-scrollbar { width: 4px; }
        .chat-body::-webkit-scrollbar-thumb { background: #E8C0A8; border-radius: 2px; }

        .day-divider {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 24px 16px;
        }
        .day-divider::before, .day-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border);
        }
        .day-divider span {
            font-size: 11px;
            font-weight: 600;
            color: var(--ink-lt);
            letter-spacing: 0.5px;
            white-space: nowrap;
        }

        .msg-row {
            display: flex;
            align-items: flex-end;
            gap: 8px;
            padding: 3px 24px;
            animation: msgIn 0.25s cubic-bezier(0.34,1.56,0.64,1) both;
        }
        @keyframes msgIn {
            from { opacity: 0; transform: translateY(10px) scale(0.95); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }
        .msg-row.user { flex-direction: row-reverse; }

        .msg-avatar {
            width: 30px; height: 30px;
            border-radius: 9px;
            display: flex; align-items: center; justify-content: center;
            font-size: 13px;
            font-weight: 700;
            flex-shrink: 0;
            margin-bottom: 2px;
        }
        .msg-avatar.admin-av {
            background: linear-gradient(135deg, var(--brand), var(--brand-dk));
            color: white;
        }
        .msg-avatar.user-av {
            background: linear-gradient(135deg, #1C1008, #6B4C34);
            color: white;
        }

        .msg-bubble-wrap { max-width: 72%; display: flex; flex-direction: column; gap: 2px; }
        .msg-row.user .msg-bubble-wrap { align-items: flex-end; }

        .msg-bubble {
            padding: 11px 15px;
            border-radius: 18px;
            font-size: 14px;
            line-height: 1.55;
            word-break: break-word;
            position: relative;
        }
        .msg-bubble.admin {
            background: white;
            color: var(--ink);
            border: 1px solid var(--border);
            border-bottom-left-radius: 5px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }
        .msg-bubble.user {
            background: linear-gradient(135deg, var(--brand), var(--brand-dk));
            color: white;
            border-bottom-right-radius: 5px;
            box-shadow: 0 4px 14px rgba(232,80,26,0.3);
        }
        .msg-time {
            font-size: 10.5px;
            color: var(--ink-lt);
            padding: 0 4px;
        }

        /* Typing indicator */
        .typing-indicator {
            display: none;
            align-items: flex-end;
            gap: 8px;
            padding: 3px 24px;
        }
        .typing-indicator.visible { display: flex; }
        .typing-bubble {
            background: white;
            border: 1px solid var(--border);
            border-radius: 18px;
            border-bottom-left-radius: 5px;
            padding: 12px 16px;
            display: flex;
            gap: 4px;
            align-items: center;
        }
        .typing-dot {
            width: 7px; height: 7px;
            background: var(--ink-lt);
            border-radius: 50%;
            animation: typingBounce 1.2s ease-in-out infinite;
        }
        .typing-dot:nth-child(2) { animation-delay: 0.2s; }
        .typing-dot:nth-child(3) { animation-delay: 0.4s; }
        @keyframes typingBounce {
            0%,60%,100% { transform: translateY(0); }
            30%          { transform: translateY(-5px); }
        }

        /* Empty state */
        .empty-chat {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 24px;
            text-align: center;
        }
        .empty-chat-icon {
            width: 80px; height: 80px;
            background: var(--brand-lt);
            border-radius: 24px;
            display: flex; align-items: center; justify-content: center;
            font-size: 36px;
            margin-bottom: 20px;
            box-shadow: 0 8px 24px rgba(232,80,26,0.15);
        }
        .empty-chat h3 {
            font-family: 'Playfair Display', serif;
            font-size: 20px;
            font-weight: 700;
            color: var(--ink);
            margin-bottom: 8px;
        }
        .empty-chat p {
            font-size: 13.5px;
            color: var(--ink-lt);
            line-height: 1.6;
            max-width: 280px;
        }

        /* Quick replies */
        .quick-replies {
            padding: 12px 24px 4px;
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        .quick-btn {
            background: white;
            border: 1px solid var(--border);
            border-radius: 100px;
            padding: 7px 14px;
            font-size: 12.5px;
            font-weight: 500;
            color: var(--ink-mid);
            cursor: pointer;
            font-family: 'DM Sans', sans-serif;
            transition: all 0.2s;
        }
        .quick-btn:hover {
            background: var(--brand-lt);
            border-color: rgba(232,80,26,0.3);
            color: var(--brand);
        }

        /* ── INPUT BAR ── */
        .input-bar {
            background: rgba(255,250,247,0.95);
            backdrop-filter: blur(16px);
            border-top: 1px solid var(--border);
            padding: 14px 20px;
            display: flex;
            align-items: flex-end;
            gap: 10px;
            flex-shrink: 0;
        }
        .input-wrap {
            flex: 1;
            background: white;
            border: 1.5px solid var(--border);
            border-radius: 16px;
            display: flex;
            align-items: flex-end;
            padding: 10px 14px;
            gap: 8px;
            transition: border-color 0.2s;
        }
        .input-wrap:focus-within {
            border-color: rgba(232,80,26,0.4);
            box-shadow: 0 0 0 3px rgba(232,80,26,0.07);
        }
        #msgInput {
            flex: 1;
            border: none;
            outline: none;
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            color: var(--ink);
            background: transparent;
            resize: none;
            max-height: 120px;
            line-height: 1.5;
        }
        #msgInput::placeholder { color: var(--ink-lt); }

        .send-btn {
            width: 44px; height: 44px;
            background: linear-gradient(135deg, var(--brand), var(--brand-dk));
            border: none;
            border-radius: 13px;
            cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            color: white;
            font-size: 16px;
            transition: all 0.22s;
            box-shadow: 0 4px 14px rgba(232,80,26,0.35);
            flex-shrink: 0;
        }
        .send-btn:hover { transform: translateY(-2px) scale(1.05); box-shadow: 0 6px 20px rgba(232,80,26,0.45); }
        .send-btn:active { transform: scale(0.95); }
        .send-btn:disabled { opacity: 0.5; transform: none; cursor: not-allowed; }
    </style>
</head>
<body>

<!-- TOPBAR -->
<div class="topbar">
    <div class="topbar-left">
        <a href="/" class="topbar-back">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
        </a>
        <div class="shop-avatar">
            🍔
            <div class="online-dot"></div>
        </div>
        <div class="shop-info">
            <h3>FoodShop Hỗ trợ</h3>
            <p>● Đang trực tuyến</p>
        </div>
    </div>
    <a href="/" class="nav-logo-wrap">
        <div class="nav-logo-icon">🍔</div>
        <span class="nav-logo-text">Food<span>Shop</span></span>
    </a>
</div>

<!-- CHAT BODY -->
<div class="chat-body" id="chatBody">

    @if($messages->isEmpty())
    <div class="empty-chat" id="emptyState">
        <div class="empty-chat-icon">💬</div>
        <h3>Chào {{ session('user')['name'] }}!</h3>
        <p>Bạn có câu hỏi về đơn hàng, menu hay cần hỗ trợ gì? Chúng tôi luôn sẵn sàng giúp bạn!</p>
    </div>
    @else
    @foreach($messages as $msg)
    <div class="msg-row {{ $msg->sender === 'user' ? 'user' : '' }}">
        @if($msg->sender === 'admin')
        <div class="msg-avatar admin-av">🍔</div>
        @endif
        <div class="msg-bubble-wrap">
            <div class="msg-bubble {{ $msg->sender }}">{{ $msg->content }}</div>
            <div class="msg-time">{{ $msg->created_at->format('H:i') }}</div>
        </div>
        @if($msg->sender === 'user')
        <div class="msg-avatar user-av">{{ strtoupper(substr(session('user')['name'], 0, 1)) }}</div>
        @endif
    </div>
    @endforeach
    @endif

    <!-- Typing indicator -->
    <div class="typing-indicator" id="typingIndicator">
        <div class="msg-avatar admin-av">🍔</div>
        <div class="typing-bubble">
            <div class="typing-dot"></div>
            <div class="typing-dot"></div>
            <div class="typing-dot"></div>
        </div>
    </div>
</div>

<!-- QUICK REPLIES (chỉ show khi chưa có tin nhắn) -->
@if($messages->isEmpty())
<div class="quick-replies" id="quickReplies">
    <button class="quick-btn" onclick="quickSend('Đơn hàng của tôi đâu rồi?')">📦 Theo dõi đơn hàng</button>
    <button class="quick-btn" onclick="quickSend('Hôm nay có khuyến mãi gì không?')">🎁 Khuyến mãi</button>
    <button class="quick-btn" onclick="quickSend('Thời gian giao hàng là bao lâu?')">⏱️ Thời gian giao hàng</button>
    <button class="quick-btn" onclick="quickSend('Tôi muốn đổi/hủy đơn hàng')">🔄 Đổi/Hủy đơn</button>
</div>
@endif

<!-- INPUT BAR -->
<div class="input-bar">
    <div class="input-wrap">
        <textarea id="msgInput" placeholder="Nhắn tin cho FoodShop..." rows="1"></textarea>
    </div>
    <button class="send-btn" id="sendBtn" onclick="sendMessage()">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
    </button>
</div>

<script>
const CSRF  = document.querySelector('meta[name="csrf-token"]').content;
const USER_INITIAL = '{{ strtoupper(substr(session("user")["name"], 0, 1)) }}';
let lastId = {{ $messages->isNotEmpty() ? $messages->last()->id : 0 }};
let polling;

function scrollBottom(smooth = true) {
    const body = document.getElementById('chatBody');
    body.scrollTo({ top: body.scrollHeight, behavior: smooth ? 'smooth' : 'instant' });
}

function formatTime(dateStr) {
    const d = new Date(dateStr);
    return d.getHours().toString().padStart(2,'0') + ':' + d.getMinutes().toString().padStart(2,'0');
}

function appendMessage(content, sender, time) {
    // Ẩn empty state
    const empty = document.getElementById('emptyState');
    if (empty) empty.remove();
    const qr = document.getElementById('quickReplies');
    if (qr) qr.remove();

    const indicator = document.getElementById('typingIndicator');
    const row = document.createElement('div');
    row.className = 'msg-row' + (sender === 'user' ? ' user' : '');
    row.innerHTML = `
        ${sender === 'admin' ? '<div class="msg-avatar admin-av">🍔</div>' : ''}
        <div class="msg-bubble-wrap">
            <div class="msg-bubble ${sender}">${escapeHtml(content)}</div>
            <div class="msg-time">${time}</div>
        </div>
        ${sender === 'user' ? `<div class="msg-avatar user-av">${USER_INITIAL}</div>` : ''}
    `;
    document.getElementById('chatBody').insertBefore(row, indicator);
    scrollBottom();
}

function escapeHtml(str) {
    return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')
              .replace(/"/g,'&quot;').replace(/\n/g,'<br>');
}

async function sendMessage() {
    const input = document.getElementById('msgInput');
    const content = input.value.trim();
    if (!content) return;

    const btn = document.getElementById('sendBtn');
    btn.disabled = true;
    input.value = '';
    input.style.height = 'auto';

    appendMessage(content, 'user', new Date().toLocaleTimeString('vi',{hour:'2-digit',minute:'2-digit'}));

    try {
        const res = await fetch('/chat/send', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ content })
        });
        const data = await res.json();
        if (data.message) lastId = data.message.id;
    } catch(e) { console.error(e); }

    btn.disabled = false;
    input.focus();
}

async function quickSend(text) {
    document.getElementById('msgInput').value = text;
    await sendMessage();
}

// Auto-resize textarea
document.getElementById('msgInput').addEventListener('input', function() {
    this.style.height = 'auto';
    this.style.height = Math.min(this.scrollHeight, 120) + 'px';
});

// Enter to send
document.getElementById('msgInput').addEventListener('keydown', function(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        sendMessage();
    }
});

// Polling mỗi 2.5s
async function poll() {
    try {
        const res  = await fetch(`/chat/poll?last_id=${lastId}`);
        const data = await res.json();
        if (data.messages && data.messages.length > 0) {
            data.messages.forEach(msg => {
                if (msg.sender === 'admin') {
                    appendMessage(msg.content, 'admin', formatTime(msg.created_at));
                }
                lastId = Math.max(lastId, msg.id);
            });
        }
    } catch(e) {}
}

polling = setInterval(poll, 2500);

// Scroll to bottom on load
window.addEventListener('load', () => scrollBottom(false));
</script>
</body>
</html>