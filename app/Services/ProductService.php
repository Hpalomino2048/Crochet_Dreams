<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProductService
{
    public function __construct(
        protected SkuGenerator $skuGenerator
    ) {}

    /**
     * Valida los datos del producto.
     */
    public function validate(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'sku'          => ['nullable', 'string', 'max:100', Rule::unique('products', 'sku')->ignore($ignoreId)],
            'name'         => ['required', 'string', 'max:255'],
            'slug'         => ['nullable', 'string', 'max:255', Rule::unique('products', 'slug')->ignore($ignoreId)],
            'descriptions' => ['nullable', 'string'],
            'price'        => ['required', 'numeric', 'min:0', 'max:9999999.99'],
            'currency'     => ['nullable', 'string', 'size:3'],
            'stock'        => ['required', 'integer', 'min:0'],
            'weight'       => ['nullable', 'numeric', 'min:0', 'max:9999.999'],
            'thumbnail'    => ['nullable', 'file', 'image', 'mimes:jpeg,jpg,png,webp', 'max:4096'],
            'image'        => ['nullable', 'array', 'max:10'],
            'image.*'      => ['file', 'image', 'mimes:jpeg,jpg,png,webp', 'max:4096'],
            'category'     => ['required', 'string', 'max:120'],
            'subcategory'  => ['nullable', 'string', 'max:100'],
            'tamaño'       => ['required', 'string', 'max:50'],
        ]);
    }

    /**
     * Crea un nuevo producto.
     */
    public function create(array $validated, Request $request): Product
    {
        $data = $this->prepareProductData($validated, $request);
        
        return Product::create($data);
    }

    /**
     * Actualiza un producto existente.
     */
    public function update(Product $product, array $validated, Request $request): void
    {
        $data = [
            'sku'          => $validated['sku'] ?? $product->sku,
            'name'         => $validated['name'],
            'slug'         => $validated['slug'] ?? Str::slug($validated['name']),
            'descriptions' => $validated['descriptions'] ?? null,
            'price'        => $validated['price'],
            'currency'     => $validated['currency'] ?? 'MXN',
            'stock'        => $validated['stock'],
            'weight'       => $validated['weight'] ?? null,
            'category'     => $validated['category'],
            'subcategory'  => $validated['subcategory'] ?? null,
            'tamaño'       => $validated['tamaño'],
        ];

        // El manejo de archivos se delega al trait
        $product->update($data);
    }

    /**
     * Prepara los datos del producto para creación.
     */
    private function prepareProductData(array $validated, Request $request): array
    {
        return [
            'sku'          => $validated['sku'] ?: $this->skuGenerator->generate(),
            'name'         => $validated['name'],
            'slug'         => $validated['slug'] ?: Str::slug($validated['name']),
            'descriptions' => $validated['descriptions'] ?? null,
            'price'        => $validated['price'],
            'currency'     => $validated['currency'] ?? 'MXN',
            'stock'        => $validated['stock'],
            'weight'       => $validated['weight'] ?? null,
            'thumbnail'    => $this->handleThumbnail($request),
            'image'        => $this->handleImages($request),
            'category'     => $validated['category'],
            'subcategory'  => $validated['subcategory'] ?? null,
            'tamaño'       => $validated['tamaño'],
        ];
    }

    /**
     * Maneja la subida del thumbnail.
     */
    private function handleThumbnail(Request $request): ?string
    {
        if ($request->hasFile('thumbnail') && $request->file('thumbnail')->isValid()) {
            return $request->file('thumbnail')->store('products/thumbnails', 'public');
        }
        return null;
    }

    /**
     * Maneja la subida de imágenes múltiples.
     */
    private function handleImages(Request $request): ?array
    {
        if (!$request->hasFile('image')) {
            return null;
        }

        $imagesPaths = [];
        foreach ($request->file('image') as $img) {
            if ($img && $img->isValid()) {
                $imagesPaths[] = $img->store('products/images', 'public');
            }
        }

        return !empty($imagesPaths) ? $imagesPaths : null;
    }
}