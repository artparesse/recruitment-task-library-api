<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Author extends Model
{
    use HasFactory;

    protected $fillable = ['first_name', 'last_name', 'latest_book_title'];

    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class);
    }
}
