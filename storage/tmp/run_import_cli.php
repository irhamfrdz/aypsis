<?php
// Simple CLI importer to run against the current app DB for debugging.
// Usage: php storage/tmp/run_import_cli.php

require __DIR__ . '/../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Karyawan;

$path = __DIR__ . '/datakaryawan_test.csv';
if (!file_exists($path)) { echo "file not found: $path\n"; exit(1); }
$contents = file_get_contents($path);
$lines = preg_split('/\r\n|\n|\r/', $contents);
$firstLine = '';
foreach ($lines as $l) { if (trim($l) !== '') { $firstLine = $l; break; }}
$delimiterCandidates = [',',';',"\t"];
$delimiter = ',';
$bestCount = -1;
foreach ($delimiterCandidates as $cand) { $cnt = substr_count($firstLine, $cand); if ($cnt > $bestCount) { $bestCount = $cnt; $delimiter = $cand; }}

$handle = fopen($path, 'r');
$header = null; $lineNumber = 0; $processed = 0; $skipped = []; $errors = [];
$k = new Karyawan();
$allowed = $k->getFillable();
while (($row = fgetcsv($handle,0,$delimiter)) !== false) {
    $lineNumber++;
    if (!$header) { $header = array_map('trim',$row); continue; }
    if (count($row) < count($header)) { while(count($row) < count($header)) $row[] = ''; }
    elseif (count($row) > count($header)) { $row = array_slice($row,0,count($header)); }
    $normalizedHeader = array_map(function($h){ return trim(strtolower($h)); }, $header);
    $dataRaw = array_combine($normalizedHeader, $row);
    if (!$dataRaw) { $skipped[] = "Line $lineNumber: failed to combine"; continue; }
    $nik = trim($dataRaw['nik'] ?? '');
    if (!$nik) { $skipped[] = "Line $lineNumber: missing nik"; continue; }
    // Normalize dates: convert Indonesian months and common formats to Y-m-d; empty/unparseable -> null
    $dateCols = ['tanggal_lahir','tanggal_masuk','tanggal_berhenti','tanggal_masuk_sebelumnya','tanggal_berhenti_sebelumnya'];
    $normalizeDate = function($val) {
        $val = trim((string)$val);
        if ($val === '') return null;
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $val)) return $val;
        $v = str_replace(['.', '/'], [' ', ' '], $val);
        $map = [
            'jan'=>'jan','januari'=>'jan',
            'feb'=>'feb','februari'=>'feb',
            'mar'=>'mar','maret'=>'mar',
            'apr'=>'apr','april'=>'apr',
            'mei'=>'may',
            'jun'=>'jun','juni'=>'jun',
            'jul'=>'jul','juli'=>'jul',
            'agu'=>'aug','agustus'=>'aug',
            'sep'=>'sep','september'=>'sep',
            'okt'=>'oct','oktober'=>'oct',
            'nov'=>'nov','november'=>'nov',
            'des'=>'dec','desember'=>'dec',
        ];
        $v = preg_replace_callback('/\b([A-Za-z]+)\b/u', function($m) use ($map) {
            $low = strtolower($m[1]);
            return $map[$low] ?? $m[1];
        }, $v);
        $ts = strtotime($v);
        if ($ts === false) return null;
        return date('Y-m-d', $ts);
    };
    foreach ($dateCols as $dc) {
        $dataRaw[$dc] = $normalizeDate($dataRaw[$dc] ?? '');
    }
    $payload = [];
    foreach ($allowed as $col) {
        if (array_key_exists($col, $dataRaw)) {
            $val = $dataRaw[$col];
            $val = ($val === null) ? null : trim($val);
            if ($val === '') $val = null;
            $payload[$col] = $val;
        }
    }
    try {
        Karyawan::updateOrCreate(['nik'=>$nik], $payload);
        $processed++;
    } catch (\Exception $e) {
        $errors[] = "Line $lineNumber (nik=$nik): " . $e->getMessage();
    }
}
fclose($handle);

echo "Delimiter detected: {$delimiter}\n";
echo "Header cols: " . implode(',', $header) . "\n";
echo "Processed: $processed\n";
echo "Skipped: " . count($skipped) . "\n";
if (count($skipped) > 0) { echo implode("\n", array_slice($skipped,0,50)) . "\n"; }
if (count($errors) > 0) { echo "Errors:\n" . implode("\n", $errors) . "\n"; }

echo "Final DB count: " . Karyawan::count() . "\n";
