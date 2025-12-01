<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('category', 120)->nullable()->after('slug');
            $table->string('color1', 50)->nullable()->after('category');
            $table->string('color2', 50)->nullable()->after('color1');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['category', 'color1', 'color2']);
        });
    }

};
