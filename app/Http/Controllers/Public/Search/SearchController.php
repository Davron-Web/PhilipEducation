<?php

namespace App\Http\Controllers\Public\Search;

use App\Http\Controllers\Controller;
use App\Services\SearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function index(Request $request, SearchService $search): View
    {
        $query = (string) $request->input('q', '');
        $groups = $search->search($query, 12);

        return view('public.search.index', [
            'query' => $query,
            'groups' => $groups,
            'total' => $search->countAll($groups),
        ]);
    }

    /** Подсказки под строкой поиска в шапке. */
    public function suggest(Request $request, SearchService $search): JsonResponse
    {
        $query = (string) $request->input('q', '');
        $groups = $search->search($query, SearchService::SUGGEST_LIMIT);

        return response()->json([
            'query' => $query,
            'total' => $search->countAll($groups),
            'groups' => $groups->map(fn ($items) => $items->values()),
        ]);
    }
}
