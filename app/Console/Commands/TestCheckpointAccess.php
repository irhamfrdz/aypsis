<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\SuratJalan;

class TestCheckpointAccess extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-checkpoint-access';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test checkpoint access authorization';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Find user 'supir' (JONI)
        $user = User::where('username', 'supir')->with('karyawan')->first();

        if (!$user) {
            $this->error('User supir not found');
            return 1;
        }

        // Find surat jalan with supir JONI
        $suratJalan = SuratJalan::where('supir', 'JONI')->first();

        if (!$suratJalan) {
            $this->error('Surat jalan with supir JONI not found');
            return 1;
        }

        $this->info("Testing authorization for:");
        $this->info("User: {$user->username}");
        $this->info("User name (accessor): {$user->name}");
        $this->info("Karyawan nama_lengkap: {$user->karyawan->nama_lengkap}");
        $this->info("Karyawan nama: " . ($user->karyawan->nama ?? 'null'));
        $this->info("Surat Jalan supir: {$suratJalan->supir}");

        // Test authorization logic
        $userNamaLengkap = $user->karyawan->nama_lengkap ?? $user->username;
        $userNama = $user->karyawan->nama ?? $user->username;
        $userName = $user->name;

        $hasAccess = ($userNamaLengkap === $suratJalan->supir ||
                     $userNama === $suratJalan->supir ||
                     $userName === $suratJalan->supir ||
                     $user->username === $suratJalan->supir);

        $this->info("\nAuthorization check:");
        $this->info("userNamaLengkap === suratJalan->supir: " . ($userNamaLengkap === $suratJalan->supir ? 'TRUE' : 'FALSE'));
        $this->info("userNama === suratJalan->supir: " . ($userNama === $suratJalan->supir ? 'TRUE' : 'FALSE'));
        $this->info("userName === suratJalan->supir: " . ($userName === $suratJalan->supir ? 'TRUE' : 'FALSE'));
        $this->info("username === suratJalan->supir: " . ($user->username === $suratJalan->supir ? 'TRUE' : 'FALSE'));

        if ($hasAccess) {
            $this->info("\n✅ ACCESS GRANTED - User should be able to access checkpoint");
        } else {
            $this->error("\n❌ ACCESS DENIED - User will be blocked from accessing checkpoint");
        }

        return 0;
    }
}
