<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tutor extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'secondary_phone',
        'email',
        'address',
        'dni_type',
        'dni',
        'dni_expedition_city',
        'residence_city',
    ];

    public function dniExpeditionCity(): BelongsTo
    {
        return $this->belongsTo(City::class, 'dni_expedition_city');
    }

    public function residenceCity(): BelongsTo
    {
        return $this->belongsTo(City::class, 'residence_city');
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'students_has_tutors', 'tutor_id', 'student_id')
            ->withPivot('relationship', 'is_primary_contact')
            ->withTimestamps();
    }
}
