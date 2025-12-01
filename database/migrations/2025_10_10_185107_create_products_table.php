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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('sku', 64)->unique();
            $table->string('name', 160);
            $table->string('slug', 180)->unique();
            $table->text('descriptions')->nullable();
            $table->decimal('price', 12, 2);
            $table->char('currency', 3)->default('MXN');
            $table->decimal('weight', 10, 3)->nullable(); // en kg
            $table->string('thumbnail', 2048)->nullable();
            $table->json('image')->nullable();
            $table->integer('stock')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
