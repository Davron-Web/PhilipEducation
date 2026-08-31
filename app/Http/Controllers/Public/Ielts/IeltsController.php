<?php

namespace App\Http\Controllers\Public\Ielts;

use App\Http\Controllers\Controller;
use App\Models\Ielts\IeltsTask;
use App\Services\GeminiService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class IeltsController extends Controller
{
    /**
     * Список заданий IELTS Writing, сгруппированных по Task 1 / Task 2,
     * с лучшим band score пользователя по каждому (если уже сдавал).
     */
    public function index(): View
    {
        $tasks = IeltsTask::orderBy('id')->get();

        $bestScores = Auth::user()->ieltsSubmissions()
            ->selectRaw('ielts_task_id, MAX(band_score) as best_band')
            ->groupBy('ielts_task_id')
            ->pluck('best_band', 'ielts_task_id');

        $task1 = $tasks->where('type', 'writing_task1')->values();
        $task2 = $tasks->where('type', 'writing_task2')->values();

        return view('public.ielts.index', compact('task1', 'task2', 'bestScores'));
    }

    /**
     * Страница задания: формулировка (+график для Task 1), поле ответа
     * и прошлые попытки с оценкой Gemini.
     */
    public function show(IeltsTask $task): View
    {
        $submissions = $task->submissions()
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('public.ielts.show', compact('task', 'submissions'));
    }

    /**
     * Отправка ответа на проверку — Gemini выставляет band score и разбор.
     */
    public function submit(Request $request, IeltsTask $task, GeminiService $gemini): RedirectResponse
    {
        $data = $request->validate([
            'answer_text' => 'required|string|min:50',
        ]);

        $wordCount = str_word_count(strip_tags($data['answer_text']));

        $submission = $task->submissions()->create([
            'user_id' => Auth::id(),
            'answer_text' => $data['answer_text'],
            'word_count' => $wordCount,
        ]);

        try {
            $result = $gemini->gradeIeltsWriting($task->type, $task->prompt, $data['answer_text']);
            $submission->update([
                'band_score' => $result['band'],
                'feedback' => $result['feedback'],
            ]);
        } catch (\Throwable $e) {
            $submission->update([
                'feedback' => 'Не удалось получить оценку от ИИ-проверяющего: '.$e->getMessage(),
            ]);
        }

        return redirect()->route('ielts.writing.show', $task)->with('success', 'Ответ отправлен на проверку.');
    }
}
