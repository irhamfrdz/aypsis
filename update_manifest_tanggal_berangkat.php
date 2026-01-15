<?php

/**
 * Script untuk mengubah tanggal berangkat pada table manifest
 * dengan nomor voyage SR01BJ26
 * 
 * Cara menjalankan:
 * php update_manifest_tanggal_berangkat.php
 */

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "=== Update Tanggal Berangkat Manifest ===\n\n";

    $voyage = 'SR01BJ26';
    $tanggalBaru = '2026-01-14';

    // Cari manifest dengan nomor voyage tersebut
    $manifests = DB::table('manifests')
        ->where('no_voyage', $voyage)
        ->get();

    if ($manifests->isEmpty()) {
        echo "⚠ Tidak ada manifest dengan nomor voyage '{$voyage}'\n";
        exit(0);
    }

    echo "Ditemukan " . $manifests->count() . " manifest dengan nomor voyage '{$voyage}':\n\n";

    foreach ($manifests as $manifest) {
        echo "ID: {$manifest->id}\n";
        echo "Nomor Voyage: {$manifest->no_voyage}\n";
        echo "Tanggal Berangkat Lama: {$manifest->tanggal_berangkat}\n";
        echo "---\n";
    }

    echo "\nApakah Anda yakin ingin mengubah tanggal berangkat menjadi {$tanggalBaru}? (y/n): ";
    $confirm = trim(fgets(STDIN));

    if (strtolower($confirm) !== 'y') {
        echo "\n❌ Update dibatalkan\n";
        exit(0);
    }

    // Update tanggal berangkat
    $updated = DB::table('manifests')
        ->where('no_voyage', $voyage)
        ->update([
            'tanggal_berangkat' => $tanggalBaru,
            'updated_at' => now()
        ]);

    echo "\n✓ Berhasil mengubah tanggal berangkat!\n";
    echo "Total manifest yang diupdate: {$updated}\n";
    echo "Tanggal berangkat baru: {$tanggalBaru}\n";
    echo "\n=== Verifikasi Data Setelah Update ===\n\n";

    // Verifikasi data setelah update
    $verifyManifests = DB::table('manifests')
        ->where('no_voyage', $voyage)
        ->get();

    foreach ($verifyManifests as $manifest) {
        echo "ID: {$manifest->id}\n";
        echo "Nomor Voyage: {$manifest->no_voyage}\n";
        echo "Tanggal Berangkat: {$manifest->tanggal_berangkat}\n";
        echo "Updated At: {$manifest->updated_at}\n";
        echo "---\n";
    }

    echo "\n✓ Proses selesai!\n";

} catch (\Exception $e) {
    echo "\n✗ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
