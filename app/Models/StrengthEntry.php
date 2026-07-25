<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StrengthEntry extends Model
{
    protected $fillable = [
        'strength_session_id',
        'exercise_id',
        'weight_kg',
        'reps',
        'sets',
    ];

    protected function casts(): array
    {
        return [
            'weight_kg' => 'float',
        ];
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(StrengthSession::class, 'strength_session_id');
    }

    public function exercise(): BelongsTo
    {
        return $this->belongsTo(Exercise::class);
    }

    public function summary(): string
    {
        $weight = $this->weight_kg !== null
            ? rtrim(rtrim(number_format($this->weight_kg, 1, ',', '.'), '0'), ',').' kg × '
            : '';

        return $weight.$this->reps.' × '.$this->sets;
    }
}
