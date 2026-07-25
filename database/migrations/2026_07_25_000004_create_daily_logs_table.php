<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->boolean('feierabend')->nullable();
            $table->boolean('mittag_vorbereitet')->nullable();
            $table->string('naschen')->nullable();
            $table->unsignedTinyInteger('craving')->nullable();
            $table->unsignedTinyInteger('schlaf')->nullable();
            $table->unsignedTinyInteger('energie')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_logs');
    }
};
