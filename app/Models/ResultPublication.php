<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResultPublication extends Model
{
    protected $fillable = [
        'class_id',
        'term',
        'session',
        'published_at',
        'published_by',
        'note',
    ];

    protected $casts = [
        'class_id' => 'integer',
        'term' => 'integer',
        'published_at' => 'datetime',
        'published_by' => 'integer',
    ];

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'published_by');
    }
}

