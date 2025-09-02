<?php
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use App\Models\MasterPricelistSewaKontainer;

$in = __DIR__ . '/output_preview.csv';
$out = __DIR__ . '/output_preview_updated.csv';
if (!file_exists($in)) { echo "input not found: $in\n"; exit(1); }
$fh = fopen($in,'r');
$h = fgetcsv($fh,0,';');
if ($h === false) { echo "empty input\n"; exit(1); }

// normalize header
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
    $periode = isset($hmap['periode']) && isset($row[$hmap['periode']]) ? intval($row[$hmap['periode']]) : 1;

    // compute periodStart
    $periodStartForLookup = null;
    if ($tanggal_awal !== '') {
        try {
            $orig = new DateTime($tanggal_awal);
            $origDay = (int)$orig->format('j');
            $periodStartForLookup = (clone $orig)->modify('+' . max(0,$periode-1) . ' months');
            $last = (int)$periodStartForLookup->format('t');
            $dayToSet = min($origDay, $last);
            $periodStartForLookup->setDate((int)$periodStartForLookup->format('Y'), (int)$periodStartForLookup->format('n'), $dayToSet);
        } catch (Exception $e) { $periodStartForLookup = null; }
    }

    if ($vendor !== '' && $size !== '' && $periodStartForLookup !== null) {
        try {
            $pr = MasterPricelistSewaKontainer::where('vendor', $vendor)
                ->where('ukuran_kontainer', (int)$size)
                ->where('tanggal_harga_awal','<=',$periodStartForLookup->format('Y-m-d'))
                ->where(function($q) use ($periodStartForLookup){ $q->whereNull('tanggal_harga_akhir')->orWhere('tanggal_harga_akhir','>=',$periodStartForLookup->format('Y-m-d')); })
                ->orderBy('tanggal_harga_awal','desc')
                ->first();
            if ($pr) {
                $prTarif = trim((string)($pr->tarif ?? ''));
                $harga = (float)$pr->harga;
                // compute period end and daysInPeriod (cap by overall tanggal_akhir if present)
                $periodEnd = (clone $periodStartForLookup)->modify('+1 month')->modify('-1 day');
                if (!empty($row[$tanggalAkhirIdx] ?? '')) {
                    try { $overallEnd = new DateTime($row[$tanggalAkhirIdx]); if ($overallEnd < $periodEnd) $periodEnd = $overallEnd; } catch (Exception $e) {}
                }
                $daysInPeriod = (int)$periodStartForLookup->diff($periodEnd)->format('%a') + 1;

                if (strtolower($prTarif) === 'harian') {
                    // harga is per-day: dpp = harga_harian * days_in_period
                    $new = number_format($harga * $daysInPeriod,2,'.','');
                } else {
                    $new = number_format($harga,2,'.','');
                }
                // overwrite dpp
                if ($dppIdx !== null) {
                    $row[$dppIdx] = $new;
                    $overwritten++;
                }
            }
            else {
                // fallback: try lookup by ukuran only (ignore vendor)
                try {
                    $pr2 = MasterPricelistSewaKontainer::where('ukuran_kontainer', (int)$size)
                        ->where('tanggal_harga_awal','<=',$periodStartForLookup->format('Y-m-d'))
                        ->where(function($q) use ($periodStartForLookup){ $q->whereNull('tanggal_harga_akhir')->orWhere('tanggal_harga_akhir','>=',$periodStartForLookup->format('Y-m-d')); })
                        ->orderBy('tanggal_harga_awal','desc')
                        ->first();
                    if ($pr2) {
                        $prTarif2 = trim((string)($pr2->tarif ?? ''));
                        $harga2 = (float)$pr2->harga;
                        if (strtolower($prTarif2) === 'harian') {
                            $new2 = number_format($harga2 * $daysInPeriod,2,'.','');
                        } else {
                            $new2 = number_format($harga2,2,'.','');
                        }
                        if ($dppIdx !== null) { $row[$dppIdx] = $new2; $overwritten++; }
                    }
                } catch (Exception $e) {}
            }
        } catch (Exception $e) {}
    }

    fputcsv($outfh, $row, ';');
    $rows++;
}

fclose($fh); fclose($outfh);
echo "Wrote $rows rows to $out (overwritten=$overwritten)\n";
