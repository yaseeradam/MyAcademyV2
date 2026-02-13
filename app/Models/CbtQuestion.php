<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CbtQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'type',
        'prompt',
        'marks',
        'position',
    ];

    protected $casts = [
        'exam_id' => 'integer',
        'marks' => 'integer',
        'position' => 'integer',
    ];

    public function exam(): BelongsTo
    {
        return $this->belongsTo(CbtExam::class, 'exam_id');
    }

    public function options(): HasMany
    {
        return $this->hasMany(CbtOption::class, 'question_id')->orderBy('position');
    }
}

