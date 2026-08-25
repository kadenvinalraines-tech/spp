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

                // Filter details by isDue() and remove bills that have no due details
                foreach ($unpaidBills as $key => $bill) {
                    $dueDetails = $bill->details->filter(function($detail) {
                        return $detail->isDue();
                    });
                    
                    if ($dueDetails->isEmpty()) {
                        $unpaidBills->forget($key);
                    } else {
                        $bill->setRelation('details', $dueDetails);
                    }
                }

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

            $totalAmountToPay = 0;
            $studentId = null;
            $studentNis = null;
            $billDetails = [];
            
            foreach ($data['payments'] as $paymentData) {
                $billDetail = BillDetail::with('bill.student')->lockForUpdate()->findOrFail($paymentData['bill_detail_id']);
                $amountToPay = floatval($paymentData['amount']);
                
                $remainingAmount = $billDetail->amount - $billDetail->paid_amount;
                if ($amountToPay > $remainingAmount) {
                    return back()->with('error', 'Nominal bayar melebihi sisa tagihan pada salah satu item.');
                }
                
                $totalAmountToPay += $amountToPay;
                if (!$studentId) {
                    $studentId = $billDetail->bill->student_id;
                    $studentNis = $billDetail->bill->student->nis;
                }
                
                $billDetails[] = [
                    'model' => $billDetail,
                    'amountToPay' => $amountToPay,
                ];
            }

            if ($totalAmountToPay <= 0) {
                 return back()->with('error', 'Total nominal bayar tidak valid.');
            }

            $activeYearId = \App\Models\AcademicYear::getActiveId();

            $todayDate = now()->toDateString();
            $todayCount = Payment::whereDate('created_at', $todayDate)->count() + 1;
            $sequence = str_pad($todayCount, 3, '0', STR_PAD_LEFT);
            $transactionNumber = date('Ymd') . '-' . $studentNis . '-' . $sequence;

            $payment = Payment::create([
                'transaction_number' => $transactionNumber,
                'student_id' => $studentId,
                'user_id' => auth()->id(),
                'academic_year_id' => $activeYearId,
                'date' => now()->toDateString(),
                'total_amount' => $totalAmountToPay,
                'payment_method' => $data['payment_method'],
                'status' => 'success',
            ]);

            foreach ($billDetails as $item) {
                $bd = $item['model'];
                $amt = $item['amountToPay'];
                $bill = $bd->bill;
                
                PaymentDetail::create([
                    'payment_id' => $payment->id,
                    'bill_detail_id' => $bd->id,
                    'amount_paid' => $amt,
                ]);

                if ($data['payment_method'] === 'Gratis / Beasiswa') {
                    // Decrease the total amount of the bill (discount) instead of counting it as paid money
                    $bd->amount -= $amt;
                    if ($bd->amount < 0) {
                        $bd->amount = 0;
                    }
                    if ($bd->paid_amount >= $bd->amount) {
                        $bd->status = 'paid';
                    }
                    $bd->save();

                    $bill->total_amount -= $amt;
                    if ($bill->total_amount < 0) {
                        $bill->total_amount = 0;
                    }
                    if ($bill->total_paid >= $bill->total_amount) {
                        $bill->status = 'paid';
                    } else {
                        $bill->status = 'partial';
                    }
                    $bill->save();
                } else {
                    $bd->paid_amount += $amt;
                    if ($bd->paid_amount >= $bd->amount) {
                        $bd->status = 'paid';
                    }
                    $bd->save();

                    $bill->total_paid += $amt;
                    if ($bill->total_paid >= $bill->total_amount) {
                        $bill->status = 'paid';
                    } else {
                        $bill->status = 'partial';
                    }
                    $bill->save();
                }
            }

            DB::commit();

            $this->sendWaNotification($payment);

            return redirect()->back()->with('success', "Pembayaran massal berhasil diproses dengan No Transaksi: {$transactionNumber}");

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

    private function sendWaNotification(Payment $payment)
    {
        $payment->load(['student', 'details.billDetail.bill.financePost']);
        $student = $payment->student;
        if (!$student || !$student->phone) return;

        try {
            $templateText = \App\Models\SchoolSetting::get('wa_template_receipt');
            if (!$templateText) {
                $templateText = "TERIMA KASIH\n\nHalo, Wali Murid dari *[NAMA_SISWA]*\nKami telah menerima pembayaran sebesar *Rp [NOMINAL_BAYAR]*.\n\nRincian Pembayaran:\n[RINCIAN_BAYAR]\n\nSisa Tunggakan Saat Ini: *Rp [SISA_TAGIHAN]*\nTerima kasih atas kerja samanya.";
            }

            $rincianBayarArr = [];
            foreach ($payment->details as $pd) {
                $bd = $pd->billDetail;
                $namaPos = $bd->bill->financePost->name ?? 'Tagihan';
                if ($bd->month) {
                    $namaPos .= " (" . $bd->month . ")";
                }
                $rincianBayarArr[] = "- " . $namaPos . ": Rp " . number_format($pd->amount_paid, 0, ',', '.');
            }
            $rincianBayar = implode("\n", $rincianBayarArr);
            
            // Hitung Sisa Tunggakan dan Rinciannya
            $unpaidBills = Bill::with(['financePost', 'academicYear', 'details'])->where('student_id', $student->id)->get();
            $sisaTunggakan = 0;
            $rincianTunggakanArr = [];

            foreach($unpaidBills as $b) {
                $dueDetails = $b->details->filter(function($detail) {
                    return $detail->isDue() && $detail->amount > $detail->paid_amount;
                });
                
                foreach ($dueDetails as $detail) {
                    $sisa = $detail->amount - $detail->paid_amount;
                    $sisaTunggakan += $sisa;
                    $namaPos = $b->financePost->name ?? 'Tagihan';
                    if ($detail->month) {
                        $monthName = \Carbon\Carbon::create()->month($detail->month)->translatedFormat('F');
                        $startYear = \Carbon\Carbon::parse($b->academicYear->start_date ?? now())->year;
                        $billYear = ($detail->month >= 7 && $detail->month <= 12) ? $startYear : $startYear + 1;
                        $namaPos .= " (" . strtoupper($monthName) . " " . $billYear . ")";
                    }
                    $rincianTunggakanArr[] = "- " . $namaPos . ": Rp " . number_format($sisa, 0, ',', '.');
                }
            }
            
            $rincianTunggakanText = empty($rincianTunggakanArr) ? "Tidak ada tunggakan." : implode("\n", $rincianTunggakanArr);

            $formattedBayar = number_format($payment->total_amount, 0, ',', '.');
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

            $delayMinMs = intval(\App\Models\SchoolSetting::get('wa_delay_min', 3)) * 1000;
            $delayMaxMs = intval(\App\Models\SchoolSetting::get('wa_delay_max', 7)) * 1000;

            $gatewayUrl = \App\Models\SchoolSetting::get('wa_gateway_url', env('WA_GATEWAY_URL', 'http://localhost:3000'));
            
            $messages = [
                [
                    'phone' => $student->phone,
                    'message' => $text
                ]
            ];

            // Cek apakah ada tagihan bulanan (1 tahun) yang menjadi lunas karena pembayaran ini
            $fullyPaidBills = [];
            foreach ($payment->details as $pd) {
                $bill = $pd->billDetail->bill;
                if ($bill->status === 'paid' && $bill->financePost->type === 'monthly' && !in_array($bill->id, $fullyPaidBills)) {
                    $fullyPaidBills[] = $bill->id;
                    $namaPos = $bill->financePost->name;
                    $messages[] = [
                        'phone' => $student->phone,
                        'message' => "🎉 *SELAMAT!* 🎉\n\nTagihan *{$namaPos}* untuk 1 TAHUN PENUH telah dinyatakan *LUNAS*.\nTerima kasih atas kedisiplinan dan kerja samanya!"
                    ];
                }
            }

            \Illuminate\Support\Facades\Http::timeout(5)->post($gatewayUrl . '/send-bulk', [
                'messages' => $messages,
                'delayMin' => $delayMinMs,
                'delayMax' => $delayMaxMs,
            ]);
        } catch (\Exception $waErr) {
            // Abaikan error WA agar pembayaran tetap sukses
        }
    }
}
