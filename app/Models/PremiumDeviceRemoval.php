<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PremiumDeviceRemoval extends Model
{
    protected $fillable = [
        'premium_device_id',
        'removed_by_user_id',
    ];

    protected $casts = [
        'premium_device_id' => 'integer',
        'removed_by_user_id' => 'integer',
    ];

    public function device(): BelongsTo
    {
        return $this->belongsTo(PremiumDevice::class, 'premium_device_id');
    }

    public function removedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'removed_by_user_id');
    }
}

