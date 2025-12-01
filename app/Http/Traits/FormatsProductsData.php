<?php

namespace App\Http\Traits;

use App\Models\Product;

trait FormatsProductsData
{
    /**
     * Formatea un producto para la lista.
     */
    public function deleteList(): Response
    {
        $products = Product::with('colors')
            ->latest()
            ->paginate(50)
            ->through(fn($product) => $this->formatProductForList($product));

        return Inertia::render('admin/Delete_Product', [
            'products' => $products,
            'flash' => session()->only(['success', 'error']),
        ]);
    }
    protected function formatProductForList(Product $product): array
    {
        return [
            'id'           => $product->id,
            'sku'          => $product->sku,
            'name'         => $product->name,
            'slug'         => $product->slug,
            'descriptions' => $product->descriptions,
            'price'        => $product->price,
            'currency'     => $product->currency,
            'stock'        => $product->stock,
            'thumbnail'    => $product->thumbnail ? asset('storage/' . $product->thumbnail) : null,
            'category'     => $product->category,
            'subcategory'  => $product->subcategory,
            'tamaño'       => $product->tamaño,
            'created_at'   => $product->created_at?->format('d/m/Y'),
            'colors'       => $product->colors->map(fn($color) => $this->formatColorBasic($color)),
        ];
    }

    /**
     * Formatea un producto para edición.
     */
    protected function formatProductForEdit(Product $product): array
    {
        return [
            'id'           => $product->id,
            'sku'          => $product->sku,
            'name'         => $product->name,
            'slug'         => $product->slug,
            'descriptions' => $product->descriptions,
            'price'        => $product->price,
            'currency'     => $product->currency,
            'stock'        => $product->stock,
            'weight'       => $product->weight,
            'thumbnail'    => $product->thumbnail ? asset('storage/' . $product->thumbnail) : null,
            'image'        => $this->formatImages($product->image),
            'category'     => $product->category,
            'subcategory'  => $product->subcategory,
            'tamaño'       => $product->tamaño,
            'colors'       => $product->colors->map(fn($color) => $this->formatColorDetailed($color)),
        ];
    }

    /**
     * Formatea un color con información básica.
     */
    protected function formatColorBasic($color): array
    {
        return [
            'id'         => $color->id,
            'name'       => $color->name,
            'code'       => $color->code,
            'stock'      => $color->stock,
            'is_default' => $color->is_default,
            'image'      => $color->image ? asset('storage/' . $color->image) : null,
        ];
    }

    /**
     * Formatea un color con información detallada.
     */
    protected function formatColorDetailed($color): array
    {
        return [
            'id'         => $color->id,
            'name'       => $color->name,
            'code'       => $color->code,
            'stock'      => $color->stock,
            'is_default' => $color->is_default,
            'image'      => $color->image ? asset('storage/' . $color->image) : null,
            'gallery'    => $this->formatImages($color->gallery),
        ];
    }

    /**
     * Formatea un array de imágenes agregando la URL completa.
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