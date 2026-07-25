<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('strava_id')->unique();
            $table->string('name');
            $table->string('sport_type');
            $table->timestamp('started_at');
            $table->unsignedInteger('moving_time_s')->default(0);
            $table->unsignedInteger('elapsed_time_s')->default(0);
            $table->float('distance_m')->nullable();
            $table->float('elevation_gain_m')->nullable();
            $table->float('avg_heartrate')->nullable();
            $table->float('max_heartrate')->nullable();
            $table->float('avg_watts')->nullable();
            $table->unsignedInteger('np_watts')->nullable();
            $table->float('calories')->nullable();
            $table->float('kilojoules')->nullable();
            $table->float('relative_effort')->nullable();
            $table->boolean('indoor')->default(false);
            $table->json('raw');
            $table->timestamps();

            $table->index(['user_id', 'started_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
