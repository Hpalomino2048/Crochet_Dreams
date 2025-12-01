<?php
// ============================================
// MIGRACIÓN 1: Eliminar columnas color1 y color2
// Nombre: 2024_11_17_000001_remove_colors_from_products_table.php
// ============================================

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
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['color1', 'color2']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Restaurar las columnas en caso de rollback
            $table->string('color1')->nullable();
            $table->string('color2')->nullable();
        });
    }
};
