<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Author;
use Illuminate\Console\Command;

class CreateAuthorCommand extends Command
{
    protected $signature = 'author:create';

    protected $description = 'Create a new author';

    public function handle(): void
    {
        $firstName = $this->ask('Enter first name:');
        $lastName = $this->ask('Enter last name:');

        if (empty($firstName) || empty($lastName)) {
            $this->error('First name and last name are required.');
            return;
        }

        $author = Author::create([
            'first_name' => $firstName,
            'last_name' => $lastName,
        ]);

        $this->info("Author '{$firstName} {$lastName}' created (ID: {$author->id}).");
    }
}
