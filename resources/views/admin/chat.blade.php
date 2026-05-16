@extends('layouts.admin')

@section('title', 'Hộp thư — Nhắn tin với khách')
@section('page-title', 'Hộp thư')
@section('page-sub', 'Nhắn tin trực tiếp với khách hàng')

@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    /* ── LAYOUT ── */
    .chat-shell {
        display: grid;
        grid-template-columns: 300px 1fr;
        height: calc(100vh - 64px - 48px);
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 20px;
        overflow: hidden;
    }

    /* ── SIDEBAR ── */
    .chat-sidebar {
        border-right: 1px solid var(--border);
        display: flex;
        flex-direction: column;
        background: var(--surface);
    }
    .sidebar-head {
        padding: 18px 18px 14px;
        border-bottom: 1px solid var(--border);
        flex-shrink: 0;
    }
    .sidebar-head h3 {
        font-size: 14px;
        font-weight: 700;
        color: var(--ink);
        margin-bottom: 10px;
    }
    .search-box {
        display: flex;
        align-items: center;
        gap: 8px;
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 8px 12px;
    }
    .search-box svg { color: var(--ink-3); flex-shrink: 0; }
    .search-box input {
        border: none; outline: none; background: transparent;
        font-family: 'DM Sans', sans-serif;
        font-size: 13px; color: var(--ink);
        width: 100%;
    }
    .search-box input::placeholder { color: var(--ink-3); }

    .user-list { flex: 1; overflow-y: auto; }
    .user-list::-webkit-scrollbar { width: 3px; }
    .user-list::-webkit-scrollbar-thumb { background: var(--border); border-radius: 2px; }

    .user-item {
        display: flex;
        align-items: center;
        gap: 11px;
        padding: 13px 18px;
        cursor: pointer;
        transition: background 0.15s;
        border-bottom: 1px solid rgba(232,80,26,0.05);
        text-decoration: none;
        position: relative;
    }
    .user-item:hover { background: rgba(232,80,26,0.04); }
    .user-item.active { background: var(--brand-lt); border-right: 3px solid var(--brand); }

    /* ── AVATAR chung ── */
    .av-wrap {
        position: relative;
        flex-shrink: 0;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        color: white;
    }
    .av-wrap img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
    .av-initials {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .av-40  { width: 40px;  height: 40px;  border-radius: 12px; font-size: 15px; }
    .av-38  { width: 38px;  height: 38px;  border-radius: 11px; font-size: 15px; }
    .av-28  { width: 28px;  height: 28px;  border-radius: 8px;  font-size: 12px; }

    .av-default { background: linear-gradient(135deg, var(--ink), #6B4C34); }
    .av-active  { background: linear-gradient(135deg, var(--brand), var(--brand-dk)); }
    .av-admin-c { background: linear-gradient(135deg, var(--brand), var(--brand-dk)); }

    .user-info { flex: 1; min-width: 0; }
    .user-name {
        font-size: 13.5px;
        font-weight: 600;
        color: var(--ink);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .user-preview {
        font-size: 12px;
        color: var(--ink-3);
        margin-top: 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .unread-badge {
        background: var(--brand);
        color: white;
        font-size: 10px;
        font-weight: 700;
        min-width: 18px; height: 18px;
        border-radius: 9px;
        display: flex; align-items: center; justify-content: center;
        padding: 0 5px;
        flex-shrink: 0;
    }

    .sidebar-empty {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 32px 20px;
        text-align: center;
        color: var(--ink-3);
    }
    .sidebar-empty svg { margin-bottom: 12px; opacity: 0.4; }
    .sidebar-empty p { font-size: 13px; line-height: 1.5; }

    /* ── MAIN PANEL ── */
    .chat-main {
        display: flex;
        flex-direction: column;
        background: var(--card);
    }

    .chat-topbar {
        height: 64px;
        padding: 0 22px;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-shrink: 0;
    }
    .chat-topbar-left { display: flex; align-items: center; gap: 12px; }
    .chat-user-name { font-size: 14.5px; font-weight: 700; color: var(--ink); }
    .chat-user-sub  { font-size: 12px; color: var(--ink-3); margin-top: 2px; }

    .chat-actions { display: flex; gap: 8px; }
    .chat-action-btn {
        width: 34px; height: 34px;
        border-radius: 9px;
        background: var(--surface);
        border: 1px solid var(--border);
        display: flex; align-items: center; justify-content: center;
        color: var(--ink-3);
        cursor: pointer;
        transition: all 0.18s;
        font-size: 13px;
        text-decoration: none;
    }
    .chat-action-btn:hover { background: var(--brand-lt); color: var(--brand); border-color: rgba(232,80,26,0.25); }

    /* Messages area */
    .msg-area {
        flex: 1;
        overflow-y: auto;
        padding: 20px 0;
        display: flex;
        flex-direction: column;
    }
    .msg-area::-webkit-scrollbar { width: 4px; }
    .msg-area::-webkit-scrollbar-thumb { background: #E8C0A8; border-radius: 2px; }

    .day-sep {
        display: flex; align-items: center; gap: 10px;
        padding: 8px 22px 14px;
    }
    .day-sep::before, .day-sep::after {
        content: ''; flex: 1; height: 1px; background: var(--border);
    }
    .day-sep span { font-size: 11px; font-weight: 600; color: var(--ink-3); white-space: nowrap; }

    .msg-row {
        display: flex;
        align-items: flex-end;
        gap: 8px;
        padding: 3px 22px;
        animation: fadeUp 0.2s ease both;
    }
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(8px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .msg-row.admin-msg { flex-direction: row-reverse; }

    .bubble-wrap { max-width: 68%; display: flex; flex-direction: column; gap: 2px; }
    .msg-row.admin-msg .bubble-wrap { align-items: flex-end; }

    .bubble {
        padding: 10px 14px;
        border-radius: 16px;
        font-size: 13.5px;
        line-height: 1.55;
        word-break: break-word;
    }
    .bubble.from-user {
        background: var(--surface);
        color: var(--ink);
        border: 1px solid var(--border);
        border-bottom-left-radius: 4px;
    }
    .bubble.from-admin {
        background: linear-gradient(135deg, var(--brand), var(--brand-dk));
        color: white;
        border-bottom-right-radius: 4px;
        box-shadow: 0 3px 12px rgba(232,80,26,0.28);
    }
    .bubble-time { font-size: 10.5px; color: var(--ink-3); padding: 0 3px; }

    /* Typing */
    .typing-row {
        display: none;
        align-items: flex-end;
        gap: 8px;
        padding: 3px 22px;
    }
    .typing-row.show { display: flex; }
    .typing-dots {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 16px;
        border-bottom-left-radius: 4px;
        padding: 11px 14px;
        display: flex; gap: 4px; align-items: center;
    }
    .tdot {
        width: 6px; height: 6px;
        background: var(--ink-lt);
        border-radius: 50%;
        animation: tdBounce 1.2s ease-in-out infinite;
    }
    .tdot:nth-child(2) { animation-delay: 0.2s; }
    .tdot:nth-child(3) { animation-delay: 0.4s; }
    @keyframes tdBounce {
        0%,60%,100% { transform: translateY(0); }
        30%          { transform: translateY(-5px); }
    }

    /* No conversation selected */
    .no-conv {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: var(--ink-3);
        text-align: center;
        padding: 40px;
    }
    .no-conv-icon {
        width: 72px; height: 72px;
        background: var(--surface);
        border-radius: 20px;
        display: flex; align-items: center; justify-content: center;
        font-size: 30px;
        margin-bottom: 18px;
    }
    .no-conv h3 { font-size: 16px; font-weight: 600; color: var(--ink-2); margin-bottom: 6px; }
    .no-conv p  { font-size: 13px; line-height: 1.5; }

    /* Input bar */
    .admin-input-bar {
        border-top: 1px solid var(--border);
        padding: 14px 18px;
        display: flex;
        align-items: flex-end;
        gap: 10px;
        flex-shrink: 0;
        background: var(--surface);
    }
    .admin-input-wrap {
        flex: 1;
        background: var(--card);
        border: 1.5px solid var(--border);
        border-radius: 14px;
        padding: 10px 14px;
        transition: border-color 0.2s;
    }
    .admin-input-wrap:focus-within {
        border-color: rgba(232,80,26,0.4);
        box-shadow: 0 0 0 3px rgba(232,80,26,0.06);
    }
    #adminInput {
        width: 100%;
        border: none; outline: none;
        font-family: 'DM Sans', sans-serif;
        font-size: 13.5px; color: var(--ink);
        background: transparent;
        resize: none;
        max-height: 100px;
        line-height: 1.5;
    }
    #adminInput::placeholder { color: var(--ink-3); }

    .admin-send-btn {
        width: 42px; height: 42px;
        background: linear-gradient(135deg, var(--brand), var(--brand-dk));
        border: none; border-radius: 12px;
        cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        color: white;
        transition: all 0.2s;
        box-shadow: 0 3px 12px rgba(232,80,26,0.3);
        flex-shrink: 0;
    }
    .admin-send-btn:hover { transform: translateY(-2px); box-shadow: 0 5px 18px rgba(232,80,26,0.4); }
    .admin-send-btn:active { transform: scale(0.96); }
    .admin-send-btn:disabled { opacity: 0.5; transform: none; cursor: not-allowed; }

    /* Canned responses */
    .canned-bar {
        padding: 10px 18px 0;
        display: flex;
        gap: 7px;
        flex-wrap: wrap;
        border-top: 1px solid var(--border);
        background: var(--surface);
        padding-bottom: 0;
    }
    .canned-btn {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 5px 11px;
        font-size: 12px; font-weight: 500;
        color: var(--ink-mid);
        cursor: pointer;
        font-family: 'DM Sans', sans-serif;
        transition: all 0.18s;
        white-space: nowrap;
    }
    .canned-btn:hover {
        background: var(--brand-lt);
        color: var(--brand);
        border-color: rgba(232,80,26,0.3);
    }
</style>
@endsection

@section('content')

<div class="chat-shell">

    <!-- ── SIDEBAR ── -->
    <div class="chat-sidebar">
        <div class="sidebar-head">
            <h3>Hộp thư ({{ $users->count() }})</h3>
            <div class="search-box">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" placeholder="Tìm khách hàng..." id="searchUser">
            </div>
        </div>

        <div class="user-list" id="userList">
            @forelse($users as $u)
            @php
                $isActive = isset($user) && $user->id === $u->id;
                $avColor  = $isActive ? 'av-active' : 'av-default';
                $uInit    = strtoupper(substr($u->name ?? 'U', 0, 1));
            @endphp
            <a href="/admin/chat/{{ $u->id }}"
               class="user-item {{ $isActive ? 'active' : '' }}"
               data-name="{{ strtolower($u->name) }}"
               data-uid="{{ $u->id }}">

                {{-- Avatar sidebar --}}
                <div class="av-wrap av-40 {{ $avColor }}">
                    @if(!empty($u->avatar))
                        <img src="{{ asset('storage/' . $u->avatar) }}" alt="{{ $uInit }}"
                             onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                        <div class="av-initials" style="display:none">{{ $uInit }}</div>
                    @else
                        <div class="av-initials">{{ $uInit }}</div>
                    @endif
                </div>

                <div class="user-info">
                    <div class="user-name">{{ $u->name }}</div>
                    <div class="user-preview">{{ $u->email }}</div>
                </div>
                @if($u->unread_count > 0)
                <div class="unread-badge" id="badge-{{ $u->id }}">{{ $u->unread_count }}</div>
                @else
                <div class="unread-badge" id="badge-{{ $u->id }}" style="display:none">0</div>
                @endif
            </a>
            @empty
            <div class="sidebar-empty">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                <p>Chưa có tin nhắn nào từ khách hàng</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- ── MAIN PANEL ── -->
    <div class="chat-main">

        @isset($user)
        @php $userInit = strtoupper(substr($user->name ?? 'U', 0, 1)); @endphp

        <!-- Topbar -->
        <div class="chat-topbar">
            <div class="chat-topbar-left">

                {{-- Avatar topbar --}}
                <div class="av-wrap av-38 av-active">
                    @if(!empty($user->avatar))
                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $userInit }}"
                             onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                        <div class="av-initials" style="display:none">{{ $userInit }}</div>
                    @else
                        <div class="av-initials">{{ $userInit }}</div>
                    @endif
                </div>

                <div>
                    <div class="chat-user-name">{{ $user->name }}</div>
                    <div class="chat-user-sub">{{ $user->email }}</div>
                </div>
            </div>
            <div class="chat-actions">
                <a href="/admin/orders?user={{ $user->id }}" class="chat-action-btn" title="Xem đơn hàng">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
                </a>
            </div>
        </div>

        <!-- Messages -->
        <div class="msg-area" id="msgArea">
            @forelse($messages as $msg)
            <div class="msg-row {{ $msg->sender === 'admin' ? 'admin-msg' : '' }}">

                @if($msg->sender === 'user')
                {{-- Avatar bubble user --}}
                <div class="av-wrap av-28 av-default">
                    @if(!empty($user->avatar))
                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $userInit }}"
                             onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                        <div class="av-initials" style="display:none">{{ $userInit }}</div>
                    @else
                        <div class="av-initials">{{ $userInit }}</div>
                    @endif
                </div>
                @endif

                <div class="bubble-wrap">
                    <div class="bubble {{ $msg->sender === 'admin' ? 'from-admin' : 'from-user' }}">
                        {{ $msg->content }}
                    </div>
                    <div class="bubble-time">{{ $msg->created_at->format('H:i') }}</div>
                </div>

                @if($msg->sender === 'admin')
                <div class="av-wrap av-28 av-admin-c">
                    <div class="av-initials">AD</div>
                </div>
                @endif

            </div>
            @empty
            <div style="flex:1;display:flex;align-items:center;justify-content:center;color:var(--ink-3);font-size:13px">
                Chưa có tin nhắn nào
            </div>
            @endforelse

            <!-- Typing indicator -->
            <div class="typing-row" id="typingRow">
                {{-- Avatar typing --}}
                <div class="av-wrap av-28 av-default">
                    @if(!empty($user->avatar))
                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $userInit }}"
                             onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                        <div class="av-initials" style="display:none">{{ $userInit }}</div>
                    @else
                        <div class="av-initials">{{ $userInit }}</div>
                    @endif
                </div>
                <div class="typing-dots">
                    <div class="tdot"></div><div class="tdot"></div><div class="tdot"></div>
                </div>
            </div>
        </div>

        <!-- Canned responses -->
        <div class="canned-bar">
            <button class="canned-btn" onclick="insertCanned('Xin chào! Tôi có thể giúp gì cho bạn?')">👋 Xin chào</button>
            <button class="canned-btn" onclick="insertCanned('Đơn hàng của bạn đang được xử lý, vui lòng chờ trong giây lát!')">📦 Đang xử lý</button>
            <button class="canned-btn" onclick="insertCanned('Đơn hàng đã được giao! Cảm ơn bạn đã tin tưởng FoodShop 🎉')">✅ Đã giao</button>
            <button class="canned-btn" onclick="insertCanned('Xin lỗi vì sự bất tiện này. Chúng tôi sẽ hỗ trợ bạn ngay!')">🙏 Xin lỗi</button>
            <button class="canned-btn" onclick="insertCanned('Cảm ơn bạn đã phản hồi! Chúc bạn ngon miệng 🍔')">🙂 Cảm ơn</button>
        </div>

        <!-- Input bar -->
        <div class="admin-input-bar">
            <div class="admin-input-wrap">
                <textarea id="adminInput" placeholder="Nhắn tin phản hồi khách hàng..." rows="1"></textarea>
            </div>
            <button class="admin-send-btn" id="adminSendBtn" onclick="adminSend()">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
            </button>
        </div>

        @else
        <!-- No conversation selected -->
        <div class="no-conv">
            <div class="no-conv-icon">💬</div>
            <h3>Chọn cuộc trò chuyện</h3>
            <p>Chọn một khách hàng từ danh sách bên trái để xem và phản hồi tin nhắn</p>
        </div>
        @endisset

    </div>
</div>

@endsection

@section('scripts')
@isset($user)
<script>
const CSRF    = document.querySelector('meta[name="csrf-token"]').content;
const UID     = {{ $user->id }};
const UAVATAR = @json(!empty($user->avatar) ? asset('storage/' . $user->avatar) : null);
const UINIT   = '{{ strtoupper(substr($user->name, 0, 1)) }}';
let lastId    = {{ isset($messages) && $messages->isNotEmpty() ? $messages->last()->id : 0 }};

function scrollBottom(smooth = true) {
    const area = document.getElementById('msgArea');
    area.scrollTo({ top: area.scrollHeight, behavior: smooth ? 'smooth' : 'instant' });
}

function escapeHtml(str) {
    return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')
              .replace(/"/g,'&quot;').replace(/\n/g,'<br>');
}

function formatTime(str) {
    const d = new Date(str);
    return d.getHours().toString().padStart(2,'0')+':'+d.getMinutes().toString().padStart(2,'0');
}

/**
 * Tạo HTML avatar phía JS — khớp với markup Blade inline ở trên.
 */
function makeAvHtml(size, color) {
    if (UAVATAR) {
        return `<div class="av-wrap ${size} ${color}">
                    <img src="${UAVATAR}" alt="${UINIT}"
                         onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                    <div class="av-initials" style="display:none">${UINIT}</div>
                </div>`;
    }
    return `<div class="av-wrap ${size} ${color}"><div class="av-initials">${UINIT}</div></div>`;
}

function appendMsg(content, sender, time) {
    const indicator   = document.getElementById('typingRow');
    const row         = document.createElement('div');
    row.className     = 'msg-row' + (sender === 'admin' ? ' admin-msg' : '');

    const userAvHtml  = makeAvHtml('av-28', 'av-default');
    const adminAvHtml = `<div class="av-wrap av-28 av-admin-c"><div class="av-initials">AD</div></div>`;

    row.innerHTML = `
        ${sender === 'user' ? userAvHtml : ''}
        <div class="bubble-wrap">
            <div class="bubble ${sender === 'admin' ? 'from-admin' : 'from-user'}">${escapeHtml(content)}</div>
            <div class="bubble-time">${time}</div>
        </div>
        ${sender === 'admin' ? adminAvHtml : ''}
    `;
    document.getElementById('msgArea').insertBefore(row, indicator);
    scrollBottom();
}

async function adminSend() {
    const input   = document.getElementById('adminInput');
    const content = input.value.trim();
    if (!content) return;

    const btn = document.getElementById('adminSendBtn');
    btn.disabled  = true;
    input.value   = '';
    input.style.height = 'auto';

    appendMsg(content, 'admin', new Date().toLocaleTimeString('vi', { hour: '2-digit', minute: '2-digit' }));

    try {
        const res  = await fetch(`/admin/chat/${UID}/send`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ content })
        });
        const data = await res.json();
        if (data.message) lastId = Math.max(lastId, data.message.id);
    } catch(e) { console.error(e); }

    btn.disabled = false;
    input.focus();
}

function insertCanned(text) {
    const inp     = document.getElementById('adminInput');
    inp.value     = text;
    inp.focus();
    inp.style.height = 'auto';
    inp.style.height = Math.min(inp.scrollHeight, 100) + 'px';
}

// Auto-resize textarea
document.getElementById('adminInput').addEventListener('input', function() {
    this.style.height = 'auto';
    this.style.height = Math.min(this.scrollHeight, 100) + 'px';
});
document.getElementById('adminInput').addEventListener('keydown', function(e) {
    if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); adminSend(); }
});

// Polling
async function poll() {
    try {
        const res  = await fetch(`/admin/chat/${UID}/poll?last_id=${lastId}`);
        const data = await res.json();

        if (data.messages && data.messages.length > 0) {
            data.messages.forEach(msg => {
                if (msg.sender === 'user') {
                    appendMsg(msg.content, 'user', formatTime(msg.created_at));
                }
                lastId = Math.max(lastId, msg.id);
            });
        }

        // Cập nhật badge sidebar
        if (data.unread_map) {
            document.querySelectorAll('[id^="badge-"]').forEach(el => {
                const uid   = el.id.replace('badge-', '');
                const count = data.unread_map[uid] || 0;
                el.textContent   = count;
                el.style.display = count > 0 ? 'flex' : 'none';
            });
        }
    } catch(e) {}
}

setInterval(poll, 2500);

// Search filter
document.getElementById('searchUser').addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('.user-item').forEach(item => {
        item.style.display = item.dataset.name.includes(q) ? '' : 'none';
    });
});

window.addEventListener('load', () => scrollBottom(false));
</script>
@endisset
@endsection