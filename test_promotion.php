<?php

use App\Models\AcademicYear;
use App\Models\Student;

$activeYearName = '2027/2028';
$activeYear = AcademicYear::where('name', $activeYearName)->where('semester', 'Ganjil')->first();
$activeYearId = $activeYear->id;
$yearIds = AcademicYear::getSameYearIds($activeYearId);
$class_id = 6;

$query = Student::whereHas('studentClasses', function ($q) use ($yearIds, $class_id) {
    $q->whereIn('academic_year_id', $yearIds)
        ->where('class_id', $class_id);
})
    ->whereDoesntHave('studentClasses', function ($q) use ($activeYear) {
        $q->whereHas('academicYear', function ($q2) use ($activeYear) {
            $q2->where('start_date', '>', $activeYear->start_date);
        });
    })
    ->where('status', 'active')
    ->orderBy('name');

echo $query->toSql()."\n";
echo json_encode($query->getBindings())."\n";

$students = $query->get();
echo 'Jumlah: '.$students->count()."\n";
