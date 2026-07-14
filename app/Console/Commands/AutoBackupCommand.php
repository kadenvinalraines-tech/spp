<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SchoolSetting;
use Ifsnop\Mysqldump\Mysqldump;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AutoBackupCommand extends Command
{
    protected $signature = 'app:auto-backup';
    protected $description = 'Run automatic database backup if scheduled';

    public function handle()
    {
        $schedule = SchoolSetting::get('backup_schedule', 'none');
        $waNumber = SchoolSetting::get('backup_wa_number');

        if ($schedule == 'none' || empty($waNumber)) {
            $this->info('Auto backup is disabled or WA number is not set.');
            return;
        }

        $shouldRun = false;
        
        // As this command will be run daily via scheduler, we check if today is the correct day
        if ($schedule == 'daily') {
            $shouldRun = true;
        } elseif ($schedule == 'weekly') {
            // Run on Sunday (0)
            if (date('w') == 0) {
                $shouldRun = true;
            }
        } elseif ($schedule == 'monthly') {
            // Run on 1st of the month
            if (date('j') == 1) {
                $shouldRun = true;
            }
        }

        if (!$shouldRun) {
            $this->info('Not scheduled to run today.');
            return;
        }

        $this->info('Starting database backup...');

        try {
            $backupDir = storage_path('app/public/backups');
            if (!file_exists($backupDir)) {
                mkdir($backupDir, 0755, true);
            }

            $date = date('Y-m-d_H-i-s');
            $fileName = "backup_spp_auto_{$date}.sql";
            $filePath = $backupDir . DIRECTORY_SEPARATOR . $fileName;

            $host = env('DB_HOST', '127.0.0.1');
            $dbName = env('DB_DATABASE', 'spp');
            $user = env('DB_USERNAME', 'root');
            $pass = env('DB_PASSWORD', '');

            $dumpSettings = ['add-drop-table' => true];
            $dump = new Mysqldump("mysql:host={$host};dbname={$dbName}", $user, $pass, $dumpSettings);
            $dump->start($filePath);

            $this->info("Backup created at {$filePath}");

            // Send via WA
            $caption = "*[AUTO BACKUP]*\nBerikut adalah file otomatis backup database SKS tanggal " . date('d M Y H:i:s') . ". \n\nHarap simpan file ini dengan aman.";
            
            $response = Http::post('http://localhost:3000/send-bulk', [
                'messages' => [
                    [
                        'phone' => $waNumber,
                        'message' => $caption,
                        'media' => $filePath
                    ]
                ],
                'delayMin' => 1,
                'delayMax' => 2
            ]);

            if ($response->successful()) {
                $this->info('Backup sent to WA successfully.');
            } else {
                $this->error('Failed to send backup to WA.');
                Log::error('Auto Backup failed to send to WA. WA Gateway might be down.');
            }

        } catch (\Exception $e) {
            $this->error('Backup error: ' . $e->getMessage());
            Log::error('Auto Backup Error: ' . $e->getMessage());
        }
    }
}
