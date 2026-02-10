<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PremiumDevice extends Model
{
    protected $fillable = [
        'device_id',
        'label',
        'meta',
        'first_seen_at',
        'last_seen_at',
        'revoked_at',
        'revoked_by_user_id',
    ];

    protected $casts = [
        'meta' => 'array',
        'first_seen_at' => 'datetime',
        'last_seen_at' => 'datetime',
        'revoked_at' => 'datetime',
        'revoked_by_user_id' => 'integer',
    ];

    public function revokedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'revoked_by_user_id');
    }

    public function removals(): HasMany
    {
        return $this->hasMany(PremiumDeviceRemoval::class);
    }
}

