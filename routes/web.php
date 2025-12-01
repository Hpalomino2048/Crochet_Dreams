<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ProductControllers;
use App\Http\Controllers\Admin\DashboardadminController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\CartController;

/*
|--------------------------------------------------------------------------
| Rutas Públicas
|--------------------------------------------------------------------------
*/

Route::get('/', [WelcomeController::class, 'index'])->name('home');

Route::get('/tienda', [ShopController::class, 'index'])->name('shop.index');
Route::get('/tienda/{slug}', [ShopController::class, 'show'])->name('shop.show');

Route::get('/nosotros', function () {
    return Inertia::render('Us');
})->name('about');

Route::get('/temporada', function () {
    return Inertia::render('Season');
})->name('season');

/*
|--------------------------------------------------------------------------
| API Routes - Cart & Orders
|--------------------------------------------------------------------------
*/

Route::prefix('api')->group(function () {
    
    // Crear nueva orden
    Route::post('/create-order', [CartController::class, 'createOrder'])
        ->name('cart.create-order');
    
    // Obtener información de una orden
    Route::get('/order/{orderId}', [CartController::class, 'getOrder'])
        ->name('cart.get-order');
    
    // Actualizar estado de orden (solo admin)
    Route::patch('/order/{orderId}/status', [CartController::class, 'updateOrderStatus'])
        ->name('cart.update-order-status')
        ->middleware(['auth', 'admin']);
    
    // Webhook de MercadoPago
    Route::post('/mercadopago/webhook', [CartController::class, 'mercadoPagoWebhook'])
        ->name('mercadopago.webhook');
});

/*
|--------------------------------------------------------------------------
| Rutas Protegidas - Usuarios Normales
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', fn () => Inertia::render('Dashboard'))
        ->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| Rutas Protegidas - Administradores
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        
        // Dashboard admin
        Route::get('/', [DashboardadminController::class, 'index'])
            ->name('Dashboard');
        
        // Página personalizada para crear producto
        Route::get('Create_Product', [ProductControllers::class, 'create'])
            ->name('Create_Product');
        
        // Página con lista de productos para eliminar
        Route::get('Delete_Product', [ProductControllers::class, 'deleteList'])
            ->name('Delete_Product');
       
        // Página con lista de productos para ACTUALIZAR
        Route::get('Update_Product', [ProductControllers::class, 'updateList'])
            ->name('Update_Product');
        
        // CRUD de productos
        Route::resource('products', ProductControllers::class)
            ->except(['show'])
            ->names('products');
        
        // Sección de pedidos
        Route::get('Pedidos/Pedidos', fn () => Inertia::render('admin/Pedidos/Pedidos'))
            ->name('Pedidos.Pedidos');
    });

/*
|--------------------------------------------------------------------------
| Archivos de rutas adicionales
|--------------------------------------------------------------------------
*/

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';