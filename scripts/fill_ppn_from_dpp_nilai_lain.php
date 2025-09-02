<?php
// Compute ppn = dpp_nilai_lain * 12% for rows in output_preview.csv
require_once __DIR__ . '/../vendor/autoload.php';

$in = __DIR__ . '/output_preview.csv';
$out = __DIR__ . '/output_preview_ppn.csv';
if (!file_exists($in)) { echo "input not found: $in\n"; exit(1); }
$fh = fopen($in,'r');
$h = fgetcsv($fh,0,';');
if ($h === false) { echo "empty input\n"; exit(1); }

$header = array_map(function($c){ if($c===null) return ''; $c = preg_replace('/^\x{FEFF}/u','',$c); if(substr($c,0,3)==="\xEF\xBB\xBF") $c = substr($c,3); $c = preg_replace('/[\x00-\x1F\x7F]/u','',$c); return trim($c); }, $h);
$hmap = array_flip(array_map('strtolower',$header));
$dppNilaiIdx = $hmap['dpp_nilai_lain'] ?? null;
$ppnIdx = $hmap['ppn'] ?? null;

// if ppn column missing, append it
if ($ppnIdx === null) {
    $header[] = 'ppn';
    $ppnIdx = count($header) - 1;
    $hmap['ppn'] = $ppnIdx;
}

$outfh = fopen($out,'w'); fwrite($outfh, "\xEF\xBB\xBF"); fputcsv($outfh, $header, ';');
$rows = 0; $filled = 0;

while(($row = fgetcsv($fh,0,';')) !== false) {
    $maxIdx = max(array_values($hmap));
    for ($ii=0;$ii<=$maxIdx;$ii++) if(!isset($row[$ii])) $row[$ii]='';
    if ($dppNilaiIdx !== null && trim($row[$dppNilaiIdx]) !== '') {
        $raw = str_replace([',',' '],['',''],$row[$dppNilaiIdx]);
        $num = floatval($raw);
        $ppn = $num * 0.12;
        $row[$ppnIdx] = number_format($ppn,2,'.','');
        $filled++;
    }
    fputcsv($outfh, $row, ';');
    $rows++;
}

fclose($fh); fclose($outfh);
echo "Wrote $rows rows to $out (filled=$filled)\n";
