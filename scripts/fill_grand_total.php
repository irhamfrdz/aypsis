<?php
// Compute grand_total = dpp + ppn - pph for rows in output_preview.csv
require_once __DIR__ . '/../vendor/autoload.php';

$in = __DIR__ . '/output_preview.csv';
$out = __DIR__ . '/output_preview_grand.csv';
if (!file_exists($in)) { echo "input not found: $in\n"; exit(1); }
$fh = fopen($in,'r');
$h = fgetcsv($fh,0,';');
if ($h === false) { echo "empty input\n"; exit(1); }

$header = array_map(function($c){ if($c===null) return ''; $c = preg_replace('/^\x{FEFF}/u','',$c); if(substr($c,0,3)==="\xEF\xBB\xBF") $c = substr($c,3); $c = preg_replace('/[\x00-\x1F\x7F]/u','',$c); return trim($c); }, $h);
$hmap = array_flip(array_map('strtolower',$header));
$dppIdx = $hmap['dpp'] ?? null;
$ppnIdx = $hmap['ppn'] ?? null;
$pphIdx = $hmap['pph'] ?? null;
$grandIdx = $hmap['grand_total'] ?? null;

// if grand_total column missing, append it
if ($grandIdx === null) {
    $header[] = 'grand_total';
    $grandIdx = count($header) - 1;
    $hmap['grand_total'] = $grandIdx;
}

$outfh = fopen($out,'w'); fwrite($outfh, "\xEF\xBB\xBF"); fputcsv($outfh, $header, ';');
$rows = 0; $filled = 0;

while(($row = fgetcsv($fh,0,';')) !== false) {
    $maxIdx = max(array_values($hmap));
    for ($ii=0;$ii<=$maxIdx;$ii++) if(!isset($row[$ii])) $row[$ii]='';

    // parse numbers
    $dpp = 0.0; if ($dppIdx !== null && trim($row[$dppIdx]) !== '') { $dpp = floatval(str_replace([',',' '],['',''],$row[$dppIdx])); }
    $ppn = 0.0; if ($ppnIdx !== null && trim($row[$ppnIdx]) !== '') { $ppn = floatval(str_replace([',',' '],['',''],$row[$ppnIdx])); }
    $pph = 0.0; if ($pphIdx !== null && trim($row[$pphIdx]) !== '') { $pph = floatval(str_replace([',',' '],['',''],$row[$pphIdx])); }

    $grand = $dpp + $ppn - $pph;
    $row[$grandIdx] = number_format($grand, 2, '.', '');
    $filled++;

    fputcsv($outfh, $row, ';');
    $rows++;
}

fclose($fh); fclose($outfh);
echo "Wrote $rows rows to $out (filled=$filled)\n";
