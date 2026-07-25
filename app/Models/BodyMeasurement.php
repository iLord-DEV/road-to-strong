<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BodyMeasurement extends Model
{
    protected $fillable = [
        'user_id',
        'withings_grpid',
        'measured_at',
        'weight_kg',
        'fat_percent',
        'fat_mass_kg',
        'muscle_mass_kg',
        'water_kg',
        'bone_mass_kg',
        'bmi',
        'raw',
    ];

    protected function casts(): array
    {
        return [
            'measured_at' => 'datetime',
            'weight_kg' => 'float',
            'fat_percent' => 'float',
            'fat_mass_kg' => 'float',
            'muscle_mass_kg' => 'float',
            'water_kg' => 'float',
            'bone_mass_kg' => 'float',
            'bmi' => 'float',
            'raw' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
