<?php

namespace App\Modules\Strava;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Support\Carbon;

class ActivityImporter
{
    public function __construct(
        private readonly StravaClient $client,
    ) {}

    /**
     * Import all activities newer than the latest stored one.
     * Re-fetches a 7-day overlap so late uploads are not missed.
     */
    public function import(User $user): int
    {
        $token = $user->oauthToken('strava');

        if ($token === null) {
            return 0;
        }

        $token = $this->client->freshToken($token);

        $latest = Activity::where('user_id', $user->id)->max('started_at');
        $after = $latest !== null
            ? Carbon::parse($latest)->subDays(7)->timestamp
            : null;

        $imported = 0;
        $page = 1;

        do {
            $batch = $this->client->activities($token, $after, $page);

            foreach ($batch as $data) {
                $this->upsert($user, $data);
                $imported++;
            }

            $page++;
        } while (count($batch) === 100);

        return $imported;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function upsert(User $user, array $data): Activity
    {
        return Activity::updateOrCreate(
            ['strava_id' => $data['id']],
            [
                'user_id' => $user->id,
                'name' => $data['name'] ?? '',
                'sport_type' => $data['sport_type'] ?? $data['type'] ?? 'Workout',
                'started_at' => Carbon::parse($data['start_date'])->setTimezone(config('app.timezone')),
                'moving_time_s' => $data['moving_time'] ?? 0,
                'elapsed_time_s' => $data['elapsed_time'] ?? 0,
                'distance_m' => $data['distance'] ?? null,
                'elevation_gain_m' => $data['total_elevation_gain'] ?? null,
                'avg_heartrate' => $data['average_heartrate'] ?? null,
                'max_heartrate' => $data['max_heartrate'] ?? null,
                'avg_watts' => $data['average_watts'] ?? null,
                'np_watts' => $data['weighted_average_watts'] ?? null,
                'calories' => $data['calories'] ?? null,
                'kilojoules' => $data['kilojoules'] ?? null,
                'relative_effort' => $data['suffer_score'] ?? null,
                'indoor' => ($data['trainer'] ?? false) || ($data['sport_type'] ?? '') === 'VirtualRide',
                'raw' => $data,
            ],
        );
    }
}
