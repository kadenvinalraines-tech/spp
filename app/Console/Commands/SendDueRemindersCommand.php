<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Bill;
use App\Models\SchoolSetting;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class SendDueRemindersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spp:send-due-reminders {--days= : Override hari pengingat (contoh: 0, -3, 3)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim pengingat tagihan jatuh tempo via WA Gateway';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Mulai mengecek tagihan jatuh tempo...');

        // Get delay settings
        $delayMin = intval(SchoolSetting::get('wa_delay_min', 3));
        $delayMax = intval(SchoolSetting::get('wa_delay_max', 7));

        // Determine target date
        $reminderDays = $this->option('days') !== null ? (int) $this->option('days') : (int) SchoolSetting::get('wa_due_reminder_days', 0);
        
        // If reminder is H-3, means we look for due date = today + 3 days
        // If reminder is H+3, means we look for due date = today - 3 days
        // Wait, if reminderDays is -3, due date is today + 3 days. So subtract reminderDays from today? No.
        // Let's standardise: reminderDays = -3 means H-3. Today + 3 days = due date.
        // reminderDays = 0 means H=0. Today = due date.
        // reminderDays = 3 means H+3. Today - 3 days = due date.
        
        $targetDate = Carbon::today()->addDays(-$reminderDays)->format('Y-m-d');
        
        $this->info("Mencari tagihan dengan status belum lunas dan jatuh tempo pada: {$targetDate}");

        $bills = Bill::with(['student.user'])
            ->whereIn('status', ['unpaid', 'partial'])
            ->whereNotNull('due_date')
            ->whereDate('due_date', $targetDate)
            ->get();

        if ($bills->isEmpty()) {
            $this->info('Tidak ada tagihan yang sesuai untuk diingatkan hari ini.');
            return;
        }

        $this->info("Ditemukan {$bills->count()} tagihan. Menyiapkan pesan...");

        $templateText = SchoolSetting::get('wa_template_due_reminder', "PENGINGAT TAGIHAN\n\nHalo, Wali Murid dari *[NAMA_SISWA]*\n\nKami mengingatkan bahwa tagihan sekolah ananda sebesar *Rp [TOTAL_TUNGGAKAN]* akan jatuh tempo pada *[JATUH_TEMPO]*.\n\nBerikut rinciannya:\n[RINCIAN]\n\nMohon kerjasamanya untuk menyelesaikan pembayaran sebelum tanggal tersebut.\nAbaikan pesan ini jika sudah melakukan pembayaran. Terima kasih.");
        
        if (strpos($templateText, '[RINCIAN]') === false) {
            $templateText .= "\n\nBerikut rinciannya:\n[RINCIAN]";
        }
        $messages = [];
        $gatewayUrl = SchoolSetting::get('wa_gateway_url', env('WA_GATEWAY_URL', 'http://localhost:3000'));
        $signature = "\n\n_" . auth()->user()?->name ?? 'Admin / Keuangan' . "_"; // Signature might not be available in CLI auth. Let's use a generic one if auth is null.
        
        foreach ($bills as $bill) {
            $student = $bill->student;
            $phone = $student->phone ?? ($student->user->phone ?? null);

            if (!$phone) {
                continue;
            }

            // Normalisasi nomor telepon
            $phone = preg_replace('/[^0-9]/', '', $phone);
            if (substr($phone, 0, 1) === '0') {
                $phone = '62' . substr($phone, 1);
            }
            $phone = $phone . '@c.us';

            $remainingAmount = $bill->total_amount - $bill->total_paid;
            $rincian = "- " . ($bill->financePost->name ?? 'Tagihan') . ": Rp " . number_format($remainingAmount, 0, ',', '.');
            
            $dueDateFormatted = Carbon::parse($bill->due_date)->translatedFormat('d F Y');

            $message = str_replace(
                ['[NAMA_SISWA]', '[TOTAL_TUNGGAKAN]', '[RINCIAN]', '[JATUH_TEMPO]'],
                [$student->name, number_format($remainingAmount, 0, ',', '.'), $rincian, $dueDateFormatted],
                $templateText
            );

            // Add generic signature for automated system
            $message .= "\n\n_Pesan otomatis dari Sistem Keuangan Sekolah_";

            $messages[] = [
                'to' => $phone,
                'text' => $message
            ];
        }

        if (empty($messages)) {
            $this->info('Tidak ada siswa dengan nomor WhatsApp valid untuk diingatkan.');
            return;
        }

        $this->info("Mengirim " . count($messages) . " pesan ke WA Gateway...");

        try {
            $response = Http::timeout(10)->post($gatewayUrl . '/send-bulk', [
                'messages' => $messages,
                'delayMin' => $delayMin,
                'delayMax' => $delayMax
            ]);

            if ($response->successful()) {
                $this->info('Pesan berhasil masuk ke antrean WhatsApp Gateway.');
            } else {
                $this->error('Gagal memproses antrean. Pesan dari Gateway: ' . $response->json('message', 'Unknown Error'));
            }
        } catch (\Exception $e) {
            $this->error('Gagal terhubung ke WhatsApp Gateway Lokal. Pastikan server Node.js sedang berjalan. Error: ' . $e->getMessage());
        }
    }
}
