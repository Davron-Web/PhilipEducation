<?php

namespace App\Http\Controllers\Admin\Vocabulary;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Vocabulary\StoreExpressionRequest;
use App\Http\Requests\Admin\Vocabulary\UpdateExpressionRequest;
use App\Models\System\Level;
use App\Models\Vocabulary\Expression;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ExpressionController extends Controller
{
    public function index(): View
    {
        $expressions = Expression::with(['level', 'translations'])
            ->when(request('search'), function ($query, $search) {
                $query->where('text', 'like', "%{$search}%");
            })
            ->when(request('type'), function ($query, $type) {
                $query->where('type', $type);
            })
            ->when(request('level_id'), function ($query, $levelId) {
                $query->where('level_id', $levelId);
            })
            ->orderBy('text')
            ->paginate(20)
            ->withQueryString();

        $levels = Level::orderBy('id')->pluck('code', 'id');

        return view('admin.vocabulary.expressions.index', compact('expressions', 'levels'));
    }

    public function create(): View
    {
        $levels = Level::orderBy('id')->get();

        return view('admin.vocabulary.expressions.create', compact('levels'));
    }

    public function store(StoreExpressionRequest $request): RedirectResponse
    {
        Expression::create($request->validated());

        return redirect()
            ->route('admin.vocabulary.expressions.index')
            ->with('success', 'Expression created successfully.');
    }

    public function show(Expression $expression): View
    {
        return view('admin.vocabulary.expressions.show', [
            'expression' => $expression->load(['level', 'translations', 'userExpressions']),
        ]);
    }

    public function edit(Expression $expression): View
    {
        $levels = Level::orderBy('id')->get();

        return view('admin.vocabulary.expressions.edit', compact('expression', 'levels'));
    }

    public function update(UpdateExpressionRequest $request, Expression $expression): RedirectResponse
    {
        $expression->update($request->validated());

        return redirect()
            ->route('admin.vocabulary.expressions.index')
            ->with('success', 'Expression updated successfully.');
    }

    public function destroy(Expression $expression): RedirectResponse
    {
        if ($expression->userExpressions()->exists()) {
            return redirect()
                ->route('admin.vocabulary.expressions.index')
                ->with('error', 'Cannot delete: users have this expression in their list.');
        }

        $expression->delete();

        return redirect()
            ->route('admin.vocabulary.expressions.index')
            ->with('success', 'Expression deleted successfully.');
    }
}
