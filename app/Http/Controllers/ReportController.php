<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Expense;
use App\Models\Bill;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CashReportExport;
use App\Exports\PaymentReportExport;
use App\Exports\ExpenseReportExport;
use App\Exports\ArrearReportExport;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    public function cash(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());
        $action = $request->input('action', 'view');

        $activeYearId = \App\Models\AcademicYear::getActiveId();

        // Pemasukan (Pembayaran sukses)
        $payments = Payment::where('status', 'success')
            ->where('payment_method', '!=', 'Gratis / Beasiswa')
            ->where('academic_year_id', $activeYearId)
            ->whereBetween('date', [$startDate, $endDate])
            ->get()
            ->map(function ($item) {
                return [
                    'date' => $item->date,
                    'type' => 'Pemasukan',
                    'description' => 'Pembayaran SPP/Tagihan (Ref: ' . $item->transaction_number . ') - ' . ($item->student->name ?? ''),
                    'income' => $item->total_amount,
                    'expense' => 0,
                ];
            });

        // Pengeluaran (Approved)
        $expenses = Expense::where('status', 'approved')
            ->where('academic_year_id', $activeYearId)
            ->whereBetween('date', [$startDate, $endDate])
            ->get()
            ->map(function ($item) {
                return [
                    'date' => $item->date,
                    'type' => 'Pengeluaran',
                    'description' => $item->description . ' (' . ($item->category->name ?? '') . ')',
                    'income' => 0,
                    'expense' => $item->amount,
                ];
            });

        // Gabung dan Sortir berdasarkan tanggal
        $transactions = $payments->concat($expenses)->sortBy('date')->values();

        $totalIncome = $transactions->sum('income');
        $totalExpense = $transactions->sum('expense');
        $balance = $totalIncome - $totalExpense;

        if ($action === 'pdf' || $action === 'print') {
            $view = 'reports.print.cash';
            $settings = \App\Models\SchoolSetting::pluck('value', 'key')->all();
            if ($action === 'pdf') {
                $pdf = Pdf::loadView($view, compact('transactions', 'startDate', 'endDate', 'totalIncome', 'totalExpense', 'balance', 'action', 'settings'));
                return $pdf->download("Laporan_Kas_{$startDate}_{$endDate}.pdf");
            }
            return view($view, compact('transactions', 'startDate', 'endDate', 'totalIncome', 'totalExpense', 'balance', 'action', 'settings'));
        } elseif ($action === 'excel') {
            return Excel::download(new CashReportExport($transactions, $startDate, $endDate, $totalIncome, $totalExpense, $balance), "Laporan_Kas_{$startDate}_{$endDate}.xlsx");
        }

        return view('reports.cash', compact('transactions', 'startDate', 'endDate', 'totalIncome', 'totalExpense', 'balance'));
    }

    public function payment(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());
        $action = $request->input('action', 'view');

        $activeYearId = \App\Models\AcademicYear::getActiveId();

        $payments = Payment::with(['student.studentClasses.schoolClass', 'user'])
            ->where('academic_year_id', $activeYearId)
            ->where('status', 'success')
            ->where('payment_method', '!=', 'Gratis / Beasiswa')
            ->whereBetween('date', [$startDate, $endDate])
            ->latest('date')
            ->get();

        $total = $payments->sum('total_amount');

        if ($action === 'pdf' || $action === 'print') {
            $view = 'reports.print.payment';
            $settings = \App\Models\SchoolSetting::pluck('value', 'key')->all();
            if ($action === 'pdf') {
                $pdf = Pdf::loadView($view, compact('payments', 'startDate', 'endDate', 'total', 'action', 'settings'));
                return $pdf->download("Laporan_Pembayaran_{$startDate}_{$endDate}.pdf");
            }
            return view($view, compact('payments', 'startDate', 'endDate', 'total', 'action', 'settings'));
        } elseif ($action === 'excel') {
            return Excel::download(new PaymentReportExport($payments, $startDate, $endDate, $total), "Laporan_Pembayaran_{$startDate}_{$endDate}.xlsx");
        }

        return view('reports.payment', compact('payments', 'startDate', 'endDate', 'total'));
    }

    public function expense(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());
        $action = $request->input('action', 'view');

        $activeYearId = \App\Models\AcademicYear::getActiveId();

        $expenses = Expense::with(['category', 'user', 'approver', 'academicYear'])
            ->where('academic_year_id', $activeYearId)
            ->where('status', 'approved')
            ->whereBetween('date', [$startDate, $endDate])
            ->latest('date')
            ->get();

        $total = $expenses->sum('amount');

        if ($action === 'pdf' || $action === 'print') {
            $view = 'reports.print.expense';
            $settings = \App\Models\SchoolSetting::pluck('value', 'key')->all();
            if ($action === 'pdf') {
                $pdf = Pdf::loadView($view, compact('expenses', 'startDate', 'endDate', 'total', 'action', 'settings'));
                return $pdf->download("Laporan_Pengeluaran_{$startDate}_{$endDate}.pdf");
            }
            return view($view, compact('expenses', 'startDate', 'endDate', 'total', 'action', 'settings'));
        } elseif ($action === 'excel') {
            return Excel::download(new ExpenseReportExport($expenses, $startDate, $endDate, $total), "Laporan_Pengeluaran_{$startDate}_{$endDate}.xlsx");
        }

        return view('reports.expense', compact('expenses', 'startDate', 'endDate', 'total'));
    }

    public function arrear(Request $request)
    {
        $academicYearId = $request->input('academic_year_id');
        $classId = $request->input('class_id');
        $action = $request->input('action', 'view');

        $academicYears = AcademicYear::latest()->get();
        $classes = SchoolClass::orderBy('level')->orderBy('name')->get();

        $query = Bill::with(['student.studentClasses.schoolClass', 'academicYear', 'financePost', 'details' => function($q) {
            $q->where('status', '!=', 'paid');
        }])
            ->whereIn('status', ['unpaid', 'partial']);

        if ($academicYearId) {
            $selectedYear = AcademicYear::findOrFail($academicYearId);
            $query->whereHas('academicYear', function ($q) use ($selectedYear) {
                $q->where('start_date', '<=', $selectedYear->start_date);
            });
        }

        if ($classId) {
            $query->whereHas('student.studentClasses', function ($q) use ($classId, $academicYearId) {
                $q->where('class_id', $classId);
                if ($academicYearId) {
                    $yearIds = \App\Models\AcademicYear::getSameYearIds($academicYearId);
                    $q->whereIn('academic_year_id', $yearIds);
                }
            });
        }

        $allArrears = $query->get();
        $arrears = collect();
        $totalArrears = 0;

        foreach ($allArrears as $bill) {
            $dueDetails = $bill->details->filter(function($detail) {
                return $detail->isDue() && $detail->amount > $detail->paid_amount;
            });

            if ($dueDetails->isNotEmpty()) {
                $billDueAmount = $dueDetails->sum('amount') - $dueDetails->sum('paid_amount');
                $bill->due_arrear_amount = $billDueAmount; // Set dynamic property for views
                $totalArrears += $billDueAmount;
                $arrears->push($bill);
            }
        }

        $selYear = $academicYears->firstWhere('id', $academicYearId)->name ?? 'Semua';
        $selClass = $classes->firstWhere('id', $classId)->name ?? 'Semua';

        if ($action === 'pdf' || $action === 'print') {
            $view = 'reports.print.arrear';
            $settings = \App\Models\SchoolSetting::pluck('value', 'key')->all();
            if ($action === 'pdf') {
                $pdf = Pdf::loadView($view, compact('arrears', 'selYear', 'selClass', 'totalArrears', 'action', 'settings'));
                return $pdf->download("Laporan_Tunggakan.pdf");
            }
            return view($view, compact('arrears', 'selYear', 'selClass', 'totalArrears', 'action', 'settings'));
        } elseif ($action === 'excel') {
            return Excel::download(new ArrearReportExport($arrears, $selYear, $selClass, $totalArrears), "Laporan_Tunggakan.xlsx");
        }

        return view('reports.arrear', compact('arrears', 'academicYears', 'classes', 'academicYearId', 'classId', 'totalArrears'));
    }
}
