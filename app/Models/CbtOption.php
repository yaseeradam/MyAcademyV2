<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CbtOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_id',
        'label',
        'is_correct',
        'position',
    ];

    protected $casts = [
        'question_id' => 'integer',
        'is_correct' => 'boolean',
        'position' => 'integer',
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(CbtQuestion::class, 'question_id');
    }
}

