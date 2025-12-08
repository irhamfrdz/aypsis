<?php

/**
 * Script untuk mengubah nama kapal dari "KM. SRIWIJAYA" menjadi "KM SRIWIJAYA RAYA"
 * di tabel bls
 */

require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel environment
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Bl;
use Illuminate\Support\Facades\DB;

echo "=== UPDATE SHIP NAME SCRIPT ===\n";
echo "Mengubah nama kapal dari 'KM. SRIWIJAYA' menjadi 'KM SRIWIJAYA RAYA'\n\n";

// Cari data yang akan diupdate
$oldName = 'KM. SRIWIJAYA';
$newName = 'KM SRIWIJAYA RAYA';

echo "Mencari data dengan nama kapal: '{$oldName}'\n";

// Hitung jumlah record yang akan diupdate
$count = Bl::where('nama_kapal', $oldName)->count();
echo "Ditemukan {$count} record yang akan diupdate.\n\n";

if ($count === 0) {
    echo "Tidak ada data yang perlu diupdate.\n";
    exit(0);
}

// Tampilkan preview data yang akan diubah
echo "Preview data yang akan diubah:\n";
$previewData = Bl::where('nama_kapal', $oldName)
    ->select('id', 'nama_kapal', 'no_voyage')
    ->limit(5)
    ->get();

foreach ($previewData as $bl) {
    echo "- ID: {$bl->id}, Kapal: '{$bl->nama_kapal}', Voyage: '{$bl->no_voyage}'\n";
}

if ($count > 5) {
    echo "... dan " . ($count - 5) . " record lainnya\n";
}

echo "\n";

// Konfirmasi sebelum update
echo "Apakah Anda yakin ingin melanjutkan update? (y/N): ";
$handle = fopen("php://stdin", "r");
$confirmation = trim(fgets($handle));
fclose($handle);

if (strtolower($confirmation) !== 'y') {
    echo "Update dibatalkan.\n";
    exit(0);
}

echo "\nMemulai update...\n";

// Lakukan update menggunakan transaction untuk safety
try {
    DB::beginTransaction();

    $updatedCount = Bl::where('nama_kapal', $oldName)
        ->update(['nama_kapal' => $newName]);

    DB::commit();

    echo "✅ Update berhasil!\n";
    echo "Jumlah record yang diupdate: {$updatedCount}\n";

    // Verifikasi hasil update
    $verifyCount = Bl::where('nama_kapal', $newName)->count();
    echo "Verifikasi: {$verifyCount} record sekarang memiliki nama kapal '{$newName}'\n";

    // Pastikan tidak ada lagi record dengan nama lama
    $oldCount = Bl::where('nama_kapal', $oldName)->count();
    if ($oldCount === 0) {
        echo "✅ Verifikasi berhasil: Tidak ada lagi record dengan nama kapal lama.\n";
    } else {
        echo "⚠️  Peringatan: Masih ada {$oldCount} record dengan nama kapal lama.\n";
    }

} catch (Exception $e) {
    DB::rollBack();
    echo "❌ Error terjadi selama update: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n=== UPDATE SELESAI ===\n";