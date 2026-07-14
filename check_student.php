<?php

use App\Models\Student;
use App\Models\AcademicYear;
use App\Models\SchoolClass;

$student = Student::find(58);
if ($student) {
    echo "Student: " . $student->name . "\n";
    foreach ($student->studentClasses as $sc) {
        $year = AcademicYear::find($sc->academic_year_id);
        $class = SchoolClass::find($sc->class_id);
        echo "- Year: " . $year->name . " (" . $year->semester . ") -> Class: " . $class->name . "\n";
    }
}
