<?php

/**
 * Script untuk mengisi nomor seal pada table BL dari table prospek
 * Jalankan: php update_bl_no_seal_from_prospek.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=================================================\n";
echo "Update Nomor Seal BL dari Prospek\n";
echo "=================================================\n\n";

try {
    // Ambil semua BL yang memiliki prospek_id dan no_seal di BL masih kosong
    $bls = DB::table('bls')
        ->whereNotNull('prospek_id')
        ->where(function($query) {
            $query->whereNull('no_seal')
                  ->orWhere('no_seal', '');
        })
        ->get();

    echo "Ditemukan " . $bls->count() . " BL yang no_seal-nya kosong dan memiliki prospek_id.\n\n";

    if ($bls->count() === 0) {
        echo "Tidak ada data yang perlu diupdate.\n";
        exit(0);
    }

    $updated = 0;
    $skipped = 0;
    $errors = 0;

    DB::beginTransaction();

    foreach ($bls as $bl) {
        try {
            // Ambil data prospek
            $prospek = DB::table('prospek')
                ->where('id', $bl->prospek_id)
                ->first();

            if (!$prospek) {
                echo "❌ BL ID {$bl->id}: Prospek ID {$bl->prospek_id} tidak ditemukan\n";
                $errors++;
                continue;
            }

            // Cek apakah prospek punya no_seal
            if (empty($prospek->no_seal)) {
                echo "⊘  BL ID {$bl->id}: Prospek tidak memiliki no_seal (dilewati)\n";
                $skipped++;
                continue;
            }

            // Update no_seal di BL
            DB::table('bls')
                ->where('id', $bl->id)
                ->update([
                    'no_seal' => $prospek->no_seal,
                    'updated_at' => now()
                ]);

            echo "✓  BL ID {$bl->id}: No Seal diupdate menjadi '{$prospek->no_seal}'\n";
            $updated++;

        } catch (\Exception $e) {
            echo "❌ Error pada BL ID {$bl->id}: " . $e->getMessage() . "\n";
            $errors++;
        }
    }

    DB::commit();

    echo "\n=================================================\n";
    echo "HASIL UPDATE:\n";
    echo "=================================================\n";
    echo "✓  Berhasil diupdate: {$updated} record\n";
    echo "⊘  Dilewati (prospek tidak punya seal): {$skipped} record\n";
    echo "❌ Error: {$errors} record\n";
    echo "=================================================\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ FATAL ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
