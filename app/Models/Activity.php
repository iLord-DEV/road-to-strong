<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Activity extends Model
{
    protected $fillable = [
        'user_id',
        'strava_id',
        'name',
        'sport_type',
        'started_at',
        'moving_time_s',
        'elapsed_time_s',
        'distance_m',
        'elevation_gain_m',
        'avg_heartrate',
        'max_heartrate',
        'avg_watts',
        'np_watts',
        'calories',
        'kilojoules',
        'relative_effort',
        'indoor',
        'raw',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'indoor' => 'boolean',
            'raw' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sportLabel(): string
    {
        return match ($this->sport_type) {
            'Ride' => 'Rennrad',
            'VirtualRide' => 'Kickr',
            'Rowing', 'VirtualRow' => 'WaterRower',
            'WeightTraining' => 'Krafttraining',
            'Run' => 'Laufen',
            'Walk' => 'Spaziergang',
            'Hike' => 'Wanderung',
            default => $this->sport_type,
        };
    }

    public function durationFormatted(): string
    {
        $minutes = intdiv($this->moving_time_s, 60);

        if ($minutes < 60) {
            return $minutes.' min';
        }

        return intdiv($minutes, 60).':'.str_pad((string) ($minutes % 60), 2, '0', STR_PAD_LEFT).' h';
    }
}
