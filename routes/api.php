<?php

declare(strict_types=1);

use App\Http\Controllers\Api\BookController;
use Illuminate\Support\Facades\Route;

Route::apiResource('books', BookController::class);
