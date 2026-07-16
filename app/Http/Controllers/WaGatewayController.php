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

    public function sendBulk(Request $request)
    {
        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id'
        ]);

        $students = Student::whereIn('id', $request->student_ids)->get();
        
        $messages = [];
        $petugasName = Auth::user()->name;

        foreach ($students as $student) {
            if (!$student->phone) {
                continue; // Lewati siswa yang tidak ada nomor telepon
            }

            // Hitung total tunggakan untuk anak ini
            $bills = Bill::where('student_id', $student->id)
                         ->where('status', '!=', 'Lunas')
                         ->get();

            if ($bills->isEmpty()) {
                continue; // Anak tidak punya tunggakan
            }

            $totalTunggakan = 0;
            $rincian = "";
            foreach ($bills as $bill) {
                $sisaTagihan = $bill->total_amount - $bill->total_paid;
                if ($sisaTagihan > 0) {
                    $totalTunggakan += $sisaTagihan;
                    
                    // Cek apakah ada detail bulan untuk rincian yang lebih jelas
                    $detailMonths = [];
                    foreach ($bill->details as $detail) {
                        $sisaDetail = $detail->amount - $detail->paid_amount;
                        if ($sisaDetail > 0 && $detail->month) {
                            $detailMonths[] = $detail->month;
                        }
                    }

                    $namaPos = $bill->financePost->name ?? 'Tagihan';
                    if (count($detailMonths) > 0) {
                        $namaPos .= " (" . implode(', ', $detailMonths) . ")";
                    }

                    $rincian .= "- " . $namaPos . ": Rp " . number_format($sisaTagihan, 0, ',', '.') . "\n";
                }
            }

            // Ambil template dari pengaturan
            $templateType = $request->input('type') === 'personal' ? 'wa_template_personal' : 'wa_template_bulk';
            $templateText = \App\Models\SchoolSetting::get($templateType);
            
            if (!$templateText) {
                // Default fallback jika kosong
                if ($templateType === 'wa_template_personal') {
                    $templateText = "Halo, Orang Tua/Wali dari *[NAMA_SISWA]*\n\nBerdasarkan catatan kami, ananda memiliki tagihan biaya sekolah sebesar *Rp [TOTAL_TUNGGAKAN]*.\n\nBerikut rinciannya:\n[RINCIAN]\nMohon untuk segera diselesaikan. Terima kasih.";
                } else {
                    $templateText = "PEMBERITAHUAN MASSAL\nHalo, Wali Murid dari *[NAMA_SISWA]*\n\nKami menginformasikan adanya tagihan sekolah yang belum lunas sebesar *Rp [TOTAL_TUNGGAKAN]*.\n\nRincian:\n[RINCIAN]\nHarap segera melunasi tagihan tersebut. Abaikan pesan ini jika sudah membayar.";
                }
            }

            // Replace Placeholder
            $formattedTunggakan = number_format($totalTunggakan, 0, ',', '.');
            $text = str_replace(
                ['[NAMA_SISWA]', '[TOTAL_TUNGGAKAN]', '[RINCIAN]'],
                [$student->name, $formattedTunggakan, $rincian],
                $templateText
            );

            // Tambahkan watermark paten (tidak bisa dihapus)
            $text .= "\n\n---\n";
            $text .= "_Pesan ini dikirim secara otomatis oleh " . $petugasName . " - Sistem Keuangan Sekolah_";

            $messages[] = [
                'phone' => $student->phone,
                'message' => $text
            ];
        }

        if (count($messages) === 0) {
            return back()->with('error', 'Tidak ada pesan yang bisa dikirim. Pastikan siswa yang dipilih memiliki tagihan dan nomor telepon (WhatsApp).');
        }

        try {
            $delayMin = intval(\App\Models\SchoolSetting::get('wa_delay_min', 3));
            $delayMax = intval(\App\Models\SchoolSetting::get('wa_delay_max', 7));

            $response = Http::timeout(10)->post($this->getGatewayUrl() . '/send-bulk', [
                'messages'  => $messages,
                'delayMin'  => $delayMin,
                'delayMax'  => $delayMax,
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
}
