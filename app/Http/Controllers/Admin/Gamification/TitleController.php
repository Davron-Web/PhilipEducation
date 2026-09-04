<?php

namespace App\Http\Controllers\Admin\Gamification;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Gamification\StoreTitleRequest;
use App\Http\Requests\Admin\Gamification\UpdateTitleRequest;
use App\Models\Gamification\Title;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TitleController extends Controller
{
    public function index(): View
    {
        $titles = Title::withCount('users')
            ->orderBy('min_xp')
            ->when(request('search'), fn ($query, $search) => $query->where('name', 'like', "%{$search}%"))
            ->paginate(20)
            ->withQueryString();

        return view('admin.gamification.titles.index', compact('titles'));
    }

    public function create(): View
    {
        return view('admin.gamification.titles.create');
    }

    public function store(StoreTitleRequest $request): RedirectResponse
    {
        Title::create($request->validated());

        return redirect()
            ->route('admin.gamification.titles.index')
            ->with('success', 'Титул создан');
    }

    public function show(Title $title): View
    {
        return view('admin.gamification.titles.show', [
            'title' => $title->loadCount('users'),
            'holders' => $title->users()->paginate(20),
        ]);
    }

    public function edit(Title $title): View
    {
        return view('admin.gamification.titles.edit', compact('title'));
    }

    public function update(UpdateTitleRequest $request, Title $title): RedirectResponse
    {
        $title->update($request->validated());

        return redirect()
            ->route('admin.gamification.titles.index')
            ->with('success', 'Титул обновлён');
    }

    public function destroy(Title $title): RedirectResponse
    {
        // Как и с достижениями: полученное звание нельзя стереть из истории.
        if ($title->users()->exists()) {
            return redirect()
                ->route('admin.gamification.titles.index')
                ->with('error', 'Нельзя удалить: титул уже получен учениками. Снимите галочку «активен», чтобы он перестал выдаваться.');
        }

        $title->delete();

        return redirect()
            ->route('admin.gamification.titles.index')
            ->with('success', 'Титул удалён');
    }
}
