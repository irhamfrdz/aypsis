<?php

use App\Models\LangsirBatam;
use Illuminate\Support\Facades\DB;

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Script hanya dapat dijalankan melalui CLI.');
}

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Gunakan string agar nol di depan nomor surat jalan tetap dipertahankan.
$noSuratJalan = '0037893';

try {
    $deleted = DB::transaction(function () use ($noSuratJalan) {
        $langsirs = LangsirBatam::withTrashed()
            ->where('no_surat_jalan', $noSuratJalan)
            ->lockForUpdate()
            ->get();

        if ($langsirs->isEmpty()) {
            echo "Tidak ditemukan data langsir dengan nomor surat jalan {$noSuratJalan}.\n";

            return 0;
        }

        foreach ($langsirs as $langsir) {
            // Gunakan alur penghapusan aplikasi, termasuk history kontainer terkait.
            $langsir->deletePermanently();
            echo "Dihapus: ID {$langsir->id}, transaksi {$langsir->no_transaksi}, surat jalan {$noSuratJalan}.\n";
        }

        return $langsirs->count();
    });

    echo "Selesai. {$deleted} data langsir dihapus permanen.\n";
} catch (\Throwable $exception) {
    fwrite(STDERR, "Penghapusan dibatalkan dan transaksi di-rollback: {$exception->getMessage()}\n");
    exit(1);
}
