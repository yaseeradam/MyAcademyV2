<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceMark extends Model
{
    use HasFactory;

    protected $fillable = [
        'sheet_id',
        'student_id',
        'status',
        'note',
    ];

    protected $casts = [
        'sheet_id' => 'integer',
        'student_id' => 'integer',
    ];

    public function sheet(): BelongsTo
    {
        return $this->belongsTo(AttendanceSheet::class, 'sheet_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}

