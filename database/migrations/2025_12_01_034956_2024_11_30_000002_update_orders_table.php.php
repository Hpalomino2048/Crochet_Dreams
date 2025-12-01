<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * NOTA: Esta migración modifica la tabla 'orders' existente
     * para añadir campos adicionales necesarios
     */
    public function up(): void
    {
        // Verificar si la tabla ya existe
        if (!Schema::hasTable('orders')) {
            // Si no existe, crearla con todos los campos
            Schema::create('orders', function (Blueprint $table) {
                $table->id();
                $table->string('order_number', 50)->unique();
                
                // Relaciones - Compatible con tu BD actual
                $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
                $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('set null');
                $table->foreignId('cart_id')->nullable()->constrained('carts')->onDelete('cascade');
                
                // Estados del pedido
                $table->enum('status', [
                    'pending',      // Pendiente
                    'paid',         // Pagada
                    'processing',   // En procesamiento
                    'shipped',      // Enviada
                    'delivered',    // Entregada
                    'cancelled'     // Cancelada
                ])->default('pending');
                
                // Estados de pago
                $table->enum('payment_status', [
                    'pending',      // Pendiente de pago
                    'paid',         // Pagada
                    'failed',       // Pago fallido
                    'refunded'      // Reembolsada
                ])->default('pending');
                
                // Información de pago
                $table->string('payment_method', 50)->nullable();
                $table->string('payment_id')->nullable();
                $table->timestamp('paid_at')->nullable();
                
                // Montos
                $table->decimal('subtotal', 12, 2);
                $table->decimal('shipping_cost', 12, 2)->default(0);
                $table->decimal('tax', 12, 2)->default(0);
                $table->decimal('discount', 12, 2)->default(0);
                $table->decimal('total', 12, 2);
                
                // Información de envío (compatibilidad con address)
                $table->string('shipping_full_name', 160)->nullable();
                $table->string('shipping_address', 200)->nullable();
                $table->string('shipping_city', 120)->nullable();
                $table->string('shipping_state', 120)->nullable();
                $table->string('shipping_postal_code', 20)->nullable();
                $table->char('shipping_country', 2)->default('MX');
                $table->string('shipping_phone', 30)->nullable();
                
                // Notas
                $table->text('notes')->nullable();
                $table->text('admin_notes')->nullable();
                
                $table->timestamps();
                
                // Índices
                $table->index('order_number');
                $table->index('user_id');
                $table->index('customer_id');
                $table->index('status');
                $table->index('payment_status');
                $table->index('created_at');
            });
        } else {
            // Si ya existe, agregar solo los campos que faltan
            Schema::table('orders', function (Blueprint $table) {
                // Verificar y agregar customer_id si no existe
                if (!Schema::hasColumn('orders', 'customer_id')) {
                    $table->foreignId('customer_id')->nullable()
                        ->after('user_id')
                        ->constrained('customers')
                        ->onDelete('set null');
                }
                
                // Verificar y agregar payment_method si no existe
                if (!Schema::hasColumn('orders', 'payment_method')) {
                    $table->string('payment_method', 50)->nullable()->after('payment_status');
                }
                
                // Verificar y agregar payment_id si no existe
                if (!Schema::hasColumn('orders', 'payment_id')) {
                    $table->string('payment_id')->nullable()->after('payment_method');
                }
                
                // Verificar y agregar paid_at si no existe
                if (!Schema::hasColumn('orders', 'paid_at')) {
                    $table->timestamp('paid_at')->nullable()->after('payment_id');
                }
                
                // Verificar y agregar notas si no existen
                if (!Schema::hasColumn('orders', 'notes')) {
                    $table->text('notes')->nullable();
                }
                
                if (!Schema::hasColumn('orders', 'admin_notes')) {
                    $table->text('admin_notes')->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Solo eliminar las columnas agregadas, no la tabla completa
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'customer_id')) {
                $table->dropForeign(['customer_id']);
                $table->dropColumn('customer_id');
            }
            if (Schema::hasColumn('orders', 'payment_method')) {
                $table->dropColumn('payment_method');
            }
            if (Schema::hasColumn('orders', 'payment_id')) {
                $table->dropColumn('payment_id');
            }
            if (Schema::hasColumn('orders', 'paid_at')) {
                $table->dropColumn('paid_at');
            }
            if (Schema::hasColumn('orders', 'notes')) {
                $table->dropColumn('notes');
            }
            if (Schema::hasColumn('orders', 'admin_notes')) {
                $table->dropColumn('admin_notes');
            }
        });
    }
};