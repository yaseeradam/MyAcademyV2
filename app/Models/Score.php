<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Score extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'subject_id',
        'class_id',
        'term',
        'session',
        'ca1',
        'ca2',
        'exam',
        'total',
        'grade',
        'position',
    ];

    protected $casts = [
        'student_id' => 'integer',
        'subject_id' => 'integer',
        'class_id' => 'integer',
        'term' => 'integer',
        'ca1' => 'integer',
        'ca2' => 'integer',
        'exam' => 'integer',
        'total' => 'integer',
        'position' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $score) {
            $score->total = (int) $score->ca1 + (int) $score->ca2 + (int) $score->exam;
            $score->grade = self::gradeForTotal($score->total);
        });
    }

    public static function gradeForTotal(int $total): string
    {
        return match (true) {
            $total >= 70 => 'A',
            $total >= 60 => 'B',
            $total >= 50 => 'C',
            $total >= 45 => 'D',
            $total >= 40 => 'E',
            default => 'F',
        };
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }
}
