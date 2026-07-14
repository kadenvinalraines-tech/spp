<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\Auditable;

class SchoolClass extends Model
{
    use Auditable;
    protected $table = 'classes';

    protected $fillable = [
        'name',
        'level',
        'major'
    ];

    public function studentClasses()
    {
        return $this->hasMany(StudentClass::class, 'class_id');
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'student_classes', 'class_id', 'student_id')
                    ->withPivot('academic_year_id')
                    ->withTimestamps();
    }
}
