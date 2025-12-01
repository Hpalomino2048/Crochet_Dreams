<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * NOTA: Esta migración modifica la tabla 'order_items' existente
     * para integrarla con product_colors
     */
    public function up(): void
    {
        if (!Schema::hasTable('order_items')) {
            // Si no existe, crear la tabla completa
            Schema::create('order_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained()->onDelete('cascade');
                $table->foreignId('product_id')->constrained()->onDelete('restrict');
                $table->foreignId('product_color_id')->nullable()
                    ->constrained('product_colors')
                    ->onDelete('set null');
                
                // Información del producto al momento de la compra
                $table->string('product_name', 160);
                $table->string('color_name', 50)->nullable(); // Nombre del color al momento de compra
                $table->string('sku', 64)->nullable();
                $table->decimal('product_price', 12, 2);
                $table->integer('quantity');
                $table->decimal('subtotal', 12, 2);
                
                $table->timestamps();
                
                // Índices
                $table->index('order_id');
                $table->index('product_id');
                $table->index('product_color_id');
            });
        } else {
            // Si ya existe, agregar solo los campos que faltan
            Schema::table('order_items', function (Blueprint $table) {
                // Verificar y agregar product_color_id si no existe
                if (!Schema::hasColumn('order_items', 'product_color_id')) {
                    $table->foreignId('product_color_id')->nullable()
                        ->after('product_id')
                        ->constrained('product_colors')
                        ->onDelete('set null');
                    $table->index('product_color_id');
                }
                
                // Verificar y agregar color_name si no existe
                if (!Schema::hasColumn('order_items', 'color_name')) {
                    $table->string('color_name', 50)->nullable()->after('product_name');
                }
                
                // Verificar y agregar sku si no existe
                if (!Schema::hasColumn('order_items', 'sku')) {
                    $table->string('sku', 64)->nullable()->after('color_name');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            if (Schema::hasColumn('order_items', 'product_color_id')) {
                $table->dropForeign(['product_color_id']);
                $table->dropColumn('product_color_id');
            }
            if (Schema::hasColumn('order_items', 'color_name')) {
                $table->dropColumn('color_name');
            }
            if (Schema::hasColumn('order_items', 'sku')) {
                $table->dropColumn('sku');
            }
        });
    }
};