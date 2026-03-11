<?php

declare(strict_types=1);

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AuthorController;
use App\Http\Controllers\Api\BookController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::apiResource('books', BookController::class)->only(['index', 'show']);
Route::apiResource('books', BookController::class)
    ->only(['store', 'update', 'destroy'])
    ->middleware('auth:sanctum');

Route::apiResource('authors', AuthorController::class)->only(['index', 'show']);
