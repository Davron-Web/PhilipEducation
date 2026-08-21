<?php

namespace App\Http\Controllers\Admin\Book;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Book\StoreBookRequest;
use App\Http\Requests\Admin\Book\UpdateBookRequest;
use App\Models\Book\Book;
use App\Models\Book\BookPage;
use App\Models\System\Level;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BookController extends Controller
{
    public function index(): View
    {
        $books = Book::with(['level', 'pages'])
            ->when(request('search'), function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('author', 'like', "%{$search}%");
                });
            })
            ->when(request('level_id'), function ($query, $levelId) {
                $query->where('level_id', $levelId);
            })
            ->when(request('is_published') !== null, function ($query) {
                $query->where('is_published', request('is_published'));
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $levels = Level::orderBy('name')->get();

        return view('admin.book.books.index', compact('books', 'levels'));
    }

    public function create(): View
    {
        $levels = Level::orderBy('name')->get();

        return view('admin.book.books.create', compact('levels'));
    }

    public function store(StoreBookRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $pages = $data['pages'] ?? [];
        unset($data['pages']);

        $book = DB::transaction(function () use ($data, $pages) {
            $book = Book::create($data);

            foreach (array_values($pages) as $index => $page) {
                BookPage::create([
                    'book_id' => $book->id,
                    'page_number' => $index + 1,
                    'title' => $page['title'] ?? null,
                    'content' => $page['content'],
                ]);
            }

            return $book;
        });

        return redirect()
            ->route('admin.book.books.show', $book)
            ->with('success', 'Книга создана');
    }

    public function show(Book $book): View
    {
        return view('admin.book.books.show', [
            'book' => $book->load(['level', 'pages']),
        ]);
    }

    public function edit(Book $book): View
    {
        $levels = Level::orderBy('name')->get();
        $book->load('pages');

        return view('admin.book.books.edit', compact('book', 'levels'));
    }

    public function update(UpdateBookRequest $request, Book $book): RedirectResponse
    {
        $data = $request->validated();
        $pages = $data['pages'] ?? [];
        unset($data['pages']);

        DB::transaction(function () use ($data, $pages, $book) {
            $book->update($data);

            $submittedIds = [];

            foreach (array_values($pages) as $index => $page) {
                $pageId = $page['id'] ?? null;

                if ($pageId) {
                    BookPage::where('id', $pageId)->where('book_id', $book->id)->update([
                        'page_number' => $index + 1,
                        'title' => $page['title'] ?? null,
                        'content' => $page['content'],
                    ]);
                    $submittedIds[] = (int) $pageId;
                } else {
                    $newPage = BookPage::create([
                        'book_id' => $book->id,
                        'page_number' => $index + 1,
                        'title' => $page['title'] ?? null,
                        'content' => $page['content'],
                    ]);
                    $submittedIds[] = $newPage->id;
                }
            }

            BookPage::where('book_id', $book->id)->whereNotIn('id', $submittedIds)->delete();
        });

        return redirect()
            ->route('admin.book.books.show', $book)
            ->with('success', 'Книга обновлена');
    }

    public function destroy(Book $book): RedirectResponse
    {
        $book->delete();

        return redirect()
            ->route('admin.book.books.index')
            ->with('success', 'Книга удалена');
    }
}
