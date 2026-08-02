<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyLog extends Model
{
    /**
     * Allowed values per habit field — the single source of truth
     * for validation and the dashboard buttons.
     */
    public const FIELDS = [
        'schlaf' => [1, 2, 3, 4, 5],
        'durchgeschlafen' => [1, 0],
        'energie' => [1, 2, 3, 4, 5],
        // Minuten seit Mitternacht: 6:00 bis 10:00 in 30er-Schritten
        'arbeitsbeginn' => [360, 390, 420, 450, 480, 510, 540, 570, 600],
        'mittag_vorbereitet' => [1, 0],
        'mittagspause' => [1, 0],
        'feierabend' => [1, 0],
        'naschen' => ['keines', 'bewusst', 'automatisch'],
        'craving' => [0, 1, 2, 3],
        'cannabis_vortag' => [1, 0],
    ];

    protected $fillable = [
        'user_id',
        'date',
        'feierabend',
        'mittag_vorbereitet',
        'mittagspause',
        'naschen',
        'craving',
        'schlaf',
        'durchgeschlafen',
        'energie',
        'arbeitsbeginn',
        'cannabis_vortag',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'feierabend' => 'boolean',
            'mittag_vorbereitet' => 'boolean',
            'mittagspause' => 'boolean',
            'durchgeschlafen' => 'boolean',
            'cannabis_vortag' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
