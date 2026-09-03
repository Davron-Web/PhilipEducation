<?php

use App\Models\Book\Book;
use App\Models\Book\BookPage;
use App\Models\User;

// Книги закрыты подпиской, поэтому тестовому пользователю её выдаём.
$user = fn () => tap(User::factory()->create(), fn ($u) => giveSubscription($u));

it('redirects guests away from the book list', function () {
    $this->get('/books')->assertRedirect('/login');
});

it('lists only published books', function () use ($user) {
    Book::factory()->create(['title' => 'Published Book', 'is_published' => true]);
    Book::factory()->draft()->create(['title' => 'Draft Book']);

    $response = $this->actingAs($user())->get('/books');

    $response->assertOk();
    $response->assertSee('Published Book');
    $response->assertDontSee('Draft Book');
});

it('lets a user read a published book and creates reading progress', function () use ($user) {
    $book = Book::factory()->create(['is_published' => true]);
    BookPage::factory()->create(['book_id' => $book->id, 'page_number' => 1, 'content' => 'Page one content.']);
    BookPage::factory()->create(['book_id' => $book->id, 'page_number' => 2, 'content' => 'Page two content.']);

    $u = $user();
    $response = $this->actingAs($u)->get("/books/{$book->id}/read");

    $response->assertOk();
    $this->assertDatabaseHas('book_reads', [
        'user_id' => $u->id,
        'book_id' => $book->id,
        'current_page' => 1,
    ]);
});

it('404s when reading an unpublished book', function () use ($user) {
    $book = Book::factory()->draft()->create();

    $this->actingAs($user())
        ->get("/books/{$book->id}/read")
        ->assertNotFound();
});

it('saves reading progress and marks the book completed on the last page', function () use ($user) {
    $book = Book::factory()->create(['is_published' => true]);
    BookPage::factory()->create(['book_id' => $book->id, 'page_number' => 1]);
    BookPage::factory()->create(['book_id' => $book->id, 'page_number' => 2]);

    $u = $user();

    $response = $this->actingAs($u)->postJson("/books/{$book->id}/progress", ['page' => 2]);

    $response->assertOk()->assertJson(['ok' => true, 'current_page' => 2, 'completed' => true]);
    $this->assertDatabaseHas('book_reads', [
        'user_id' => $u->id,
        'book_id' => $book->id,
        'current_page' => 2,
    ]);
    $this->assertNotNull($u->bookReads()->first()->completed_at);
});
