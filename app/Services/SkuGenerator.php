<?php

namespace App\Services;

use App\Models\Product;

class SkuGenerator
{
    /**
     * Genera el siguiente SKU automáticamente.
     * Formato: SKU-00001, SKU-00002, etc.
     */
    public function generate(string $prefix = 'SKU-'): string
    {
        $skus = Product::where('sku', 'like', $prefix . '%')
            ->pluck('sku')
            ->map(function ($sku) use ($prefix) {
                $number = str_replace($prefix, '', $sku);
                return is_numeric($number) ? (int) $number : 0;
            })
            ->filter(fn($num) => $num > 0);

        if ($skus->isEmpty()) {
            return $prefix . '00001';
        }

        $nextNumber = $skus->max() + 1;

        return $prefix . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    }
}