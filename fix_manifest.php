<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

use App\Models\Manifest;
use App\Models\Prospek;
use App\Models\TandaTerimaLclKontainerPivot;
use Illuminate\Support\Facades\DB;

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "\n============================================================\n";
echo "  SCRIPT PERBAIKAN DATA MANIFEST LCL DUPLIKAT (VOYAGE MISMATCH)  \n";
echo "============================================================\n\n";

echo "Menganalisis tabel manifest...\n";

// Mengambil semua data manifest bertipe LCL yang memiliki nomor tanda terima
$manifests = Manifest::where('tipe_kontainer', 'LCL')
    ->whereNotNull('nomor_tanda_terima')
    ->get();

$toDelete = [];

foreach ($manifests as $manifest) {
    // 1. Cek kecocokan Voyage & Seal menggunakan tabel Prospek
    // Karena Prospek adalah sumber kebenaran untuk relasi Kontainer + Seal + Voyage
    $prospek = Prospek::where('nomor_kontainer', $manifest->nomor_kontainer)
        ->where('no_seal', $manifest->no_seal)
        ->first();

    if ($prospek) {
        // Normalisasi nomor voyage untuk perbandingan
        $prospekVoyage = strtoupper(trim($prospek->no_voyage));
        $manifestVoyage = strtoupper(trim($manifest->no_voyage));

        if ($prospekVoyage !== $manifestVoyage) {
            // Mismatch Voyage! Data manifest ini salah voyage.
            $toDelete[] = $manifest;

            continue;
        }
    }

    // 2. Cek ke validan pivot tanda terima
    $exists = TandaTerimaLclKontainerPivot::where('nomor_kontainer', $manifest->nomor_kontainer)
        ->where('nomor_seal', $manifest->no_seal)
        ->whereHas('tandaTerima', function ($q) use ($manifest) {
            $q->where('nomor_tanda_terima', $manifest->nomor_tanda_terima);
        })
        ->exists();

    if (! $exists) {
        $toDelete[] = $manifest;
    }
}

$count = count($toDelete);
echo "Hasil analisis: Ditemukan {$count} data manifest LCL yang TIDAK VALID.\n\n";

if ($count > 0) {
    echo "Daftar manifest bocor/mismatch voyage yang terdeteksi:\n";
    echo str_repeat('-', 95)."\n";
    printf("%-6s | %-15s | %-12s | %-12s | %-15s | %-20s\n", 'ID', 'No. Kontainer', 'No. Seal', 'Voyage', 'Kapal', 'No. Tanda Terima');
    echo str_repeat('-', 95)."\n";
    foreach ($toDelete as $m) {
        printf(
            "%-6d | %-15s | %-12s | %-12s | %-15s | %-20s\n",
            $m->id,
            $m->nomor_kontainer,
            $m->no_seal ?? '-',
            $m->no_voyage ?? '-',
            \Illuminate\Support\Str::limit($m->nama_kapal ?? '-', 13),
            $m->nomor_tanda_terima
        );
    }
    echo str_repeat('-', 95)."\n\n";

    echo "Apakah Anda ingin menghapus {$count} data manifest yang tidak valid/mismatch di atas? (y/n): ";
    $input = trim(fgets(STDIN));

    if (strtolower($input) === 'y') {
        DB::beginTransaction();
        try {
            foreach ($toDelete as $m) {
                $m->delete();
            }
            DB::commit();
            echo "\n[OK] Sukses menghapus {$count} data manifest yang tidak valid!\n";
        } catch (\Exception $e) {
            DB::rollBack();
            echo "\n[ERROR] Gagal menghapus data: ".$e->getMessage()."\n";
        }
    } else {
        echo "\nDibatalkan. Tidak ada data yang dihapus.\n";
    }
} else {
    echo "[INFO] Semua data manifest LCL Anda sudah bersih & valid.\n";
}
echo "\nSelesai.\n";
