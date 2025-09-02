<?php
require __DIR__ . '/../vendor/autoload.php';
use Illuminate\Support\Facades\DB;

// Boot Laravel
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Backing up tagihan_kontainer_sewa to backups/tagihan_backup.csv...\n";
$rows = DB::table('tagihan_kontainer_sewa')->get();
@mkdir(__DIR__ . '/../backups', 0755, true);
$csvFile = __DIR__ . '/../backups/tagihan_backup_' . date('YmdHis') . '.csv';
$fh = fopen($csvFile, 'w');
if ($rows->count()) {
    // write header
    fputcsv($fh, array_keys((array)$rows->first()));
    foreach ($rows as $r) {
        fputcsv($fh, array_values((array)$r));
    }
    echo "Wrote " . $rows->count() . " rows to $csvFile\n";
} else {
    echo "No rows to backup.\n";
}
fclose($fh);

// Delete pivot rows first
// Delete pivot rows first (use delete to avoid FK truncate restriction)
$cntPivot = DB::table('tagihan_kontainer_sewa_kontainers')->count();
DB::table('tagihan_kontainer_sewa_kontainers')->delete();
echo "Deleted $cntPivot rows from tagihan_kontainer_sewa_kontainers\n";

// Delete tagihan rows
$cnt = DB::table('tagihan_kontainer_sewa')->count();
DB::table('tagihan_kontainer_sewa')->delete();
echo "Deleted $cnt rows from tagihan_kontainer_sewa\n";

// Reset AUTO_INCREMENT - using raw SQL for MySQL
$connection = DB::getPdo();
$dbName = DB::getDatabaseName();
try {
    $stmt = $connection->prepare("ALTER TABLE `tagihan_kontainer_sewa` AUTO_INCREMENT = 1");
    $stmt->execute();
    echo "Reset AUTO_INCREMENT for tagihan_kontainer_sewa to 1\n";
} catch (Exception $e) {
    echo "Failed to reset AUTO_INCREMENT: " . $e->getMessage() . "\n";
}

echo "Done cleanup. Next group_code will start from A001 when computed as count+1.\n";
