<?php

namespace App\Modules\Withings;

use App\Models\BodyMeasurement;
use App\Models\User;
use Illuminate\Support\Carbon;

class MeasurementImporter
{
    // Withings measure types
    private const WEIGHT = 1;

    private const FAT_PERCENT = 6;

    private const FAT_MASS = 8;

    private const MUSCLE_MASS = 76;

    private const WATER = 77;

    private const BONE_MASS = 88;

    public function __construct(
        private readonly WithingsClient $client,
    ) {}

    /**
     * Import all measurement groups newer than the latest stored one.
     * Re-fetches a 30-day overlap so edited measurements are updated.
     */
    public function import(User $user): int
    {
        $token = $user->oauthToken('withings');

        if ($token === null) {
            return 0;
        }

        $token = $this->client->freshToken($token);

        $latest = BodyMeasurement::where('user_id', $user->id)->max('measured_at');
        $startdate = $latest !== null
            ? Carbon::parse($latest)->subDays(30)->timestamp
            : null;

        $imported = 0;

        foreach ($this->client->measurements($token, $startdate) as $group) {
            $this->upsert($user, $group);
            $imported++;
        }

        return $imported;
    }

    /**
     * @param  array<string, mixed>  $group
     */
    private function upsert(User $user, array $group): BodyMeasurement
    {
        $values = [];

        foreach ($group['measures'] ?? [] as $measure) {
            // Real value = value * 10^unit (unit is a negative exponent)
            $values[$measure['type']] = round($measure['value'] * pow(10, $measure['unit']), 2);
        }

        $weight = $values[self::WEIGHT] ?? null;
        $height = config('services.withings.height_m');

        return BodyMeasurement::updateOrCreate(
            ['withings_grpid' => $group['grpid']],
            [
                'user_id' => $user->id,
                'measured_at' => Carbon::createFromTimestamp($group['date'], config('app.timezone')),
                'weight_kg' => $weight,
                'fat_percent' => $values[self::FAT_PERCENT] ?? null,
                'fat_mass_kg' => $values[self::FAT_MASS] ?? null,
                'muscle_mass_kg' => $values[self::MUSCLE_MASS] ?? null,
                'water_kg' => $values[self::WATER] ?? null,
                'bone_mass_kg' => $values[self::BONE_MASS] ?? null,
                'bmi' => ($weight !== null && $height) ? round($weight / ($height ** 2), 1) : null,
                'raw' => $group,
            ],
        );
    }
}
