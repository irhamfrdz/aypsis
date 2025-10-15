<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\SuratJalan;

class TestSupirDashboard extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-supir-dashboard';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test supir dashboard data fetching';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Find user 'supir'
        $user = User::where('username', 'supir')->with('karyawan')->first();

        if (!$user) {
            $this->error('User supir not found');
            return 1;
        }

        $this->info("User found: {$user->username}");
        $this->info("User name (accessor): {$user->name}");
        $this->info("Karyawan nama_lengkap: {$user->karyawan->nama_lengkap}");
        $this->info("Is supir: " . ($user->isSupir() ? 'Yes' : 'No'));

        // Test the same logic as in SupirDashboardController
        $supirId = $user->karyawan->id ?? null;
        $supirUsername = $user->username;
        $supirName = $user->name;  // This will use the accessor
        $supirNamaLengkap = $user->karyawan->nama_lengkap ?? $supirUsername;

        $this->info("\nDashboard query parameters:");
        $this->info("supirId: {$supirId}");
        $this->info("supirUsername: {$supirUsername}");
        $this->info("supirName: {$supirName}");
        $this->info("supirNamaLengkap: {$supirNamaLengkap}");

        // Query surat jalan
        $suratJalans = SuratJalan::where(function($query) use ($supirNamaLengkap, $supirUsername, $supirName) {
                         $query->where('supir', $supirNamaLengkap)
                               ->orWhere('supir', $supirUsername)
                               ->orWhere('supir', $supirName);
                     })
                     ->whereIn('status', ['belum masuk checkpoint', 'checkpoint_completed'])
                     ->latest()
                     ->get();

        $this->info("\nSurat Jalan found: " . $suratJalans->count());

        foreach ($suratJalans as $sj) {
            $this->info("- ID: {$sj->id}, No: {$sj->no_surat_jalan}, Supir: '{$sj->supir}', Status: {$sj->status}");
        }

        // Also check all surat jalan to see what's in database
        $this->info("\nAll Surat Jalan in database:");
        $allSj = SuratJalan::all(['id', 'no_surat_jalan', 'supir', 'status']);
        foreach ($allSj as $sj) {
            $this->info("- ID: {$sj->id}, No: {$sj->no_surat_jalan}, Supir: '{$sj->supir}', Status: {$sj->status}");
        }

        return 0;
    }
}
