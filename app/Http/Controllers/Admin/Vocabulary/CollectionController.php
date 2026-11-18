<?php

namespace App\Http\Controllers\Admin\Vocabulary;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Vocabulary\StoreCollectionRequest;
use App\Http\Requests\Admin\Vocabulary\UpdateCollectionRequest;
use App\Models\Vocabulary\Collection;
use App\Models\Vocabulary\Word;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CollectionController extends Controller
{
    public function index(Request $request): View
    {
        $collections = Collection::withCount('words')
            ->when($request->query('type'), fn ($query, $type) => $query->where('type', $type))
            ->when($request->query('search'), fn ($query, $search) => $query->where('title_ru', 'like', "%{$search}%"))
            ->orderBy('type')
            ->orderBy('sort_order')
            ->orderBy('title_ru')
            ->paginate(30)
            ->withQueryString();

        return view('admin.vocabulary.collections.index', compact('collections'));
    }

    public function create(): View
    {
        return view('admin.vocabulary.collections.create');
    }

    public function store(StoreCollectionRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_published'] = $request->boolean('is_published');

        $collection = Collection::create($data);

        return redirect()
            ->route('admin.vocabulary.collections.show', $collection)
            ->with('success', 'Коллекция создана.');
    }

    public function show(Request $request, Collection $collection): View
    {
        $collection->load(['words' => fn ($query) => $query->withPivot('sort_order')]);

        $search = trim((string) $request->query('search'));

        $availableWords = $search !== ''
            ? Word::where('word', 'like', "%{$search}%")
                ->whereNotIn('id', $collection->words->pluck('id'))
                ->orderBy('word')
                ->limit(50)
                ->get()
            : collect();

        return view('admin.vocabulary.collections.show', compact('collection', 'availableWords', 'search'));
    }

    public function edit(Collection $collection): View
    {
        return view('admin.vocabulary.collections.edit', compact('collection'));
    }

    public function update(UpdateCollectionRequest $request, Collection $collection): RedirectResponse
    {
        $data = $request->validated();
        $data['is_published'] = $request->boolean('is_published');

        $collection->update($data);

        return redirect()
            ->route('admin.vocabulary.collections.show', $collection)
            ->with('success', 'Коллекция обновлена.');
    }

    public function destroy(Collection $collection): RedirectResponse
    {
        $collection->delete();

        return redirect()
            ->route('admin.vocabulary.collections.index')
            ->with('success', 'Коллекция удалена.');
    }

    /**
     * Массовое добавление: несколько word_id из чекбокс-списка сразу.
     * sort_order — по очереди, начиная с текущего максимума в коллекции,
     * чтобы не перезаписать порядок уже добавленных слов.
     */
    public function attachWords(Request $request, Collection $collection): RedirectResponse
    {
        $data = $request->validate([
            'word_ids' => ['required', 'array'],
            'word_ids.*' => ['integer', 'exists:words,id'],
        ]);

        $nextOrder = (int) $collection->words()->max('collection_word.sort_order') + 1;

        $rows = collect($data['word_ids'])
            ->diff($collection->words->pluck('id'))
            ->values()
            ->map(fn ($wordId, $index) => [
                'collection_id' => $collection->id,
                'word_id' => $wordId,
                'sort_order' => $nextOrder + $index,
                'created_at' => now(),
                'updated_at' => now(),
            ])
            ->all();

        if ($rows) {
            DB::table('collection_word')->insert($rows);
        }

        return redirect()
            ->route('admin.vocabulary.collections.show', $collection)
            ->with('success', count($rows).' слов добавлено в коллекцию.');
    }

    public function detachWord(Collection $collection, Word $word): RedirectResponse
    {
        $collection->words()->detach($word->id);

        return redirect()
            ->route('admin.vocabulary.collections.show', $collection)
            ->with('success', "Слово «{$word->word}» удалено из коллекции.");
    }

    /**
     * Принимает новый порядок как массив word_id (в нужном порядке) и
     * переписывает sort_order по позиции в массиве.
     */
    public function reorderWords(Request $request, Collection $collection)
    {
        $data = $request->validate([
            'word_ids' => ['required', 'array'],
            'word_ids.*' => ['integer'],
        ]);

        foreach ($data['word_ids'] as $index => $wordId) {
            DB::table('collection_word')
                ->where('collection_id', $collection->id)
                ->where('word_id', $wordId)
                ->update(['sort_order' => $index]);
        }

        return response()->json(['ok' => true]);
    }
}
