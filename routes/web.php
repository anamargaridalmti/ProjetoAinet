<?php

use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\Admin\OrderManagementController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PriceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TshirtImageController;
use App\Livewire\Cart\CartPage;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

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

// --- Rotas do Carrinho de Compras ---
// The cart page is a Livewire full-page component for reactive, server-side interactivity.
Route::get('/cart', \App\Livewire\Cart\CartPage::class)->name('cart.show');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::patch('/cart/update/{key}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/remove/{key}', [CartController::class, 'destroy'])->name('cart.remove');
Route::delete('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

// G4: Checkout – requires authentication (anonymous users are redirected to login)
Route::middleware('auth')->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'show'])->name('cart.checkout');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('cart.checkout.store');
});

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

    // G4: Histórico de Encomendas
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');

    // G5: Gestão de Imagens Personalizadas por parte do Cliente
    Route::get('/customer/tshirt-images', [\App\Http\Controllers\CustomerTshirtImageController::class, 'index'])->name('customer.tshirt-images.index');
    Route::get('/customer/tshirt-images/create', [\App\Http\Controllers\CustomerTshirtImageController::class, 'create'])->name('customer.tshirt-images.create');
    Route::post('/customer/tshirt-images', [\App\Http\Controllers\CustomerTshirtImageController::class, 'store'])->name('customer.tshirt-images.store');
    Route::get('/customer/tshirt-images/{tshirt_image}/edit', [\App\Http\Controllers\CustomerTshirtImageController::class, 'edit'])->name('customer.tshirt-images.edit');
    Route::put('/customer/tshirt-images/{tshirt_image}', [\App\Http\Controllers\CustomerTshirtImageController::class, 'update'])->name('customer.tshirt-images.update');
    Route::delete('/customer/tshirt-images/{tshirt_image}', [\App\Http\Controllers\CustomerTshirtImageController::class, 'destroy'])->name('customer.tshirt-images.destroy');

    // Rota de Streaming Seguro para Imagens Privadas (Dono, Funcionários e Admins)
    Route::get('/images/private/{filename}', [\App\Http\Controllers\CustomerTshirtImageController::class, 'showPrivateImage'])->name('images.private');

    // G6: Download do Recibo em PDF (autorização via OrderPolicy@downloadReceipt)
    Route::get('/orders/{order}/receipt', [OrderController::class, 'downloadReceipt'])->name('orders.receipt.download');
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
})->name('images.catalog'); 

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
    // 1. Limpar e normalizar o código recebido (remover espaços e colocar em minúsculas)
    $codeClean = strtolower(trim(str_replace('#', '', $code)));
    
    // Diretoria física onde estão guardadas as t-shirts base
    $dir = storage_path('app/public/tshirt_base/');
    $finalPath = null;

    if (is_dir($dir)) {
        $files = scandir($dir);
        
        // 2. Procura Inteligente por Substring (Aproximação de Texto)
        // Se o código for 'white', vai fazer correspondência com 'plain_white.png' ou 'white_plain.png'
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                $fileLower = strtolower($file);
                
                if (str_contains($fileLower, $codeClean)) {
                    $finalPath = $dir . $file;
                    break;
                }
            }
        }
    }

    // 3. Failsafe (Salvaguarda): Se não encontrar a cor específica, tenta carregar o modelo branco padrão
    if (!$finalPath || !file_exists($finalPath)) {
        foreach (['plain_white.png', 'white.png', 'plain_white.PNG'] as $fallback) {
            if (file_exists($dir . $fallback)) {
                $finalPath = $dir . $fallback;
                break;
            }
        }
    }

    // 4. Se mesmo com a salvaguarda nada for encontrado, lança o erro 404
    if (!$finalPath || !file_exists($finalPath)) {
        abort(404, 'Ficheiro base de t-shirt não encontrado na pasta storage.');
    }

    // 5. Servir a imagem com o cabeçalho correto para o browser renderizar as camadas do CSS
    $file = file_get_contents($finalPath);
    $type = mime_content_type($finalPath);
    return response($file, 200)->header("Content-Type", $type);
})->name('img-tshirt-base');

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

    // G8: Painel de Estatísticas do Negócio
    Route::get('/admin/statistics', [\App\Http\Controllers\Admin\AdminStatsController::class, 'index'])->name('admin.statistics');
});

// --- Configurações Adicionais e Placeholders ---
//Route::view('/cart', 'home')->name('cart.show');

// --- Backoffice de Gestão de Encomendas (Funcionários e Admins) ---
Route::middleware(['auth', 'verified', 'staff'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/orders', [OrderManagementController::class, 'index'])->name('orders.index');
    Route::patch('/orders/{order}/close', [OrderManagementController::class, 'close'])->name('orders.close');
    Route::patch('/orders/{order}/cancel', [OrderManagementController::class, 'cancel'])->name('orders.cancel');
});

require __DIR__ . '/settings.php';
