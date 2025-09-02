<?php
// Compute dpp_nilai_lain = dpp * 11 / 12 for rows in output_preview.csv
require_once __DIR__ . '/../vendor/autoload.php';

$in = __DIR__ . '/output_preview.csv';
$out = __DIR__ . '/output_preview_nilai_lain.csv';
if (!file_exists($in)) { echo "input not found: $in\n"; exit(1); }
$fh = fopen($in,'r');
$h = fgetcsv($fh,0,';');
if ($h === false) { echo "empty input\n"; exit(1); }

$header = array_map(function($c){ if($c===null) return ''; $c = preg_replace('/^\x{FEFF}/u','',$c); if(substr($c,0,3)==="\xEF\xBB\xBF") $c = substr($c,3); $c = preg_replace('/[\x00-\x1F\x7F]/u','',$c); return trim($c); }, $h);
$hmap = array_flip(array_map('strtolower',$header));
$dppIdx = $hmap['dpp'] ?? null;
$dppNilaiIdx = $hmap['dpp_nilai_lain'] ?? null;

// if dpp_nilai_lain column missing, append it
if ($dppNilaiIdx === null) {
    $header[] = 'dpp_nilai_lain';
    $dppNilaiIdx = count($header) - 1;
    $hmap['dpp_nilai_lain'] = $dppNilaiIdx;
}

$outfh = fopen($out,'w'); fwrite($outfh, "\xEF\xBB\xBF"); fputcsv($outfh, $header, ';');
$rows = 0; $filled = 0;

while(($row = fgetcsv($fh,0,';')) !== false) {
    $maxIdx = max(array_values($hmap));
    for ($ii=0;$ii<=$maxIdx;$ii++) if(!isset($row[$ii])) $row[$ii]='';
    if ($dppIdx !== null && trim($row[$dppIdx]) !== '') {
        // parse numeric dpp (may have formatted decimals)
        $raw = str_replace([',',' '],['',''],$row[$dppIdx]);
        $num = floatval($raw);
        $val = $num * 11 / 12.0;
        $row[$dppNilaiIdx] = number_format($val, 2, '.', '');
        $filled++;
    }
    fputcsv($outfh, $row, ';');
    $rows++;
}

fclose($fh); fclose($outfh);
echo "Wrote $rows rows to $out (filled=$filled)\n";
