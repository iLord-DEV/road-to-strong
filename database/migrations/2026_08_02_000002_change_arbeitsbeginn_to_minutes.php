<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daily_logs', function (Blueprint $table) {
            // Minutes since midnight (360-600), allows 30-minute steps
            $table->unsignedSmallInteger('arbeitsbeginn')->nullable()->change();
        });

        // Convert values recorded as plain hours (6-10) to minutes
        DB::table('daily_logs')
            ->whereNotNull('arbeitsbeginn')
            ->where('arbeitsbeginn', '<=', 10)
            ->update(['arbeitsbeginn' => DB::raw('arbeitsbeginn * 60')]);
    }

    public function down(): void
    {
        DB::table('daily_logs')
            ->whereNotNull('arbeitsbeginn')
            ->update(['arbeitsbeginn' => DB::raw('arbeitsbeginn / 60')]);

        Schema::table('daily_logs', function (Blueprint $table) {
            $table->unsignedTinyInteger('arbeitsbeginn')->nullable()->change();
        });
    }
};
