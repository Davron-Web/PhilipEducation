<?php

namespace App\Http\Controllers\Admin\Content;

use App\Http\Controllers\Controller;
use App\Models\Content\GrammarTopic;
use App\Models\System\Level;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class GrammarTopicController extends Controller
{
    public function index()
    {
        $grammarTopics = GrammarTopic::with('level')
            ->when(request('search'), function ($query, $search) {
                $query->where('title', 'like', "%{$search}%");
            })
            ->when(request('level_id'), function ($query, $levelId) {
                $query->where('level_id', $levelId);
            })
            ->orderBy(Level::select('code')->whereColumn('levels.id', 'grammar_topics.level_id'))
            ->orderBy('title')
            ->paginate(20)
            ->withQueryString();

        $levels = Level::orderBy('name')->get();

        return view('admin.content.grammartopics.index', compact('grammarTopics', 'levels'));
    }

    public function create()
    {
        $levels = Level::orderBy('name')->get();

        return view('admin.content.grammartopics.create', compact('levels'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'level_id' => ['required', 'integer', 'exists:levels,id'],
        ], [
            'title.required' => 'Укажите название правила.',
            'level_id.required' => 'Выберите уровень.',
        ]);

        // принимаем любое имя поля теории
        $theory = $request->input('theory_content') ?: $request->input('description');

        if (! $theory) {
            return back()->withInput()
                ->withErrors(['theory_content' => 'Заполните теорию правила.']);
        }

        $columns = Schema::getColumnListing('grammar_topics');

        $payload = [
            'title' => $data['title'],
            'level_id' => $data['level_id'],
            'order_number' => $request->input('order_number', 1) ?? 1,
        ];

        // кладём теорию в ту колонку, которая реально есть в таблице
        foreach (['theory_content', 'description', 'content', 'theory'] as $col) {
            if (in_array($col, $columns, true)) {
                $payload[$col] = $theory;
                break;
            }
        }

        // убираем всё, чего нет в таблице
        $payload = array_intersect_key($payload, array_flip($columns));

        // forceCreate обходит $fillable
        GrammarTopic::forceCreate($payload);

        return redirect()
            ->route('admin.content.grammartopics.index')
            ->with('success', 'Тема грамматики успешно создана.');
    }

    public function show($id)
    {
        $topic = GrammarTopic::find($id);

        return view('admin.content.grammartopics.show', [
            'grammarTopic' => $topic->load('level'),
        ]);
    }

    public function edit(GrammarTopic $grammartopic)
    {
        $levels = Level::orderBy('name')->get();

        return view('admin.content.grammartopics.edit', compact('grammartopic', 'levels'));
    }

    public function update(Request $request, GrammarTopic $grammartopic)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'level_id' => ['required', 'integer', 'exists:levels,id'],
        ], [
            'title.required' => 'Укажите название правила.',
            'level_id.required' => 'Выберите уровень.',
        ]);

        $theory = $request->input('theory_content') ?: $request->input('description');

        if (! $theory) {
            return back()->withInput()
                ->withErrors(['theory_content' => 'Заполните теорию правила.']);
        }

        $columns = Schema::getColumnListing('grammar_topics');

        $payload = [
            'title' => $data['title'],
            'level_id' => $data['level_id'],
            'order_number' => $request->input('order_number', 1) ?? 1,
        ];

        foreach (['theory_content', 'description', 'content', 'theory'] as $col) {
            if (in_array($col, $columns, true)) {
                $payload[$col] = $theory;
                break;
            }
        }

        $payload = array_intersect_key($payload, array_flip($columns));

        // forceFill обходит $fillable
        $grammartopic->forceFill($payload)->save();

        return redirect()
            ->route('admin.content.grammartopics.index')
            ->with('success', 'Тема грамматики успешно обновлена.');
    }

    public function destroy(GrammarTopic $grammartopic)
    {
        $grammartopic->delete();

        return redirect()
            ->route('admin.content.grammartopics.index')
            ->with('success', 'Тема грамматики успешно удалена.');
    }
}
