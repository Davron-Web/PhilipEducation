<?php

namespace App\Http\Controllers\Public\Book;

use App\Http\Controllers\Controller;
use App\Models\Book\Book;
use App\Models\Book\BookRead;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BookController extends Controller
{
    public function index(): View
    {
        $books = Book::with('level')
            ->withCount('pages')
            ->where('is_published', true)
            ->latest()
            ->get();

        $readsByBook = Auth::user()->bookReads()->pluck('current_page', 'book_id');

        return view('public.books.index', compact('books', 'readsByBook'));
    }

    public function read(Request $request, Book $book): View
    {
        abort_unless($book->is_published, 404);

        $book->load(['level', 'pages' => function ($query) {
            $query->orderBy('page_number');
        }]);

        $totalPages = $book->pages->count();
        $page = max(1, min((int) $request->query('page', 1), max(1, $totalPages)));

        $currentPage = $book->pages->firstWhere('page_number', $page);

        $read = BookRead::firstOrCreate(
            ['user_id' => Auth::id(), 'book_id' => $book->id],
            ['current_page' => $page]
        );

        return view('public.books.read', compact('book', 'currentPage', 'page', 'totalPages', 'read'));
    }

    public function saveProgress(Request $request, Book $book): JsonResponse
    {
        $data = $request->validate([
            'page' => ['required', 'integer', 'min:1'],
        ]);

        $totalPages = $book->pages()->count();
        $page = min($data['page'], max(1, $totalPages));
        $isLastPage = $totalPages > 0 && $page >= $totalPages;

        $existing = BookRead::where('user_id', Auth::id())->where('book_id', $book->id)->first();

        $read = BookRead::updateOrCreate(
            ['user_id' => Auth::id(), 'book_id' => $book->id],
            [
                'current_page' => $page,
                'completed_at' => $isLastPage ? now() : $existing?->completed_at,
            ]
        );

        return response()->json(['ok' => true, 'current_page' => $read->current_page, 'completed' => (bool) $read->completed_at]);
    }
}
