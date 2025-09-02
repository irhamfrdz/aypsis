<?php
$path = __DIR__ . '/datakaryawan_test.csv';
if (!file_exists($path)) { echo "file not found\n"; exit(1); }
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
    // simulate date parse
    $dateCols = ['tanggal_lahir','tanggal_masuk','tanggal_berhenti','tanggal_masuk_sebelumnya','tanggal_berhenti_sebelumnya'];
    foreach ($dateCols as $dc) {
        if (!empty($dataRaw[$dc])) {
            $val = trim($dataRaw[$dc]);
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/',$val)) {
                $ts = strtotime(str_replace(['.','/'],['-','-'],$val));
                if ($ts !== false) { $dataRaw[$dc] = date('Y-m-d',$ts); }
            }
        }
    }
    $processed++;
}
fclose($handle);

echo "Delimiter detected: {$delimiter}\n";
echo "Header cols: " . implode(',', $header) . "\n";
echo "Processed: $processed\n";
echo "Skipped: " . count($skipped) . "\n";
if (count($skipped) > 0) { echo implode("\n", array_slice($skipped,0,10)) . "\n"; }
if (count($errors) > 0) { echo "Errors: " . implode("\n", $errors) . "\n"; }
