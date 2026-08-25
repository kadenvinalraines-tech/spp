<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Bill;
use App\Models\Payment;
use App\Models\Expense;
use App\Models\AcademicYear;
use App\Models\FinancePost;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        if ($user->roles->isEmpty()) {
            abort(403, 'Anda tidak memiliki role untuk mengakses sistem.');
        }

        $activeYearId = AcademicYear::getActiveId();
        $activeYear = AcademicYear::find($activeYearId);
        $allAcademicYears = AcademicYear::orderBy('start_date', 'desc')->get();

        $totalSiswa = Student::where('status', 'active')->count();
        
        $filterRange = $request->get('filter_range', 'up_to_active');

        // Setup base queries
        $billQuery = Bill::query();
        $paymentQuery = Payment::where('status', 'success')->where('payment_method', '!=', 'Gratis / Beasiswa');
        $expenseQuery = Expense::where('status', 'approved');

        if ($filterRange === 'active_semester') {
            $billQuery->where('academic_year_id', $activeYearId);
            $paymentQuery->where('academic_year_id', $activeYearId);
            $expenseQuery->where('academic_year_id', $activeYearId);
        } elseif ($filterRange === 'active_year') {
            $yearIds = AcademicYear::getSameYearIds($activeYearId);
            $billQuery->whereIn('academic_year_id', $yearIds);
            $paymentQuery->whereIn('academic_year_id', $yearIds);
            $expenseQuery->whereIn('academic_year_id', $yearIds);
        } elseif ($filterRange === 'all_time') {
            // No academic year filter, takes everything from the beginning of time
        } elseif (\Illuminate\Support\Str::startsWith($filterRange, 'manual_')) {
            $manualId = str_replace('manual_', '', $filterRange);
            $billQuery->where('academic_year_id', $manualId);
            $paymentQuery->where('academic_year_id', $manualId);
            $expenseQuery->where('academic_year_id', $manualId);
        } else {
            // Default: up_to_active
            // Tunggakan dihitung dari masa lalu sampai semester aktif
            $billQuery->whereHas('academicYear', function($q) use ($activeYear) {
                if ($activeYear) {
                    $q->where('start_date', '<=', $activeYear->start_date);
                }
            });
            // Pemasukan dan pengeluaran khusus semester ini saja
            $paymentQuery->where('academic_year_id', $activeYearId);
            $expenseQuery->where('academic_year_id', $activeYearId);
        }
        
        $totalTagihan = (clone $billQuery)->sum('total_amount');
        $totalPembayaranTagihan = (clone $billQuery)->sum('total_paid');
        $totalTunggakan = $totalTagihan - $totalPembayaranTagihan;
        
        $persentasePembayaran = $totalTagihan > 0 ? round(($totalPembayaranTagihan / $totalTagihan) * 100, 2) : 0;

        // Pemasukan & Pengeluaran
        $totalPembayaran = $paymentQuery->sum('total_amount');
        $totalPengeluaran = $expenseQuery->sum('amount');
            
        $saldoKas = $totalPembayaran - $totalPengeluaran;

        $stats = [
            'total_siswa' => $totalSiswa,
            'total_tagihan' => $totalTagihan,
            'total_pembayaran' => $totalPembayaran,
            'total_tunggakan' => $totalTunggakan,
            'persentase_pembayaran' => $persentasePembayaran,
            'saldo_kas' => $saldoKas,
            'total_pengeluaran' => $totalPengeluaran,
            'tahun_ajaran' => $activeYear ? $activeYear->name . ' (' . $activeYear->semester . ')' : 'Belum Diatur'
        ];

        // Tunggakan Per Pos Keuangan
        $financePosts = FinancePost::withSum(['bills' => function($q) use ($filterRange, $activeYearId, $activeYear) {
            if ($filterRange === 'active_semester') {
                $q->where('academic_year_id', $activeYearId);
            } elseif ($filterRange === 'active_year') {
                $yearIds = AcademicYear::getSameYearIds($activeYearId);
                $q->whereIn('academic_year_id', $yearIds);
            } elseif ($filterRange === 'all_time') {
                // No filter
            } elseif (\Illuminate\Support\Str::startsWith($filterRange, 'manual_')) {
                $manualId = str_replace('manual_', '', $filterRange);
                $q->where('academic_year_id', $manualId);
            } else { // up_to_active
                if ($activeYear) {
                    $q->whereHas('academicYear', function($sq) use ($activeYear) {
                        $sq->where('start_date', '<=', $activeYear->start_date);
                    });
                }
            }
        }], 'total_amount')
        ->withSum(['bills' => function($q) use ($filterRange, $activeYearId, $activeYear) {
            if ($filterRange === 'active_semester') {
                $q->where('academic_year_id', $activeYearId);
            } elseif ($filterRange === 'active_year') {
                $yearIds = AcademicYear::getSameYearIds($activeYearId);
                $q->whereIn('academic_year_id', $yearIds);
            } elseif ($filterRange === 'all_time') {
                // No filter
            } elseif (\Illuminate\Support\Str::startsWith($filterRange, 'manual_')) {
                $manualId = str_replace('manual_', '', $filterRange);
                $q->where('academic_year_id', $manualId);
            } else { // up_to_active
                if ($activeYear) {
                    $q->whereHas('academicYear', function($sq) use ($activeYear) {
                        $sq->where('start_date', '<=', $activeYear->start_date);
                    });
                }
            }
        }], 'total_paid')
        ->get();

        $tunggakanPerPos = collect();
        foreach($financePosts as $pos) {
            $tagihan = $pos->bills_sum_total_amount ?? 0;
            $dibayar = $pos->bills_sum_total_paid ?? 0;
            $tunggakan = $tagihan - $dibayar;
            if ($tunggakan > 0) {
                $tunggakanPerPos->push([
                    'name' => $pos->name,
                    'tunggakan' => $tunggakan
                ]);
            }
        }
        $tunggakanPerPos = $tunggakanPerPos->sortByDesc('tunggakan')->values();

        // Tunggakan per kelas
        $tunggakanPerKelasRaw = \Illuminate\Support\Facades\DB::table('bills')
            ->join('student_classes', function($join) {
                $join->on('bills.student_id', '=', 'student_classes.student_id')
                     ->on('bills.academic_year_id', '=', 'student_classes.academic_year_id');
            })
            ->join('classes as school_classes', 'student_classes.class_id', '=', 'school_classes.id')
            ->select(
                'school_classes.name as class_name',
                \Illuminate\Support\Facades\DB::raw('SUM(bills.total_amount) as total_tagihan'),
                \Illuminate\Support\Facades\DB::raw('SUM(bills.total_paid) as total_dibayar')
            );
            
        if ($filterRange === 'active_semester') {
            $tunggakanPerKelasRaw->where('bills.academic_year_id', $activeYearId);
        } elseif ($filterRange === 'active_year') {
            $yearIds = AcademicYear::getSameYearIds($activeYearId);
            $tunggakanPerKelasRaw->whereIn('bills.academic_year_id', $yearIds);
        } elseif ($filterRange === 'all_time') {
            // no filter
        } elseif (\Illuminate\Support\Str::startsWith($filterRange, 'manual_')) {
            $manualId = str_replace('manual_', '', $filterRange);
            $tunggakanPerKelasRaw->where('bills.academic_year_id', $manualId);
        } else {
            if ($activeYear) {
                $tunggakanPerKelasRaw->join('academic_years', 'bills.academic_year_id', '=', 'academic_years.id')
                    ->where('academic_years.start_date', '<=', $activeYear->start_date);
            }
        }
        
        $tunggakanPerKelasRaw = $tunggakanPerKelasRaw->groupBy('school_classes.id', 'school_classes.name')->get();
        
        $tunggakanPerKelas = collect();
        foreach($tunggakanPerKelasRaw as $row) {
            $tunggakan = $row->total_tagihan - $row->total_dibayar;
            if ($tunggakan > 0) {
                $tunggakanPerKelas->push([
                    'name' => $row->class_name,
                    'tunggakan' => $tunggakan
                ]);
            }
        }
        $tunggakanPerKelas = $tunggakanPerKelas->sortByDesc('tunggakan')->values();

        // Data Grafik Pembayaran 12 Bulan Terakhir
        $chartData = ['labels' => [], 'data' => []];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $total = Payment::where('status', 'success')
                ->where('payment_method', '!=', 'Gratis / Beasiswa')
                ->whereYear('date', $date->year)
                ->whereMonth('date', $date->month)
                ->sum('total_amount');
                
            $chartData['labels'][] = $date->translatedFormat('M Y');
            $chartData['data'][] = $total;
        }

        if ($user->hasRole('Super Admin')) {
            $view = 'dashboard.superadmin';
        } elseif ($user->hasRole('Admin')) {
            $view = 'dashboard.admin';
        } elseif ($user->hasRole('Kepala Sekolah')) {
            $view = 'dashboard.kepala_sekolah';
        } elseif ($user->hasRole('Yayasan')) {
            $view = 'dashboard.yayasan';
        } elseif ($user->hasRole('Kepala Tata Usaha') || $user->hasRole('Tata Usaha')) {
            $view = 'dashboard.tata_usaha';
        } elseif ($user->hasRole('Orang Tua')) {
            $view = 'dashboard.orang_tua';
        } else {
            // Default fallback
            $view = 'dashboard.tata_usaha'; 
        }

        return view($view, compact('stats', 'tunggakanPerPos', 'tunggakanPerKelas', 'chartData', 'allAcademicYears'));
    }
}
