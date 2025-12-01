<?php

namespace App\Http\Traits;

use App\Models\Product;
use App\Models\ProductColor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

trait HandlesProductsColors
{
    /**
     * Crea los colores del producto.
     */
    protected function createProductColors(Product $product, array $colors, Request $request): void
    {
        foreach ($colors as $index => $colorData) {
            $data = [
                'product_id' => $product->id,
                'name'       => $colorData['name'] ?? '',
                'code'       => $colorData['code'] ?? null,
                'stock'      => $colorData['stock'] ?? 0,
                'is_default' => $colorData['is_default'] ?? false,
            ];

            if ($request->hasFile("colors.{$index}.image")) {
                $data['image'] = $request->file("colors.{$index}.image")
                    ->store('products/colors', 'public');
            }

            if ($request->hasFile("colors.{$index}.gallery")) {
                $data['gallery'] = $this->uploadColorGallery($request, $index);
            }

            ProductColor::create($data);
        }
    }

    /**
     * Actualiza los colores del producto.
     */
    protected function updateProductColors(Product $product, array $colors, Request $request): void
    {
        $existingColorIds = [];

        foreach ($colors as $index => $colorData) {
            $colorId = $colorData['id'] ?? null;

            if ($colorId && $color = ProductColor::find($colorId)) {
                $existingColorIds[] = $this->updateExistingColor($color, $colorData, $request, $index);
            } else {
                $newColor = $this->createNewColor($product, $colorData, $request, $index);
                $existingColorIds[] = $newColor->id;
            }
        }

        $this->deleteRemovedColors($product, $existingColorIds);
    }

    /**
     * Actualiza un color existente.
     */
    protected function updateExistingColor(ProductColor $color, array $colorData, Request $request, int $index): int
    {
        $data = [
            'name'       => $colorData['name'] ?? $color->name,
            'code'       => $colorData['code'] ?? $color->code,
            'stock'      => $colorData['stock'] ?? $color->stock,
            'is_default' => $colorData['is_default'] ?? false,
        ];

        if ($request->hasFile("colors.{$index}.image")) {
            $this->deleteFile($color->image);
            $data['image'] = $request->file("colors.{$index}.image")
                ->store('products/colors', 'public');
        }

        if ($request->hasFile("colors.{$index}.gallery")) {
            if ($color->gallery) {
                foreach ($color->gallery as $oldImage) {
                    $this->deleteFile($oldImage);
                }
            }
            $data['gallery'] = $this->uploadColorGallery($request, $index);
        }

        $color->update($data);
        return $color->id;
    }

    /**
     * Crea un nuevo color.
     */
    protected function createNewColor(Product $product, array $colorData, Request $request, int $index): ProductColor
    {
        $data = [
            'product_id' => $product->id,
            'name'       => $colorData['name'] ?? '',
            'code'       => $colorData['code'] ?? null,
            'stock'      => $colorData['stock'] ?? 0,
            'is_default' => $colorData['is_default'] ?? false,
        ];

        if ($request->hasFile("colors.{$index}.image")) {
            $data['image'] = $request->file("colors.{$index}.image")
                ->store('products/colors', 'public');
        }

        if ($request->hasFile("colors.{$index}.gallery")) {
            $data['gallery'] = $this->uploadColorGallery($request, $index);
        }

        return ProductColor::create($data);
    }

    /**
     * Sube la galería de un color.
     */
    protected function uploadColorGallery(Request $request, int $index): ?array
    {
        $galleryPaths = [];
        foreach ($request->file("colors.{$index}.gallery") as $img) {
            if ($img && $img->isValid()) {
                $galleryPaths[] = $img->store('products/colors/gallery', 'public');
            }
        }
        return !empty($galleryPaths) ? $galleryPaths : null;
    }

    /**
     * Elimina colores que ya no están en la lista.
     */
    protected function deleteRemovedColors(Product $product, array $existingColorIds): void
    {
        $colorsToDelete = ProductColor::where('product_id', $product->id)
            ->whereNotIn('id', $existingColorIds)
            ->get();

        foreach ($colorsToDelete as $color) {
            $this->deleteColorFiles($color);
            $color->delete();
        }
    }

    /**
     * Elimina todos los archivos asociados a un color.
     */
    protected function deleteColorFiles(ProductColor $color): void
    {
        $this->deleteFile($color->image);
        
        if ($color->gallery && is_array($color->gallery)) {
            foreach ($color->gallery as $imagePath) {
                $this->deleteFile($imagePath);
            }
        }
    }

    /**
     * Elimina una imagen de la galería de un color.
     */
    protected function removeColorGallery(ProductColor $color, int $index): array
    {
        $gallery = $color->gallery ?? [];
        
        if (!isset($gallery[$index])) {
            return [
                'success' => false,
                'message' => 'Imagen no encontrada'
            ];
        }
        
        $this->deleteFile($gallery[$index]);
        array_splice($gallery, $index, 1);
        
        $color->update([
            'gallery' => !empty($gallery) ? array_values($gallery) : null
        ]);
        
        return [
            'success' => true,
            'message' => 'Imagen eliminada correctamente',
            'remaining_images' => count($gallery)
        ];
    }

    /**
     * Elimina un color del producto.
     */
    protected function deleteColor(ProductColor $color): array
    {
        $colorCount = ProductColor::where('product_id', $color->product_id)->count();
        
        if ($colorCount <= 1) {
            return [
                'success' => false,
                'message' => 'No se puede eliminar el único color del producto'
            ];
        }
        
        if ($color->is_default) {
            $newDefault = ProductColor::where('product_id', $color->product_id)
                ->where('id', '!=', $color->id)
                ->first();
            
            if ($newDefault) {
                $newDefault->update(['is_default' => true]);
            }
        }
        
        $this->deleteColorFiles($color);
        $color->delete();
        
        return [
            'success' => true,
            'message' => 'Color eliminado correctamente'
        ];
    }
}