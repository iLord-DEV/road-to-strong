<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Exercise extends Model
{
    use SoftDeletes;

    public const WORKOUTS = ['A', 'B'];

    protected $fillable = [
        'user_id',
        'name',
        'workout',
        'position',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function entries(): HasMany
    {
        return $this->hasMany(StrengthEntry::class);
    }

    /**
     * The most recent logged entry for this exercise (progression baseline).
     */
    public function lastEntry(): ?StrengthEntry
    {
        return $this->entries()
            ->join('strength_sessions', 'strength_sessions.id', '=', 'strength_entries.strength_session_id')
            ->orderByDesc('strength_sessions.performed_at')
            ->orderByDesc('strength_entries.id')
            ->select('strength_entries.*')
            ->first();
    }
}
