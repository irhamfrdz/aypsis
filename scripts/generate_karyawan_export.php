<?php
// Generate a semicolon-delimited CSV export of karyawans with UTF-8 BOM
// Force sqlite usage for this script and ensure DB path is absolute
putenv('DB_CONNECTION=sqlite');
$dbFile = realpath(__DIR__ . '/../database/database.sqlite') ?: __DIR__ . '/../database/database.sqlite';
putenv('DB_DATABASE=' . $dbFile);
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Karyawan;

$exportDir = __DIR__ . '/../public/exports';
if (!is_dir($exportDir)) {
    if (!mkdir($exportDir, 0755, true)) {
        echo "ERR: failed to create export dir $exportDir\n";
        exit(1);
    }
}
$timestamp = date('Ymd_His');
$filename = "karyawan_export_{$timestamp}.csv";
$path = $exportDir . '/' . $filename;

$model = new Karyawan;
$columns = $model->getFillable();

$fp = fopen($path, 'w');
if ($fp === false) {
    echo "ERR: cannot open $path for writing\n";
    exit(1);
}
// Write UTF-8 BOM for Excel
fwrite($fp, "\xEF\xBB\xBF");
// Header row (DB column names)
fputcsv($fp, $columns, ';', '"');
$counter = 0;

foreach (Karyawan::orderBy('id')->cursor() as $row) {
    $out = [];
    foreach ($columns as $col) {
        $val = $row->$col;
        if ($val instanceof DateTimeInterface) {
            $val = $val->format('Y-m-d');
        } elseif ($val instanceof \Illuminate\Support\Carbon || $val instanceof DateTimeInterface) {
            // carbon handled above but keep safe
            try { $val = $val->format('Y-m-d'); } catch (Throwable $e) { $val = (string)$val; }
        } elseif (is_null($val)) {
            $val = '';
        }
        $out[] = $val;
    }
    fputcsv($fp, $out, ';', '"');
    $counter++;
}

fclose($fp);

echo "WROTE={$path};ROWS={$counter}\n";
