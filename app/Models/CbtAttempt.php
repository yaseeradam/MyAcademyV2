<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CbtAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'exam_id',
        'student_id',
        'ip_address',
        'allowed_ip',
        'started_at',
        'last_activity_at',
        'submitted_at',
        'terminated_at',
        'terminated_by',
        'termination_reason',
        'score',
        'max_score',
        'percent',
    ];

    protected $casts = [
        'exam_id' => 'integer',
        'student_id' => 'integer',
        'started_at' => 'datetime',
        'last_activity_at' => 'datetime',
        'submitted_at' => 'datetime',
        'terminated_at' => 'datetime',
        'terminated_by' => 'integer',
        'score' => 'integer',
        'max_score' => 'integer',
        'percent' => 'decimal:2',
    ];

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(CbtExam::class, 'exam_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(CbtAnswer::class, 'attempt_id');
    }
}
