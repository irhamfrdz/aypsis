<?php
// Writes up to 50 rows with empty dpp from output_preview.csv to stdout and a sample file
require_once __DIR__ . '/../vendor/autoload.php';
$in = __DIR__ . '/output_preview.csv';
$out = __DIR__ . '/empty_dpp_samples.csv';
if (!file_exists($in)) { echo "input not found: $in\n"; exit(1); }
$fh = fopen($in,'r');
$h = fgetcsv($fh,0,';');
if ($h === false) { echo "empty input\n"; exit(1); }
$header = array_map(function($c){ if($c===null) return ''; $c = preg_replace('/^\x{FEFF}/u','',$c); if(substr($c,0,3)==="\xEF\xBB\xBF") $c = substr($c,3); $c = preg_replace('/[\x00-\x1F\x7F]/u','',$c); return trim($c); }, $h);
$hmap = array_flip(array_map('strtolower',$header));
$dppIdx = $hmap['dpp'] ?? null;
if ($dppIdx === null) { echo "no dpp column in header\n"; exit(1); }

$outfh = fopen($out,'w'); fwrite($outfh, "\xEF\xBB\xBF"); fputcsv($outfh, $header, ';');
$count = 0; $idx = 0;
echo "Showing up to 50 rows where dpp is empty:\n";
while(($row = fgetcsv($fh,0,';')) !== false) {
    $idx++;
    // normalize
    for ($ii=0;$ii<count($header);$ii++) if(!isset($row[$ii])) $row[$ii]='';
    if (trim($row[$dppIdx]) === '') {
        // print a short summary line
        $vendor = $row[$hmap['vendor'] ?? 0] ?? '';
        $nomor = $row[$hmap['nomor_kontainer'] ?? ($hmap['nomor'] ?? 1)] ?? '';
        $size = $row[$hmap['size'] ?? ($hmap['ukuran'] ?? 2)] ?? '';
        $ta = $row[$hmap['tanggal_awal'] ?? 4] ?? '';
        $tk = $row[$hmap['tanggal_akhir'] ?? 5] ?? '';
        $periode = $row[$hmap['periode'] ?? 6] ?? '';
        $masa = $row[$hmap['masa'] ?? 7] ?? '';
        echo sprintf("%3d) vendor=%s nomor=%s size=%s periode=%s tanggal_awal=%s tanggal_akhir=%s masa=%s\n", $idx, $vendor, $nomor, $size, $periode, $ta, $tk, $masa);
        fputcsv($outfh, $row, ';');
        $count++;
        if ($count >= 50) break;
    }
}
fclose($fh); fclose($outfh);
echo "Wrote $count sample rows to $out\n";
