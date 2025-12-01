<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    /**
     * Muestra la tienda con todos los productos disponibles.
     */
    public function index(): Response
    {
        $products = Product::with('colors')
            ->where('stock', '>', 0)
            ->latest()
            ->get()
            ->map(fn($product) => $this->formatProductForShop($product));

        // mejor reusar la misma consulta base
        $baseQuery = Product::where('stock', '>', 0);

        $categories = $baseQuery->clone()
            ->distinct()
            ->pluck('category')
            ->filter()
            ->values();

        $sizes = $baseQuery->clone()
            ->distinct()
            ->pluck('tamaño')   // o 'size' si la columna se llama así
            ->filter()
            ->values();

        return Inertia::render('Shop', [   // <-- AQUÍ el cambio importante
            'products'   => $products,
            'categories' => $categories,
            'sizes'      => $sizes,
        ]);
    }


    /**
     * Muestra un producto individual.
     */
    public function show($slug): Response
    {
        $product = Product::with('colors')
            ->where('slug', $slug)
            ->firstOrFail();

        // Productos relacionados (misma categoría)
        $relatedProducts = Product::with('colors')
            ->where('category', $product->category)
            ->where('id', '!=', $product->id)
            ->where('stock', '>', 0)
            ->limit(4)
            ->get()
            ->map(fn($p) => $this->formatProductForShop($p));

        return Inertia::render('Shop/ProductDetail', [
            'product' => $this->formatProductForDetail($product),
            'relatedProducts' => $relatedProducts,
        ]);
    }

    /**
     * Formatea un producto para la vista de tienda (tarjetas).
     */
    protected function formatProductForShop(Product $product): array
    {
        $defaultColor = $product->colors->firstWhere('is_default', true) 
            ?? $product->colors->first();

        return [
            'id'          => $product->id,
            'sku'         => $product->sku,
            'name'        => $product->name,
            'slug'        => $product->slug,
            'description' => $product->descriptions,
            'price'       => (float) $product->price,
            'currency'    => $product->currency ?? 'MXN',
            'stock'       => $product->stock,
            'category'    => $product->category,
            'size'        => $product->tamaño,
            'thumbnail'   => $product->thumbnail 
                ? asset('storage/' . $product->thumbnail) 
                : ($defaultColor && $defaultColor->image 
                    ? asset('storage/' . $defaultColor->image) 
                    : null),
            'colors'      => $product->colors->map(fn($color) => [
                'id'         => $color->id,
                'name'       => $color->name,
                'code'       => $color->code,
                'stock'      => $color->stock,
                'is_default' => (bool) $color->is_default,
                'image'      => $color->image ? asset('storage/' . $color->image) : null,
            ])->toArray(),
            'has_colors'  => $product->colors->count() > 0,
            'total_stock' => $product->colors->count() > 0 
                ? $product->colors->sum('stock') 
                : $product->stock,
        ];
    }

    /**
     * Formatea un producto para la vista de detalle.
     */
    protected function formatProductForDetail(Product $product): array
    {
        $baseData = $this->formatProductForShop($product);
        
        $defaultColor = $product->colors->firstWhere('is_default', true) 
            ?? $product->colors->first();

        return array_merge($baseData, [
            'weight'      => $product->weight,
            'subcategory' => $product->subcategory,
            'images'      => $this->formatImages($product->image),
            'colors'      => $product->colors->map(fn($color) => [
                'id'         => $color->id,
                'name'       => $color->name,
                'code'       => $color->code,
                'stock'      => $color->stock,
                'is_default' => (bool) $color->is_default,
                'image'      => $color->image ? asset('storage/' . $color->image) : null,
                'gallery'    => $this->formatImages($color->gallery),
            ])->toArray(),
        ]);
    }

    /**
     * Formatea un array de imágenes.
     */
    protected function formatImages(?array $images): array
    {
        if (!$images) {
            return [];
        }
        return collect($images)
            ->map(fn($img) => asset('storage/' . $img))
            ->toArray();
    }
}