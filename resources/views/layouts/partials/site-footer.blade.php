{{-- Подвал сайта: разделы, соцсети, копирайт. --}}
<footer class="relative mt-20 overflow-hidden bg-gradient-to-br from-ink via-brand to-sky text-white">
    <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
        <div class="grid gap-10 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                <div class="mb-3 flex items-center gap-2">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-white/15">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M6 6l2 3" /><path d="M18 6l-2 3" />
                            <ellipse cx="12" cy="13" rx="7" ry="8" />
                            <circle cx="9" cy="12" r="1.4" fill="currentColor" stroke="none" />
                            <circle cx="15" cy="12" r="1.4" fill="currentColor" stroke="none" />
                        </svg>
                    </span>
                    <span class="text-lg font-extrabold">Philip Education</span>
                </div>
                <p class="max-w-xs text-sm leading-relaxed text-white/70">
                    Изучайте английский язык через уроки, упражнения и живую практику слов — от A1 до C1.
                </p>
            </div>

            <div>
                <h3 class="mb-3 text-sm font-bold uppercase tracking-wider text-white/50">Разделы</h3>
                <ul class="space-y-2 text-sm text-white/80">
                    <li><a href="{{ route('lessons.index') }}" class="transition hover:text-sun">Уроки</a></li>
                    <li><a href="{{ route('words.index') }}" class="transition hover:text-sun">Словарь</a></li>
                    <li><a href="{{ route('grammartopics.index') }}" class="transition hover:text-sun">Грамматика</a></li>
                    <li><a href="{{ route('tests.index') }}" class="transition hover:text-sun">Тесты</a></li>
                    <li><a href="{{ route('books.index') }}" class="transition hover:text-sun">Книги</a></li>
                </ul>
            </div>

            <div>
                <h3 class="mb-3 text-sm font-bold uppercase tracking-wider text-white/50">Платформа</h3>
                <ul class="space-y-2 text-sm text-white/80">
                    @guest
                        <li><a href="{{ url('/') }}#how-it-works" class="transition hover:text-sun">Как это работает</a></li>
                        <li><a href="{{ route('register') }}" class="transition hover:text-sun">Регистрация</a></li>
                        <li><a href="{{ route('login') }}" class="transition hover:text-sun">Вход</a></li>
                    @else
                        <li><a href="{{ route('user.dashboard') }}" class="transition hover:text-sun">Личный кабинет</a></li>
                        <li><a href="{{ route('achievements.index') }}" class="transition hover:text-sun">Достижения</a></li>
                        <li><a href="{{ route('profiles.index') }}" class="transition hover:text-sun">Профиль</a></li>
                    @endguest
                </ul>
            </div>

            <div>
                <h3 class="mb-3 text-sm font-bold uppercase tracking-wider text-white/50">Мы в соцсетях</h3>
                <div class="flex gap-2">
                    @foreach (['Telegram', 'Instagram', 'YouTube'] as $network)
                        <a
                            href="#"
                            class="flex h-10 w-10 items-center justify-center rounded-full bg-white/10 text-white/80 transition hover:-translate-y-0.5 hover:bg-sun hover:text-ink"
                            aria-label="{{ $network }}"
                        >
                            <span class="text-xs font-bold">{{ mb_substr($network, 0, 1) }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="mt-12 flex flex-col items-center justify-between gap-4 border-t border-white/15 pt-6 text-sm text-white/60 sm:flex-row">
            <p>&copy; {{ date('Y') }} Philip Education. Все права защищены.</p>
            <p>Сделано с 💙 для тех, кто учит английский</p>
        </div>
    </div>
</footer>
