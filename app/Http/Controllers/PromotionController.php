<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PromotionController extends Controller
{
    public function index(Request $request)
    {
        $classes = SchoolClass::orderBy('level')->orderBy('name')->get();
        $class_id = $request->get('class_id');
        $activeYearId = \App\Models\AcademicYear::getActiveId();
        $yearIds = \App\Models\AcademicYear::getSameYearIds($activeYearId);

        $academicYears = \App\Models\AcademicYear::where('semester', 'Ganjil')->orderBy('name', 'desc')->get();
        $activeYear = \App\Models\AcademicYear::find($activeYearId);

        $students = collect();
        if ($class_id && $activeYear) {
            $students = Student::whereHas('studentClasses', function($q) use ($yearIds, $class_id) {
                    $q->whereIn('academic_year_id', $yearIds)
                      ->where('class_id', $class_id);
                })
                ->where('status', 'active')
                ->orderBy('name')
                ->get();
        }

        return view('promotions.index', compact('classes', 'students', 'class_id', 'academicYears'));
    }

    public function process(Request $request)
    {
            $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id',
            'action' => 'required|in:promote,graduate',
            'target_class_id' => 'required_if:action,promote|nullable|exists:classes,id',
            'target_academic_year_id' => 'required_if:action,promote|nullable|exists:academic_years,id',
        ]);

        try {
            DB::beginTransaction();

            $studentIds = $request->student_ids;

            if ($request->action === 'graduate') {
                Student::whereIn('id', $studentIds)->update(['status' => 'graduated']);
                $message = count($studentIds) . ' Siswa berhasil diluluskan.';
            } else {
                $targetYearId = $request->target_academic_year_id;
                
                foreach($studentIds as $id) {
                    \App\Models\StudentClass::updateOrCreate(
                        ['student_id' => $id, 'academic_year_id' => $targetYearId],
                        ['class_id' => $request->target_class_id]
                    );
                }
                
                $targetClass = SchoolClass::findOrFail($request->target_class_id);
                $targetYear = \App\Models\AcademicYear::findOrFail($targetYearId);
                $message = count($studentIds) . ' Siswa berhasil dipindahkan ke ' . $targetClass->name . ' untuk Tahun Ajaran ' . $targetYear->name . '.';
            }

            DB::commit();

            return redirect()->route('promotions.index')->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }
}
