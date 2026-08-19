<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('keranjang_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('parfum_id')
                ->constrained('parfums')
                ->cascadeOnDelete();

            $table->unsignedInteger('jumlah')->default(1);

            $table->timestamps();

            $table->unique(['user_id', 'parfum_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('keranjang_items');
    }
};