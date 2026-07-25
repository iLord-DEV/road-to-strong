<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\BodyMeasurement;
use App\Models\DailyLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WeekTest extends TestCase
{
    use RefreshDatabase;

    public function test_week_page_shows_aggregates(): void
    {
        $user = User::factory()->create();

        BodyMeasurement::create([
            'user_id' => $user->id,
            'withings_grpid' => 1,
            'measured_at' => now()->startOfWeek()->addDay()->setTime(7, 0),
            'weight_kg' => 78.4,
            'raw' => [],
        ]);

        Activity::create([
            'user_id' => $user->id,
            'strava_id' => 1,
            'name' => 'Kickr',
            'sport_type' => 'VirtualRide',
            'started_at' => now()->startOfWeek()->addDays(1)->setTime(18, 0),
            'moving_time_s' => 3600,
            'elapsed_time_s' => 3600,
            'indoor' => true,
            'raw' => [],
        ]);

        DailyLog::create([
            'user_id' => $user->id,
            'date' => now()->startOfWeek()->addDay(),
            'feierabend' => true,
            'naschen' => 'keines',
            'schlaf' => 4,
        ]);

        $this->actingAs($user)->get('/woche')
            ->assertOk()
            ->assertSee('78,4')
            ->assertSee('1:00')
            ->assertSee('1 Einheit')
            ->assertSee('Feierabend eingehalten')
            ->assertSee('Keines 1');
    }

    public function test_week_page_without_data(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/woche')
            ->assertOk()
            ->assertSee('Keine Messung diese Woche.')
            ->assertSee('Diese Woche noch nichts erfasst.');
    }
}
