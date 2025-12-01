<?php

// ============================================
// MODELO: Product
// Ubicación: app/Models/Product.php
// ============================================

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku',
        'name',
        'slug',
        'descriptions',
        'price',
        'currency',
        'weight',
        'thumbnail',
        'image',
        'stock',
        'category',
        'subcategory',
        'tamaño',
        'created_at',
        'updated_at',
    ];

    /**
     * Relación: Un producto tiene muchos colores
     */
    public function colors()
    {
        return $this->hasMany(ProductColor::class);
    }

    /**
     * Obtener el color predeterminado del producto
     */
    public function defaultColor()
    {
        return $this->hasOne(ProductColor::class)->where('is_default', true);
    }

    /**
     * Obtener colores disponibles (con stock)
     */
    public function availableColors()
    {
        return $this->hasMany(ProductColor::class)->where('stock', '>', 0);
    }
}

// ============================================
// MODELO: ProductColor
// Ubicación: app/Models/ProductColor.php
// ============================================

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductColor extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'name',
        'code',
        'stock',
        'is_default',
        'image',
        'gallery',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'stock' => 'integer',
        'gallery' => 'array', // Convierte JSON a array automáticamente
    ];

    /**
     * Relación: Un color pertenece a un producto
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Accessor: Obtener la URL completa de la imagen
     */
    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }

    /**
     * Accessor: Obtener URLs completas de la galería
     */
    public function getGalleryUrlsAttribute()
    {
        if (!$this->gallery) {
            return [];
        }

        return collect($this->gallery)->map(function ($image) {
            return asset('storage/' . $image);
        })->toArray();
    }
}
