{{-- Подвал сайта: разделы, соцсети, копирайт. Всегда тёмно-синий, как шапка —
     фирменная деталь темы "Philip Elite", не зависит от переключателя темы. --}}
<footer class="relative mt-20 bg-navy">
    <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="grid gap-10 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                <div class="mb-3 flex items-center gap-2">
                    <x-logo :size="36" />
                </div>
                <p class="max-w-xs text-sm leading-relaxed text-white/60">
                    {{ __('site.footer.tagline') }}
                </p>
            </div>

            <div>
                <h3 class="mb-4 text-sm font-bold uppercase tracking-wider text-gold">{{ __('site.footer.sections') }}</h3>
                <ul class="space-y-2 text-sm text-white/60">
                    <li><a href="{{ route('lessons.index') }}" class="transition hover:text-gold">{{ __('site.nav.lessons') }}</a></li>
                    <li><a href="{{ route('words.index') }}" class="transition hover:text-gold">{{ __('site.nav.vocabulary') }}</a></li>
                    <li><a href="{{ route('grammartopics.index') }}" class="transition hover:text-gold">{{ __('site.nav.grammar') }}</a></li>
                    <li><a href="{{ route('tests.index') }}" class="transition hover:text-gold">{{ __('site.nav.tests') }}</a></li>
                    <li><a href="{{ route('books.index') }}" class="transition hover:text-gold">{{ __('site.nav.books') }}</a></li>
                </ul>
            </div>

            <div>
                <h3 class="mb-4 text-sm font-bold uppercase tracking-wider text-gold">{{ __('site.footer.platform') }}</h3>
                <ul class="space-y-2 text-sm text-white/60">
                    @guest
                        <li><a href="{{ url('/') }}#how-it-works" class="transition hover:text-gold">{{ __('site.header.how_it_works') }}</a></li>
                        <li><a href="{{ route('billing.plans') }}" class="transition hover:text-gold">{{ __('site.billing.nav') }}</a></li>
                        <li><a href="{{ route('register') }}" class="transition hover:text-gold">{{ __('site.footer.registration') }}</a></li>
                        <li><a href="{{ route('login') }}" class="transition hover:text-gold">{{ __('site.footer.login') }}</a></li>
                    @else
                        <li><a href="{{ route('user.dashboard') }}" class="transition hover:text-gold">{{ __('site.header.dashboard') }}</a></li>
                        <li><a href="{{ route('achievements.index') }}" class="transition hover:text-gold">{{ __('site.nav.achievements') }}</a></li>
                        <li><a href="{{ route('progress.map') }}" class="transition hover:text-gold">Карта прогресса</a></li>
                        <li><a href="{{ route('profiles.index') }}" class="transition hover:text-gold">{{ __('site.header.profile') }}</a></li>
                    @endguest
                </ul>
            </div>

            <div>
                <h3 class="mb-4 text-sm font-bold uppercase tracking-wider text-gold">{{ __('site.footer.social') }}</h3>
                <div class="flex gap-2">
                    @php
                        // Иконки — не текст: соцсети узнаются по форме значка,
                        // а не по первой букве названия ("T" ничего не говорит
                        // про Telegram). currentColor — та же заливка/обводка
                        // подхватывает и обычный цвет, и hover-инверсию ниже.
                        $socialIcons = [
                            'Telegram' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M20.665 3.717 2.935 10.65c-1.21.484-1.203 1.157-.219 1.457l4.53 1.414 10.5-6.63c.494-.303.946-.14.574.192l-8.5 7.676-.324 4.607c.47 0 .674-.215.935-.47l2.244-2.18 4.66 3.44c.858.474 1.475.23 1.69-.795l3.06-14.43c.313-1.26-.472-1.83-1.42-1.415Z"/></svg>',
                            'Instagram' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>',
                            'YouTube' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.25 29 29 0 0 0-.46-5.33z"/><polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02" fill="currentColor" stroke="none"/></svg>',
                        ];
                    @endphp
                    @foreach ($socialIcons as $network => $icon)
                        <a
                            href="#"
                            class="flex h-10 w-10 items-center justify-center rounded-full border border-white/15 text-white/60 transition hover:-translate-y-0.5 hover:border-gold hover:bg-gold hover:text-navy"
                            aria-label="{{ $network }}"
                        >
                            {!! $icon !!}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="mt-14 flex flex-col items-center justify-between gap-4 border-t border-white/10 pt-6 text-sm text-white/40 sm:flex-row">
            <p>&copy; {{ date('Y') }} Philip Education. {{ __('site.footer.rights') }}</p>
            <p>{{ __('site.footer.subtitle') }}</p>
        </div>

        {{-- Обязательное условие бесплатной лицензии Flaticon на иконку
             маскота (public/images/panda.png) — атрибуция должна быть
             видна на сайте, где иконка используется. --}}
        <p class="mt-3 text-center text-xs text-white/25 sm:text-left">Icon by Icongeek26 from Flaticon</p>
    </div>
</footer>
