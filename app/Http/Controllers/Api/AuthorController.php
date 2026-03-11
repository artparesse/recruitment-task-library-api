<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AuthorResource;
use App\Models\Author;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AuthorController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $request->validate(['search' => ['nullable', 'string', 'max:255']]);

        $authors = Author::with('books')
            ->when(
                $request->validated('search'),
                fn ($query, $search) => $query->whereHas(
                    'books',
                    fn ($q) => $q->where('title', 'LIKE', "%{$search}%")
                )
            )
            ->paginate();

        return AuthorResource::collection($authors);
    }

    public function show(Author $author): AuthorResource
    {
        return new AuthorResource($author->load('books'));
    }
}
