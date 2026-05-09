/**
 * FoodShop — AI Chatbox Widget
 */

(function () {
    'use strict';

    /* =============================================
       CẤU HÌNH
    ============================================= */
    const CONFIG = {
        API_ENDPOINT: '/api/chat',
        API_HEADERS: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
        },
        BOT_NAME: 'FoodBot',
        BOT_AVATAR: '🤖',
        WELCOME_MSG: 'Xin chào! Tôi là FoodBot 🍔\nBạn cần tư vấn món ăn hay cần hỗ trợ đặt hàng?',
        QUICK_REPLIES: [
            'Menu hôm nay có gì?',
            'Khuyến mãi mới nhất',
            'Theo dõi đơn hàng',
            'Liên hệ hỗ trợ',
        ],
        ERROR_MSG: 'Xin lỗi, có lỗi xảy ra. Vui lòng thử lại sau nhé! 🙏',
    };

    /* =============================================
       KHỞI TẠO
    ============================================= */
    let chatHistory = [];
    let isOpen = false;
    let isLoading = false;

    function createWidget() {
        const html = `
        <button id="fs-chat-bubble" aria-label="Mở chat hỗ trợ">
            <svg class="icon-chat" viewBox="0 0 24 24"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/></svg>
            <svg class="icon-close" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
            <div id="fs-chat-unread">1</div>
        </button>

        <div id="fs-chat-window" role="dialog" aria-label="Chat hỗ trợ FoodShop">
            <div class="fs-chat-header">
                <div class="fs-chat-header-avatar">${CONFIG.BOT_AVATAR}</div>
                <div class="fs-chat-header-info">
                    <div class="fs-chat-header-name">${CONFIG.BOT_NAME}</div>
                    <div class="fs-chat-header-status">
                        <div class="fs-chat-status-dot"></div>
                        <span>Đang hoạt động</span>
                    </div>
                </div>
                <button class="fs-chat-header-close" id="fs-close-btn" aria-label="Đóng chat">
                    <svg viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
                </button>
            </div>

            <div class="fs-chat-messages" id="fs-messages"></div>

            <div class="fs-chat-footer">
                <textarea
                    id="fs-chat-input"
                    placeholder="Nhắn tin cho FoodBot..."
                    rows="1"
                    aria-label="Nhập tin nhắn"
                ></textarea>
                <button id="fs-send-btn" aria-label="Gửi tin nhắn">
                    <svg viewBox="0 0 24 24"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
                </button>
            </div>
            <p class="fs-chat-note">FoodShop AI · Phản hồi trong vài giây</p>
        </div>
        `;

        const wrapper = document.createElement('div');
        wrapper.innerHTML = html;
        document.body.appendChild(wrapper);
    }

    /* =============================================
       HELPERS
    ============================================= */
    function getTime() {
        return new Date().toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
    }

    function appendMessage(text, role, showQuickReplies = false) {
        const container = document.getElementById('fs-messages');
        const isUser = role === 'user';

        const row = document.createElement('div');
        row.className = `fs-msg-row ${role}`;

        const safeText = text
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/\n/g, '<br>');

        if (isUser) {
            row.innerHTML = `
                <div>
                    <div class="fs-bubble">${safeText}</div>
                    <span class="fs-msg-time">${getTime()}</span>
                </div>
            `;
        } else {
            row.innerHTML = `
                <div class="fs-msg-avatar">${CONFIG.BOT_AVATAR}</div>
                <div>
                    <div class="fs-bubble">${safeText}</div>
                    <span class="fs-msg-time">${getTime()}</span>
                    ${showQuickReplies ? renderQuickReplies() : ''}
                </div>
            `;
        }

        container.appendChild(row);
        container.scrollTop = container.scrollHeight;

        if (showQuickReplies) {
            row.querySelectorAll('.fs-quick-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    sendMessage(btn.textContent.trim());
                    row.querySelector('.fs-quick-replies')?.remove();
                });
            });
        }
    }

    function renderQuickReplies() {
        const btns = CONFIG.QUICK_REPLIES
            .map(q => `<button class="fs-quick-btn">${q}</button>`)
            .join('');
        return `<div class="fs-quick-replies">${btns}</div>`;
    }

    function showTyping() {
        removeTyping();
        const container = document.getElementById('fs-messages');
        const row = document.createElement('div');
        row.id = 'fs-typing';
        row.className = 'fs-typing-row';
        row.innerHTML = `
            <div class="fs-msg-avatar">${CONFIG.BOT_AVATAR}</div>
            <div class="fs-typing-bubble">
                <div class="fs-typing-dot"></div>
                <div class="fs-typing-dot"></div>
                <div class="fs-typing-dot"></div>
            </div>
        `;
        container.appendChild(row);
        container.scrollTop = container.scrollHeight;
    }

    function removeTyping() {
        document.getElementById('fs-typing')?.remove();
    }

    /* =============================================
       CORE — Gọi API backend
    ============================================= */
    async function callAPI(userMessage) {
        chatHistory.push({ role: 'user', content: userMessage });

        try {
            const response = await fetch(CONFIG.API_ENDPOINT, {
                method: 'POST',
                headers: CONFIG.API_HEADERS,
                body: JSON.stringify({
                    message: userMessage,
                    history: chatHistory,
                }),
            });

            if (!response.ok) throw new Error(`HTTP ${response.status}`);

            const data = await response.json();
            const botReply = data.reply ?? data.message ?? data.answer ?? CONFIG.ERROR_MSG;

            chatHistory.push({ role: 'assistant', content: botReply });
            return botReply;

        } catch (err) {
            console.error('[FoodShop Chatbox] API Error:', err);
            chatHistory.pop();
            return CONFIG.ERROR_MSG;
        }
    }

    /* =============================================
       SEND MESSAGE
    ============================================= */
    async function sendMessage(text) {
        const input = document.getElementById('fs-chat-input');
        const msg = (text ?? input?.value ?? '').trim();
        if (!msg || isLoading) return;

        if (input) { input.value = ''; input.style.height = 'auto'; }

        appendMessage(msg, 'user');

        isLoading = true;
        showTyping();

        const reply = await callAPI(msg);

        removeTyping();
        appendMessage(reply, 'bot');
        isLoading = false;
    }

    /* =============================================
       TOGGLE
    ============================================= */
    function openChat() {
        isOpen = true;
        document.getElementById('fs-chat-window').classList.add('open');
        document.getElementById('fs-chat-bubble').classList.add('open');
        document.getElementById('fs-chat-unread').classList.remove('show');
        document.getElementById('fs-chat-input')?.focus();
    }

    function closeChat() {
        isOpen = false;
        document.getElementById('fs-chat-window').classList.remove('open');
        document.getElementById('fs-chat-bubble').classList.remove('open');
    }

    function toggleChat() {
        isOpen ? closeChat() : openChat();
    }

    /* =============================================
       EVENTS
    ============================================= */
    function bindEvents() {
        document.getElementById('fs-chat-bubble').addEventListener('click', toggleChat);
        document.getElementById('fs-close-btn').addEventListener('click', closeChat);
        document.getElementById('fs-send-btn').addEventListener('click', () => sendMessage());

        document.getElementById('fs-chat-input').addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });

        document.getElementById('fs-chat-input').addEventListener('input', function () {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 96) + 'px';
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && isOpen) closeChat();
        });
    }

    /* =============================================
       INIT
    ============================================= */
    function init() {
        createWidget();
        bindEvents();

        setTimeout(() => {
            appendMessage(CONFIG.WELCOME_MSG, 'bot', true);
        }, 400);

        setTimeout(() => {
            if (!isOpen) {
                document.getElementById('fs-chat-unread').classList.add('show');
            }
        }, 2000);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();