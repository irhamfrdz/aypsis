<?php
/**
 * Restore Karyawan from latest export CSV.
 * Safety: creates a DB dump (mysqldump) if available, then runs import inside a DB transaction.
 * Usage: php restore_karyawan_from_csv.php
 */

require __DIR__ . '/../vendor/autoload.php';

// Load .env minimally
$env = parse_ini_file(__DIR__ . '/../.env', false, INI_SCANNER_RAW);
$db = [
    'host' => $env['DB_HOST'] ?? '127.0.0.1',
    'port' => $env['DB_PORT'] ?? 3306,
    'name' => $env['DB_DATABASE'] ?? 'aypsis',
    'user' => $env['DB_USERNAME'] ?? 'root',
    'pass' => $env['DB_PASSWORD'] ?? '',
];

// find latest csv
$files = glob(__DIR__ . '/../public/exports/karyawan_export_*.csv');
if (!$files) {
    echo "No karyawan export CSV found.\n";
    exit(1);
}
usort($files, function($a,$b){ return filemtime($b) - filemtime($a); });
$file = $files[0];
echo "Using export file: $file\n";

// attempt to mysqldump database as safety
$dumpFile = __DIR__ . '/db_snapshot_' . date('Ymd_His') . '.sql';
$mysqldumpCmd = sprintf('mysqldump -h %s -P %s -u%s %s %s > "%s"',
    $db['host'], $db['port'], $db['user'], $db['name'], ($db['pass']!==''? "-p{$db['pass']}":''), $dumpFile
);
$dumpAvailable = true;
exec('mysqldump --version 2>&1', $out, $rc);
if ($rc !== 0) {
    echo "mysqldump not available on PATH; skipping DB snapshot. Proceeding with transaction-only restore.\n";
    $dumpAvailable = false;
} else {
    echo "Creating DB snapshot to: $dumpFile\n";
    passthru($mysqldumpCmd, $rc2);
    if ($rc2 !== 0) {
        echo "mysqldump failed (exit $rc2). Aborting.\n";
        exit(1);
    }
    echo "Snapshot created.\n";
}

// open CSV and parse
$fp = fopen($file, 'r');
$first = fgets($fp);
rewind($fp);
$delim = substr_count($first, ';') > substr_count($first, ',') ? ';' : ',';
$headers = fgetcsv($fp, 0, $delim);
$headers[0] = preg_replace('/^\xEF\xBB\xBF/', '', $headers[0]);
$expected = [
    'nik','nama_panggilan','nama_lengkap','plat','email','ktp','kk','alamat','rt_rw','kelurahan','kecamatan','kabupaten','provinsi','kode_pos','alamat_lengkap','tempat_lahir','tanggal_lahir','no_hp','jenis_kelamin','status_perkawinan','agama','divisi','pekerjaan','tanggal_masuk','tanggal_berhenti','tanggal_masuk_sebelumnya','tanggal_berhenti_sebelumnya','catatan','status_pajak','nama_bank','akun_bank','atas_nama','jkn','cabang','nik_supervisor','supervisor'
];

// map headers to expected; allow subset
$map = [];
foreach ($headers as $i => $h) {
    $h = trim($h);
    if (in_array($h, $expected)) $map[$i] = $h;
}
if (empty($map)) { echo "No recognizable columns found. Aborting.\n"; exit(1); }

// Initialize Laravel application to use Eloquent
putenv('APP_ENV=local');
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
// boot
$kernel->bootstrap();

use App\Models\Karyawan;
use Illuminate\Support\Facades\DB;

$rows = 0; $created = 0; $updated = 0; $errors = [];

DB::beginTransaction();
try {
    while (($data = fgetcsv($fp, 0, $delim)) !== false) {
        $rows++;
        $payload = [];
        foreach ($map as $idx => $col) {
            $payload[$col] = $data[$idx] ?? null;
        }
        // normalize empty strings
        foreach ($payload as $k=>$v) if ($v==='') $payload[$k] = null;
        if (empty($payload['nik'])) {
            $errors[] = "Row $rows: missing nik";
            continue;
        }
        // date normalization: try to parse common formats
        foreach (['tanggal_lahir','tanggal_masuk','tanggal_berhenti','tanggal_masuk_sebelumnya','tanggal_berhenti_sebelumnya'] as $dcol) {
            if (!empty($payload[$dcol])) {
                $ts = strtotime($payload[$dcol]);
                if ($ts !== false && $ts !== -1) {
                    $payload[$dcol] = date('Y-m-d', $ts);
                } else {
                    // leave as-is; model will attempt convertible formats
                }
            }
        }
        $model = Karyawan::where('nik', $payload['nik'])->first();
        if ($model) {
            $model->fill($payload);
            $model->save();
            $updated++;
        } else {
            Karyawan::create($payload);
            $created++;
        }
    }
    // Validate counts
    echo "Rows parsed: $rows\nCreated: $created\nUpdated: $updated\n";
    if (!empty($errors)) {
        echo "Errors summary:\n".implode("\n", $errors)."\n";
        echo "Errors found, rolling back transaction. No changes were applied.\n";
        DB::rollBack();
        exit(1);
    }
    // commit
    DB::commit();
    echo "Import complete. Changes committed.\n";
} catch (Throwable $e) {
    DB::rollBack();
    echo "Exception during import: " . $e->getMessage() . "\nStack: " . $e->getTraceAsString() . "\n";
    // If dump made, offer restore command
    if ($dumpAvailable) {
        echo "You can restore DB snapshot with: mysql -u{$db['user']} -p -h {$db['host']} {$db['name']} < \"$dumpFile\"\n";
    }
    exit(1);
}

fclose($fp);

