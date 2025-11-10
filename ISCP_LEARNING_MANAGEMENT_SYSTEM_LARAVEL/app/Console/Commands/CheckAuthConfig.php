<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckAuthConfig extends Command
{
    protected $signature = 'system:check-auth';
    protected $description = 'Periksa konfigurasi auth Laravel untuk memastikan guard dan provider benar.';

    public function handle()
    {
        $authConfig = config('auth');

        $this->info('===   AUTH CONFIGURATION CHECK ===');
        $this->line('Default guard: ' . $authConfig['defaults']['guard']);
        $this->line('Default passwords: ' . $authConfig['defaults']['passwords']);

        $this->newLine();
        $this->info('Guard yang tersedia:');
        foreach ($authConfig['guards'] as $guard => $details) {
            $this->line("- {$guard} → driver: {$details['driver']}, provider: {$details['provider']}");
        }

        $this->newLine();
        $this->info('Provider yang tersedia:');
        foreach ($authConfig['providers'] as $provider => $details) {
            $this->line("- {$provider} → model: {$details['model']}");
        }

        $this->newLine();
        $this->info(' Pemeriksaan selesai. Jika semua nama cocok, auth config aman.');
    }
}
