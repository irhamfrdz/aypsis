<?php

declare(strict_types=1);

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("Script ini hanya boleh dijalankan melalui CLI.\n");
}

if (array_diff(array_slice($argv, 1), ['--execute'])) {
    fwrite(STDERR, "Penggunaan: php update_manifest_ap14bj26_tanggal_berangkat.php [--execute]\n");
    exit(1);
}

require __DIR__.'/vendor/autoload.php';

$app = require __DIR__.'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$voyage = 'AP14BJ26';
$tanggalBaru = '2026-10-15';
$execute = in_array('--execute', $argv, true);

try {
    $query = DB::table('manifests')->where('no_voyage', $voyage);

    echo "Voyage: {$voyage}\n";
    echo "Tanggal berangkat baru: {$tanggalBaru}\n";
    echo 'Total manifest: '.$query->count()."\n";
    echo "Tanggal yang tersimpan sekarang:\n";

    foreach ((clone $query)->select('tanggal_berangkat')
        ->selectRaw('COUNT(*) AS jumlah')
        ->groupBy('tanggal_berangkat')
        ->orderBy('tanggal_berangkat')
        ->get() as $row) {
        echo '  '.($row->tanggal_berangkat ?? 'NULL').": {$row->jumlah}\n";
    }

    $perluDiubah = (clone $query)
        ->where(function ($q) use ($tanggalBaru) {
            $q->whereNull('tanggal_berangkat')
                ->orWhere('tanggal_berangkat', '<>', $tanggalBaru);
        })
        ->count();

    echo "Manifest yang perlu diubah: {$perluDiubah}\n";

    if (! $execute) {
        echo "Pratinjau saja. Jalankan dengan --execute untuk mengubah data.\n";
        exit(0);
    }

    if ($perluDiubah === 0) {
        echo "Tidak ada data yang perlu diubah.\n";
        exit(0);
    }

    $diubah = DB::transaction(function () use ($voyage, $tanggalBaru): int {
        return DB::table('manifests')
            ->where('no_voyage', $voyage)
            ->where(function ($q) use ($tanggalBaru) {
                $q->whereNull('tanggal_berangkat')
                    ->orWhere('tanggal_berangkat', '<>', $tanggalBaru);
            })
            ->update(['tanggal_berangkat' => $tanggalBaru]);
    });

    echo "Manifest diubah: {$diubah}\n";
    echo 'Manifest dengan tanggal baru: '.DB::table('manifests')
        ->where('no_voyage', $voyage)
        ->whereDate('tanggal_berangkat', $tanggalBaru)
        ->count()."\n";
} catch (Throwable $e) {
    fwrite(STDERR, 'Gagal: '.$e->getMessage()."\n");
    exit(1);
}
