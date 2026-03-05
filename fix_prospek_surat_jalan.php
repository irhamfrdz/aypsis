<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Prospek;
use App\Models\TandaTerimaLcl;
use App\Models\TandaTerimaTanpaSuratJalan;
use Illuminate\Support\Facades\DB;

/**
 * Script to fix empty or truncated no_surat_jalan in prospek table
 * by extracting the receipt number from the keterangan field.
 */
function fixProspekSuratJalan() {
    $prospeks = Prospek::where(function($q) {
            $q->whereNull('no_surat_jalan')
              ->orWhere('no_surat_jalan', '')
              ->orWhereRaw('LENGTH(no_surat_jalan) <= 3');
        })
        ->get();

    echo "Found " . $prospeks->count() . " prospek records with empty or potentially truncated no_surat_jalan.\n";

    $updatedCount = 0;

    foreach ($prospeks as $prospek) {
        $found = false;
        $newNoSuratJalan = null;

        // Pattern 1: Tanda Terima Tanpa Surat Jalan
        if (preg_match('/Tanda Terima Tanpa Surat Jalan:\s*([^|]+)/', $prospek->keterangan, $matches)) {
            $newNoSuratJalan = trim($matches[1]);
            $found = true;
        }
        // Pattern 2: Auto-created from Tanda Terima LCL
        elseif (preg_match('/Auto-created from Tanda Terima LCL:\s*([^|]+)/', $prospek->keterangan, $matches)) {
            $newNoSuratJalan = trim($matches[1]);
            $found = true;
        }
        // Pattern 3: Transfer dari Tanda Terima LCL (Multi items)
        elseif (strpos($prospek->keterangan, 'Transfer dari Tanda Terima LCL') !== false) {
            // Find LCL receipts by container number
            if ($prospek->nomor_kontainer) {
                $ttLcls = TandaTerimaLcl::whereHas('kontainerPivot', function($q) use ($prospek) {
                    $q->where('nomor_kontainer', $prospek->nomor_kontainer);
                })->get();

                if ($ttLcls->isNotEmpty()) {
                    $newNoSuratJalan = $ttLcls->pluck('nomor_tanda_terima')->unique()->implode(', ');
                    $found = true;
                }
            }
        }

        if ($found && $newNoSuratJalan) {
            // Only update if it's different from current
            if ($prospek->no_surat_jalan !== $newNoSuratJalan) {
                $prospek->no_surat_jalan = $newNoSuratJalan;
                $prospek->save();
                $updatedCount++;
                echo "Updated Prospek ID: {$prospek->id} -> no_surat_jalan: {$newNoSuratJalan}\n";
            }
        }
    }

    echo "Finished. Total updated: {$updatedCount} records.\n";
}

fixProspekSuratJalan();
