<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Author;
use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $authors = Author::factory()->count(15)->create();

        Book::factory()->count(100)->create()->each(function (Book $book) use ($authors): void {
            $book->authors()->attach(
                $authors->random(rand(1, 3))->pluck('id')
            );
        });
    }
}
