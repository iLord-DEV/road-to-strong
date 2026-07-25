<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('body_measurements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('withings_grpid')->unique();
            $table->timestamp('measured_at');
            $table->decimal('weight_kg', 5, 2)->nullable();
            $table->decimal('fat_percent', 4, 1)->nullable();
            $table->decimal('fat_mass_kg', 5, 2)->nullable();
            $table->decimal('muscle_mass_kg', 5, 2)->nullable();
            $table->decimal('water_kg', 5, 2)->nullable();
            $table->decimal('bone_mass_kg', 4, 2)->nullable();
            $table->decimal('bmi', 4, 1)->nullable();
            $table->json('raw');
            $table->timestamps();

            $table->index(['user_id', 'measured_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('body_measurements');
    }
};
