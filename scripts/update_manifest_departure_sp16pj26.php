<?php

declare(strict_types=1);

use App\Models\Manifest;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;

require dirname(__DIR__).'/vendor/autoload.php';

$app = require_once dirname(__DIR__).'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

const TARGET_VOYAGE = 'SP16PJ26';
const TARGET_DEPARTURE_DATE = '2026-09-26';

$execute = in_array('--execute', $argv, true);

try {
    $query = Manifest::query()->where('no_voyage', TARGET_VOYAGE);

    $total = (clone $query)->count();
    $ships = (clone $query)
        ->select('nama_kapal')
        ->whereNotNull('nama_kapal')
        ->distinct()
        ->orderBy('nama_kapal')
        ->pluck('nama_kapal')
        ->all();
    $currentDates = (clone $query)
        ->select('tanggal_berangkat')
        ->distinct()
        ->orderBy('tanggal_berangkat')
        ->pluck('tanggal_berangkat')
        ->map(fn ($date) => $date === null ? 'NULL' : (string) $date)
        ->all();

    echo 'Environment       : '.app()->environment().PHP_EOL;
    echo 'Voyage            : '.TARGET_VOYAGE.PHP_EOL;
    echo 'Tanggal tujuan     : '.TARGET_DEPARTURE_DATE.PHP_EOL;
    echo 'Jumlah manifest    : '.$total.PHP_EOL;
    echo 'Kapal              : '.($ships === [] ? '-' : implode(', ', $ships)).PHP_EOL;
    echo 'Tanggal sebelumnya : '.($currentDates === [] ? '-' : implode(', ', $currentDates)).PHP_EOL;

    if ($total === 0) {
        fwrite(STDERR, PHP_EOL.'Dibatalkan: manifest dengan voyage '.TARGET_VOYAGE.' tidak ditemukan.'.PHP_EOL);
        exit(2);
    }

    if (! $execute) {
        echo PHP_EOL.'PREVIEW SAJA - database belum diubah.'.PHP_EOL;
        echo 'Jalankan kembali dengan --execute untuk menerapkan perubahan.'.PHP_EOL;
        exit(0);
    }

    [$updated, $verified] = DB::transaction(function () use ($total): array {
        $lockedIds = Manifest::query()
            ->where('no_voyage', TARGET_VOYAGE)
            ->lockForUpdate()
            ->pluck('id');

        if ($lockedIds->count() !== $total) {
            throw new RuntimeException('Jumlah manifest berubah saat proses berjalan. Tidak ada data yang diubah.');
        }

        $updated = Manifest::query()
            ->whereKey($lockedIds)
            ->update(['tanggal_berangkat' => TARGET_DEPARTURE_DATE]);

        $verified = Manifest::query()
            ->whereKey($lockedIds)
            ->whereDate('tanggal_berangkat', TARGET_DEPARTURE_DATE)
            ->count();

        if ($verified !== $total) {
            throw new RuntimeException("Verifikasi gagal: {$verified} dari {$total} manifest memiliki tanggal yang benar.");
        }

        return [$updated, $verified];
    });

    echo PHP_EOL."BERHASIL: {$updated} manifest diperbarui dan {$verified} manifest terverifikasi.".PHP_EOL;
} catch (Throwable $exception) {
    fwrite(STDERR, PHP_EOL.'GAGAL: '.$exception->getMessage().PHP_EOL);
    exit(1);
}
