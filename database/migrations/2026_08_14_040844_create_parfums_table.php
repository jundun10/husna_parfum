<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parfums', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->decimal('harga', 15, 2);
            $table->unsignedInteger('stok')->default(0);
            $table->string('foto')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parfums');
    }
};