<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicSession extends Model
{
    protected $fillable = [
        'name',
        'starts_on',
        'ends_on',
        'is_active',
    ];

    protected $casts = [
        'starts_on' => 'date',
        'ends_on' => 'date',
        'is_active' => 'boolean',
    ];

    public static function activeName(): ?string
    {
        try {
            $name = self::query()->where('is_active', true)->value('name');
        } catch (\Throwable) {
            return null;
        }

        return is_string($name) && preg_match('/^\\d{4}\\/\\d{4}$/', $name) === 1 ? $name : null;
    }
}
