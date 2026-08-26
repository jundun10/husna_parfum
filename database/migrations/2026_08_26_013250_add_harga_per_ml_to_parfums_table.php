<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('parfums', function (Blueprint $table) {
            $table->decimal('harga_per_ml', 15, 2)
                ->default(0)
                ->after('harga');
        });
    }

    public function down(): void
    {
        Schema::table('parfums', function (Blueprint $table) {
            $table->dropColumn('harga_per_ml');
        });
    }
};