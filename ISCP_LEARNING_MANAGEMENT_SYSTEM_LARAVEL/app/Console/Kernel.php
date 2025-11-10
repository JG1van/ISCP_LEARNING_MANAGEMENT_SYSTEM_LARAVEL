<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Daftar command custom aplikasi.
     */
    protected $commands = [
        \App\Console\Commands\CheckAuthConfig::class, // <— tambahkan ini
    ];

    /**
     * Jadwal command (jika kamu ingin menjalankan otomatis).
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
    }

    /**
     * Daftarkan command untuk aplikasi.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
