<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductColor;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\DB;

class DashboardadminController extends Controller
{
    public function index(): Response
    {
        // Estadísticas básicas
        $totalProducts = Product::count();
        $lowStockProducts = Product::where('stock', '<', 10)->count();
        
        // Contar variantes (colores) con stock bajo
        $lowStockVariants = ProductColor::where('stock', '<', 10)->count();
        
        // Total de productos/variantes con stock bajo (combinado)
        $totalLowStock = $lowStockProducts + $lowStockVariants;

        $stats = [
            'totalProducts' => $totalProducts,
            'totalOrders' => 0, // Cuando tengas el modelo Order
            'lowStockProducts' => $totalLowStock,
        ];

        // Pedidos recientes (cuando implementes el modelo Order)
        $recentOrders = [];
        
        // Top productos por stock
        $topProducts = Product::select('name', 'stock', 'price')
            ->orderBy('stock', 'desc')
            ->take(5)
            ->get()
            ->map(function ($product) {
                return [
                    'name' => $product->name,
                    'stock' => $product->stock,
                    'price' => '$' . number_format($product->price, 2),
                ];
            })
            ->toArray();

        // NUEVO: Variantes con stock bajo agrupadas por producto
        $lowStockVariantsByProduct = ProductColor::with('product:id,name')
            ->where('stock', '<', 10)
            ->get()
            ->groupBy('product.name')
            ->map(function ($colors, $productName) {
                return [
                    'product' => $productName,
                    'count' => $colors->count(),
                    'variants' => $colors->map(function ($color) {
                        return [
                            'name' => $color->name,
                            'stock' => $color->stock,
                        ];
                    })->toArray(),
                ];
            })
            ->values()
            ->toArray();

        return Inertia::render('admin/Dashboard', [
            'stats' => $stats,
            'recentOrders' => $recentOrders,
            'topProducts' => $topProducts,
            'lowStockVariants' => $lowStockVariantsByProduct, // Nuevo dato
        ]);
    }
}