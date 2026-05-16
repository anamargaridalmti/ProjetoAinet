<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\CategoryController;


Route::view('/', 'home')->name('home');

Route::resource('colors', ColorController::class);
Route::resource('categories', CategoryController::class);

Route::delete('categories/{category}/image', [CategoryController::class, 'destroyImage'])->name('categories.image.destroy');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

require __DIR__ . '/settings.php';
