<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\BodyMeasurement;
use App\Models\FtpEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_history_page_shows_charts_and_totals(): void
    {
        $user = User::factory()->create();

        foreach (range(0, 20) as $daysAgo) {
            BodyMeasurement::create([
                'user_id' => $user->id,
                'withings_grpid' => $daysAgo + 1,
                'measured_at' => now()->subDays($daysAgo)->setTime(7, 0),
                'weight_kg' => 78.0 + $daysAgo * 0.05,
                'fat_percent' => 20.0,
                'raw' => [],
            ]);
        }

        Activity::create([
            'user_id' => $user->id,
            'strava_id' => 1,
            'name' => 'Ride',
            'sport_type' => 'Ride',
            'started_at' => now()->subDays(3),
            'moving_time_s' => 7200,
            'elapsed_time_s' => 7200,
            'distance_m' => 60000,
            'elevation_gain_m' => 800,
            'kilojoules' => 1500,
            'raw' => [],
        ]);

        $this->actingAs($user)->get('/verlauf')
            ->assertOk()
            ->assertSee('Gewicht')
            ->assertSee('Körperfett')
            ->assertSee('Training pro Monat')
            ->assertSee('polyline', false)
            ->assertSee('FTP-Historie');
    }

    public function test_history_page_without_data(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/verlauf')
            ->assertOk()
            ->assertSee('Noch nicht genug Messungen');
    }

    public function test_rolling_mean_smooths_daily_values(): void
    {
        $user = User::factory()->create();

        // 7 days: six at 78.0, one outlier at 85.0 -> rolling mean stays close to 79
        foreach (range(0, 6) as $daysAgo) {
            BodyMeasurement::create([
                'user_id' => $user->id,
                'withings_grpid' => $daysAgo + 1,
                'measured_at' => now()->subDays($daysAgo)->setTime(7, 0),
                'weight_kg' => $daysAgo === 3 ? 85.0 : 78.0,
                'raw' => [],
            ]);
        }

        $response = $this->actingAs($user)->get('/verlauf?zeitraum=6m');
        $response->assertOk();

        // Headline shows the latest rolling mean: (6*78 + 85) / 7 = 79.0
        $response->assertSee('79,0');
    }
}
