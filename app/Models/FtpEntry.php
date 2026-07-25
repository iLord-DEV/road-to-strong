<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FtpEntry extends Model
{
    protected $fillable = [
        'user_id',
        'watts',
        'tested_at',
    ];

    protected function casts(): array
    {
        return [
            'tested_at' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
