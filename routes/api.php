<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CategoryController;

Route::middleware('auth:api')->group(function () {
    Route::prefix('articles')->group(function () {
        Route::get('/get/{id}', [ArticleController::class, 'index']);
        Route::post('/post', [ArticleController::class, 'store']);
        Route::put('/update/{id}', [ArticleController::class, 'update']);
        Route::delete('/delete/{id}', [ArticleController::class, 'destroy']);
        Route::get('/search', [ArticleController::class, 'search']);
    });

    Route::prefix('categories')->group(function () {
        Route::get('/get', [CategoryController::class, 'index']);
        Route::post('/post', [CategoryController::class, 'store']);
        Route::delete('/delete/{id}', [CategoryController::class, 'destroy']);
    });

    Route::get('/users', [AuthController::class, 'profile']);
    Route::get('/auth/logout', [AuthController::class, 'logout']);
});

Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);
