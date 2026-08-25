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
        $activeYearId = \App\Models\AcademicYear::getActiveId();

        $query = \App\Models\Student::with(['studentClasses' => function($q) use ($activeYearId) {
            $q->where('academic_year_id', $activeYearId)->with('schoolClass');
        }, 'bills' => function($q) use ($request) {
            $q->with(['academicYear', 'financePost'])->orderBy('due_date', 'asc');
            if ($request->filled('finance_post_id')) {
                $q->where('finance_post_id', $request->finance_post_id);
            }
        }])->whereHas('bills', function($q) use ($request) {
            if ($request->filled('finance_post_id')) {
                $q->where('finance_post_id', $request->finance_post_id);
            }
        });

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('nis', 'like', '%' . $request->search . '%')
                  ->orWhereHas('bills', function($qBill) use ($request) {
                      $qBill->where('invoice_number', 'like', '%' . $request->search . '%');
                  });
            });
        }

        if ($request->filled('class_id')) {
            $query->whereHas('studentClasses', function($q) use ($request, $activeYearId) {
                $q->where('class_id', $request->class_id)
                  ->where('academic_year_id', $activeYearId);
            });
        }

        $perPage = $request->input('per_page', 15);
        $paginateLimit = $perPage === 'all' ? $query->count() ?: 1 : (int)$perPage;

        $students = $query->orderBy('name', 'asc')
            ->paginate($paginateLimit)
            ->withQueryString();
            
        $classes = SchoolClass::orderBy('level')->orderBy('name')->get();
        $financePosts = \App\Models\FinancePost::orderBy('name')->get();

        return view('bills.index', compact('students', 'classes', 'financePosts'));
    }

    public function printReport(Request $request)
    {
        $activeYearId = \App\Models\AcademicYear::getActiveId();

        $query = \App\Models\Student::with(['studentClasses' => function($q) use ($activeYearId) {
            $q->where('academic_year_id', $activeYearId)->with('schoolClass');
        }, 'bills' => function($q) use ($request) {
            $q->with(['academicYear', 'financePost', 'details'])->orderBy('due_date', 'asc');
            if ($request->filled('finance_post_id')) {
                $q->where('finance_post_id', $request->finance_post_id);
            }
        }])->whereHas('bills', function($q) use ($request) {
            if ($request->filled('finance_post_id')) {
                $q->where('finance_post_id', $request->finance_post_id);
            }
        });

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('nis', 'like', '%' . $request->search . '%')
                  ->orWhereHas('bills', function($qBill) use ($request) {
                      $qBill->where('invoice_number', 'like', '%' . $request->search . '%');
                  });
            });
        }

        if ($request->filled('class_id')) {
            $query->whereHas('studentClasses', function($q) use ($request, $activeYearId) {
                $q->where('class_id', $request->class_id)
                  ->where('academic_year_id', $activeYearId);
            });
        }

        $students = $query->orderBy('name', 'asc')->get();
        
        // Group students by class name
        $groupedStudents = $students->groupBy(function($student) use ($activeYearId) {
            $studentClass = $student->studentClasses->where('academic_year_id', $activeYearId)->first();
            return $studentClass ? $studentClass->schoolClass->name : 'Tanpa Kelas';
        })->sortKeys();

        $settings = \App\Models\SchoolSetting::pluck('value', 'key')->all();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('bills.report_pdf', compact('groupedStudents', 'settings', 'request'));
        
        return $pdf->stream("laporan-tagihan.pdf");
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

        $students = \App\Models\Student::with('feeExemptions')
            ->whereIn('id', $data['student_ids'])
            ->where('status', 'active')
            ->get();
            
        $className = 'Siswa Terpilih';

        if ($students->isEmpty()) {
            return back()->with('error', 'Tidak ada siswa yang valid dipilih.')->withInput();
        }

        $financePost = FinancePost::findOrFail($data['finance_post_id']);
        $isMonthly = $financePost->type === 'monthly';

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
                // Cek apakah siswa dibebaskan dari biaya ini
                if ($student->feeExemptions->contains('id', $data['finance_post_id'])) {
                    continue;
                }

                if (!in_array($student->id, $existingBills)) {
                    // Generate Nomor Invoice Unik
                    // Format: INV-Ymd-StudentID-Microtime
                    $invoiceNumber = 'INV-' . date('Ymd') . '-' . $student->id . '-' . rand(1000, 9999);

                    $billTotal = $isMonthly ? $data['amount'] * 12 : $data['amount'];

                    $bill = Bill::create([
                        'invoice_number' => $invoiceNumber,
                        'student_id' => $student->id,
                        'academic_year_id' => $data['academic_year_id'],
                        'finance_post_id' => $data['finance_post_id'],
                        'total_amount' => $billTotal,
                        'total_paid' => 0,
                        'status' => 'unpaid',
                        'due_date' => $data['due_date'],
                    ]);

                    if ($isMonthly) {
                        // Generate 12 months: 7 to 12, then 1 to 6
                        $months = [7, 8, 9, 10, 11, 12, 1, 2, 3, 4, 5, 6];
                        foreach ($months as $m) {
                            BillDetail::create([
                                'bill_id' => $bill->id,
                                'month' => $m,
                                'amount' => $data['amount'],
                                'paid_amount' => 0,
                                'status' => 'unpaid'
                            ]);
                        }
                    } else {
                        // Generate rincian tagihan 1 baris utuh
                        BillDetail::create([
                            'bill_id' => $bill->id,
                            'month' => null, // Tidak spesifik per bulan
                            'amount' => $data['amount'],
                            'paid_amount' => 0,
                            'status' => 'unpaid'
                        ]);
                    }

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

    // Endpoint untuk mengambil siswa berdasarkan kelas (via AJAX)
    public function getStudentsByClass($class_id)
    {
        $activeYearId = \App\Models\AcademicYear::getActiveId();
        $yearIds = \App\Models\AcademicYear::getSameYearIds($activeYearId);
        
        if ($class_id === 'all') {
            $students = \App\Models\Student::with('feeExemptions')->whereHas('studentClasses', function($q) use ($yearIds) {
                $q->whereIn('academic_year_id', $yearIds);
            })->where('status', 'active')->orderBy('name')->get();
        } else {
            $schoolClass = SchoolClass::findOrFail($class_id);
            $students = $schoolClass->students()->with('feeExemptions')->wherePivotIn('academic_year_id', $yearIds)->where('status', 'active')->orderBy('name')->get();
        }

        return response()->json($students);
    }

    public function showStudentBills(\App\Models\Student $student)
    {
        $bills = $student->bills()->with(['academicYear', 'financePost', 'details'])->orderBy('created_at', 'desc')->get();
        return view('bills.student_show', compact('student', 'bills'));
    }

    public function destroy(Bill $bill)
    {
        if ($bill->total_paid > 0 || $bill->status !== 'unpaid') {
            return back()->with('error', 'Tagihan tidak dapat dihapus karena sudah ada pembayaran masuk.');
        }

        try {
            DB::beginTransaction();
            // Delete bill details first (although cascade should handle this, doing it explicitly is safer)
            $bill->details()->delete();
            $bill->delete();
            DB::commit();

            return back()->with('success', 'Tagihan berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat menghapus tagihan: ' . $e->getMessage());
        }
    }
    public function updateDueDate(Request $request, Bill $bill)
    {
        $request->validate([
            'due_date' => 'required|date'
        ]);

        try {
            $bill->update([
                'due_date' => $request->due_date
            ]);
            return back()->with('success', 'Jatuh tempo berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui jatuh tempo: ' . $e->getMessage());
        }
    }

    public function manageDueDates(Request $request)
    {
        $activeYearId = \App\Models\AcademicYear::getActiveId();

        // Get unique combinations of finance_post_id, academic_year_id, and due_date
        $batches = Bill::with(['financePost', 'academicYear'])
            ->select('finance_post_id', 'academic_year_id', 'due_date', DB::raw('count(id) as total_bills'))
            ->groupBy('finance_post_id', 'academic_year_id', 'due_date')
            ->orderBy('due_date', 'desc')
            ->paginate(15);

        return view('bills.due_dates', compact('batches'));
    }

    public function bulkUpdateDueDate(Request $request)
    {
        $request->validate([
            'finance_post_id' => 'required|exists:finance_posts,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'old_due_date' => 'required|date',
            'new_due_date' => 'required|date',
        ]);

        try {
            DB::beginTransaction();

            $updated = Bill::where('finance_post_id', $request->finance_post_id)
                ->where('academic_year_id', $request->academic_year_id)
                ->where('due_date', $request->old_due_date)
                ->update(['due_date' => $request->new_due_date]);

            DB::commit();

            return back()->with('success', "Berhasil memperbarui jatuh tempo massal untuk {$updated} tagihan.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan sistem saat memperbarui jatuh tempo massal: ' . $e->getMessage());
        }
    }
}
