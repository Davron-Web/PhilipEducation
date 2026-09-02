{{-- Подвал сайта: разделы, соцсети, копирайт. --}}
<footer class="relative mt-20 border-t border-line bg-armor2">
    <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
        <div class="grid gap-10 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                <div class="mb-3 flex items-center gap-2">
                    <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-brand shadow-soft">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M6 6l2 3" /><path d="M18 6l-2 3" />
                            <ellipse cx="12" cy="13" rx="7" ry="8" />
                            <circle cx="9" cy="12" r="1.4" fill="#fff" stroke="none" />
                            <circle cx="15" cy="12" r="1.4" fill="#fff" stroke="none" />
                        </svg>
                    </span>
                    <span class="text-lg font-extrabold text-ink">Philip Education</span>
                </div>
                <p class="max-w-xs text-sm leading-relaxed text-ink/60">
                    Изучайте английский язык через уроки, упражнения и живую практику слов — от A1 до C1.
                </p>
            </div>

            <div>
                <h3 class="mb-3 text-sm font-bold uppercase tracking-wider text-ink/40">Разделы</h3>
                <ul class="space-y-2 text-sm text-ink/70">
                    <li><a href="{{ route('lessons.index') }}" class="transition hover:text-brand">Уроки</a></li>
                    <li><a href="{{ route('words.index') }}" class="transition hover:text-brand">Словарь</a></li>
                    <li><a href="{{ route('grammartopics.index') }}" class="transition hover:text-brand">Грамматика</a></li>
                    <li><a href="{{ route('tests.index') }}" class="transition hover:text-brand">Тесты</a></li>
                    <li><a href="{{ route('books.index') }}" class="transition hover:text-brand">Книги</a></li>
                </ul>
            </div>

            <div>
                <h3 class="mb-3 text-sm font-bold uppercase tracking-wider text-ink/40">Платформа</h3>
                <ul class="space-y-2 text-sm text-ink/70">
                    @guest
                        <li><a href="{{ url('/') }}#how-it-works" class="transition hover:text-brand">Как это работает</a></li>
                        <li><a href="{{ route('register') }}" class="transition hover:text-brand">Регистрация</a></li>
                        <li><a href="{{ route('login') }}" class="transition hover:text-brand">Вход</a></li>
                    @else
                        <li><a href="{{ route('user.dashboard') }}" class="transition hover:text-brand">Личный кабинет</a></li>
                        <li><a href="{{ route('achievements.index') }}" class="transition hover:text-brand">Достижения</a></li>
                        <li><a href="{{ route('profiles.index') }}" class="transition hover:text-brand">Профиль</a></li>
                    @endguest
                </ul>
            </div>

            <div>
                <h3 class="mb-3 text-sm font-bold uppercase tracking-wider text-ink/40">Мы в соцсетях</h3>
                <div class="flex gap-2">
                    @foreach (['Telegram', 'Instagram', 'YouTube'] as $network)
                        <a
                            href="#"
                            class="flex h-10 w-10 items-center justify-center rounded-full bg-surface2 text-ink/60 transition hover:-translate-y-0.5 hover:bg-brand hover:text-white"
                            aria-label="{{ $network }}"
                        >
                            <span class="text-xs font-bold">{{ mb_substr($network, 0, 1) }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="mt-12 flex flex-col items-center justify-between gap-4 border-t border-line pt-6 text-sm text-ink/50 sm:flex-row">
            <p>&copy; {{ date('Y') }} Philip Education. Все права защищены.</p>
            <p>Платформа для изучения английского языка</p>
        </div>
    </div>
</footer>
