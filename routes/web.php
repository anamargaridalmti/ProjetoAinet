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
use App\Http\Controllers\PasswordResetController;



//Rotas Públicas da Loja
Route::view('/', 'home')->name('home');
Route::get('/catalog', [TshirtImageController::class, 'index'])->name('catalog.index');

Route::resource('colors', ColorController::class);
Route::resource('categories', CategoryController::class);

//Autenticação (Login / Logout)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

//G1: Verificação de E-mail & Reset de Passwords (Mailtrap)
// --- VERIFICAÇÃO DE E-MAIL ---
// Ecrã que avisa o utilizador que ele precisa de verificar o e-mail
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

// Processa o clique no link enviado para o e-mail
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect()->route('dashboard')->with('status', 'E-mail verificado com sucesso!');
})->middleware(['auth', 'signed'])->name('verification.verify');

// Reenviar o e-mail de verificação
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Link de verificação reenviado!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');


// --- RECUPERAÇÃO DE PASSWORD (RESET) ---
// Ecrã com o formulário para introduzir o e-mail ("Esqueci-me da senha")
Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->middleware('guest')->name('password.request');

// Processa o envio do e-mail de recuperação do link
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->middleware('guest')->name('password.email');

// Ecrã com o formulário para introduzir a nova password (onde o utilizador aterra vindo do e-mail)
Route::get('/reset-password/{token}', function ($token) {
    return view('auth.reset-password', ['token' => $token]);
})->middleware('guest')->name('password.reset');

// Processa a gravação da nova password na Base de Dados
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->middleware('guest')->name('password.update');

//Gestão de Imagens das Categorias
Route::delete('categories/{category}/image', [CategoryController::class, 'destroyImage'])->name('categories.image.destroy');

//Rotas Protegidas (Clientes e Staff Autenticados & Verificados)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    // Perfil do Utilizador
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

//Sistema de Leitura Direta de Ficheiros
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

// 3. Fotos de Perfil dos Utilizadores
Route::get('/img-profiles/{filename}', function ($filename) {
    $path = storage_path('app/public/profiles/' . $filename);

    if (!file_exists($path)) {
        abort(404);
    }

    $file = file_get_contents($path);
    $type = mime_content_type($path);

    return response($file, 200)->header("Content-Type", $type);
});

//Rotas Exclusivas do Administrador (G1 - Segurança e Staff)

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


//Configurações Adicionais e Placeholders

Route::view('/cart', 'home')->name('cart.show');

require __DIR__ . '/settings.php';
