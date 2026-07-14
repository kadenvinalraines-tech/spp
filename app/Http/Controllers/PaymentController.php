<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Bill;
use App\Models\BillDetail;
use App\Models\Payment;
use App\Models\PaymentDetail;
use App\Http\Requests\StorePaymentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $student = null;
        $unpaidBills = collect();
        $paymentHistory = collect();
        
        $academicYears = \App\Models\AcademicYear::orderBy('start_date', 'desc')->get();
        $activeYearId = session('active_academic_year_id');
        // Jika belum ada di session, cari yang is_active = true
        if (!$activeYearId) {
            $activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
            $activeYearId = $activeYear ? $activeYear->id : null;
        }

        $selectedAcademicYearId = $request->has('academic_year_id') ? $request->get('academic_year_id') : $activeYearId;
        $selectedYear = $selectedAcademicYearId !== 'all' ? $academicYears->firstWhere('id', $selectedAcademicYearId) : null;

        if ($request->filled('student_id')) {
            $student = Student::with(['studentClasses.schoolClass'])->find($request->student_id);
            
            if ($student) {
                // Get Unpaid/Partial Bills
                $query = Bill::with(['financePost', 'academicYear', 'details' => function($q) {
                    $q->where('status', '!=', 'paid');
                }])
                ->where('student_id', $student->id)
                ->where('status', '!=', 'paid');

                if ($selectedYear) {
                    // Hanya tampilkan tagihan tahun ajaran yang dipilih, DAN tahun-tahun sebelumnya (Tunggakan)
                    $query->whereHas('academicYear', function($q) use ($selectedYear) {
                        $q->where('start_date', '<=', $selectedYear->start_date);
                    });
                }

                $unpaidBills = $query->get();

                // Get Payment History
                $paymentHistory = Payment::with(['user', 'details.billDetail.bill.financePost'])
                ->where('student_id', $student->id)
                ->latest()
                ->get();
            }
        }

        // Untuk fitur pencarian siswa via select (semua status agar tunggakan bisa dibayar)
        $students = Student::with(['studentClasses.schoolClass'])->get();

        return view('payments.index', compact('student', 'unpaidBills', 'paymentHistory', 'students', 'academicYears', 'selectedAcademicYearId'));
    }

    public function store(StorePaymentRequest $request)
    {
        $data = $request->validated();
        
        try {
            DB::beginTransaction();

            $billDetail = BillDetail::with('bill')->lockForUpdate()->findOrFail($data['bill_detail_id']);
            $bill = $billDetail->bill;

            $amountToPay = floatval($data['amount']);
            $remainingAmount = $billDetail->amount - $billDetail->paid_amount;

            if ($amountToPay > $remainingAmount) {
                return back()->with('error', 'Nominal bayar melebihi sisa tagihan.');
            }

            $activeYearId = \App\Models\AcademicYear::getActiveId();

            // Generate Transaction Number (Format: YYYYMMDD-NIS-SEQ)
            $todayDate = now()->toDateString();
            $todayCount = Payment::whereDate('created_at', $todayDate)->count() + 1;
            $sequence = str_pad($todayCount, 3, '0', STR_PAD_LEFT);
            $transactionNumber = date('Ymd') . '-' . $bill->student->nis . '-' . $sequence;

            $payment = Payment::create([
                'transaction_number' => $transactionNumber,
                'student_id' => $bill->student_id,
                'user_id' => auth()->id(),
                'academic_year_id' => $activeYearId,
                'date' => now()->toDateString(),
                'total_amount' => $amountToPay,
                'payment_method' => $data['payment_method'],
                'status' => 'success',
            ]);

            // Create PaymentDetail
            PaymentDetail::create([
                'payment_id' => $payment->id,
                'bill_detail_id' => $billDetail->id,
                'amount_paid' => $amountToPay,
            ]);

            // Update BillDetail
            $billDetail->paid_amount += $amountToPay;
            if ($billDetail->paid_amount >= $billDetail->amount) {
                $billDetail->status = 'paid';
            }
            $billDetail->save();

            // Update parent Bill
            $bill->total_paid += $amountToPay;
            if ($bill->total_paid >= $bill->total_amount) {
                $bill->status = 'paid';
            } else {
                $bill->status = 'partial';
            }
            $bill->save();

            DB::commit();

            // Kirim notifikasi WA (Dipisahkan ke method private)
            $this->sendWaNotification($bill, $billDetail, $amountToPay);

            return redirect()->back()->with('success', "Pembayaran berhasil diproses dengan No Transaksi: {$transactionNumber}");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    public function print(Payment $payment)
    {
        $payment->load(['student.studentClasses.schoolClass', 'user', 'details.billDetail.bill.financePost']);
        $settings = \App\Models\SchoolSetting::pluck('value', 'key')->all();

        $pdf = Pdf::loadView('payments.receipt_pdf', compact('payment', 'settings'));
        
        return $pdf->stream("kuitansi-{$payment->transaction_number}.pdf");
    }

    private function sendWaNotification(Bill $bill, BillDetail $billDetail, float $amountToPay)
    {
        $student = $bill->student;
        if (!$student || !$student->phone) return;

        try {
            $templateText = \App\Models\SchoolSetting::get('wa_template_receipt');
            if (!$templateText) {
                $templateText = "TERIMA KASIH\n\nHalo, Wali Murid dari *[NAMA_SISWA]*\nKami telah menerima pembayaran sebesar *Rp [NOMINAL_BAYAR]*.\n\nRincian Pembayaran:\n[RINCIAN_BAYAR]\n\nSisa Tunggakan Saat Ini: *Rp [SISA_TAGIHAN]*\nTerima kasih atas kerja samanya.";
            }

            $namaPos = $bill->financePost->name ?? 'Tagihan';
            if ($billDetail->month) {
                $namaPos .= " (" . $billDetail->month . ")";
            }
            $rincianBayar = "- " . $namaPos . ": Rp " . number_format($amountToPay, 0, ',', '.');
            
            // Hitung Sisa Tunggakan dan Rinciannya
            $unpaidBills = Bill::with('financePost')->where('student_id', $student->id)->get();
            $sisaTunggakan = 0;
            $rincianTunggakanArr = [];

            foreach($unpaidBills as $b) {
                $sisa = $b->total_amount - $b->total_paid;
                if($sisa > 0) {
                    $sisaTunggakan += $sisa;
                    $namaPos = $b->financePost->name ?? 'Tagihan';
                    $rincianTunggakanArr[] = "- " . $namaPos . ": Rp " . number_format($sisa, 0, ',', '.');
                }
            }
            
            $rincianTunggakanText = empty($rincianTunggakanArr) ? "Tidak ada tunggakan." : implode("\n", $rincianTunggakanArr);

            $formattedBayar = number_format($amountToPay, 0, ',', '.');
            $formattedSisa = number_format($sisaTunggakan, 0, ',', '.');
            $rincianSisaText = $formattedSisa . "\n\nRincian Tunggakan:\n" . $rincianTunggakanText;

            $text = str_replace(
                ['[NAMA_SISWA]', '[NOMINAL_BAYAR]', '[RINCIAN_BAYAR]', '[SISA_TAGIHAN]'],
                [$student->name, $formattedBayar, $rincianBayar, $rincianSisaText],
                $templateText
            );

            $petugasName = auth()->user()->name ?? 'Petugas';
            $text .= "\n\n---\n";
            $text .= "_Kuitansi Elektronik - Diterima oleh " . $petugasName . "_";

            $delayMin = intval(\App\Models\SchoolSetting::get('wa_delay_min', 3));
            $delayMax = intval(\App\Models\SchoolSetting::get('wa_delay_max', 7));

            $gatewayUrl = env('WA_GATEWAY_URL', 'http://localhost:3000');
            \Illuminate\Support\Facades\Http::timeout(5)->post($gatewayUrl . '/send-bulk', [
                'messages' => [
                    [
                        'phone' => $student->phone,
                        'message' => $text
                    ]
                ],
                'delayMin' => $delayMin,
                'delayMax' => $delayMax,
            ]);
        } catch (\Exception $waErr) {
            // Abaikan error WA agar pembayaran tetap sukses
        }
    }
}
