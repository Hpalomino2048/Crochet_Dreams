<?php

namespace App\Http\Traits;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

trait HandlesProductFiles
{
    /**
     * Elimina un archivo del storage.
     */
    protected function deleteFile(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    /**
     * Elimina todos los archivos asociados a un producto.
     */
    protected function deleteProductFiles(Product $product): void
    {
        $this->deleteFile($product->thumbnail);
        
        if ($product->image && is_array($product->image)) {
            foreach ($product->image as $imagePath) {
                $this->deleteFile($imagePath);
            }
        }
    }

    /**
     * Elimina una imagen específica de la galería del producto.
     */
    protected function removeProductImage(Product $product, int $index): array
    {
        $images = $product->image ?? [];
        
        if (!isset($images[$index])) {
            return [
                'success' => false,
                'message' => 'Imagen no encontrada'
            ];
        }
        
        $this->deleteFile($images[$index]);
        array_splice($images, $index, 1);
        
        $product->update([
            'image' => !empty($images) ? array_values($images) : null
        ]);
        
        return [
            'success' => true,
            'message' => 'Imagen eliminada correctamente',
            'remaining_images' => count($images)
        ];
    }

    /**
     * Limpia los archivos subidos en caso de error.
     */
    protected function cleanupUploadedFiles(Request $request, array $validated): void
    {
        if ($request->hasFile('thumbnail')) {
            $this->deleteFile($validated['thumbnail'] ?? null);
        }
        
        if ($request->hasFile('image')) {
            foreach ($validated['image'] ?? [] as $path) {
                $this->deleteFile($path);
            }
        }
    }

    /**
     * Actualiza el thumbnail del producto.
     */
    protected function updateThumbnail(Product $product, Request $request): ?string
    {
        if ($request->hasFile('thumbnail') && $request->file('thumbnail')->isValid()) {
            $this->deleteFile($product->thumbnail);
            return $request->file('thumbnail')->store('products/thumbnails', 'public');
        }
        
        return null;
    }

    /**
     * Actualiza las imágenes del producto (reemplaza todas).
     */
    protected function updateImages(Product $product, Request $request): ?array
    {
        if (!$request->hasFile('image')) {
            return null;
        }
        
        // Eliminar imágenes anteriores
        if ($product->image && is_array($product->image)) {
            foreach ($product->image as $oldImage) {
                $this->deleteFile($oldImage);
            }
        }
        
        // Subir nuevas imágenes
        $imagesPaths = [];
        foreach ($request->file('image') as $img) {
            if ($img && $img->isValid()) {
                $imagesPaths[] = $img->store('products/images', 'public');
            }
        }
        
        return !empty($imagesPaths) ? $imagesPaths : null;
    }
}