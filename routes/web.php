<?php

use App\Http\Controllers\FrontendController;
use Illuminate\Support\Facades\Route;

// Home
Route::get('/', [FrontendController::class, 'home'])->name('home');

// Blog
Route::get('/blog', [FrontendController::class, 'posts'])->name('posts');
Route::get('/blog/{slug}', [FrontendController::class, 'post'])->name('post');

// Pages — универсальный catch-all, ВСЕГДА последним
Route::get('/{slug}', [FrontendController::class, 'page'])->name('page');