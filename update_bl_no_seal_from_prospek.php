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
    // Ambil semua BL yang no_seal masih kosong dan punya nomor kontainer
    $bls = DB::table('bls')
        ->whereNotNull('nomor_kontainer')
        ->where('nomor_kontainer', '!=', '')
        ->where(function($query) {
            $query->whereNull('no_seal')
                  ->orWhere('no_seal', '')
                  ->orWhere('no_seal', '-');
        })
        ->get();

    echo "Ditemukan " . $bls->count() . " BL yang no_seal-nya kosong dan memiliki nomor_kontainer.\n\n";

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
            // Cari prospek berdasarkan nomor_kontainer, nama_kapal, dan no_voyage
            $prospek = DB::table('prospek')
                ->where('nomor_kontainer', $bl->nomor_kontainer)
                ->where('nama_kapal', $bl->nama_kapal)
                ->where('no_voyage', $bl->no_voyage)
                ->whereNotNull('no_seal')
                ->where('no_seal', '!=', '')
                ->first();

            if (!$prospek) {
                echo "⊘  BL ID {$bl->id} (Kontainer: {$bl->nomor_kontainer}, Kapal: {$bl->nama_kapal}, Voyage: {$bl->no_voyage}): Prospek tidak ditemukan atau tidak punya no_seal (dilewati)\n";
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

            echo "✓  BL ID {$bl->id} (Kontainer: {$bl->nomor_kontainer}, Kapal: {$bl->nama_kapal}, Voyage: {$bl->no_voyage}): No Seal diupdate menjadi '{$prospek->no_seal}'\n";
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
