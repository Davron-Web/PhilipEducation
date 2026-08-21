@extends('layouts.admin')

@section('title', 'Переводы слов')

@section('content')
    <x-admin.page-header title="Переводы слов">
        <x-slot:actions>
            <a href="{{ route('admin.vocabulary.wordtranslations.create') }}" class="btn btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Добавить перевод
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="card">
        <form method="GET" class="filters">
            <input type="text" name="search" class="input" placeholder="Поиск по переводу..." value="{{ request('search') }}">
            <select name="word_id" class="select">
                <option value="">Все слова</option>
                @foreach($words as $id => $word)
                    <option value="{{ $id }}" {{ request('word_id') == $id ? 'selected' : '' }}>{{ $word }}</option>
                @endforeach
            </select>
            <select name="language" class="select">
                <option value="">Все языки</option>
                @foreach($languages as $language)
                    <option value="{{ $language }}" {{ request('language') == $language ? 'selected' : '' }}>{{ strtoupper($language) }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-ghost">Фильтр</button>
            <a href="{{ route('admin.vocabulary.wordtranslations.index') }}" class="btn btn-ghost">Сброс</a>
        </form>
    </div>

    <div class="card table-card">
        <div class="table-scroll">
            <table class="tbl">
                <thead>
                <tr>
                    <th style="width:70px">ID</th>
                    <th>Слово</th>
                    <th style="width:100px">Язык</th>
                    <th>Перевод</th>
                    <th>Определение</th>
                    <th>Пример</th>
                    <th style="width:220px;text-align:right">Действия</th>
                </tr>
                </thead>
                <tbody>
                @forelse($translations as $translation)
                    <tr>
                        <td class="id-cell">#{{ $translation->id }}</td>
                        <td>
                            @if($translation->word)
                                <a class="chip-link" href="{{ route('admin.vocabulary.words.show', $translation->word) }}">{{ $translation->word->word }}</a>
                            @else
                                <span class="id-cell">—</span>
                            @endif
                        </td>
                        <td><span class="badge badge-primary">{{ strtoupper($translation->language) }}</span></td>
                        <td class="text-cell">{{ $translation->translation }}</td>
                        <td class="text-cell">{{ $translation->definition ? Str::limit($translation->definition, 50) : '—' }}</td>
                        <td class="text-cell">{{ $translation->example ? Str::limit($translation->example, 50) : '—' }}</td>
                        <td>
                            <div class="actions">
                                <a href="{{ route('admin.vocabulary.wordtranslations.show', $translation) }}" class="btn btn-sm btn-ghost">Просмотр</a>
                                <a href="{{ route('admin.vocabulary.wordtranslations.edit', $translation) }}" class="btn btn-sm btn-warning">Изменить</a>
                                <form action="{{ route('admin.vocabulary.wordtranslations.destroy', $translation) }}" method="POST" onsubmit="return confirm('Удалить перевод?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Удалить</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr class="empty-row"><td colspan="7">Переводы не найдены</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if(method_exists($translations, 'hasPages') && $translations->hasPages())
        <div class="pagination">{{ $translations->withQueryString()->links() }}</div>
    @endif
@endsection
