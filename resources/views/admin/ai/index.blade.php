@extends('layouts.admin')

@section('title', 'AI-генерация')

@push('styles')
<style>
    .ai-grid { display: grid; grid-template-columns: 1.2fr 1fr; gap: 20px; }
    .ai-chat { display: flex; flex-direction: column; gap: 10px; max-height: 360px; overflow-y: auto; margin-bottom: 12px; }
    .ai-msg { padding: 10px 14px; border-radius: 12px; font-size: 14px; max-width: 85%; white-space: pre-wrap; }
    .ai-msg.user { background: var(--blue); color: #fff; align-self: flex-end; }
    .ai-msg.ai { background: var(--bg); align-self: flex-start; }
    .ai-row { display: flex; gap: 10px; }
    .ai-row input { margin: 0; flex: 1; }
    .ai-pre { background: #0F172A; color: #E2E8F0; border-radius: 12px; padding: 14px; overflow: auto; font-size: 12px; max-height: 320px; }
    @media (max-width: 900px) { .ai-grid { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
    <x-admin.page-header title="🤖 AI-генерация (Gemini)" />

    <div class="ai-grid">
        <div>
            <div class="card">
                <form method="POST" action="{{ route('admin.ai.generate') }}">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Тема урока</label>
                        <input type="text" name="topic" class="form-input" value="{{ old('topic') }}"
                               placeholder="Например: Present Simple, Past Simple, Travel vocabulary…" required>
                        @error('topic')
                            <div class="alert-card" style="background:rgba(239,68,68,.08);color:var(--red);margin-top:8px">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-accent">⚡ Сгенерировать урок + упражнения + тест</button>
                </form>
            </div>

            @if(session('status'))
                <div class="card" style="margin-top:20px">
                    <div class="alert-card success">{{ session('status') }}</div>
                    @if(session('lessonId'))
                        <a href="{{ route('admin.content.lessons.show', session('lessonId')) }}" class="btn btn-primary btn-sm" style="margin-top:10px">Открыть урок</a>
                    @endif
                    <pre class="ai-pre" style="margin-top:10px">{{ json_encode(session('pack'), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            @endif
        </div>

        <div class="card">
            <h3>💬 AI-помощник</h3>
            <div class="ai-chat" id="chat"></div>
            <div class="ai-row">
                <input id="question" class="form-input" placeholder="Спросите про английский…">
                <button type="button" class="btn btn-primary" onclick="sendQuestion()">➤</button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const chat = document.getElementById('chat');

    function addMsg(cls, text) {
        const d = document.createElement('div');
        d.className = 'ai-msg ' + cls;
        d.textContent = text;
        chat.appendChild(d);
        chat.scrollTop = chat.scrollHeight;
        return d;
    }

    async function sendQuestion() {
        const input = document.getElementById('question');
        const q = input.value.trim();
        if (!q) return;
        input.value = '';
        addMsg('user', q);
        const wait = addMsg('ai', 'Думаю…');
        try {
            const res = await fetch("{{ route('admin.ai.chat') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ question: q })
            });
            const data = await res.json();
            wait.textContent = data.answer ?? ('Ошибка ' + res.status);
        } catch (e) {
            wait.textContent = 'Ошибка соединения: ' + e.message;
        }
        chat.scrollTop = chat.scrollHeight;
    }

    document.getElementById('question').addEventListener('keydown', e => {
        if (e.key === 'Enter') sendQuestion();
    });
</script>
@endpush
