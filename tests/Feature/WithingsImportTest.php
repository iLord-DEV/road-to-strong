<?php

namespace Tests\Feature;

use App\Models\BodyMeasurement;
use App\Models\OauthToken;
use App\Models\User;
use App\Modules\Withings\MeasurementImporter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WithingsImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_measurements_are_imported_with_unit_conversion(): void
    {
        $user = User::factory()->create();

        OauthToken::create([
            'user_id' => $user->id,
            'provider' => 'withings',
            'access_token' => 'token',
            'refresh_token' => 'refresh',
            'expires_at' => now()->addHour(),
        ]);

        Http::fake([
            'wbsapi.withings.net/measure' => Http::response([
                'status' => 0,
                'body' => [
                    'measuregrps' => [
                        [
                            'grpid' => 555,
                            'date' => 1784873400,
                            'measures' => [
                                // value * 10^unit: 78400 * 10^-3 = 78.40 kg
                                ['type' => 1, 'value' => 78400, 'unit' => -3],
                                ['type' => 6, 'value' => 241, 'unit' => -1],
                                ['type' => 76, 'value' => 55200, 'unit' => -3],
                            ],
                        ],
                    ],
                ],
            ]),
        ]);

        $count = app(MeasurementImporter::class)->import($user);

        $this->assertSame(1, $count);

        $measurement = BodyMeasurement::firstWhere('withings_grpid', 555);
        $this->assertSame(78.4, $measurement->weight_kg);
        $this->assertSame(24.1, $measurement->fat_percent);
        $this->assertSame(55.2, $measurement->muscle_mass_kg);
    }
}
