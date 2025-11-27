<?php
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ProductControllers;
use App\Http\Controllers\Admin\DashboardadminController;

/*
|--------------------------------------------------------------------------
| Rutas públicas (permiten acceso con o sin autenticación)
|--------------------------------------------------------------------------
*/

// Estas rutas usan el middleware 'web' que viene por defecto
// y permiten acceso tanto a usuarios autenticados como no autenticados
Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('/tienda', function () {
    return Inertia::render('Shop');
})->name('Shop');

Route::get('/nosotros', function () {
    return Inertia::render('Us');
})->name('about');

Route::get('/temporada', function () {
    return Inertia::render('Season');
})->name('season');

/*
|--------------------------------------------------------------------------
| Rutas protegidas - Usuarios normales
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', fn () => Inertia::render('Dashboard'))
        ->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| Rutas protegidas - Administradores
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
        
        // NUEVA: Página con lista de productos para eliminar
        Route::get('Delete_Product', [ProductControllers::class, 'deleteList'])
            ->name('Delete_Product');

       // NUEVA: Página con lista de productos para ACTUALIZAR (lista + modal)
        Route::get('Update_Product', [ProductControllers::class, 'updateList'])
        ->name('Update_Product');

        
        // CRUD de productos (incluye admin.products.destroy)
        Route::resource('products', ProductControllers::class)
            ->except(['show'])
            ->names('products');
        
        /* Sección de rutas para pedidos */
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