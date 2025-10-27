<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Prospek;
use App\Models\SuratJalan;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update existing prospek data yang belum punya surat_jalan_id
        $this->updateExistingProspekData();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tidak perlu rollback untuk data update
    }

    /**
     * Update existing prospek data berdasarkan keterangan yang ada
     */
    private function updateExistingProspekData(): void
    {
        echo "Updating existing prospek data...\n";

        // Ambil semua prospek yang belum ada surat_jalan_id
        $prospeks = Prospek::whereNull('surat_jalan_id')
                          ->whereNotNull('keterangan')
                          ->where('keterangan', 'LIKE', '%Surat Jalan:%')
                          ->get();

        $updated = 0;
        $failed = 0;

        foreach ($prospeks as $prospek) {
            try {
                // Parse keterangan untuk mendapatkan nomor surat jalan
                $keterangan = $prospek->keterangan;
                
                // Pattern: "Surat Jalan: SJ-2024-001 |" atau similar
                if (preg_match('/Surat Jalan: ([^\|]+) \|/', $keterangan, $matches)) {
                    $noSuratJalan = trim($matches[1]);
                    
                    // Cari surat jalan berdasarkan nomor
                    $suratJalan = SuratJalan::where('no_surat_jalan', $noSuratJalan)->first();
                    
                    if ($suratJalan) {
                        // Update prospek dengan data surat jalan
                        $prospek->update([
                            'no_surat_jalan' => $suratJalan->no_surat_jalan,
                            'surat_jalan_id' => $suratJalan->id
                        ]);
                        
                        $updated++;
                        echo "Updated prospek {$prospek->id} with surat jalan {$suratJalan->no_surat_jalan}\n";
                    } else {
                        $failed++;
                        echo "Surat jalan not found for prospek {$prospek->id}: {$noSuratJalan}\n";
                    }
                } else {
                    $failed++;
                    echo "Could not parse surat jalan from keterangan for prospek {$prospek->id}: {$keterangan}\n";
                }
            } catch (\Exception $e) {
                $failed++;
                echo "Error updating prospek {$prospek->id}: {$e->getMessage()}\n";
            }
        }

        echo "\nUpdate complete: {$updated} updated, {$failed} failed\n";
    }
};