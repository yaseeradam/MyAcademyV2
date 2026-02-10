<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certificate extends Model
{
    protected $fillable = [
        'student_id',
        'type',
        'title',
        'body',
        'issued_on',
        'serial_number',
        'issued_by',
    ];

    protected $casts = [
        'student_id' => 'integer',
        'issued_by' => 'integer',
        'issued_on' => 'date',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function issuer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }
}

