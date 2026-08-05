<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SchoolSetting;
use Illuminate\Support\Facades\Log;

class SyncTimeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spp:sync-time';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Windows OS time with NTP server';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $ntpServer = SchoolSetting::get('ntp_server', 'time.windows.com');
        $this->info("Starting time synchronization with NTP Server: {$ntpServer}...");
        Log::info("Starting time synchronization with NTP Server: {$ntpServer}...");

        try {
            $cmdConfig = 'w32tm /config /manualpeerlist:"' . $ntpServer . '" /syncfromflags:manual /reliable:yes /update';
            exec($cmdConfig . ' 2>&1', $outputConfig, $returnConfig);

            $cmdResync = 'w32tm /resync';
            exec($cmdResync . ' 2>&1', $outputResync, $returnResync);

            $fullOutput = implode("\n", array_merge($outputConfig, $outputResync));

            if (strpos(strtolower($fullOutput), 'access is denied') !== false || strpos(strtolower($fullOutput), 'akses ditolak') !== false) {
                $this->error("Access is denied. Scheduler/Cron must run as Administrator.");
                Log::error("Time Sync failed: Access is Denied. Run scheduler as Administrator.");
                return Command::FAILURE;
            }

            if ($returnResync === 0) {
                $this->info("Time synchronized successfully!");
                Log::info("Time synchronized successfully with {$ntpServer}.");
                return Command::SUCCESS;
            } else {
                $this->error("Failed to sync time. Output: " . $fullOutput);
                Log::error("Failed to sync time. Output: " . $fullOutput);
                return Command::FAILURE;
            }

        } catch (\Exception $e) {
            $this->error("System error: " . $e->getMessage());
            Log::error("Time Sync exception: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
