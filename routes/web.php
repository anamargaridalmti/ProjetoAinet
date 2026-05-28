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

/*
|--------------------------------------------------------------------------
| Rotas Públicas da Loja
|--------------------------------------------------------------------------
*/

Route::view('/', 'home')->name('home');
Route::get('/catalog', [TshirtImageController::class, 'index'])->name('catalog.index');

Route::resource('colors', ColorController::class);
Route::resource('categories', CategoryController::class);

/*
|--------------------------------------------------------------------------
| Autenticação (Login / Logout)
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Gestão de Imagens das Categorias
|--------------------------------------------------------------------------
*/
Route::delete('categories/{category}/image', [CategoryController::class, 'destroyImage'])->name('categories.image.destroy');

/*
|--------------------------------------------------------------------------
| Rotas Protegidas (Clientes e Staff Autenticados)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    // Perfil do Utilizador
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

/*
|--------------------------------------------------------------------------
| Sistema de Leitura Direta de Ficheiros (Blindado contra falhas do Windows)
|--------------------------------------------------------------------------
*/

// 1. Imagens do Catálogo de T-Shirts
Route::get('/images/catalog/{filename}', function ($filename) {
    $path = 'tshirt_images/' . $filename;

    if (!Storage::disk('public')->exists($path)) {
        abort(404);
    }

    $file = Storage::disk('public')->get($path);
    $type = Storage::disk('public')->mimeType($path);

    return Response::make($file, 200)->header("Content-Type", $type);
});

// 2. Imagens das Categorias
Route::get('/img-categories/{filename}', function ($filename) {
    $path = storage_path('app/public/categories/' . $filename);

    if (!file_exists($path)) {
        abort(404);
    }

    $file = file_get_contents($path);
    $type = mime_content_type($path);

    return response($file, 200)->header("Content-Type", $type);
});

// 3. Fotos de Perfil dos Utilizadores (Avatares)
Route::get('/img-profiles/{filename}', function ($filename) {
    $path = storage_path('app/public/profiles/' . $filename);

    if (!file_exists($path)) {
        abort(404);
    }

    $file = file_get_contents($path);
    $type = mime_content_type($path);

    return response($file, 200)->header("Content-Type", $type);
});

/*
|--------------------------------------------------------------------------
| Rotas Exclusivas do Administrador (G1 - Segurança e Staff)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    // Lista de Utilizadores e Filtros
    Route::get('/admin/users', [AdminUserController::class, 'index'])->name('admin.users.index');

    // Bloquear/Desbloquear Utilizador
    Route::patch('/admin/users/{user}/toggle-block', [AdminUserController::class, 'toggleBlock'])->name('admin.users.toggle-block');

    // Soft Delete de Utilizadores
    Route::delete('/admin/users/{user}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');

    // Mostrar formulário de criação de Staff
    Route::get('/admin/users/create', [AdminUserController::class, 'create'])->name('admin.users.create');

    // Submeter o formulário de criação de Staff
    Route::post('/admin/users', [AdminUserController::class, 'store'])->name('admin.users.store');
});

/*
|--------------------------------------------------------------------------
| Configurações Adicionais
|--------------------------------------------------------------------------
*/

Route::view('/cart', 'home')->name('cart.show');


require __DIR__ . '/settings.php';
