<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Student;
use App\Models\Bill;
use Illuminate\Support\Facades\Auth;

class WaGatewayController extends Controller
{
    private function getGatewayUrl()
    {
        return \App\Models\SchoolSetting::get('wa_gateway_url', env('WA_GATEWAY_URL', 'http://localhost:3000'));
    }

    public function index()
    {
        return view('wa-gateway.index');
    }

    public function status()
    {
        try {
            $response = Http::timeout(5)->get($this->getGatewayUrl() . '/status');
            return response()->json($response->json());
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function logs()
    {
        try {
            $response = Http::timeout(5)->get($this->getGatewayUrl() . '/logs');
            return response()->json($response->json());
        } catch (\Exception $e) {
            return response()->json([]);
        }
    }

    public function resend(Request $request)
    {
        $request->validate([
            'phone' => 'required',
            'message' => 'required',
            'name' => 'nullable'
        ]);

        try {
            $messages = [
                [
                    'phone' => $request->input('phone'),
                    'name' => $request->input('name', 'Tidak diketahui'),
                    'message' => $request->input('message')
                ]
            ];

            $response = Http::timeout(10)->post($this->getGatewayUrl() . '/send-bulk', [
                'messages'  => $messages,
                'delayMin'  => 0,
                'delayMax'  => 1000,
            ]);

            if ($response->successful() && $response->json('success')) {
                return response()->json(['success' => true, 'message' => 'Pesan dimasukkan kembali ke antrean.']);
            } else {
                return response()->json(['success' => false, 'message' => 'Gagal mengulang pengiriman.']);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal terhubung ke WA Gateway.']);
        }
    }

    public function sendBulk(Request $request)
    {
        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id'
        ]);

        $bills = \App\Models\Bill::whereIn('student_id', $request->student_ids)->with(['student', 'financePost', 'details'])->get();
        
        $messages = [];
        $petugasName = Auth::user()->name;

        // Kelompokkan tagihan berdasarkan siswa
        $groupedBills = $bills->groupBy('student_id');

        foreach ($groupedBills as $studentId => $studentBills) {
            $student = $studentBills->first()->student;
            
            if (!$student || !$student->phone) {
                continue; // Lewati siswa yang tidak ada nomor telepon
            }

            $totalTunggakan = 0;
            $rincian = "";
            $earliestDueDate = null;
            $latestDueDate = null;

            foreach ($studentBills as $bill) {
                if ($bill->status === 'paid') continue;

                $sisaTagihan = 0;
                $detailMonths = [];
                
                foreach ($bill->details as $detail) {
                    if (!$detail->isDue()) continue;

                    $sisaDetail = $detail->amount - $detail->paid_amount;
                    if ($sisaDetail > 0) {
                        $sisaTagihan += $sisaDetail;
                        if ($detail->month) {
                            $detailMonths[] = $detail->month;
                        }
                    }
                }

                if ($sisaTagihan > 0) {
                    $totalTunggakan += $sisaTagihan;
                    
                    $namaPos = $bill->financePost->name ?? 'Tagihan';
                    if (count($detailMonths) > 0) {
                        $namaPos .= " (" . implode(', ', $detailMonths) . ")";
                    }
                    
                    // Format tanggal jatuh tempo individu
                    $itemDueDate = $bill->due_date ? \Carbon\Carbon::parse($bill->due_date)->translatedFormat('d F Y') : '-';
                    $rincian .= "- " . $namaPos . " [Jatuh tempo: {$itemDueDate}]: Rp " . number_format($sisaTagihan, 0, ',', '.') . "\n";
                    
                    // Tracking rentang tanggal jatuh tempo
                    if (!$earliestDueDate || $bill->due_date < $earliestDueDate) $earliestDueDate = $bill->due_date;
                    if (!$latestDueDate || $bill->due_date > $latestDueDate) $latestDueDate = $bill->due_date;
                }
            }
            
            if ($totalTunggakan == 0) continue;

            // Ambil template dari pengaturan
            if ($request->input('type') === 'personal') {
                $templateType = 'wa_template_personal';
            } elseif ($request->input('type') === 'due_reminder') {
                $templateType = 'wa_template_due_reminder';
            } else {
                $templateType = 'wa_template_bulk';
            }

            $templateText = \App\Models\SchoolSetting::get($templateType);
            
            if (!$templateText) {
                // Default fallback jika kosong
                if ($templateType === 'wa_template_personal') {
                    $templateText = "Halo, Orang Tua/Wali dari *[NAMA_SISWA]*\n\nBerdasarkan catatan kami, ananda memiliki tagihan biaya sekolah sebesar *Rp [TOTAL_TUNGGAKAN]*.\n\nBerikut rinciannya:\n[RINCIAN]\nMohon untuk segera diselesaikan. Terima kasih.";
                } elseif ($templateType === 'wa_template_due_reminder') {
                    $templateText = "PENGINGAT TAGIHAN\n\nHalo, Wali Murid dari *[NAMA_SISWA]*\n\nKami mengingatkan bahwa tagihan sekolah ananda sebesar *Rp [TOTAL_TUNGGAKAN]* akan jatuh tempo pada *[JATUH_TEMPO]*.\n\nBerikut rinciannya:\n[RINCIAN]\n\nMohon kerjasamanya untuk menyelesaikan pembayaran sebelum tanggal tersebut.\nAbaikan pesan ini jika sudah melakukan pembayaran. Terima kasih.";
                } else {
                    $templateText = "PEMBERITAHUAN MASSAL\nHalo, Wali Murid dari *[NAMA_SISWA]*\n\nKami menginformasikan adanya tagihan sekolah yang belum lunas sebesar *Rp [TOTAL_TUNGGAKAN]*.\n\nRincian:\n[RINCIAN]\nHarap segera melunasi tagihan tersebut. Abaikan pesan ini jika sudah membayar.";
                }
            }

            // Pastikan template custom dari database tetap menampilkan [RINCIAN]
            if (strpos($templateText, '[RINCIAN]') === false) {
                $templateText .= "\n\nBerikut rinciannya:\n[RINCIAN]";
            }

            // Dapatkan teks jatuh tempo umum (untuk placeholder JATUH_TEMPO jika masih ada)
            if ($earliestDueDate == $latestDueDate && $earliestDueDate) {
                $formattedDueDate = \Carbon\Carbon::parse($earliestDueDate)->translatedFormat('d F Y');
            } else {
                $formattedDueDate = "berbagai tanggal (lihat rincian)";
            }

            // Replace Placeholder
            $formattedTunggakan = number_format($totalTunggakan, 0, ',', '.');
            $text = str_replace(
                ['[NAMA_SISWA]', '[TOTAL_TUNGGAKAN]', '[RINCIAN]', '[JATUH_TEMPO]'],
                [$student->name, $formattedTunggakan, $rincian, $formattedDueDate],
                $templateText
            );

            // Tambahkan watermark paten (tidak bisa dihapus)
            $text .= "\n\n---\n";
            $text .= "_Pesan ini dikirim secara otomatis oleh " . $petugasName . " - Sistem Keuangan Sekolah_";

            $messages[] = [
                'phone' => $student->phone,
                'name' => $student->name,
                'message' => $text
            ];
        }

        if (count($messages) === 0) {
            \Illuminate\Support\Facades\Log::warning('sendBulk: No messages generated.', ['student_ids' => $request->student_ids, 'type' => $request->input('type')]);
            return back()->with('error', 'Tidak ada pesan yang bisa dikirim. Pastikan siswa yang dipilih memiliki tagihan dan nomor telepon (WhatsApp).');
        }

        \Illuminate\Support\Facades\Log::info('sendBulk: Attempting to send messages', ['type' => $request->input('type'), 'count' => count($messages), 'sample' => $messages[0] ?? null]);

        try {
            $delayMinMs = intval(\App\Models\SchoolSetting::get('wa_delay_min', 3)) * 1000;
            $delayMaxMs = intval(\App\Models\SchoolSetting::get('wa_delay_max', 7)) * 1000;

            $response = Http::timeout(10)->post($this->getGatewayUrl() . '/send-bulk', [
                'messages'  => $messages,
                'delayMin'  => $delayMinMs,
                'delayMax'  => $delayMaxMs,
            ]);

            if ($response->successful() && $response->json('success')) {
                return back()->with('success', $response->json('message'));
            } else {
                return back()->with('error', 'Gagal memproses antrean. Pesan dari Gateway: ' . $response->json('message'));
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal terhubung ke WhatsApp Gateway Lokal. Pastikan server Node.js sedang berjalan.');
        }
    }

    public function sendDueReminders()
    {
        try {
            \Illuminate\Support\Facades\Artisan::call('spp:send-due-reminders');
            $output = \Illuminate\Support\Facades\Artisan::output();
            
            // Simpan log atau sekadar tampilkan output artisan di session
            return back()->with('success', 'Perintah pengingat jatuh tempo berhasil dijalankan: ' . $output);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menjalankan perintah pengingat: ' . $e->getMessage());
        }
    }
}
