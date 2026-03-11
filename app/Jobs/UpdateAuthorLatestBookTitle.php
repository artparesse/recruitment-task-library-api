<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Book;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class UpdateAuthorLatestBookTitle implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Book $book,
    ) {}

    public function handle(): void
    {
        foreach ($this->book->authors as $author) {
            $author->latest_book_title = $this->book->title;
            $author->save();
        }
    }
}
