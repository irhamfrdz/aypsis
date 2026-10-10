<?php

declare(strict_types=1);

use App\Models\Bl;
use App\Models\Manifest;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

require dirname(__DIR__).'/vendor/autoload.php';

$app = require_once dirname(__DIR__).'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

// Default target
$targetVoyage = 'AS17PJ26';
$targetDepartureDate = '2026-10-08';

// Parsing argumen CLI (--execute, --with-bl, --voyage=..., --date=...)
$execute = false;
$withBl = false;

foreach ($argv as $arg) {
    if ($arg === '--execute') {
        $execute = true;
    } elseif ($arg === '--with-bl') {
        $withBl = true;
    } elseif (str_starts_with($arg, '--voyage=')) {
        $targetVoyage = strtoupper(trim(substr($arg, 9)));
    } elseif (str_starts_with($arg, '--date=')) {
        $targetDepartureDate = trim(substr($arg, 7));
    }
}

try {
    $manifestQuery = Manifest::query()->where('no_voyage', $targetVoyage);

    $totalManifest = (clone $manifestQuery)->count();
    $ships = (clone $manifestQuery)
        ->select('nama_kapal')
        ->whereNotNull('nama_kapal')
        ->distinct()
        ->orderBy('nama_kapal')
        ->pluck('nama_kapal')
        ->all();
    $currentManifestDates = (clone $manifestQuery)
        ->select('tanggal_berangkat')
        ->distinct()
        ->orderBy('tanggal_berangkat')
        ->pluck('tanggal_berangkat')
        ->map(fn ($date) => $date === null ? 'NULL' : (string) $date)
        ->all();

    $totalBl = 0;
    $hasBlTable = Schema::hasTable('bls');
    if ($hasBlTable) {
        $totalBl = Bl::query()->where('no_voyage', $targetVoyage)->count();
    }

    echo '=================================================='.PHP_EOL;
    echo '  UPDATE TANGGAL BERANGKAT MANIFEST AYPSIS'.PHP_EOL;
    echo '=================================================='.PHP_EOL;
    echo 'Environment        : '.app()->environment().PHP_EOL;
    echo 'Voyage Target      : '.$targetVoyage.PHP_EOL;
    echo 'Tanggal Baru       : '.$targetDepartureDate.PHP_EOL;
    echo 'Kapal              : '.($ships === [] ? '-' : implode(', ', $ships)).PHP_EOL;
    echo 'Jumlah Manifest    : '.$totalManifest.PHP_EOL;
    echo 'Tgl Manifest Lama  : '.($currentManifestDates === [] ? '-' : implode(', ', $currentManifestDates)).PHP_EOL;
    if ($hasBlTable) {
        echo 'Jumlah BL (bls)    : '.$totalBl.' record'.PHP_EOL;
    }
    echo '--------------------------------------------------'.PHP_EOL;

    if ($totalManifest === 0) {
        fwrite(STDERR, PHP_EOL."PERINGATAN: Manifest dengan voyage '{$targetVoyage}' tidak ditemukan di database.".PHP_EOL);
        exit(2);
    }

    if (! $execute) {
        echo PHP_EOL.'[MODE PREVIEW] Database belum diubah.'.PHP_EOL;
        echo 'Jalankan perintah berikut di terminal untuk mengeksekusi:'.PHP_EOL;
        echo "  php scripts/update_manifest_departure_as17pj26.php --execute".PHP_EOL;
        if ($totalBl > 0) {
            echo PHP_EOL.'Tip: Jika ingin mengupdate tanggal berangkat tabel BL (bls) juga:'.PHP_EOL;
            echo "  php scripts/update_manifest_departure_as17pj26.php --execute --with-bl".PHP_EOL;
        }
        echo PHP_EOL;
        exit(0);
    }

    [$updatedManifest, $verifiedManifest, $updatedBl] = DB::transaction(function () use ($targetVoyage, $targetDepartureDate, $totalManifest, $withBl, $hasBlTable): array {
        $lockedManifestIds = Manifest::query()
            ->where('no_voyage', $targetVoyage)
            ->lockForUpdate()
            ->pluck('id');

        if ($lockedManifestIds->count() !== $totalManifest) {
            throw new RuntimeException('Jumlah manifest berubah saat transaksi berlangsung. Proses dibatalkan demi keamanan.');
        }

        $updatedManifest = Manifest::query()
            ->whereKey($lockedManifestIds)
            ->update(['tanggal_berangkat' => $targetDepartureDate]);

        $verifiedManifest = Manifest::query()
            ->whereKey($lockedManifestIds)
            ->whereDate('tanggal_berangkat', $targetDepartureDate)
            ->count();

        if ($verifiedManifest !== $totalManifest) {
            throw new RuntimeException("Verifikasi manifest gagal: {$verifiedManifest} dari {$totalManifest} record sesuai.");
        }

        $updatedBl = 0;
        if ($withBl && $hasBlTable) {
            $updatedBl = Bl::query()
                ->where('no_voyage', $targetVoyage)
                ->lockForUpdate()
                ->update(['tanggal_berangkat' => $targetDepartureDate]);
        }

        return [$updatedManifest, $verifiedManifest, $updatedBl];
    });

    echo PHP_EOL."BERHASIL: {$updatedManifest} record manifest berhasil diperbarui ke tanggal {$targetDepartureDate}.".PHP_EOL;
    echo "Verifikasi: {$verifiedManifest} record manifest sudah terkonfirmasi dengan tanggal baru.".PHP_EOL;
    if ($withBl && $hasBlTable) {
        echo "Info: {$updatedBl} record BL (bls) juga berhasil diperbarui ke tanggal {$targetDepartureDate}.".PHP_EOL;
    }
    echo PHP_EOL;
} catch (Throwable $exception) {
    fwrite(STDERR, PHP_EOL.'GAGAL: '.$exception->getMessage().PHP_EOL);
    exit(1);
}
