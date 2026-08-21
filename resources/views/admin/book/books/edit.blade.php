@extends('layouts.admin')

@section('title', 'Редактировать книгу')

@section('content')
    <x-admin.page-header :title="'Редактировать: ' . $book->title" :backRoute="route('admin.book.books.show', $book)">
        <x-slot:actions>
            <form action="{{ route('admin.book.books.destroy', $book) }}" method="POST" onsubmit="return confirm('Удалить книгу?')" style="margin:0">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger">Удалить книгу</button>
            </form>
        </x-slot:actions>
    </x-admin.page-header>

    <form action="{{ route('admin.book.books.update', $book) }}" method="POST" class="card">
        @csrf
        @method('PUT')
        <h3>Данные книги</h3>
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Название *</label>
                <input type="text" name="title" class="form-input" value="{{ old('title', $book->title) }}" required>
                @error('title')<div class="error-text">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Автор *</label>
                <input type="text" name="author" class="form-input" value="{{ old('author', $book->author) }}" required>
                @error('author')<div class="error-text">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Уровень</label>
                <select name="level_id" class="form-select">
                    <option value="">— не выбран —</option>
                    @foreach($levels as $level)
                        <option value="{{ $level->id }}" {{ old('level_id', $book->level_id) == $level->id ? 'selected' : '' }}>{{ $level->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Обложка (URL изображения)</label>
                <input type="text" name="cover_image" class="form-input" value="{{ old('cover_image', $book->cover_image) }}" placeholder="https://...">
            </div>
            <div class="form-group full">
                <div class="form-check">
                    <label class="switch">
                        <input type="hidden" name="is_published" value="0">
                        <input type="checkbox" name="is_published" value="1" {{ old('is_published', $book->is_published) ? 'checked' : '' }}>
                        <span class="slider"></span>
                    </label>
                    <span>Опубликовать книгу</span>
                </div>
            </div>
            <div class="form-group full">
                <label class="form-label">Описание</label>
                <textarea name="description" class="form-textarea" rows="3">{{ old('description', $book->description) }}</textarea>
            </div>
        </div>

        <h3 style="margin-top:24px">Страницы книги</h3>
        <div id="pagesContainer">
            @foreach($book->pages as $page)
                <div class="card page-block" style="margin-top:14px">
                    <div class="table-head">
                        <h3>Страница <span class="page-num"></span></h3>
                        <button type="button" class="btn btn-sm btn-danger remove-page-btn">Удалить страницу</button>
                    </div>
                    <div class="form-grid">
                        <input type="hidden" name="pages[{{ $loop->index }}][id]" value="{{ $page->id }}">
                        <div class="form-group full">
                            <label class="form-label">Заголовок страницы (необязательно)</label>
                            <input type="text" name="pages[{{ $loop->index }}][title]" class="form-input" value="{{ $page->title }}">
                        </div>
                        <div class="form-group full">
                            <label class="form-label">Текст страницы *</label>
                            <textarea name="pages[{{ $loop->index }}][content]" class="form-textarea" rows="6" required>{{ $page->content }}</textarea>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="form-actions" style="justify-content:flex-start;margin-top:0">
            <button type="button" id="addPageBtn" class="btn btn-ghost">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Добавить страницу
            </button>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                Сохранить изменения
            </button>
            <a href="{{ route('admin.book.books.show', $book) }}" class="btn btn-ghost">Отмена</a>
        </div>
    </form>
@endsection

@push('scripts')
<script>
    (function () {
        const container = document.getElementById('pagesContainer');
        const addBtn = document.getElementById('addPageBtn');
        let counter = {{ $book->pages->count() }};

        function renumber() {
            container.querySelectorAll('.page-block').forEach((block, i) => {
                block.querySelector('.page-num').textContent = i + 1;
            });
        }

        function bindRemove(block) {
            block.querySelector('.remove-page-btn').addEventListener('click', () => {
                block.remove();
                renumber();
            });
        }

        function addPage() {
            const index = counter++;
            const block = document.createElement('div');
            block.className = 'card page-block';
            block.style.marginTop = '14px';
            block.innerHTML = `
                <div class="table-head">
                    <h3>Страница <span class="page-num"></span></h3>
                    <button type="button" class="btn btn-sm btn-danger remove-page-btn">Удалить страницу</button>
                </div>
                <div class="form-grid">
                    <div class="form-group full">
                        <label class="form-label">Заголовок страницы (необязательно)</label>
                        <input type="text" name="pages[${index}][title]" class="form-input">
                    </div>
                    <div class="form-group full">
                        <label class="form-label">Текст страницы *</label>
                        <textarea name="pages[${index}][content]" class="form-textarea" rows="6" placeholder="Абзацы разделяйте пустой строкой" required></textarea>
                    </div>
                </div>
            `;
            bindRemove(block);
            container.appendChild(block);
            renumber();
        }

        container.querySelectorAll('.page-block').forEach(bindRemove);
        renumber();
        addBtn.addEventListener('click', addPage);
    })();
</script>
@endpush
