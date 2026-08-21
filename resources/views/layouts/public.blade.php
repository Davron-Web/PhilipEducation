@extends('layouts.app')

{{-- SEO: заголовок страницы --}}
@section('title', isset($title) ? $title . ' — ' . config('app.name') : config('app.name'))

{{-- Дополнительные мета-теги (OG, description, keywords) --}}
@push('meta')
    @if(!empty($meta_description))
        <meta name="description" content="{{ $meta_description }}">
    @endif
    @if(!empty($og_image))
        <meta property="og:image" content="{{ $og_image }}">
    @endif
@endpush

{{-- Основной контент --}}
@section('content')
    <div class="public-page">

        {{-- Хлебные крошки --}}
        @hasSection('breadcrumbs')
            <nav class="breadcrumbs" aria-label="breadcrumb">
                <div class="container">
                    @yield('breadcrumbs')
                </div>
            </nav>
        @endif

        <div class="container">
            <div class="public-layout">

                {{-- Основной блок --}}
                <section class="public-layout__content">
                    @yield('page_content')
                </section>

                {{-- Сайдбар (опциональный) --}}
                @hasSection('sidebar')
                    <aside class="public-layout__sidebar">
                        @yield('sidebar')
                    </aside>
                @endif

            </div>
        </div>
    </div>
@endsection

{{-- Дополнительные скрипты конкретной страницы --}}
@push('scripts')
    @yield('page_scripts')
@endpush
