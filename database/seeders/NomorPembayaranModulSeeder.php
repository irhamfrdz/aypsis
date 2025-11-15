<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\NomorTerakhir;
use Carbon\Carbon;

class NomorPembayaranModulSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('=== Seeder Modul Nomor Pembayaran ===');
        $this->command->newLine();

        // Check if module already exists
        $existing = NomorTerakhir::where('modul', 'nomor_pembayaran')->first();

        if ($existing) {
            $this->command->comment('⚠ Modul "nomor_pembayaran" sudah ada dengan nomor terakhir: ' . $existing->nomor_terakhir);
            $this->command->newLine();
            
            $this->command->info('Detail:');
            $this->command->info('- Modul: ' . $existing->modul);
            $this->command->info('- Nomor Terakhir: ' . $existing->nomor_terakhir);
            $this->command->info('- Created At: ' . $existing->created_at);
            $this->command->info('- Updated At: ' . $existing->updated_at);
        } else {
            // Create new module
            NomorTerakhir::create([
                'modul' => 'nomor_pembayaran',
                'nomor_terakhir' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            $this->command->info('✓ Modul "nomor_pembayaran" berhasil dibuat');
            $this->command->info('- Nomor Terakhir: 0');
            $this->command->info('- Format: {kode_bank}-{bulan}-{tahun}-{sequence}');
            $this->command->info('- Contoh: TST-11-25-000001');
        }

        $this->command->newLine();
        $this->command->info('=== Seeder Completed ===');
    }
}
