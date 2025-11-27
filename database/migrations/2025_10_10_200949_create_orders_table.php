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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('cart_id')->nullable()->constrained('carts')->cascadeOnDelete();
            
            // Datos de contacto (snapshot)
            $table->string('buyer_email', 255);
            $table->string('buyer_name', 160);
            
            // Direcciones (snapshot)
            $table->json('shipping_address');
            $table->json('billing_address')->nullable();
            
            // Montos
            $table->decimal('subtotal', 12, 2);
            $table->decimal('discount_total', 12, 2)->default(0.00);
            $table->decimal('shipping_total', 12, 2)->default(0.00);
            $table->decimal('tax_total', 12, 2)->default(0.00);
            $table->decimal('grand_total', 12, 2);
            $table->char('currency', 3)->default('MXN');
            
            // Estado y pago
            $table->enum('status', ['pending', 'paid', 'processing', 'shipped', 'completed', 'cancelled', 'refunded'])->default('pending');
            $table->string('payment_method', 60)->nullable();
            
            // Timestamps
            $table->timestamp('placed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};