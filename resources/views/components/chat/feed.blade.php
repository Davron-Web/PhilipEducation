{{--
    Лента общего чата + поле ввода — вынесена из public/chat/index.blade.php,
    чтобы не дублировать разметку между standalone-страницей (/chat) и
    выезжающей панелью (x-chat-drawer). Вся Alpine-логика — в
    public/assets/js/chat.js (chatFeed()), тут только разметка.

    Props:
      initialMessages, oldestId, hasMoreHistory, isBanned — серверные
      данные для НЕленивого рендера (standalone-страница, уже знает всё
      на момент рендера Blade).
      lazy — true для панели: initialMessages игнорируется, chatFeed()
      сам подтянет данные через fetch('/chat/messages/initial') при
      первом открытии (см. chat.js).
--}}
@props([
    'initialMessages' => [],
    'oldestId' => null,
    'hasMoreHistory' => false,
    'isBanned' => false,
    'lazy' => false,
])

<div
    x-data='chatFeed(@json($initialMessages), {{ $oldestId ?? "null" }}, {{ $hasMoreHistory ? "true" : "false" }}, {{ $isBanned ? "true" : "false" }}, {{ $lazy ? "true" : "false" }})'
    x-init="init()"
    {{ $attributes->merge(['class' => 'flex min-h-0 flex-1 flex-col overflow-hidden']) }}
>
    <div
        x-ref="feed"
        @scroll.debounce.300ms="onScroll()"
        class="min-h-0 flex-1 overflow-y-auto px-4 py-4 sm:px-6"
    >
        <template x-if="lazy && !bootstrapped">
            <div class="flex h-full flex-col items-center justify-center gap-2 text-center">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" class="animate-spin text-ink/30"><path d="M21 12a9 9 0 1 1-9-9" /></svg>
                <p class="text-xs text-ink/40">Загрузка чата…</p>
            </div>
        </template>

        <template x-if="(!lazy || bootstrapped)">
            <div>
                <template x-if="hasMoreHistory">
                    <div class="pb-4 text-center">
                        <button
                            type="button"
                            @click="loadMore()"
                            :disabled="loadingMore"
                            class="text-xs font-bold uppercase tracking-wider text-brand hover:text-brand/80 disabled:opacity-50"
                        >
                            <span x-show="!loadingMore">Показать ещё</span>
                            <span x-show="loadingMore">Загрузка…</span>
                        </button>
                    </div>
                </template>

                <template x-if="messages.length === 0">
                    <div class="flex h-full flex-col items-center justify-center gap-3 py-10 text-center">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" class="text-ink/25"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" /></svg>
                        <p class="text-sm text-ink/50">Пока никто ничего не написал.<br>Будь первым.</p>
                    </div>
                </template>

                <template x-for="item in feedItems" :key="item.key">
                    <div>
                        <template x-if="item.type === 'divider'">
                            <div class="my-4 flex items-center justify-center">
                                <span class="rounded-full bg-surface2 px-3 py-1 text-xs font-medium text-ink/50" x-text="item.label"></span>
                            </div>
                        </template>

                        <template x-if="item.type === 'message'">
                            <div
                                class="group/row flex items-end gap-2"
                                :class="[
                                    item.data.is_own ? 'flex-row-reverse' : '',
                                    item.showHeader ? 'mt-4 first:mt-0' : 'mt-0.5',
                                ]"
                            >
                                <template x-if="!item.data.is_own">
                                    <div class="h-8 w-8 shrink-0">
                                        <template x-if="item.showHeader">
                                            <template x-if="item.data.user.avatar_url">
                                                <img :src="item.data.user.avatar_url" alt="" class="h-8 w-8 rounded-full object-cover">
                                            </template>
                                        </template>
                                        <template x-if="item.showHeader && !item.data.user.avatar_url">
                                            <span
                                                class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-brand to-sky text-xs font-extrabold text-white"
                                                x-text="item.data.user.initial"
                                            ></span>
                                        </template>
                                    </div>
                                </template>

                                <div class="relative flex max-w-[75%] items-end gap-1" :class="item.data.is_own ? 'flex-row-reverse' : ''">
                                    <div>
                                        <template x-if="item.showHeader && !item.data.is_own">
                                            <div class="mb-1 flex items-baseline gap-2 px-1">
                                                <span class="text-sm font-medium text-ink" x-text="item.data.user.name"></span>
                                                <span class="text-xs text-ink/40" x-text="item.data.created_at_human"></span>
                                            </div>
                                        </template>
                                        <template x-if="item.showHeader && item.data.is_own">
                                            <div class="mb-1 px-1 text-right">
                                                <span class="text-xs text-ink/40" x-text="item.data.created_at_human"></span>
                                            </div>
                                        </template>

                                        <div
                                            class="inline-block rounded-2xl px-3.5 py-2 text-sm"
                                            :class="item.data.is_own
                                                ? 'rounded-br-md bg-brand text-white'
                                                : 'rounded-bl-md bg-surface2 text-ink'"
                                            style="white-space: pre-wrap; overflow-wrap: anywhere;"
                                            x-text="item.data.body"
                                        ></div>
                                    </div>

                                    <div
                                        class="relative shrink-0 opacity-0 transition-opacity group-hover/row:opacity-100 max-sm:opacity-100"
                                        @click.outside="if (openMenuId === item.data.id) openMenuId = null"
                                    >
                                        <button
                                            type="button"
                                            @click="toggleMenu(item.data.id)"
                                            class="flex h-6 w-6 items-center justify-center rounded-full text-ink/35 hover:bg-surface2 hover:text-ink/70"
                                            aria-label="Действия с сообщением"
                                        >
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><circle cx="5" cy="12" r="2" /><circle cx="12" cy="12" r="2" /><circle cx="19" cy="12" r="2" /></svg>
                                        </button>

                                        <div
                                            x-show="openMenuId === item.data.id"
                                            class="absolute top-7 z-10 w-40 overflow-hidden rounded-xl border border-line bg-armor2 py-1 shadow-softLg"
                                            :class="item.data.is_own ? 'right-0' : 'left-0'"
                                        >
                                            <template x-if="item.data.is_own">
                                                <button
                                                    type="button"
                                                    @click="deleteMessage(item.data.id)"
                                                    class="block w-full px-3 py-2 text-left text-sm text-red-500 hover:bg-surface2"
                                                >Удалить</button>
                                            </template>
                                            <template x-if="!item.data.is_own">
                                                <button
                                                    type="button"
                                                    @click="report(item.data.id)"
                                                    class="block w-full px-3 py-2 text-left text-sm text-ink hover:bg-surface2"
                                                >Пожаловаться</button>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>
            </div>
        </template>
    </div>

    <template x-if="isBanned">
        <div class="border-t border-line px-4 py-4 text-center text-sm text-ink/50 sm:px-6">
            Вы временно не можете писать в общий чат.
        </div>
    </template>
    <template x-if="!isBanned">
        <form @submit.prevent="send()" class="border-t border-line p-3 sm:p-4">
            <div class="flex items-end gap-2">
                <div class="relative flex-1">
                    <textarea
                        x-ref="composer"
                        x-model="draft"
                        @input="autoResize($el)"
                        @keydown="onComposerKeydown($event)"
                        maxlength="500"
                        rows="1"
                        placeholder="Написать сообщение…"
                        class="max-h-32 w-full resize-none rounded-2xl border border-line bg-armor px-3.5 py-2.5 pr-12 text-sm text-ink focus:border-brand/50 focus:outline-none"
                    ></textarea>
                    <span class="pointer-events-none absolute bottom-2 right-3 text-[11px] text-ink/35" x-text="draft.length + '/500'"></span>
                </div>

                <button
                    type="submit"
                    :disabled="!draft.trim() || sending"
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-brand text-white transition hover:bg-brand/90 disabled:cursor-not-allowed disabled:opacity-40"
                    aria-label="Отправить"
                >
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 2 11 13" /><path d="M22 2 15 22l-4-9-9-4 20-7Z" /></svg>
                </button>
            </div>
            <p class="mt-1.5 px-1 text-xs text-red-500" x-show="error" x-text="error"></p>
        </form>
    </template>
</div>
