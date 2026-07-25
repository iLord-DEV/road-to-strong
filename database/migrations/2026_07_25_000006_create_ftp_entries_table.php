<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ftp_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('watts');
            $table->date('tested_at');
            $table->timestamps();

            $table->index(['user_id', 'tested_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ftp_entries');
    }
};
