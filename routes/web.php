<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TshirtImageController;
use App\Http\Controllers\AdminUserController;

Route::view('/', 'home')->name('home');

Route::resource('colors', ColorController::class);
Route::resource('categories', CategoryController::class);
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::delete('categories/{category}/image', [CategoryController::class, 'destroyImage'])->name('categories.image.destroy');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

Route::middleware(['auth', 'verified'])->group(function () {

    //Perfil do Cliente
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

Route::get('/catalog', [TshirtImageController::class, 'index'])->name('catalog.index');

Route::get('/images/catalog/{filename}', function ($filename) {
    $path = 'tshirt_images/' . $filename;

    if (!Storage::disk('public')->exists($path)) {
        abort(404);
    }

    $file = Storage::disk('public')->get($path);
    $type = Storage::disk('public')->mimeType($path);

    return Response::make($file, 200)->header("Content-Type", $type);
});

Route::get('/img-profiles/{filename}', function ($filename) {
    $path = 'profiles/' . $filename;

    if (!Storage::disk('public')->exists($path)) {
        abort(404);
    }

    $file = Storage::disk('public')->get($path);
    $type = Storage::disk('public')->mimeType($path);

    return response($file, 200)->header("Content-Type", $type);
});

// Rotas Exclusivas do Administrador (G1)
Route::middleware(['auth', 'verified'])->group(function () {

    // Lista de Utilizadores e Filtros
    Route::get('/admin/users', [AdminUserController::class, 'index'])->name('admin.users.index');

    // Bloquear/Desbloquear Utilizador
    Route::patch('/admin/users/{user}/toggle-block', [AdminUserController::class, 'toggleBlock'])->name('admin.users.toggle-block');

    //delete
    Route::delete('/admin/users/{user}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');

    // Mostrar formulário de criação de Staff
    Route::get('/admin/users/create', [AdminUserController::class, 'create'])->name('admin.users.create');

    // Submeter o formulário de criação
    Route::post('/admin/users', [AdminUserController::class, 'store'])->name('admin.users.store');
});

require __DIR__ . '/settings.php';
