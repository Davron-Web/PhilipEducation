@extends('layouts.admin')

@section('title', $collection->title_ru)

@section('content')
    @php $typeLabels = ['level' => 'Уровень', 'topic' => 'Тема', 'exam' => 'Экзамен']; @endphp

    <x-admin.page-header :title="$collection->title_ru" :backRoute="route('admin.vocabulary.collections.index')">
        <x-slot:sub><span class="live-dot"></span> {{ $typeLabels[$collection->type] ?? $collection->type }} · slug: {{ $collection->slug }} · {{ $collection->words->count() }} слов</x-slot:sub>
        <x-slot:actions>
            <a href="{{ route('admin.vocabulary.collections.edit', $collection) }}" class="btn btn-warning">Изменить коллекцию</a>
        </x-slot:actions>
    </x-admin.page-header>

    @php
        $wordsById = $collection->words->keyBy('id')->map(fn ($word) => [
            'id' => $word->id,
            'word' => $word->word,
            'translation' => optional($word->translations->first())->translation ?? '',
        ]);
    @endphp

    <div
        class="card"
        x-data="{
            order: {{ Js::from($collection->words->pluck('id')) }},
            words: {{ Js::from($wordsById) }},
            reorderUrl: '{{ route('admin.vocabulary.collections.words.reorder', $collection) }}',
            csrf: document.querySelector('meta[name=csrf-token]').content,
            move(index, delta) {
                const target = index + delta;
                if (target < 0 || target >= this.order.length) return;
                const copy = this.order.slice();
                [copy[index], copy[target]] = [copy[target], copy[index]];
                this.order = copy;
                this.save();
            },
            save() {
                fetch(this.reorderUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrf },
                    body: JSON.stringify({ word_ids: this.order }),
                }).catch(() => {});
            },
        }"
    >
        <h3 style="margin-bottom:12px">Слова в коллекции</h3>

        @if ($collection->words->isEmpty())
            <x-admin.empty-state message="В коллекции пока нет слов — добавьте их формой ниже." />
        @else
            <table class="table">
                <thead>
                    <tr><th style="width:90px">Порядок</th><th>Слово</th><th>Перевод</th><th style="width:120px"></th></tr>
                </thead>
                <tbody>
                    <template x-for="(id, i) in order" :key="id">
                        <tr>
                            <td>
                                <button type="button" class="btn btn-sm btn-ghost" @click="move(i, -1)" :disabled="i === 0" title="Выше">↑</button>
                                <button type="button" class="btn btn-sm btn-ghost" @click="move(i, 1)" :disabled="i === order.length - 1" title="Ниже">↓</button>
                            </td>
                            <td x-text="words[id].word"></td>
                            <td x-text="words[id].translation"></td>
                            <td>
                                <form method="POST" :action="'{{ url('admin/vocabulary/collections/'.$collection->id.'/words') }}/' + id" @submit.prevent="if (confirm('Убрать «' + words[id].word + '» из коллекции?')) $el.submit()">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Убрать</button>
                                </form>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        @endif
    </div>

    <div class="card" style="margin-top:20px">
        <h3 style="margin-bottom:12px">Массовое добавление слов</h3>
        <form method="GET" class="filters" style="margin-bottom:16px">
            <input type="text" name="search" class="input" placeholder="Искать слово для добавления..." value="{{ $search }}">
            <button type="submit" class="btn btn-ghost">Искать</button>
        </form>

        @if ($search !== '')
            <form action="{{ route('admin.vocabulary.collections.words.attach', $collection) }}" method="POST">
                @csrf
                @if ($availableWords->isEmpty())
                    <p style="color:var(--muted)">Ничего не найдено (или все совпадения уже в коллекции).</p>
                @else
                    <div style="display:grid;gap:8px;max-height:360px;overflow-y:auto;margin-bottom:16px">
                        @foreach ($availableWords as $word)
                            <label style="display:flex;align-items:center;gap:8px">
                                <input type="checkbox" name="word_ids[]" value="{{ $word->id }}">
                                {{ $word->word }}
                                @if ($word->category)
                                    <span class="chip-link" style="cursor:default">{{ $word->category }}</span>
                                @endif
                            </label>
                        @endforeach
                    </div>
                    <button type="submit" class="btn btn-primary">Добавить выбранные</button>
                @endif
            </form>
        @endif
    </div>
@endsection
