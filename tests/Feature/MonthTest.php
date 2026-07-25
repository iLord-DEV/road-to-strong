<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\BodyMeasurement;
use App\Models\DailyLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MonthTest extends TestCase
{
    use RefreshDatabase;

    public function test_month_page_shows_aggregates(): void
    {
        $user = User::factory()->create();

        BodyMeasurement::create([
            'user_id' => $user->id,
            'withings_grpid' => 1,
            'measured_at' => now()->startOfMonth()->addDays(2),
            'weight_kg' => 78.0,
            'raw' => [],
        ]);
        BodyMeasurement::create([
            'user_id' => $user->id,
            'withings_grpid' => 2,
            'measured_at' => now()->startOfMonth()->subMonth()->addDays(2),
            'weight_kg' => 79.0,
            'raw' => [],
        ]);

        Activity::create([
            'user_id' => $user->id,
            'strava_id' => 1,
            'name' => 'Ride',
            'sport_type' => 'Ride',
            'started_at' => now()->startOfMonth()->addDays(3),
            'moving_time_s' => 5400,
            'elapsed_time_s' => 5400,
            'raw' => [],
        ]);

        DailyLog::create([
            'user_id' => $user->id,
            'date' => now()->startOfMonth()->addDays(2),
            'feierabend' => true,
            'naschen' => 'keines',
        ]);

        $this->actingAs($user)->get('/monat')
            ->assertOk()
            ->assertSee('78,0')
            ->assertSee('−1,0 kg zum Vormonat')
            ->assertSee('1:30 h')
            ->assertSee('Naschfreie Tage');
    }

    public function test_month_page_without_data(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/monat')
            ->assertOk()
            ->assertSee('Keine Messung in diesem Monat.')
            ->assertSee('Diesen Monat noch nichts erfasst.');
    }
}
