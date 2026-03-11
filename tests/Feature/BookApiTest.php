<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Jobs\UpdateAuthorLatestBookTitle;
use App\Models\Author;
use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class BookApiTest extends TestCase
{
    use RefreshDatabase;

    private function authToken(): string
    {
        $user = User::factory()->create();

        return $user->createToken('test')->plainTextToken;
    }

    // POST /api/books

    public function test_store_book_returns_201_with_valid_data(): void
    {
        $authors = Author::factory()->count(2)->create();

        $response = $this->withToken($this->authToken())
            ->postJson('/api/books', [
                'title' => 'Test Book',
                'author_ids' => $authors->pluck('id')->toArray(),
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('books', ['title' => 'Test Book']);

        $book = Book::where('title', 'Test Book')->first();
        foreach ($authors as $author) {
            $this->assertDatabaseHas('author_book', [
                'book_id' => $book->id,
                'author_id' => $author->id,
            ]);
        }
    }

    public function test_store_book_returns_422_when_title_missing(): void
    {
        $author = Author::factory()->create();

        $this->withToken($this->authToken())
            ->postJson('/api/books', [
                'author_ids' => [$author->id],
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['title']);
    }

    public function test_store_book_returns_422_when_author_id_does_not_exist(): void
    {
        $this->withToken($this->authToken())
            ->postJson('/api/books', [
                'title' => 'Test Book',
                'author_ids' => [999],
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['author_ids.0']);
    }

    public function test_store_book_returns_422_when_author_ids_empty(): void
    {
        $this->withToken($this->authToken())
            ->postJson('/api/books', [
                'title' => 'Test Book',
                'author_ids' => [],
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['author_ids']);
    }

    public function test_store_book_returns_401_without_token(): void
    {
        $author = Author::factory()->create();

        $this->postJson('/api/books', [
            'title' => 'Test Book',
            'author_ids' => [$author->id],
        ])
            ->assertStatus(401);
    }

    public function test_store_book_dispatches_update_author_latest_book_title_job(): void
    {
        Queue::fake();

        $author = Author::factory()->create();

        $this->withToken($this->authToken())
            ->postJson('/api/books', [
                'title' => 'Queued Book',
                'author_ids' => [$author->id],
            ])
            ->assertStatus(201);

        Queue::assertPushed(UpdateAuthorLatestBookTitle::class);
    }

    // DELETE /api/books/{id}

    public function test_destroy_book_returns_204_and_removes_from_db(): void
    {
        $book = Book::factory()->create();

        $this->withToken($this->authToken())
            ->deleteJson("/api/books/{$book->id}")
            ->assertStatus(204);

        $this->assertDatabaseMissing('books', ['id' => $book->id]);
    }

    public function test_destroy_book_returns_404_for_nonexistent_book(): void
    {
        $this->withToken($this->authToken())
            ->deleteJson('/api/books/999')
            ->assertStatus(404);
    }

    public function test_destroy_book_removes_pivot_entries(): void
    {
        $author = Author::factory()->create();
        $book = Book::factory()->create();
        $book->authors()->attach($author->id);

        $this->withToken($this->authToken())
            ->deleteJson("/api/books/{$book->id}")
            ->assertStatus(204);

        $this->assertDatabaseMissing('author_book', [
            'book_id' => $book->id,
            'author_id' => $author->id,
        ]);
    }

    public function test_destroy_book_returns_401_without_token(): void
    {
        $book = Book::factory()->create();

        $this->deleteJson("/api/books/{$book->id}")
            ->assertStatus(401);
    }
}
