<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exercises', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('workout'); // A | B
            $table->unsignedInteger('position')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('strength_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('workout'); // A | B
            $table->date('performed_at');
            $table->timestamps();

            $table->index(['user_id', 'performed_at']);
        });

        Schema::create('strength_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('strength_session_id')->constrained()->cascadeOnDelete();
            $table->foreignId('exercise_id')->constrained()->cascadeOnDelete();
            $table->decimal('weight_kg', 5, 2)->nullable();
            $table->unsignedSmallInteger('reps');
            $table->unsignedTinyInteger('sets');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('strength_entries');
        Schema::dropIfExists('strength_sessions');
        Schema::dropIfExists('exercises');
    }
};
