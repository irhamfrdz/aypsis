<?php
// Read preview CSV and set tarif column to 'Bulanan' or 'Harian' based on period length.
$in = __DIR__ . '/output_preview.csv';
$out = __DIR__ . '/output_preview_labeled.csv';
if (!file_exists($in)) { echo "input not found: $in\n"; exit(1); }
$fh = fopen($in,'r');
$h = fgetcsv($fh,0,';'); if ($h === false) { echo "empty input\n"; exit(1); }
$header = array_map(function($c){ if($c===null) return ''; $c = preg_replace('/^\x{FEFF}/u','',$c); if(substr($c,0,3)==="\xEF\xBB\xBF") $c = substr($c,3); $c = preg_replace('/[\x00-\x1F\x7F]/u','',$c); return trim($c); }, $h);
$hmap = array_flip(array_map('strtolower',$header));
$tarifIdx = $hmap['tarif'] ?? null;
$tanggalIdx = $hmap['tanggal_awal'] ?? null;
$tanggalAkhirIdx = $hmap['tanggal_akhir'] ?? null;
$periodeIdx = $hmap['periode'] ?? null;
if ($tarifIdx === null || $tanggalIdx === null) { echo "required columns missing\n"; exit(1); }
$outfh = fopen($out,'w'); fwrite($outfh, "\xEF\xBB\xBF"); fputcsv($outfh, $header, ';');
$rows = 0;
while(($row = fgetcsv($fh,0,';')) !== false) {
    // normalize length
    $maxIdx = max(array_values($hmap)); for ($ii=0;$ii<=$maxIdx;$ii++) if(!isset($row[$ii])) $row[$ii] = '';
    $tanggal_awal = trim($row[$tanggalIdx] ?? '');
    $periode = $periodeIdx !== null && isset($row[$periodeIdx]) && trim($row[$periodeIdx])!=='' ? intval($row[$periodeIdx]) : 1;
    if ($tanggal_awal === '') { fputcsv($outfh,$row,';'); $rows++; continue; }
    try {
        $orig = new DateTime($tanggal_awal);
        $origDay = (int)$orig->format('j');
        $periodStart = (clone $orig)->modify('+' . max(0,$periode-1) . ' months');
        $last = (int)$periodStart->format('t');
        $dayToSet = min($origDay,$last);
        $periodStart->setDate((int)$periodStart->format('Y'), (int)$periodStart->format('n'), $dayToSet);
        $periodEnd = (clone $periodStart)->modify('+1 month')->modify('-1 day');
        $tanggal_akhir = trim($row[$tanggalAkhirIdx] ?? '');
        if ($tanggal_akhir !== '') {
            try { $overallEnd = new DateTime($tanggal_akhir); if ($overallEnd < $periodEnd) $periodEnd = $overallEnd; } catch (Exception $e) {}
        }
        $daysInPeriod = (int)$periodStart->diff($periodEnd)->format('%a') + 1;
        $daysInFullMonth = (int)$periodStart->format('t');
        $label = ($daysInPeriod >= $daysInFullMonth) ? 'Bulanan' : 'Harian';
        $row[$tarifIdx] = $label;
    } catch (Exception $e) {
        // leave as-is on errors
    }
    fputcsv($outfh,$row,';'); $rows++;
}
fclose($fh); fclose($outfh);
// replace preview
copy($out, $in);
echo "Wrote $rows rows to $out and copied to preview\n";
