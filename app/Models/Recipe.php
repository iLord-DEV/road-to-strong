<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Recipe extends Model
{
    public const CATEGORIES = [
        'morgens' => 'Morgens',
        'mittags' => 'Mittags',
        'abends' => 'Abends',
        'snack' => 'Snacks',
    ];

    public const RATINGS = [
        'geschmack' => 'Geschmack',
        'aufwand' => 'Aufwand',
        'kalorien' => 'Kalorien',
    ];

    protected $fillable = [
        'user_id',
        'category',
        'name',
        'description',
        'instructions',
        'kcal',
        'stars_geschmack',
        'stars_aufwand',
        'stars_kalorien',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
