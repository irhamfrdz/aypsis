<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "==========================================================\n";
echo "Update Nomor Voyage di Tabel naik_kapal\n";
echo "Dari: SR18JB25\n";
echo "Ke: SR01JB26\n";
echo "==========================================================\n\n";

$oldVoyage = 'SR18JB25';
$newVoyage = 'SR01JB26';

echo "Mencari data dengan voyage: {$oldVoyage}\n\n";

// Query data yang akan diupdate
$naikKapals = DB::table('naik_kapal')
    ->where('no_voyage', $oldVoyage)
    ->get();

$totalFound = $naikKapals->count();

echo "Ditemukan: {$totalFound} record\n\n";

if ($totalFound === 0) {
    echo "Tidak ada data yang perlu diupdate.\n";
    exit(0);
}

// Tampilkan preview data yang akan diupdate
echo "Preview data yang akan diupdate:\n";
echo str_repeat("-", 150) . "\n";
printf("%-5s | %-10s | %-25s | %-25s | %-20s | %-20s\n", 
    "No", "ID", "No. Voyage Lama", "No. Voyage Baru", "Nama Kapal", "Tanggal");
echo str_repeat("-", 150) . "\n";

foreach ($naikKapals as $index => $naikKapal) {
    printf("%-5d | %-10d | %-25s | %-25s | %-20s | %-20s\n",
        ($index + 1),
        $naikKapal->id,
        $naikKapal->no_voyage ?? '-',
        $newVoyage,
        $naikKapal->nama_kapal ?? '-',
        $naikKapal->tanggal_keberangkatan ?? '-'
    );
}

echo str_repeat("-", 150) . "\n\n";

// Konfirmasi sebelum update
echo "Apakah Anda yakin ingin mengupdate {$totalFound} record? (yes/no): ";
$handle = fopen("php://stdin", "r");
$line = fgets($handle);
$confirmation = trim(strtolower($line));
fclose($handle);

if ($confirmation !== 'yes' && $confirmation !== 'y') {
    echo "\nUpdate dibatalkan.\n";
    exit(0);
}

echo "\nMemulai update...\n\n";

$successCount = 0;
$errorCount = 0;
$errors = [];

// Update setiap record
foreach ($naikKapals as $index => $naikKapal) {
    try {
        DB::table('naik_kapal')
            ->where('id', $naikKapal->id)
            ->update([
                'no_voyage' => $newVoyage,
                'updated_at' => now()
            ]);
        
        $successCount++;
        echo sprintf("[%d/%d] ✓ Updated: ID %d (Kapal: %s, Voyage: %s → %s)\n",
            ($index + 1),
            $totalFound,
            $naikKapal->id,
            $naikKapal->nama_kapal ?? '-',
            $oldVoyage,
            $newVoyage
        );
    } catch (\Exception $e) {
        $errorCount++;
        $errorMsg = "Error updating ID {$naikKapal->id}: " . $e->getMessage();
        $errors[] = $errorMsg;
        echo sprintf("[%d/%d] ✗ Failed: %s\n",
            ($index + 1),
            $totalFound,
            $errorMsg
        );
    }
}

echo "\n" . str_repeat("=", 150) . "\n";
echo "Update selesai!\n\n";
echo "Summary:\n";
echo "- Total record ditemukan: {$totalFound}\n";
echo "- Berhasil diupdate: {$successCount}\n";
echo "- Gagal diupdate: {$errorCount}\n";

if ($errorCount > 0) {
    echo "\nDetail Error:\n";
    foreach ($errors as $error) {
        echo "- {$error}\n";
    }
}

echo "\n" . str_repeat("=", 150) . "\n";

// Verifikasi hasil update
echo "\nVerifikasi hasil update:\n";
$updatedRecords = DB::table('naik_kapal')
    ->where('no_voyage', $newVoyage)
    ->count();

$oldRecords = DB::table('naik_kapal')
    ->where('no_voyage', $oldVoyage)
    ->count();

echo "Total record dengan voyage '{$newVoyage}': {$updatedRecords}\n";
echo "Total record dengan voyage '{$oldVoyage}' (sisa): {$oldRecords}\n";

echo "\nScript selesai dijalankan.\n";
