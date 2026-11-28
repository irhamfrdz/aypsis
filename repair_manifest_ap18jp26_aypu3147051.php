<?php

/**
 * Preview: php repair_manifest_ap18jp26_aypu3147051.php
 * Apply:   php repair_manifest_ap18jp26_aypu3147051.php --apply
 *
 * Removes the stale manifest for this shipment and clears its leftover TL flag.
 * Does not change other containers, voyages, BL records, or prospect data.
 */

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Script hanya boleh dijalankan melalui CLI.');
}

require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$arguments = array_slice($argv, 1);
if (array_diff($arguments, ['--apply'])) {
    fwrite(STDERR, "Penggunaan: php ".basename(__FILE__)." [--apply]\n");
    exit(1);
}
$apply = in_array('--apply', $arguments, true);
$container = 'AYPU3147051';
$voyage = 'AP18JP26';
$ship = 'KM. ALKEN PRINCESS';

// Use the same normalized identity across source and manifest records.
$shipmentQuery = static fn (string $table) => DB::table($table)
    ->whereRaw("UPPER(REPLACE(REPLACE(REPLACE(nomor_kontainer, ' ', ''), '-', ''), '.', '')) = ?", [$container])
    ->where('no_voyage', $voyage)
    ->whereRaw("UPPER(REPLACE(REPLACE(nama_kapal, '.', ''), '  ', ' ')) = ?", ['KM ALKEN PRINCESS']);

try {
    DB::transaction(function () use ($apply, $shipmentQuery, $container, $voyage, $ship) {
        $sources = $shipmentQuery('naik_kapal')->lockForUpdate()->get();
        $manifests = $shipmentQuery('manifests')->lockForUpdate()->get();
        $bls = $shipmentQuery('bls')->lockForUpdate()->get();

        if ($sources->count() !== 1) {
            throw new RuntimeException('Dibatalkan: harus ada tepat satu data naik_kapal untuk kontainer, kapal, dan voyage ini.');
        }
        $source = $sources->first();
        if ((bool) $source->sudah_ob || $source->tanggal_ob !== null || $source->supir_id !== null) {
            throw new RuntimeException('Dibatalkan: data sudah memiliki status/tanggal/supir OB. Periksa ulang sebelum memperbaiki.');
        }
        if ($bls->isNotEmpty()) {
            throw new RuntimeException('Dibatalkan: ditemukan BL terkait. Script ini hanya untuk kasus manifest tertinggal tanpa BL.');
        }
        foreach ($manifests as $manifest) {
            if (! $source->prospek_id || (int) $manifest->prospek_id !== (int) $source->prospek_id) {
                throw new RuntimeException('Dibatalkan: prospek manifest berbeda dari naik_kapal.');
            }
        }

        echo "Kapal: {$ship}\nVoyage: {$voyage}\nKontainer: {$container}\n";
        echo "Naik kapal ID: {$source->id}; sudah_ob: ".(int) $source->sudah_ob.'; is_tl: '.(int) $source->is_tl."\n";
        echo 'Manifest yang akan dihapus: '.json_encode($manifests->pluck('id')->all())."\n";
        echo "Perbaikan: hapus manifest terkait dan reset is_tl menjadi 0.\n";

        if ($manifests->isEmpty() && ! $source->is_tl) {
            echo "Data sudah bersih. Tidak ada perubahan.\n";

            return;
        }
        if (! $apply) {
            echo "PREVIEW SAJA: database tidak diubah. Gunakan --apply untuk menerapkan.\n";

            return;
        }

        $directory = storage_path('app/private/data-repairs');
        if (! is_dir($directory) && ! mkdir($directory, 0700, true) && ! is_dir($directory)) {
            throw new RuntimeException('Tidak dapat membuat direktori cadangan.');
        }
        $backup = $directory.'/ap18jp26-aypu3147051-'.date('Ymd-His').'-'.bin2hex(random_bytes(4)).'.json';
        $snapshot = json_encode([
            'created_at' => date(DATE_ATOM),
            'database' => DB::connection()->getDatabaseName(),
            'naik_kapal' => $sources->all(),
            'manifests' => $manifests->all(),
            'bls' => $bls->all(),
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        if (file_put_contents($backup, $snapshot, LOCK_EX) !== strlen($snapshot)) {
            throw new RuntimeException('Gagal menulis cadangan lengkap. Database tidak diubah.');
        }
        echo "Cadangan sebelum perubahan: {$backup}\n";

        $deleted = $manifests->isEmpty() ? 0 : DB::table('manifests')->whereIn('id', $manifests->pluck('id'))->delete();
        if ($deleted !== $manifests->count()) {
            throw new RuntimeException('Jumlah manifest terhapus tidak sesuai. Transaksi dibatalkan.');
        }
        DB::table('naik_kapal')->where('id', $source->id)->update([
            'is_tl' => false,
            'updated_at' => now(),
        ]);
        if ($shipmentQuery('manifests')->exists() || $shipmentQuery('naik_kapal')->where('is_tl', true)->exists()) {
            throw new RuntimeException('Verifikasi gagal. Transaksi dibatalkan.');
        }
    });
    if ($apply) {
        echo "Selesai. Transaksi berhasil.\n";
    }
} catch (Throwable $exception) {
    fwrite(STDERR, 'GAGAL: '.$exception->getMessage()."\nPerubahan database dalam transaksi dibatalkan.\n");
    exit(1);
}
