<?php
// Verbose expansion debug: expand parsed CSV, print per-row & per-period pricelist lookup and assignment info,
// write expanded CSV to scripts/output_preview_verbose.csv for quick inspection.
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use App\Models\MasterPricelistSewaKontainer;
$in = 'c:\\Users\\amanda\\Downloads\\template_daftar_tagihan_kontainer_sewa_rev_parsed.csv';
if (!file_exists($in)) { echo "Input not found: $in\n"; exit(1); }
$ts = (new DateTime())->format('Ymd_His');
$out = __DIR__ . "/output_preview_verbose_{$ts}.csv";
$FH = fopen($in,'r'); $header = fgetcsv($FH,0,';');
if ($header === false) { echo "Empty input\n"; exit(1); }
// normalize header
$header = array_map(function($c){ if($c===null) return ''; $c = preg_replace('/^\xEF\xBB\xBF/','',$c); $c = preg_replace('/[\x00-\x1F\x7F]/u','',$c); return trim($c); }, $header);
$norm = array_map('strtolower',$header);
$hmap = array_flip($norm);
$O = fopen($out,'w'); fwrite($O, "\xEF\xBB\xBF"); fputcsv($O, $header, ';');
$maxPreviewRows = 50; $readCount = 0; $written = 0;
echo "Verbose expansion debug: writing to $out\n";
while(($row = fgetcsv($FH,0,';')) !== false) {
    $readCount++;
    // skip empty rows
    if (count(array_filter($row, fn($c)=>trim((string)$c) !== '')) === 0) continue;
    // safely map fields
    $get = function($name) use ($row, $hmap) { $k = strtolower($name); return isset($hmap[$k]) && isset($row[$hmap[$k]]) ? $row[$hmap[$k]] : ''; };
    $vendor = $get('vendor');
    $nomor = $get('nomor_kontainer');
    $size = $get('size') !== '' ? $get('size') : $get('ukuran');
    $tanggal_awal = $get('tanggal_awal');
    $tanggal_akhir = $get('tanggal_akhir');
    $computed = $get('computed_periode'); $computed = ($computed === '' ? 1 : (int)$computed); if($computed<1) $computed=1;
    // prepare common indexes
    $periodeIdx = $hmap['periode'] ?? null;
    $masaIdx = $hmap['masa'] ?? null;
    $dppIdx = $hmap['dpp'] ?? null;
    $tarifIdx = $hmap['tarif'] ?? null;
    // original start
    $origStart = null; $origDay = null; if (!empty($tanggal_awal)) { try { $origStart = new DateTime($tanggal_awal); $origStart->setTime(0,0,0); $origDay = (int)$origStart->format('j'); } catch(Exception $e){ $origStart = null; } }
    $overallEnd = null; if (!empty($tanggal_akhir)) { try { $overallEnd = new DateTime($tanggal_akhir); $overallEnd->setTime(0,0,0); } catch(Exception $e){ $overallEnd = null; } }
    // Verbose header for this parsed row
    if ($readCount <= $maxPreviewRows) {
        echo "ROW#{$readCount}: vendor={$vendor} size={$size} nomor={$nomor} tanggal_awal={$tanggal_awal} tanggal_akhir={$tanggal_akhir} computed={$computed}\n";
    }
    for ($p=1; $p <= $computed; $p++) {
        $outRow = $row; // base
        // set periode
        if ($periodeIdx === null) $outRow[] = $p; else $outRow[$periodeIdx] = $p;
        // compute period
        $masaStr = '';
        if ($origStart !== null) {
            $periodStart = (clone $origStart)->modify('+' . ($p-1) . ' months');
            if ($origDay !== null) { $lastDay = (int)$periodStart->format('t'); $dayToSet = min($origDay, $lastDay); $periodStart->setDate((int)$periodStart->format('Y'), (int)$periodStart->format('n'), $dayToSet); }
            $end = (clone $periodStart)->modify('+1 month')->modify('-1 day');
            if ($overallEnd !== null && $overallEnd < $end) $end = clone $overallEnd;
            if ($overallEnd !== null && $periodStart > $overallEnd) { continue; }
            $months = [1=>'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
            $formatIndo = function($dt) use ($months){ $d=(int)$dt->format('j'); $m=(int)$dt->format('n'); $y=$dt->format('Y'); return $d.' '.strtolower($months[$m]).' '.$y; };
            $masaStr = $formatIndo($periodStart) . ' - ' . $formatIndo($end);
            // determine dpp via pricelist if missing
            $dppBefore = ($dppIdx !== null && isset($row[$dppIdx]) ? trim($row[$dppIdx]) : '');
            $dppAssigned = '';
            if ($dppIdx !== null && $dppBefore === '') {
                // lookup
                $pr = null;
                try {
                    if (!empty($vendor) && $size !== '') {
                        $pr = MasterPricelistSewaKontainer::where('vendor', $vendor)
                            ->where('ukuran_kontainer', (int)$size)
                            ->where('tanggal_harga_awal','<=',$periodStart->format('Y-m-d'))
                            ->where(function($q) use ($periodStart){ $q->whereNull('tanggal_harga_akhir')->orWhere('tanggal_harga_akhir','>=',$periodStart->format('Y-m-d')); })
                            ->orderBy('tanggal_harga_awal','desc')
                            ->first();
                    }
                } catch(Exception $e) { echo "Lookup error: " . $e->getMessage() . "\n"; }
                if ($pr) { $dppAssigned = (string)$pr->harga; $outRow[$dppIdx] = $dppAssigned; }
            }
            // compute tarif from dpp in outRow if present
            $tarifVal = '';
            if ($dppIdx !== null && isset($outRow[$dppIdx]) && trim($outRow[$dppIdx]) !== '') {
                $base = floatval(str_replace([',',' '],['','.'],$outRow[$dppIdx]));
                $daysInPeriod = (int)$periodStart->diff($end)->format('%a') + 1;
                $daysInFullMonth = (int)$periodStart->format('t');
                if ($daysInPeriod >= $daysInFullMonth) $tarifVal = number_format(round($base,2),2,'.',''); else $tarifVal = number_format(round($base * ($daysInPeriod / $daysInFullMonth),2),2,'.','');
                if ($tarifIdx !== null) $outRow[$tarifIdx] = $tarifVal; else $outRow[] = $tarifVal;
            }
            // masa
            if ($masaIdx === null) $outRow[] = $masaStr; else $outRow[$masaIdx] = $masaStr;
            // normalize length
            $maxIdx = count($header) - 1; for ($ii=0;$ii<=$maxIdx;$ii++) if(!isset($outRow[$ii])) $outRow[$ii]='';
            // write
            fputcsv($O, $outRow, ';'); $written++;
            // verbose print for first rows
            if ($readCount <= $maxPreviewRows) {
                $prStr = isset($pr) && $pr ? $pr->harga : '<none>';
                echo sprintf("  period=%d start=%s end=%s pricelist=%s dppBefore='%s' dppAssigned='%s' tarif='%s'\n",
                    $p, $periodStart->format('Y-m-d'), $end->format('Y-m-d'), $prStr, $dppBefore, $dppAssigned, $tarifVal
                );
            }
        }
    }
}
fclose($FH); fclose($O);
echo "Done. Wrote {$written} rows to {$out}\n";
// copy into scripts/output_preview.csv for consistency
$dst = __DIR__ . '/output_preview.csv'; copy($out, $dst);
echo "Also copied to {$dst}\n";
