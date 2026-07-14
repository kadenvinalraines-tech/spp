<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Http\Requests\StoreSchoolClassRequest;
use App\Http\Requests\UpdateSchoolClassRequest;
use Illuminate\Http\Request;

class SchoolClassController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $activeYearId = \App\Models\AcademicYear::getActiveId();
        $yearIds = \App\Models\AcademicYear::getSameYearIds($activeYearId);

        $classes = SchoolClass::withCount(['students' => function ($query) use ($yearIds) {
                $query->whereIn('student_classes.academic_year_id', $yearIds)
                      ->where('students.status', 'active');
            }])
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                             ->orWhere('level', 'like', "%{$search}%")
                             ->orWhere('major', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('classes.index', compact('classes', 'search'));
    }

    public function create()
    {
        return view('classes.create');
    }

    public function store(StoreSchoolClassRequest $request)
    {
        SchoolClass::create($request->validated());

        return redirect()->route('classes.index')
            ->with('success', 'Data kelas berhasil ditambahkan.');
    }

    public function show(Request $request, SchoolClass $class)
    {
        $search = $request->input('search');

        $activeYearId = \App\Models\AcademicYear::getActiveId();
        $yearIds = \App\Models\AcademicYear::getSameYearIds($activeYearId);

        $students = $class->students()
            ->wherePivotIn('academic_year_id', $yearIds)
            ->where('students.status', 'active')
            ->when($search, function($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('nis', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('classes.show', compact('class', 'students', 'search'));
    }

    public function edit(SchoolClass $class) // Since route param is {class}, we inject SchoolClass
    {
        return view('classes.edit', compact('class'));
    }

    public function update(UpdateSchoolClassRequest $request, SchoolClass $class)
    {
        $class->update($request->validated());

        return redirect()->route('classes.index')
            ->with('success', 'Data kelas berhasil diperbarui.');
    }

    public function destroy(SchoolClass $class)
    {
        if ($class->students()->count() > 0) {
            return redirect()->route('classes.index')
                ->with('error', 'Kelas tidak dapat dihapus karena masih ada siswa terdaftar.');
        }

        $class->delete();

        return redirect()->route('classes.index')
            ->with('success', 'Data kelas berhasil dihapus.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120',
        ]);

        try {
            \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\SchoolClassesImport, $request->file('file'));
            return redirect()->back()->with('success', 'Data kelas berhasil diimport.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal import data: ' . $e->getMessage());
        }
    }

    public function export(Request $request)
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\SchoolClassesExport, 'Data_Kelas_'.date('Ymd_His').'.xlsx');
    }
}
