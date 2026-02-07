<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'admission_number',
        'first_name',
        'last_name',
        'class_id',
        'section_id',
        'gender',
        'dob',
        'blood_group',
        'guardian_name',
        'guardian_phone',
        'guardian_address',
        'passport_photo',
        'status',
    ];

    protected $casts = [
        'class_id' => 'integer',
        'section_id' => 'integer',
        'dob' => 'date',
    ];

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function scores(): HasMany
    {
        return $this->hasMany(Score::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function getPassportPhotoUrlAttribute(): ?string
    {
        if (! $this->passport_photo) {
            return null;
        }

        $path = str_replace('\\', '/', $this->passport_photo);

        return asset('uploads/'.$path);
    }

    public function getRouteKeyName(): string
    {
        return 'admission_number';
    }
}
