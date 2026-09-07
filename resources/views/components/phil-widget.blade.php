@php
    $philAuthed = auth()->check();
@endphp

<div id="phil-widget" data-authed="{{ $philAuthed ? '1' : '0' }}" data-endpoint="{{ route('assistant.chat') }}" data-login-url="{{ route('login') }}">
    <button type="button" id="phil-toggle" aria-label="Открыть Phil — AI-помощника">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <rect x="5" y="8" width="14" height="12" rx="3"/>
            <circle cx="9.5" cy="14" r="1.4" fill="currentColor" stroke="none"/>
            <circle cx="14.5" cy="14" r="1.4" fill="currentColor" stroke="none"/>
            <path d="M9 17.5h6"/>
            <path d="M12 8V5"/>
            <circle cx="12" cy="3.5" r="1.3" fill="currentColor" stroke="none"/>
            <path d="M2.5 12.5v3"/>
            <path d="M21.5 12.5v3"/>
        </svg>
        <span id="phil-badge">Phil</span>
    </button>

    <div id="phil-panel" hidden>
        <div id="phil-panel-header">
            <div id="phil-avatar">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="5" y="8" width="14" height="12" rx="3"/>
                    <circle cx="9.5" cy="14" r="1.4" fill="currentColor" stroke="none"/>
                    <circle cx="14.5" cy="14" r="1.4" fill="currentColor" stroke="none"/>
                    <path d="M9 17.5h6"/>
                    <path d="M12 8V5"/>
                    <circle cx="12" cy="3.5" r="1.3" fill="currentColor" stroke="none"/>
                </svg>
            </div>
            <div id="phil-title">
                <strong>Phil</strong>
                <span>AI-помощник по английскому</span>
            </div>
            <button type="button" id="phil-close" aria-label="Закрыть">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>

        @if($philAuthed)
            <div id="phil-context-pill" hidden>
                <span id="phil-context-label"></span>
                <button type="button" id="phil-context-clear" aria-label="Забыть контекст">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
        @endif

        @if($philAuthed)
            <div id="phil-messages"></div>
            <div id="phil-inputrow">
                <input type="text" id="phil-input" placeholder="Спросите Phil про английский…" autocomplete="off">
                <button type="button" id="phil-send" aria-label="Отправить">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                </button>
            </div>
        @else
            <div id="phil-guest">
                <p>Войдите в аккаунт, чтобы пообщаться с Phil.</p>
                <a href="{{ route('login') }}">Войти</a>
            </div>
        @endif
    </div>
</div>

<style>
    #phil-widget, #phil-widget * { box-sizing: border-box; }
    #phil-widget { position: fixed; right: 20px; bottom: 20px; z-index: 99990; font-family: 'Inter', system-ui, sans-serif; }

    #phil-toggle {
        display: flex; align-items: center; gap: 8px;
        height: 56px; padding: 0 18px 0 14px; border: none; border-radius: 999px; cursor: pointer;
        background: #1A1A2E;
        color: #C9A961; box-shadow: 0 10px 30px rgba(26, 26, 46, .45);
        transition: transform .15s ease, box-shadow .15s ease;
    }
    #phil-toggle:hover { transform: translateY(-2px); box-shadow: 0 14px 34px rgba(26, 26, 46, .55); }
    #phil-toggle svg { width: 26px; height: 26px; flex-shrink: 0; }
    #phil-badge { font-weight: 700; font-size: 14.5px; letter-spacing: .01em; color: #fff; }

    #phil-widget.open #phil-toggle { display: none; }

    #phil-panel {
        display: none;
        position: absolute; right: 0; bottom: 70px; width: 340px; max-width: calc(100vw - 40px);
        height: 480px; max-height: calc(100vh - 110px);
        background: #FFFFFF; color: #2C3E50; border-radius: 10px; overflow: hidden;
        flex-direction: column;
        box-shadow: 0 20px 60px rgba(26, 26, 46, .25);
        border: 1px solid #E6E2D8;
    }
    html.dark #phil-panel { background: #16213E; color: #E8E6E0; border-color: #2A3555; box-shadow: 0 20px 60px rgba(0, 0, 0, .4); }
    #phil-widget.open #phil-panel { display: flex; }

    #phil-panel-header {
        display: flex; align-items: center; gap: 10px; padding: 14px 14px;
        background: #1A1A2E;
        flex-shrink: 0;
    }
    #phil-avatar {
        width: 36px; height: 36px; border-radius: 6px; background: rgba(201,169,97,.2);
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    #phil-avatar svg { width: 22px; height: 22px; color: #C9A961; }
    #phil-title { flex: 1; min-width: 0; display: flex; flex-direction: column; line-height: 1.25; }
    #phil-title strong { font-size: 15px; color: #C9A961; font-family: 'Playfair Display', serif; }
    #phil-title span { font-size: 12px; color: rgba(255,255,255,.6); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    #phil-close { background: none; border: none; color: rgba(255,255,255,.7); cursor: pointer; padding: 4px; border-radius: 4px; flex-shrink: 0; }
    #phil-close:hover { background: rgba(255,255,255,.1); color: #C9A961; }
    #phil-close svg { width: 18px; height: 18px; display: block; }

    #phil-context-pill {
        display: flex; align-items: center; gap: 6px; margin: 10px 14px 0;
        padding: 6px 6px 6px 12px; border-radius: 999px; flex-shrink: 0;
        background: rgba(201, 169, 97, .12); border: 1px solid rgba(201, 169, 97, .3);
        color: #8A6D1F; font-size: 12px; font-weight: 600;
    }
    html.dark #phil-context-pill { background: rgba(212, 175, 55, .15); border-color: rgba(212, 175, 55, .3); color: #D4AF37; }
    #phil-context-label { flex: 1; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    #phil-context-clear { background: none; border: none; color: inherit; cursor: pointer; padding: 3px; border-radius: 999px; display: flex; flex-shrink: 0; opacity: .7; }
    #phil-context-clear:hover { opacity: 1; background: rgba(201, 169, 97, .2); }
    #phil-context-clear svg { width: 12px; height: 12px; display: block; }

    #phil-messages { flex: 1; overflow-y: auto; padding: 14px; display: flex; flex-direction: column; gap: 10px; }
    .phil-msg { padding: 10px 13px; border-radius: 6px; font-size: 13.5px; line-height: 1.45; max-width: 88%; white-space: pre-wrap; word-break: break-word; }
    .phil-msg.user { align-self: flex-end; background: #1A1A2E; color: #fff; border-bottom-right-radius: 2px; }
    .phil-msg.bot { align-self: flex-start; background: #F2EFE9; color: #2C3E50; border-bottom-left-radius: 2px; }
    html.dark .phil-msg.bot { background: #1E2A47; color: #E8E6E0; }
    .phil-msg.bot ul { margin: 4px 0; padding-left: 18px; }
    .phil-msg.bot code { background: rgba(201,169,97,.18); padding: 1px 5px; border-radius: 4px; font-size: 12.5px; }
    .phil-msg.bot br:last-child { display: none; }
    .phil-msg.hint { align-self: center; color: #94897A; font-size: 12px; text-align: center; background: none; }

    #phil-inputrow { display: flex; gap: 8px; padding: 12px; border-top: 1px solid #E6E2D8; flex-shrink: 0; }
    html.dark #phil-inputrow { border-top-color: #2A3555; }
    #phil-input {
        flex: 1; min-width: 0; background: #F2EFE9; border: 1px solid #E6E2D8; border-radius: 6px;
        color: #2C3E50; padding: 10px 12px; font-size: 13.5px; outline: none;
    }
    html.dark #phil-input { background: #1E2A47; border-color: #2A3555; color: #E8E6E0; }
    #phil-input:focus { border-color: #C9A961; }
    #phil-input::placeholder { color: #9C9585; }
    #phil-send {
        width: 40px; height: 40px; flex-shrink: 0; border: none; border-radius: 6px; cursor: pointer;
        background: #1A1A2E; color: #C9A961; display: flex; align-items: center; justify-content: center;
    }
    #phil-send:hover { background: #16213E; }
    #phil-send svg { width: 18px; height: 18px; }

    #phil-guest { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 14px; padding: 20px; text-align: center; }
    #phil-guest p { margin: 0; font-size: 13.5px; color: #6B6355; }
    html.dark #phil-guest p { color: #A79E8E; }
    #phil-guest a { background: #C9A961; color: #1A1A2E; text-decoration: none; padding: 9px 20px; border-radius: 4px; font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; }
    #phil-guest a:hover { background: #D4AF37; }

    @media (max-width: 480px) {
        #phil-widget { right: 12px; bottom: 12px; }
        #phil-panel { width: calc(100vw - 24px); right: -8px; }
        #phil-badge { display: none; }
        #phil-toggle { width: 56px; padding: 0; justify-content: center; }
    }
</style>

<script>
(function () {
    var root = document.getElementById('phil-widget');
    if (!root) return;

    var toggleBtn = document.getElementById('phil-toggle');
    var closeBtn = document.getElementById('phil-close');
    var panel = document.getElementById('phil-panel');
    var isAuthed = root.dataset.authed === '1';

    // Chat history and open/closed state survive normal page navigation
    // (this app isn't a SPA) by living in sessionStorage — cleared when the
    // tab/window closes, kept while the visitor clicks around the site.
    var STORAGE_KEY = 'phil-chat-state-v1';
    function loadState() {
        try {
            var raw = sessionStorage.getItem(STORAGE_KEY);
            var parsed = raw ? JSON.parse(raw) : null;
            return parsed && typeof parsed === 'object'
                ? { open: !!parsed.open, greeted: !!parsed.greeted, messages: Array.isArray(parsed.messages) ? parsed.messages : [] }
                : { open: false, greeted: false, messages: [] };
        } catch (e) {
            return { open: false, greeted: false, messages: [] };
        }
    }
    function saveState() {
        try { sessionStorage.setItem(STORAGE_KEY, JSON.stringify(state)); } catch (e) {}
    }
    var state = loadState();

    function openPanel() {
        panel.hidden = false;
        root.classList.add('open');
        state.open = true;
        saveState();
        if (isAuthed) {
            var input = document.getElementById('phil-input');
            if (input) input.focus();
        }
    }
    function closePanel() {
        panel.hidden = true;
        root.classList.remove('open');
        state.open = false;
        saveState();
    }

    toggleBtn.addEventListener('click', openPanel);
    closeBtn.addEventListener('click', closePanel);

    // Позволяет страницам слова/выражения открыть Phil напрямую кнопкой
    // «Спросить Phil» вместо клика по плавающей кнопке.
    window.openPhilWidget = openPanel;

    if (!isAuthed) {
        if (state.open) openPanel();
        return;
    }

    var messages = document.getElementById('phil-messages');
    var input = document.getElementById('phil-input');
    var sendBtn = document.getElementById('phil-send');
    var endpoint = root.dataset.endpoint;

    // Контекст текущей страницы (слово/выражение) — страница выставляет
    // window.philContext через @push('phil-context') до подключения этого
    // скрипта (см. word/show.blade.php, expressions/show.blade.php). Учтён
    // только в СЛЕДУЮЩЕМ вопросе, история чата не трогается.
    var contextPill = document.getElementById('phil-context-pill');
    var contextLabel = document.getElementById('phil-context-label');
    var contextClear = document.getElementById('phil-context-clear');
    var activeContext = null;

    function setContext(ctx) {
        activeContext = ctx && ctx.id ? ctx : null;
        if (!contextPill) return;
        if (activeContext) {
            contextLabel.textContent = activeContext.label;
            contextPill.hidden = false;
        } else {
            contextPill.hidden = true;
        }
    }

    if (window.philContext) setContext(window.philContext);
    if (contextClear) contextClear.addEventListener('click', function () { setContext(null); });

    // `text` is always the raw string (never pre-rendered HTML) so it can be
    // safely persisted to sessionStorage and re-rendered identically on the
    // next page load.
    function addMsg(cls, text, rich, persist) {
        var d = document.createElement('div');
        d.className = 'phil-msg ' + cls;
        if (rich) { d.innerHTML = renderMarkdown(text); } else { d.textContent = text; }
        messages.appendChild(d);
        messages.scrollTop = messages.scrollHeight;
        if (persist !== false) {
            state.messages.push({ cls: cls, text: text, rich: !!rich });
            saveState();
        }
        return d;
    }

    function escapeHtml(str) {
        return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    function inlineMarkdown(str) {
        return str
            .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
            .replace(/(^|[^*])\*([^*\n]+)\*(?!\*)/g, '$1<em>$2</em>')
            .replace(/`([^`]+)`/g, '<code>$1</code>');
    }

    // Converts the small subset of markdown Gemini tends to reply with
    // (bold, italic, inline code, bullet lists) into safe HTML — input is
    // HTML-escaped first, so no raw tags from the model ever get through.
    function renderMarkdown(raw) {
        var lines = escapeHtml(raw).split('\n');
        var html = '';
        var inList = false;
        lines.forEach(function (line) {
            var trimmed = line.trim();
            var isBullet = /^[*-]\s+/.test(trimmed);
            if (isBullet) {
                if (!inList) { html += '<ul>'; inList = true; }
                html += '<li>' + inlineMarkdown(trimmed.replace(/^[*-]\s+/, '')) + '</li>';
            } else {
                if (inList) { html += '</ul>'; inList = false; }
                html += trimmed === '' ? '<br>' : inlineMarkdown(line) + '<br>';
            }
        });
        if (inList) html += '</ul>';
        return html;
    }

    // Replay any history saved from a previous page before wiring up the
    // greeting/open behaviour, so returning to the site (or clicking a link
    // while the panel is open) doesn't wipe the conversation.
    state.messages.forEach(function (m) {
        addMsg(m.cls, m.text, m.rich, false);
    });
    if (state.open) openPanel();

    function greet() {
        if (state.greeted) return;
        state.greeted = true;
        saveState();
        addMsg('hint', 'Привет! Я Phil — спроси меня что-нибудь про английский язык.');
    }
    toggleBtn.addEventListener('click', greet, { once: true });

    function csrfToken() {
        var meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.content : '';
    }

    async function send() {
        var text = input.value.trim();
        if (!text) return;
        input.value = '';
        addMsg('user', text);
        var wait = addMsg('bot', 'Печатает…', false, false);
        sendBtn.disabled = true;
        try {
            var res = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken()
                },
                body: JSON.stringify({
                    question: text,
                    context_type: activeContext ? activeContext.type : null,
                    context_id: activeContext ? activeContext.id : null,
                })
            });
            if (res.status === 429) {
                wait.textContent = 'Слишком много сообщений подряд — подождите минутку и попробуйте снова.';
                return;
            }
            var data = await res.json();
            if (data.answer) {
                wait.innerHTML = renderMarkdown(data.answer);
                state.messages.push({ cls: 'bot', text: data.answer, rich: true });
                saveState();
            } else {
                wait.textContent = 'Ошибка ' + res.status;
            }
        } catch (e) {
            wait.textContent = 'Ошибка соединения: ' + e.message;
        } finally {
            sendBtn.disabled = false;
            messages.scrollTop = messages.scrollHeight;
        }
    }

    sendBtn.addEventListener('click', send);
    input.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') send();
    });
})();
</script>
