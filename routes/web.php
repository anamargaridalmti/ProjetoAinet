<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TshirtImageController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\PriceController;
use App\Http\Controllers\CartController;

// --- Rotas Públicas da Loja (Acessíveis a visitantes anónimos) ---
Route::get('/', [TshirtImageController::class, 'index'])->name('home');
Route::get('/catalog', [TshirtImageController::class, 'index'])->name('catalog.index');

// --- Rotas de Cores Únicas e Ordenadas  ---
Route::get('/colors', [ColorController::class, 'index'])->name('colors.index');
Route::get('/colors/create', [ColorController::class, 'create'])->name('colors.create');
Route::post('/colors', [ColorController::class, 'store'])->name('colors.store');
Route::get('/colors/{color}', [ColorController::class, 'show'])->name('colors.show');
Route::get('/colors/{color}/edit', [ColorController::class, 'edit'])->name('colors.edit');
Route::put('/colors/{color}', [ColorController::class, 'update'])->name('colors.update');
Route::delete('/colors/{color}', [ColorController::class, 'destroy'])->name('colors.destroy');

// --- Rotas de Categorias Públicas ---
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('categories.show');

// --- Autenticação (Login / Logout) ---
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// --- Rotas do Carrinho de Compras --- //
Route::get('/cart', [CartController::class, 'show'])->name('cart.show');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::patch('/cart/update/{key}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/remove/{key}', [CartController::class, 'destroy'])->name('cart.remove');
Route::delete('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

// --- Verificação de E-mail & Reset de Passwords ---
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect()->route('catalog.index')->with('status', 'E-mail verificado com sucesso!');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Link de verificação reenviado!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->middleware('guest')->name('password.request');

Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->middleware('guest')->name('password.email');

Route::get('/reset-password/{token}', function ($token) {
    return view('auth.reset-password', ['token' => $token]);
})->middleware('guest')->name('password.reset');

Route::post('/reset-password', [AuthController::class, 'resetPassword'])->middleware('guest')->name('password.update');

// --- Rotas Protegidas Gerais (Clientes e Staff Autenticados & Verificados) ---
Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    // Perfil do Utilizador
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

// --- Sistema de Leitura Direta de Ficheiros ---
Route::get('/images/catalog/{filename}', function ($filename) {
    $path = 'tshirt_images/' . $filename;
    if (!Storage::disk('public')->exists($path)) {
        abort(404);
    }
    $file = Storage::disk('public')->get($path);
    $type = Storage::disk('public')->mimeType($path);
    return Response::make($file, 200)->header("Content-Type", $type);
});

Route::get('/img-categories/{filename}', function ($filename) {
    $path = storage_path('app/public/categories/' . $filename);
    if (!file_exists($path)) {
        abort(404);
    }
    $file = file_get_contents($path);
    $type = mime_content_type($path);
    return response($file, 200)->header("Content-Type", $type);
});

Route::get('/img-profiles/{filename}', function ($filename) {
    $path = storage_path('app/public/profiles/' . $filename);
    if (!file_exists($path)) {
        abort(404);
    }
    $file = file_get_contents($path);
    $type = mime_content_type($path);
    return response($file, 200)->header("Content-Type", $type);
});

Route::get('/img-tshirt-base/{code}', function ($code) {
    $path = storage_path('app/public/tshirt_base/' . strtoupper($code) . '.png');
    if (!file_exists($path)) {
        abort(404);
    }
    $file = file_get_contents($path);
    $type = mime_content_type($path);
    return response($file, 200)->header("Content-Type", $type);
});

// --- Rotas Exclusivas do Administrador ---
Route::middleware(['auth', 'verified'])->group(function () {

    // G1: Controlo de Utilizadores
    Route::get('/admin/users', [AdminUserController::class, 'index'])->name('admin.users.index');
    Route::patch('/admin/users/{user}/toggle-block', [AdminUserController::class, 'toggleBlock'])->name('admin.users.toggle-block');
    Route::delete('/admin/users/{user}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');
    Route::get('/admin/users/create', [AdminUserController::class, 'create'])->name('admin.users.create');
    Route::post('/admin/users', [AdminUserController::class, 'store'])->name('admin.users.store');

    // G2: Gestão de Categorias Privada
    Route::get('/admin/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/admin/categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('/admin/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/admin/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/admin/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/admin/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    Route::delete('/admin/categories/{category}/image', [CategoryController::class, 'destroyImage'])->name('categories.image.destroy');

    //G2: Catálogo de Imagens de T-shirt
    Route::get('/admin/tshirt-images', [TshirtImageController::class, 'adminIndex'])->name('admin.tshirt-images.index');
    Route::get('/admin/tshirt-images/create', [TshirtImageController::class, 'create'])->name('admin.tshirt-images.create');
    Route::post('/admin/tshirt-images', [TshirtImageController::class, 'store'])->name('admin.tshirt-images.store');
    Route::get('/admin/tshirt-images/{tshirt_image}/edit', [TshirtImageController::class, 'edit'])->name('admin.tshirt-images.edit');
    Route::put('/admin/tshirt-images/{tshirt_image}', [TshirtImageController::class, 'update'])->name('admin.tshirt-images.update');
    Route::delete('/admin/tshirt-images/{tshirt_image}', [TshirtImageController::class, 'destroy'])->name('admin.tshirt-images.destroy');

    // G2: Configuração Global de Preços da Loja
    Route::get('/admin/prices', [PriceController::class, 'edit'])->name('admin.prices.edit');
    Route::put('/admin/prices', [PriceController::class, 'update'])->name('admin.prices.update');
});

// --- Configurações Adicionais e Placeholders ---
//Route::view('/cart', 'home')->name('cart.show');
require __DIR__ . '/settings.php';
