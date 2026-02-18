<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TeacherAttendanceSheet extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'term',
        'session',
        'taken_by',
    ];

    protected $casts = [
        'term' => 'integer',
        'date' => 'date:Y-m-d',
        'taken_by' => 'integer',
    ];

    public function takenBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'taken_by');
    }

    public function marks(): HasMany
    {
        return $this->hasMany(TeacherAttendanceMark::class, 'sheet_id');
    }
}

