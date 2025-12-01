<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_colors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')
                  ->constrained('products')
                  ->onDelete('cascade');
            
            // Nombre del color (ej. "Rojo", "Azul pastel", etc.)
            $table->string('name', 50);
            
            // Código hexadecimal del color (opcional pero recomendado)
            $table->string('code', 7)->nullable()->comment('Ej: #FF0000');
            
            // Stock disponible para este color específico
            $table->integer('stock')->default(0);
            
            // Indica si este color es el predeterminado
            $table->boolean('is_default')->default(false);
            
            // Ruta de la imagen específica para este color
            $table->string('image')->nullable()->comment('Ruta de la imagen del producto en este color');
            
            // Galería de imágenes adicionales (JSON)
            $table->json('gallery')->nullable()->comment('Array de rutas de imágenes adicionales');
            
            $table->timestamps();
            
            // Índices para mejorar el rendimiento
            $table->index('product_id');
            $table->index('is_default');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_colors');
    }
};