<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\OauthToken;
use App\Models\User;
use App\Modules\Strava\ActivityImporter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class StravaImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_activities_are_imported_and_mapped(): void
    {
        $user = User::factory()->create();

        OauthToken::create([
            'user_id' => $user->id,
            'provider' => 'strava',
            'access_token' => 'token',
            'refresh_token' => 'refresh',
            'expires_at' => now()->addHour(),
        ]);

        Http::fake([
            'www.strava.com/api/v3/athlete/activities*' => Http::response([
                [
                    'id' => 123456,
                    'name' => 'Morgenrunde',
                    'sport_type' => 'Ride',
                    'start_date' => '2026-07-24T06:30:00Z',
                    'moving_time' => 7200,
                    'elapsed_time' => 7500,
                    'distance' => 63000.0,
                    'total_elevation_gain' => 1100.0,
                    'average_heartrate' => 138.5,
                    'average_watts' => 185.0,
                    'weighted_average_watts' => 201,
                    'kilojoules' => 1332.0,
                    'suffer_score' => 87.0,
                    'trainer' => false,
                ],
            ]),
        ]);

        $count = app(ActivityImporter::class)->import($user);

        $this->assertSame(1, $count);

        $activity = Activity::firstWhere('strava_id', 123456);
        $this->assertSame('Ride', $activity->sport_type);
        $this->assertSame(63000.0, $activity->distance_m);
        $this->assertSame(201, $activity->np_watts);
        $this->assertFalse($activity->indoor);
        // 06:30 UTC is 08:30 in Europe/Berlin (CEST)
        $this->assertSame('2026-07-24 08:30', $activity->started_at->format('Y-m-d H:i'));
    }

    public function test_virtual_rides_are_marked_indoor(): void
    {
        $user = User::factory()->create();

        OauthToken::create([
            'user_id' => $user->id,
            'provider' => 'strava',
            'access_token' => 'token',
            'refresh_token' => 'refresh',
            'expires_at' => now()->addHour(),
        ]);

        Http::fake([
            'www.strava.com/api/v3/athlete/activities*' => Http::response([
                [
                    'id' => 99,
                    'name' => 'Kickr Session',
                    'sport_type' => 'VirtualRide',
                    'start_date' => '2026-07-24T18:00:00Z',
                    'moving_time' => 3600,
                    'elapsed_time' => 3600,
                    'trainer' => true,
                ],
            ]),
        ]);

        app(ActivityImporter::class)->import($user);

        $this->assertTrue(Activity::firstWhere('strava_id', 99)->indoor);
    }
}
