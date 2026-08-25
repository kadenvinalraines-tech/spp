<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SchoolSetting;
use Ifsnop\Mysqldump\Mysqldump;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BackupController extends Controller
{
    public function manualBackup()
    {
        try {
            $waNumber = SchoolSetting::get('backup_wa_number');
            if (empty($waNumber)) {
                return redirect()->back()->with('error', 'Nomor WA penerima backup belum diatur. Silakan atur di Pengaturan.');
            }

            // Create backup directory if not exists
            $backupDir = storage_path('app/public/backups');
            if (!file_exists($backupDir)) {
                mkdir($backupDir, 0755, true);
            }

            $date = date('Y-m-d_H-i-s');
            $fileName = "backup_spp_{$date}.sql";
            $filePath = $backupDir . DIRECTORY_SEPARATOR . $fileName;

            // Database connection info
            $host = env('DB_HOST', '127.0.0.1');
            $dbName = env('DB_DATABASE', 'spp');
            $user = env('DB_USERNAME', 'root');
            $pass = env('DB_PASSWORD', '');

            // Perform backup
            $dumpSettings = ['add-drop-table' => true];
            $dump = new Mysqldump("mysql:host={$host};dbname={$dbName}", $user, $pass, $dumpSettings);
            $dump->start($filePath);

            // Send via WA
            $caption = "*[OTOMATIS]*\nBerikut adalah file backup database SKS tanggal " . date('d M Y H:i:s') . ". \n\nHarap simpan file ini dengan aman.";
            
            $gatewayUrl = \App\Models\SchoolSetting::get('wa_gateway_url', env('WA_GATEWAY_URL', 'http://localhost:3000'));
            $response = Http::post(rtrim($gatewayUrl, '/') . '/send-bulk', [
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
                return redirect()->back()->with('success', 'Backup berhasil dilakukan dan sedang dikirim ke WhatsApp Anda.');
            } else {
                return redirect()->back()->with('warning', 'Backup berhasil dibuat di lokal, tetapi gagal dikirim ke WA. Pastikan server WA aktif.');
            }

        } catch (\Exception $e) {
            Log::error('Database Backup Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat membackup database: ' . $e->getMessage());
        }
    }

    public function downloadBackup()
    {
        try {
            $backupDir = storage_path('app/public/backups');
            if (!file_exists($backupDir)) {
                mkdir($backupDir, 0755, true);
            }

            $date = date('Y-m-d_H-i-s');
            $fileName = "backup_spp_manual_{$date}.sql";
            $filePath = $backupDir . DIRECTORY_SEPARATOR . $fileName;

            $host = env('DB_HOST', '127.0.0.1');
            $dbName = env('DB_DATABASE', 'spp');
            $user = env('DB_USERNAME', 'root');
            $pass = env('DB_PASSWORD', '');

            $dumpSettings = ['add-drop-table' => true];
            $dump = new Mysqldump("mysql:host={$host};dbname={$dbName}", $user, $pass, $dumpSettings);
            $dump->start($filePath);

            return response()->download($filePath)->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            Log::error('Download Backup Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengunduh backup: ' . $e->getMessage());
        }
    }

    public function restoreBackup(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file|max:51200', // max 50MB
        ]);

        try {
            $file = $request->file('backup_file');
            
            // Periksa ekstensi secara manual untuk keamanan
            if ($file->getClientOriginalExtension() !== 'sql') {
                return redirect()->back()->with('error', 'File harus berformat .sql');
            }

            $filePath = $file->getRealPath();
            $dbHost = env('DB_HOST', '127.0.0.1');
            $dbName = env('DB_DATABASE', 'spp');
            $dbUser = env('DB_USERNAME', 'root');
            $dbPass = env('DB_PASSWORD', '');

            // Nonaktifkan pemeriksaan kunci asing sementara jika ada masalah relasi saat DROP
            \Illuminate\Support\Facades\DB::unprepared('SET FOREIGN_KEY_CHECKS=0;');

            // Hapus semua tabel yang ada saat ini sebelum me-restore
            $tables = \Illuminate\Support\Facades\DB::select('SHOW TABLES');
            foreach ($tables as $table) {
                $tableArray = (array) $table;
                $tableName = reset($tableArray);
                if ($tableName) {
                    \Illuminate\Support\Facades\DB::unprepared('DROP TABLE IF EXISTS `' . $tableName . '`');
                }
            }

            $success = false;
            
            // 1. Coba gunakan eksekusi CLI MySQL terlebih dahulu
            if (function_exists('exec')) {
                $mysqlPaths = [
                    'mysql',
                    'C:\xampp\mysql\bin\mysql.exe',
                    '/usr/bin/mysql'
                ];
                
                foreach ($mysqlPaths as $mysql) {
                    if ($mysql === 'mysql' || file_exists($mysql)) {
                        $command = sprintf(
                            '"%s" --host=%s --user=%s %s %s < "%s" 2>&1',
                            $mysql,
                            escapeshellarg($dbHost),
                            escapeshellarg($dbUser),
                            $dbPass ? '--password=' . escapeshellarg($dbPass) : '',
                            escapeshellarg($dbName),
                            $filePath
                        );
                        
                        exec($command, $output, $returnVar);
                        
                        if ($returnVar === 0) {
                            $success = true;
                            break;
                        }
                    }
                }
            }

            // 2. Fallback ke parser PHP murni jika CLI gagal (bypass max_allowed_packet)
            if (!$success) {
                $handle = fopen($filePath, "r");
                if ($handle) {
                    $query = '';
                    while (($line = fgets($handle)) !== false) {
                        $trimmed = trim($line);
                        // Abaikan komentar dan baris kosong
                        if (empty($trimmed) || str_starts_with($trimmed, '--') || str_starts_with($trimmed, '/*')) {
                            continue;
                        }
                        $query .= $line;
                        if (str_ends_with($trimmed, ';')) {
                            \Illuminate\Support\Facades\DB::unprepared($query);
                            $query = '';
                        }
                    }
                    fclose($handle);
                } else {
                    throw new \Exception("Gagal membaca file SQL.");
                }
            }

            \Illuminate\Support\Facades\DB::unprepared('SET FOREIGN_KEY_CHECKS=1;');
            
            // Jalankan migrasi agar tabel krusial seperti 'sessions' yang mungkin tidak ada di file backup lama tetap terbuat.
            \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);

            return redirect()->back()->with('success', 'Database berhasil di-restore dengan sukses.');

        } catch (\Exception $e) {
            Log::error('Restore Backup Error: ' . $e->getMessage());
            // Pastikan FOREIGN_KEY_CHECKS diaktifkan kembali jika terjadi error
            \Illuminate\Support\Facades\DB::unprepared('SET FOREIGN_KEY_CHECKS=1;');
            return redirect()->back()->with('error', 'Terjadi kesalahan saat me-restore database: ' . $e->getMessage());
        }
    }
}
