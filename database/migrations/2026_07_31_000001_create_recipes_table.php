<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('category'); // morgens | mittags | abends | snack
            $table->string('name');
            $table->string('description')->nullable();
            $table->unsignedSmallInteger('kcal')->nullable();
            $table->unsignedTinyInteger('stars_geschmack')->nullable();
            $table->unsignedTinyInteger('stars_aufwand')->nullable();
            $table->unsignedTinyInteger('stars_kalorien')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recipes');
    }
};
