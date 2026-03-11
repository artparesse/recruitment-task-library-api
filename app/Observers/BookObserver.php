<?php

declare(strict_types=1);

namespace App\Observers;

use App\Jobs\UpdateAuthorLatestBookTitle;
use App\Models\Book;

class BookObserver
{
    public function created(Book $book): void
    {
        UpdateAuthorLatestBookTitle::dispatch($book);
    }
}
