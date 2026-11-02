<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('favourites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('product_key', 100);
            $table->timestamps();

            $table->unique(['user_id', 'product_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('favourites');
    }
};
