<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\BillDetail;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\FinancePost;
use App\Http\Requests\GenerateBillRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BillController extends Controller
{
    public function index(Request $request)
    {
        $query = Bill::with(['student.studentClasses.schoolClass', 'academicYear', 'financePost'])
            ->join('students', 'bills.student_id', '=', 'students.id')
            ->select('bills.*', 'students.name as student_name');

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('students.name', 'like', '%' . $request->search . '%')
                  ->orWhere('bills.invoice_number', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('class_id')) {
            $activeYearId = \App\Models\AcademicYear::getActiveId();
            $query->whereHas('student.studentClasses', function($q) use ($request, $activeYearId) {
                $q->where('class_id', $request->class_id)
                  ->where('academic_year_id', $activeYearId);
            });
        }

        $bills = $query->orderBy('student_name', 'asc')
            ->orderBy('bills.created_at', 'desc')
            ->paginate(15)
            ->withQueryString();
            
        $classes = SchoolClass::orderBy('level')->orderBy('name')->get();

        return view('bills.index', compact('bills', 'classes'));
    }

    public function create()
    {
        $academicYears = AcademicYear::latest()->get();
        $classes = SchoolClass::orderBy('level')->orderBy('name')->get();
        $financePosts = FinancePost::where('status', 'active')->orderBy('name')->get();

        return view('bills.generate', [
            'academic_years' => $academicYears,
            'classes' => $classes,
            'finance_posts' => $financePosts
        ]);
    }

    public function store(GenerateBillRequest $request)
    {
        $data = $request->validated();
        
        $activeYearId = \App\Models\AcademicYear::getActiveId();
        $yearIds = \App\Models\AcademicYear::getSameYearIds($activeYearId);

        if ($data['class_id'] === 'all') {
            $students = \App\Models\Student::whereHas('studentClasses', function($q) use ($yearIds) {
                $q->whereIn('academic_year_id', $yearIds);
            })->where('status', 'active')->get();
            $className = 'Semua Kelas';
        } else {
            $schoolClass = SchoolClass::findOrFail($data['class_id']);
            $students = $schoolClass->students()->wherePivotIn('academic_year_id', $yearIds)->where('status', 'active')->get();
            $className = $schoolClass->name;
        }

        if ($students->isEmpty()) {
            return back()->with('error', 'Tidak ada siswa aktif di kelas yang dipilih.')->withInput();
        }

        $generatedCount = 0;

        try {
            DB::beginTransaction();

            $studentIds = $students->pluck('id')->toArray();
            
            // Ambil semua tagihan yang sudah ada untuk siswa-siswa ini
            $existingBills = Bill::whereIn('student_id', $studentIds)
                                ->where('academic_year_id', $data['academic_year_id'])
                                ->where('finance_post_id', $data['finance_post_id'])
                                ->pluck('student_id')
                                ->toArray();

            foreach ($students as $student) {
                if (!in_array($student->id, $existingBills)) {
                    // Generate Nomor Invoice Unik
                    // Format: INV-Ymd-StudentID-Microtime
                    $invoiceNumber = 'INV-' . date('Ymd') . '-' . $student->id . '-' . rand(1000, 9999);

                    $bill = Bill::create([
                        'invoice_number' => $invoiceNumber,
                        'student_id' => $student->id,
                        'academic_year_id' => $data['academic_year_id'],
                        'finance_post_id' => $data['finance_post_id'],
                        'total_amount' => $data['amount'],
                        'total_paid' => 0,
                        'status' => 'unpaid',
                        'due_date' => $data['due_date'],
                    ]);

                    // Generate rincian tagihan (Opsi A: 1 baris utuh untuk tagihan ini)
                    BillDetail::create([
                        'bill_id' => $bill->id,
                        'month' => null, // Tidak spesifik per bulan
                        'amount' => $data['amount'],
                        'paid_amount' => 0,
                        'status' => 'unpaid'
                    ]);

                    $generatedCount++;
                }
            }

            DB::commit();

            if ($generatedCount > 0) {
                return redirect()->route('bills.index')
                    ->with('success', "Berhasil me-generate tagihan untuk {$generatedCount} siswa di kelas {$className}.");
            } else {
                return redirect()->route('bills.index')
                    ->with('warning', 'Semua siswa aktif di kelas ini sudah memiliki tagihan yang sama (tidak ada tagihan baru yang digenerate).');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage())->withInput();
        }
    }

    // Endpoint untuk mendapatkan nominal default Pos Keuangan (via AJAX)
    public function getFinancePostAmount(FinancePost $financePost)
    {
        return response()->json([
            'default_amount' => floatval($financePost->default_amount)
        ]);
    }
}
