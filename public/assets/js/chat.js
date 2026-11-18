/**
 * Alpine-компонент общего чата. Поллинг раз в 5 секунд — простой вариант
 * на старте; WebSockets (Laravel Reverb) — отдельная задача потом.
 *
 * CSRF и заголовки — тем же приёмом, что и в phil-widget.blade.php:
 * токен из <meta name="csrf-token">, обычный fetch, без axios.
 */
var RU_MONTHS = ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'];

function isSameDay(a, b) {
    return a.getFullYear() === b.getFullYear() && a.getMonth() === b.getMonth() && a.getDate() === b.getDate();
}

function formatDateLabel(date) {
    var now = new Date();
    if (isSameDay(date, now)) {
        return 'Сегодня';
    }
    var yesterday = new Date(now);
    yesterday.setDate(yesterday.getDate() - 1);
    if (isSameDay(date, yesterday)) {
        return 'Вчера';
    }
    var label = date.getDate() + ' ' + RU_MONTHS[date.getMonth()];
    if (date.getFullYear() !== now.getFullYear()) {
        label += ' ' + date.getFullYear();
    }
    return label;
}

/**
 * lazy — true для выезжающей панели чата (x-chat-drawer): та рендерится
 * на КАЖДОЙ странице сайта, поэтому не может получить historyю/isBanned
 * через обычный серверный рендер Blade-страницы (так делает только
 * standalone-страница /chat, у неё lazy=false и initialMessages уже
 * заполнены). При lazy=true init() сам подтягивает бутстрап через fetch()
 * при первом открытии панели — до того момента компонент существует в
 * DOM, но ничего не показывает (isBanned=false, messages=[] — пустое
 * состояние, форма отправки скрыта до готовности, см. bootstrapped).
 */
function chatFeed(initialMessages, oldestId, hasMoreHistory, isBanned, lazy) {
    return {
        messages: initialMessages || [],
        oldestId: oldestId,
        hasMoreHistory: hasMoreHistory,
        isBanned: !!isBanned,
        lazy: !!lazy,
        bootstrapped: !lazy,
        bootstrapping: false,
        draft: '',
        error: '',
        sending: false,
        loadingMore: false,
        openMenuId: null,

        init() {
            if (this.lazy && !this.bootstrapped) {
                // Панель может открыться раньше, чем это отработает —
                // open() в x-chat-drawer сам подождёт bootstrapped.
                this.bootstrap();
                return;
            }
            this.scrollToBottom();
            setInterval(() => this.poll(), 5000);
        },

        async bootstrap() {
            if (this.bootstrapping || this.bootstrapped) {
                return;
            }
            this.bootstrapping = true;

            try {
                var res = await fetch('/chat/messages/initial', {
                    headers: { Accept: 'application/json' },
                });
                if (!res.ok) {
                    this.error = 'Не удалось загрузить чат.';
                    return;
                }
                var data = await res.json();
                this.messages = data.initialMessages || [];
                this.oldestId = data.oldestId ?? null;
                this.hasMoreHistory = !!data.hasMoreHistory;
                this.isBanned = !!data.isBanned;
            } catch (e) {
                this.error = 'Не удалось загрузить чат — проверьте соединение.';
            } finally {
                this.bootstrapping = false;
                this.bootstrapped = true;
                this.scrollToBottom();
                setInterval(() => this.poll(), 5000);
            }
        },

        // Сообщения одного автора подряд в пределах 5 минут — одна группа
        // (аватар/имя только у первого); переход на новый день — плашка
        // с датой. Ничего не мутирует messages, только вычисляет разметку
        // для рендера поверх тех же самых данных.
        get feedItems() {
            var items = [];
            var lastDateKey = null;
            var prevMessage = null;

            for (var i = 0; i < this.messages.length; i++) {
                var message = this.messages[i];
                var date = new Date(message.created_at);
                var dateKey = date.toDateString();

                if (dateKey !== lastDateKey) {
                    items.push({ type: 'divider', key: 'd-' + dateKey, label: formatDateLabel(date) });
                    lastDateKey = dateKey;
                    prevMessage = null;
                }

                var sameAuthor = prevMessage && prevMessage.user.id === message.user.id;
                var within5min = prevMessage && (date - new Date(prevMessage.created_at)) < 5 * 60 * 1000;
                var grouped = !!(sameAuthor && within5min);

                items.push({
                    type: 'message',
                    key: 'm-' + message.id,
                    data: message,
                    showHeader: !grouped,
                });
                prevMessage = message;
            }

            return items;
        },

        csrfToken() {
            var meta = document.querySelector('meta[name="csrf-token"]');
            return meta ? meta.content : '';
        },

        scrollToBottom() {
            this.$nextTick(() => {
                this.$refs.feed.scrollTop = this.$refs.feed.scrollHeight;
            });
        },

        isNearBottom() {
            var el = this.$refs.feed;
            return el.scrollHeight - el.scrollTop - el.clientHeight < 80;
        },

        autoResize(el) {
            el.style.height = 'auto';
            el.style.height = Math.min(el.scrollHeight, 128) + 'px';
        },

        onComposerKeydown(event) {
            if (event.key === 'Enter' && !event.shiftKey) {
                event.preventDefault();
                this.send();
            }
        },

        toggleMenu(id) {
            this.openMenuId = this.openMenuId === id ? null : id;
        },

        async send() {
            var body = this.draft.trim();
            if (!body || this.sending) {
                return;
            }

            this.sending = true;
            this.error = '';

            try {
                var res = await fetch('/chat/messages', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken(),
                    },
                    body: JSON.stringify({ body: body }),
                });

                if (res.status === 422) {
                    var errors = (await res.json()).errors || {};
                    this.error = Object.values(errors).flat()[0] || 'Не удалось отправить сообщение.';
                    return;
                }
                if (res.status === 429) {
                    this.error = 'Слишком много сообщений подряд — подождите немного.';
                    return;
                }
                if (res.status === 403) {
                    this.error = 'Вы не можете писать в чат.';
                    return;
                }
                if (!res.ok) {
                    this.error = 'Не удалось отправить сообщение.';
                    return;
                }

                var data = await res.json();
                this.messages.push(data.message);
                this.draft = '';
                this.$nextTick(() => {
                    if (this.$refs.composer) {
                        this.$refs.composer.style.height = 'auto';
                    }
                });
                this.scrollToBottom();
            } catch (e) {
                this.error = 'Не удалось отправить сообщение — проверьте соединение.';
            } finally {
                this.sending = false;
            }
        },

        async poll() {
            var lastId = this.messages.length ? this.messages[this.messages.length - 1].id : 0;

            try {
                var res = await fetch('/chat/messages?after_id=' + lastId, {
                    headers: { Accept: 'application/json' },
                });
                if (!res.ok) {
                    return;
                }
                var data = await res.json();
                if (data.messages && data.messages.length) {
                    var wasNearBottom = this.isNearBottom();
                    this.messages.push.apply(this.messages, data.messages);
                    if (wasNearBottom) {
                        this.scrollToBottom();
                    }
                }
            } catch (e) {
                // Сбой одного цикла опроса не критичен — следующий через 5с сам подтянет пропущенное.
            }
        },

        async loadMore() {
            if (this.loadingMore || !this.hasMoreHistory || !this.oldestId) {
                return;
            }

            this.loadingMore = true;

            try {
                var res = await fetch('/chat/messages/before?before_id=' + this.oldestId, {
                    headers: { Accept: 'application/json' },
                });
                if (!res.ok) {
                    return;
                }
                var data = await res.json();
                var el = this.$refs.feed;
                var prevHeight = el.scrollHeight;

                this.messages.unshift.apply(this.messages, data.messages);
                this.hasMoreHistory = !!data.hasMore;
                if (data.messages.length) {
                    this.oldestId = data.messages[0].id;
                }

                this.$nextTick(() => {
                    el.scrollTop = el.scrollHeight - prevHeight;
                });
            } finally {
                this.loadingMore = false;
            }
        },

        onScroll() {
            if (this.$refs.feed.scrollTop < 40) {
                this.loadMore();
            }
        },

        async report(messageId) {
            this.openMenuId = null;

            try {
                await fetch('/chat/messages/' + messageId + '/report', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken(),
                    },
                    body: JSON.stringify({}),
                });
                alert('Жалоба отправлена администратору.');
            } catch (e) {
                alert('Не удалось отправить жалобу.');
            }
        },

        async deleteMessage(messageId) {
            this.openMenuId = null;

            if (!confirm('Удалить это сообщение?')) {
                return;
            }

            try {
                var res = await fetch('/chat/messages/' + messageId, {
                    method: 'DELETE',
                    headers: {
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken(),
                    },
                });
                if (res.ok) {
                    this.messages = this.messages.filter(function (m) {
                        return m.id !== messageId;
                    });
                } else {
                    alert('Не удалось удалить сообщение.');
                }
            } catch (e) {
                alert('Не удалось удалить сообщение.');
            }
        },
    };
}
