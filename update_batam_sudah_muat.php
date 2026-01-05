<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Prospek;
use Carbon\Carbon;

echo "=================================================\n";
echo "Update Status Prospek Batam menjadi Sudah Muat\n";
echo "Tanggal: <= 31 Desember 2025\n";
echo "=================================================\n\n";

// Tanggal cutoff
$cutoffDate = Carbon::create(2025, 12, 31, 23, 59, 59);

echo "Mencari prospek dengan kriteria:\n";
echo "- Tujuan: Batam (LIKE)\n";
echo "- Tanggal: <= {$cutoffDate->format('Y-m-d')}\n";
echo "- Status: bukan 'sudah_muat'\n\n";

// Query prospek yang akan diupdate
$prospeks = Prospek::where('tujuan_pengiriman', 'LIKE', '%Batam%')
    ->where('tanggal', '<=', $cutoffDate)
    ->where('status', '!=', 'sudah_muat')
    ->get();

$totalFound = $prospeks->count();

echo "Ditemukan: {$totalFound} prospek\n\n";

if ($totalFound === 0) {
    echo "Tidak ada data yang perlu diupdate.\n";
    exit(0);
}

// Tampilkan preview data yang akan diupdate
echo "Preview data yang akan diupdate:\n";
echo str_repeat("-", 120) . "\n";
printf("%-5s | %-20s | %-15s | %-30s | %-15s | %-15s\n", 
    "No", "No. Surat Jalan", "Tanggal", "Tujuan", "Status Lama", "Status Baru");
echo str_repeat("-", 120) . "\n";

foreach ($prospeks as $index => $prospek) {
    printf("%-5d | %-20s | %-15s | %-30s | %-15s | %-15s\n",
        ($index + 1),
        $prospek->no_surat_jalan ?? '-',
        $prospek->tanggal ? $prospek->tanggal->format('Y-m-d') : '-',
        substr($prospek->tujuan_pengiriman ?? '-', 0, 30),
        $prospek->status,
        'sudah_muat'
    );
}

echo str_repeat("-", 120) . "\n\n";

// Konfirmasi sebelum update
echo "Apakah Anda yakin ingin mengupdate {$totalFound} prospek? (yes/no): ";
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

// Update setiap prospek
foreach ($prospeks as $index => $prospek) {
    try {
        $oldStatus = $prospek->status;
        $prospek->status = 'sudah_muat';
        $prospek->updated_by = 1; // Admin user ID
        $prospek->save();
        
        $successCount++;
        echo sprintf("[%d/%d] ✓ Updated: %s (Status: %s → sudah_muat)\n",
            ($index + 1),
            $totalFound,
            $prospek->no_surat_jalan ?? "ID: {$prospek->id}",
            $oldStatus
        );
    } catch (\Exception $e) {
        $errorCount++;
        $errorMsg = "Error updating {$prospek->no_surat_jalan}: " . $e->getMessage();
        $errors[] = $errorMsg;
        echo sprintf("[%d/%d] ✗ Failed: %s\n",
            ($index + 1),
            $totalFound,
            $errorMsg
        );
    }
}

echo "\n" . str_repeat("=", 120) . "\n";
echo "Update selesai!\n\n";
echo "Summary:\n";
echo "- Total prospek ditemukan: {$totalFound}\n";
echo "- Berhasil diupdate: {$successCount}\n";
echo "- Gagal diupdate: {$errorCount}\n";

if ($errorCount > 0) {
    echo "\nDetail Error:\n";
    foreach ($errors as $error) {
        echo "- {$error}\n";
    }
}

echo "\n" . str_repeat("=", 120) . "\n";

// Verifikasi hasil update
echo "\nVerifikasi hasil update:\n";
$updatedProspeks = Prospek::where('tujuan_pengiriman', 'LIKE', '%Batam%')
    ->where('tanggal', '<=', $cutoffDate)
    ->where('status', '=', 'sudah_muat')
    ->count();

echo "Total prospek Batam (tanggal <= 31 Des 2025) dengan status 'sudah_muat': {$updatedProspeks}\n";

$remainingProspeks = Prospek::where('tujuan_pengiriman', 'LIKE', '%Batam%')
    ->where('tanggal', '<=', $cutoffDate)
    ->where('status', '!=', 'sudah_muat')
    ->count();

echo "Total prospek Batam (tanggal <= 31 Des 2025) dengan status selain 'sudah_muat': {$remainingProspeks}\n";

echo "\nScript selesai dijalankan.\n";
