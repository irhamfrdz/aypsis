<?php
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use App\Models\MasterPricelistSewaKontainer;

$in = __DIR__ . '/output_preview.csv';
$out = __DIR__ . '/output_preview_ignoredates.csv';
if (!file_exists($in)) { echo "input not found: $in\n"; exit(1); }
$fh = fopen($in,'r');
$h = fgetcsv($fh,0,';');
if ($h === false) { echo "empty input\n"; exit(1); }

$header = array_map(function($c){ if($c===null) return ''; $c = preg_replace('/^\x{FEFF}/u','',$c); if(substr($c,0,3)==="\xEF\xBB\xBF") $c = substr($c,3); $c = preg_replace('/[\x00-\x1F\x7F]/u','',$c); return trim($c); }, $h);
$hmap = array_flip(array_map('strtolower',$header));
$vendorIdx = $hmap['vendor'] ?? null;
$sizeIdx = $hmap['size'] ?? ($hmap['ukuran'] ?? null);
$dppIdx = $hmap['dpp'] ?? null;
$tanggalIdx = $hmap['tanggal_awal'] ?? null;
$tanggalAkhirIdx = $hmap['tanggal_akhir'] ?? null;

if ($vendorIdx === null || $sizeIdx === null || $tanggalIdx === null) { echo "missing vendor/size/tanggal_awal columns\n"; exit(1); }

$outfh = fopen($out,'w'); fwrite($outfh, "\xEF\xBB\xBF"); fputcsv($outfh, $header, ';');
$rows = 0; $overwritten = 0;

while(($row = fgetcsv($fh,0,';')) !== false) {
    $maxIdx = max(array_values($hmap));
    for ($ii=0;$ii<=$maxIdx;$ii++) if(!isset($row[$ii])) $row[$ii]='';
    $vendor = trim($row[$vendorIdx] ?? '');
    $size = trim($row[$sizeIdx] ?? '');
    $tanggal_awal = trim($row[$tanggalIdx] ?? '');
    $tanggal_akhir = trim($row[$tanggalAkhirIdx] ?? '');
    $periode = isset($hmap['periode']) && isset($row[$hmap['periode']]) ? intval($row[$hmap['periode']]) : 1;

    // compute periodStart and periodEnd
    $periodStart = null;
    if ($tanggal_awal !== '') {
        try {
            $orig = new DateTime($tanggal_awal);
            $origDay = (int)$orig->format('j');
            $periodStart = (clone $orig)->modify('+' . max(0,$periode-1) . ' months');
            $last = (int)$periodStart->format('t');
            $dayToSet = min($origDay, $last);
            $periodStart->setDate((int)$periodStart->format('Y'), (int)$periodStart->format('n'), $dayToSet);
        } catch (Exception $e) { $periodStart = null; }
    }
    $periodEnd = null; if ($periodStart) { $periodEnd = (clone $periodStart)->modify('+1 month')->modify('-1 day'); if ($tanggal_akhir !== '') { try { $overallEnd = new DateTime($tanggal_akhir); if ($overallEnd < $periodEnd) $periodEnd = $overallEnd; } catch (Exception $e) {} } }
    $daysInPeriod = ($periodStart && $periodEnd) ? ((int)$periodStart->diff($periodEnd)->format('%a') + 1) : 0;

    // only act if dpp empty
    if ($dppIdx !== null && trim($row[$dppIdx] ?? '') === '') {
        $found = null;
        // try vendor+size ignoring date
        try {
            if ($vendor !== '' && $size !== '') {
                $found = MasterPricelistSewaKontainer::where('vendor',$vendor)->where('ukuran_kontainer',(int)$size)->orderBy('tanggal_harga_awal','desc')->first();
            }
        } catch (Exception $e) { $found = null; }
        if (!$found) {
            // try size-only latest
            try { if ($size !== '') $found = MasterPricelistSewaKontainer::where('ukuran_kontainer',(int)$size)->orderBy('tanggal_harga_awal','desc')->first(); } catch (Exception $e) { $found = null; }
        }
        if ($found) {
            $prTarif = trim((string)($found->tarif ?? ''));
            $harga = (float)$found->harga;
            if (strtolower($prTarif) === 'harian') {
                $val = $harga * max(1,$daysInPeriod);
                $row[$dppIdx] = number_format($val,2,'.','');
            } else {
                $row[$dppIdx] = number_format($harga,2,'.','');
            }
            $overwritten++;
        }
    }

    fputcsv($outfh, $row, ';');
    $rows++;
}

fclose($fh); fclose($outfh);
echo "Wrote $rows rows to $out (overwritten=$overwritten)\n";
