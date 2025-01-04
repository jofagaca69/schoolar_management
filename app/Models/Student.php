<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Student extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'birth_date',
        'dni_type',
        'dni',
        'tutor_id',
        'relationship',
        'is_primary_contact'
    ];

    public function tutors(): BelongsToMany
    {
        return $this->belongsToMany(Tutor::class, 'students_has_tutors', 'student_id', 'tutor_id')
            ->withPivot('relationship', 'is_primary_contact')
            ->withTimestamps();
    }
}
