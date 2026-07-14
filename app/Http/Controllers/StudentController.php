<?php

namespace App\Http\Controllers;

use App\Exports\StudentsExport;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Imports\StudentsImport;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $class_id = $request->get('class_id');

        $activeYearId = \App\Models\AcademicYear::getActiveId();
        $yearIds = \App\Models\AcademicYear::getSameYearIds($activeYearId);

        $query = Student::with(['studentClasses.schoolClass'])->whereHas('studentClasses', function($q) use ($yearIds, $class_id) {
            $q->whereIn('academic_year_id', $yearIds);
            if ($class_id) {
                $q->where('class_id', $class_id);
            }
        });

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%")
                    ->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        $students = $query->latest()->paginate(10)->withQueryString();
        $classes = SchoolClass::orderBy('level')->orderBy('name')->get();

        return view('students.index', compact('students', 'classes'));
    }

    public function create()
    {
        $classes = SchoolClass::orderBy('level')->orderBy('name')->get();

        return view('students.create', compact('classes'));
    }

    public function store(StoreStudentRequest $request)
    {
        $data = $request->validated();
        $class_id = $data['class_id'];
        unset($data['class_id']);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('students', 'public');
            $data['photo'] = $path;
        }

        $student = Student::create($data);

        $activeYearId = \App\Models\AcademicYear::getActiveId();
        if ($activeYearId) {
            \App\Models\StudentClass::create([
                'student_id' => $student->id,
                'class_id' => $class_id,
                'academic_year_id' => $activeYearId,
            ]);
        }

        return redirect()->route('students.index')->with('success', 'Data siswa berhasil ditambahkan.');
    }

    public function show(Student $student)
    {
        return view('students.show', compact('student'));
    }

    public function edit(Student $student)
    {
        $classes = SchoolClass::orderBy('level')->orderBy('name')->get();

        return view('students.edit', compact('student', 'classes'));
    }

    public function update(UpdateStudentRequest $request, Student $student)
    {
        $data = $request->validated();
        $class_id = $data['class_id'];
        unset($data['class_id']);

        if ($request->hasFile('photo')) {
            // Hapus foto lama jika ada
            if ($student->photo && Storage::disk('public')->exists($student->photo)) {
                Storage::disk('public')->delete($student->photo);
            }
            $path = $request->file('photo')->store('students', 'public');
            $data['photo'] = $path;
        }

        $student->update($data);

        $activeYearId = \App\Models\AcademicYear::getActiveId();
        if ($activeYearId) {
            \App\Models\StudentClass::updateOrCreate(
                ['student_id' => $student->id, 'academic_year_id' => $activeYearId],
                ['class_id' => $class_id]
            );
        }

        return redirect()->route('students.index')->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(Student $student)
    {
        if ($student->photo && Storage::disk('public')->exists($student->photo)) {
            Storage::disk('public')->delete($student->photo);
        }
        $student->delete();

        return redirect()->route('students.index')->with('success', 'Data siswa berhasil dihapus.');
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id',
        ]);

        $students = Student::whereIn('id', $request->student_ids)->get();
        foreach($students as $student) {
            if ($student->photo && Storage::disk('public')->exists($student->photo)) {
                Storage::disk('public')->delete($student->photo);
            }
            $student->delete();
        }

        return redirect()->route('students.index')->with('success', 'Data siswa yang dipilih berhasil dihapus.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120',
        ]);

        try {
            Excel::import(new StudentsImport, $request->file('file'));
            return redirect()->back()->with('success', 'Data siswa berhasil diimport.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal import data: ' . $e->getMessage());
        }
    }

    public function export(Request $request)
    {
        return Excel::download(new StudentsExport($request->search, $request->class_id), 'Data_Siswa_'.date('Ymd_His').'.xlsx');
    }
}
