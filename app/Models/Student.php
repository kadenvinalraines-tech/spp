<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Auditable;

class Student extends Model
{
    use Auditable;

    protected $guarded = ['id'];

    public function studentClasses(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(StudentClass::class);
    }

    public function bills(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Bill::class);
    }

    public function getSchoolClassAttribute()
    {
        $activeYearId = \App\Models\AcademicYear::getActiveId();
        $yearIds = \App\Models\AcademicYear::getSameYearIds($activeYearId);

        if (empty($yearIds)) return null;

        if ($this->relationLoaded('studentClasses')) {
            $studentClass = $this->studentClasses->whereIn('academic_year_id', $yearIds)->first();
        } else {
            $studentClass = $this->studentClasses()->whereIn('academic_year_id', $yearIds)->first();
        }
        return $studentClass ? $studentClass->schoolClass : null;
    }

    public function getLatestAcademicYearNameAttribute()
    {
        $latest = $this->studentClasses()
            ->join('academic_years', 'student_classes.academic_year_id', '=', 'academic_years.id')
            ->orderBy('academic_years.start_date', 'desc')
            ->select('academic_years.name')
            ->first();

        return $latest ? $latest->name : null;
    }

    public function getPhotoUrlAttribute()
    {
        return $this->photo ? asset('storage/' . $this->photo) : null;
    }
}
