<?php
// database/migrations/2025_11_25_181324_update_product_stock_automatically_with_triggers.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Primero, eliminar la foreign key existente
        Schema::table('product_colors', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
        });

        // Recrearla con cascada en UPDATE y DELETE
        Schema::table('product_colors', function (Blueprint $table) {
            $table->foreign('product_id')
                  ->references('id')
                  ->on('products')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
        });

        // Crear la FUNCIÓN que actualizará el stock
        DB::unprepared('
            CREATE OR REPLACE FUNCTION update_product_stock()
            RETURNS TRIGGER AS $$
            BEGIN
                -- Para INSERT y UPDATE usamos NEW.product_id
                IF (TG_OP = \'INSERT\' OR TG_OP = \'UPDATE\') THEN
                    UPDATE products 
                    SET stock = (
                        SELECT COALESCE(SUM(stock), 0) 
                        FROM product_colors 
                        WHERE product_id = NEW.product_id
                    )
                    WHERE id = NEW.product_id;
                    RETURN NEW;
                -- Para DELETE usamos OLD.product_id
                ELSIF (TG_OP = \'DELETE\') THEN
                    UPDATE products 
                    SET stock = (
                        SELECT COALESCE(SUM(stock), 0) 
                        FROM product_colors 
                        WHERE product_id = OLD.product_id
                    )
                    WHERE id = OLD.product_id;
                    RETURN OLD;
                END IF;
            END;
            $$ LANGUAGE plpgsql;
        ');

        // Crear los TRIGGERS que llaman a la función
        DB::unprepared('
            CREATE TRIGGER update_product_stock_after_color_insert
            AFTER INSERT ON product_colors
            FOR EACH ROW
            EXECUTE FUNCTION update_product_stock();
        ');

        DB::unprepared('
            CREATE TRIGGER update_product_stock_after_color_update
            AFTER UPDATE ON product_colors
            FOR EACH ROW
            EXECUTE FUNCTION update_product_stock();
        ');

        DB::unprepared('
            CREATE TRIGGER update_product_stock_after_color_delete
            AFTER DELETE ON product_colors
            FOR EACH ROW
            EXECUTE FUNCTION update_product_stock();
        ');
    }

    public function down(): void
    {
        // Eliminar los triggers
        DB::unprepared('DROP TRIGGER IF EXISTS update_product_stock_after_color_insert ON product_colors');
        DB::unprepared('DROP TRIGGER IF EXISTS update_product_stock_after_color_update ON product_colors');
        DB::unprepared('DROP TRIGGER IF EXISTS update_product_stock_after_color_delete ON product_colors');
        
        // Eliminar la función
        DB::unprepared('DROP FUNCTION IF EXISTS update_product_stock()');

        // Restaurar la foreign key sin cascade
        Schema::table('product_colors', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
        });

        Schema::table('product_colors', function (Blueprint $table) {
            $table->foreign('product_id')
                  ->references('id')
                  ->on('products')
                  ->onDelete('cascade');
        });
    }
};