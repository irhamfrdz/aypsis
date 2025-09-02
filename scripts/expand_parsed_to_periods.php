$php_start = true;
<?php
// Expand parsed CSV into multiple rows per periode.

// Bootstrap Laravel so we can use models (MasterPricelistSewaKontainer) for pricelist lookup
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use App\Models\MasterPricelistSewaKontainer;
$in = 'c:\\Users\\amanda\\Downloads\\template_daftar_tagihan_kontainer_sewa_rev_parsed.csv';
$ts = (new DateTime())->format('Ymd_His');
$out1 = "c:\\Users\\amanda\\Downloads\\template_expanded_start1_{$ts}.csv";
$out2 = "c:\\Users\\amanda\\Downloads\\template_expanded_start2_{$ts}.csv";

if (!file_exists($in)) { echo "Input CSV not found: {$in}\n"; exit(1); }

$FH = fopen($in, 'r');
if ($FH === false) { echo "Cannot open input\n"; exit(1); }

$header = fgetcsv($FH, 0, ';');
if ($header === false) { echo "Empty input\n"; exit(1); }
// Normalize header: trim, strip BOM from first cell, remove control chars
$header = array_map(function($c) {
    if ($c === null) return '';
    // remove BOM if present
    $c = preg_replace('/^\xEF\xBB\xBF/', '', $c);
    // remove other invisible/control chars
    $c = preg_replace('/[\x00-\x1F\x7F]/u', '', $c);
    return trim($c);
}, $header);

// prepare a lowercase header map for reliable name->index lookups
$normalizedLowerHeader = array_map('strtolower', $header);
$headerMap = array_flip($normalizedLowerHeader);
// prepare output files
$O1 = fopen($out1, 'w');
$O2 = fopen($out2, 'w');
if ($O1 === false || $O2 === false) { echo "Cannot open output files\n"; exit(1); }

// write BOM and header
fwrite($O1, "\xEF\xBB\xBF");
fwrite($O2, "\xEF\xBB\xBF");
fputcsv($O1, $header, ';');
fputcsv($O2, $header, ';');

$rows1 = 0; $rows2 = 0;
$sample1 = [];
$sample2 = [];

while (($row = fgetcsv($FH, 0, ';')) !== false) {
    if (count(array_filter($row, fn($c)=>trim((string)$c) !== '')) === 0) continue;
    // use precomputed lowercase header map for easier access
    $hmap = $headerMap;
    // read fields by header name if present
    $get = function($name) use ($row, $hmap) {
        $k = strtolower($name);
        if (isset($hmap[$k])) return $row[$hmap[$k]];
        return '';
    };

    $vendor = $get('vendor');
    $nomor = $get('nomor_kontainer');
    $group = $get('group');
    $tanggal_awal = $get('tanggal_awal');
    $tanggal_akhir = $get('tanggal_akhir');
    $computed = $get('computed_periode');
    $computed = ($computed === '' ? 1 : (int)$computed);

    // ensure computed >=1
    if ($computed < 1) $computed = 1;

    // Variant A: start at 1
    // prepare header indexes for periode and masa
    $periodeIdx = null; $masaIdx = null;
    foreach ($header as $i => $col) {
        $lc = strtolower($col);
        if ($lc === 'periode') $periodeIdx = $i;
        if ($lc === 'masa') $masaIdx = $i;
    }

    // compute period ranges: use original tanggal_awal and add (p-1) months so each period start preserves the day-of-month
    $origStart = null; $origDay = null;
    if (!empty($tanggal_awal)) {
        try { $origStart = new DateTime($tanggal_awal); $origStart->setTime(0,0,0); $origDay = (int)$origStart->format('j'); } catch (Exception $e) { $origStart = null; }
    }

    // prepare overall end date (if provided) to cap periods
    $overallEnd = null;
    if (!empty($tanggal_akhir)) {
        try { $overallEnd = new DateTime($tanggal_akhir); $overallEnd->setTime(0,0,0); } catch (Exception $e) { $overallEnd = null; }
    }

    for ($p = 1; $p <= $computed; $p++) {
        $row2 = $row;
        // set periode
        if ($periodeIdx === null) { $row2[] = $p; } else { $row2[$periodeIdx] = $p; }

    // masa as Indonesian date-range text (e.g. "21 Januari 2025 - 20 Februari 2025")
            $masaStr = '';
            if ($origStart !== null) {
                // compute period start by adding (p-1) months to original start, then ensure day-of-month is preserved when possible
                $periodStartP = (clone $origStart)->modify('+' . ($p - 1) . ' months');
                if ($origDay !== null) {
                    // adjust day to min(origDay, last day of target month)
                    $lastDay = (int)$periodStartP->format('t');
                    $dayToSet = min($origDay, $lastDay);
                    $periodStartP->setDate((int)$periodStartP->format('Y'), (int)$periodStartP->format('n'), $dayToSet);
                }
                $end = (clone $periodStartP)->modify('+1 month')->modify('-1 day');
                // cap by overall tanggal_akhir when provided
                if ($overallEnd !== null && $overallEnd < $end) {
                    $end = clone $overallEnd;
                }
                // if the period starts after overall end, skip this period
                if ($overallEnd !== null && $periodStartP > $overallEnd) {
                    continue;
                }
                // format with Indonesian month names
                $months = [1=>'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
                $formatIndo = function($dt) use ($months) {
                    $d = (int)$dt->format('j');
                    $m = (int)$dt->format('n');
                    $y = $dt->format('Y');
                    return $d . ' ' . strtolower($months[$m]) . ' ' . $y;
                };
                $masaStr = $formatIndo($periodStartP) . ' - ' . $formatIndo($end);
                // compute tarif: if dpp present use it as monthly rate; if missing, try pricelist lookup by vendor+size+date; prorate if period shorter
                // find index of relevant columns using normalized header map
                $dppIdx = $hmap['dpp'] ?? null;
                $tarifIdx = $hmap['tarif'] ?? null;
                $vendorIdx = $hmap['vendor'] ?? null;
                $sizeIdx = $hmap['size'] ?? ($hmap['ukuran'] ?? null);
                // if dpp empty try pricelist lookup
                if ($dppIdx !== null && (!isset($row[$dppIdx]) || trim($row[$dppIdx]) === '')) {
                    $vendorVal = ($vendorIdx !== null && isset($row[$vendorIdx])) ? $row[$vendorIdx] : null;
                    $sizeVal = ($sizeIdx !== null && isset($row[$sizeIdx])) ? $row[$sizeIdx] : null;
                    try {
                        if ($vendorVal !== null && $sizeVal !== null) {
                            $pr = MasterPricelistSewaKontainer::where('vendor', $vendorVal)
                                ->where('ukuran_kontainer', (int)$sizeVal)
                                ->where('tanggal_harga_awal', '<=', $periodStartP->format('Y-m-d'))
                                ->where(function($q) use ($periodStartP) {
                                    $q->whereNull('tanggal_harga_akhir')->orWhere('tanggal_harga_akhir', '>=', $periodStartP->format('Y-m-d'));
                                })
                                ->orderBy('tanggal_harga_awal', 'desc')
                                ->first();
                            if ($pr) {
                                // ensure we write into the output row's dpp index
                                $row2[$dppIdx] = (string)$pr->harga;
                                // debug
                                if (count($sample1) < 10) {
                                    $sample1[] = ["dpp_filled", $vendorVal, $nomor, $p, $pr->harga];
                                    echo "DEBUG: filled dpp for vendor={$vendorVal} nomor={$nomor} periode={$p} harga={$pr->harga}\n";
                                }
                            }
                        }
                    } catch (Exception $e) {
                        // ignore lookup errors
                    }
                }

                $tarifVal = '';
                if ($dppIdx !== null && isset($row2[$dppIdx]) && trim($row2[$dppIdx]) !== '') {
                    // determine if this period is full month or partial
                    $daysInPeriod = (int)$periodStartP->diff($end)->format('%a') + 1;
                    $daysInFullMonth = (int)$periodStartP->format('t');
                    $tarifVal = ($daysInPeriod >= $daysInFullMonth) ? 'Bulanan' : 'Harian';
                }
                if ($tarifIdx === null) { $row2[] = $tarifVal; } else { $row2[$tarifIdx] = $tarifVal; }
                // ensure dpp and tarif indexes are present in the output row (fill missing slots)
                $maxIdx = max(array_values($hmap));
                for ($ii = 0; $ii <= $maxIdx; $ii++) { if (!isset($row2[$ii])) $row2[$ii] = ''; }
            }

        // masa column
    if ($masaIdx === null) { $row2[] = $masaStr; } else { $row2[$masaIdx] = $masaStr; }
    // final normalize row length
    $maxIdx = max(array_values($hmap));
    for ($ii = 0; $ii <= $maxIdx; $ii++) { if (!isset($row2[$ii])) $row2[$ii] = ''; }
    fputcsv($O1, $row2, ';');
        $rows1++;
        if (count($sample1) < 10) $sample1[] = [$vendor,$nomor,$group,$p,$masaStr];
    }

    // Variant B: start at 2
    $start2 = 2;
    if ($start2 <= $computed) {
        // for variant B compute start from original tanggal_awal and pick periods starting from $start2
        $origStartB = null; $origDayB = null;
        if (!empty($tanggal_awal)) {
            try { $origStartB = new DateTime($tanggal_awal); $origStartB->setTime(0,0,0); $origDayB = (int)$origStartB->format('j'); } catch (Exception $e) { $origStartB = null; }
        }
        // prepare overall end for variant B as well
        $overallEndB = null;
        if (!empty($tanggal_akhir)) {
            try { $overallEndB = new DateTime($tanggal_akhir); $overallEndB->setTime(0,0,0); } catch (Exception $e) { $overallEndB = null; }
        }

        for ($p = $start2; $p <= $computed; $p++) {
            $row3 = $row;
            if ($periodeIdx === null) { $row3[] = $p; } else { $row3[$periodeIdx] = $p; }

            // masa as Indonesian date-range text
            $masaStr = '';
            if ($origStartB !== null) {
                $periodStartPB = (clone $origStartB)->modify('+' . ($p - 1) . ' months');
                if ($origDayB !== null) {
                    $lastDayB = (int)$periodStartPB->format('t');
                    $dayToSetB = min($origDayB, $lastDayB);
                    $periodStartPB->setDate((int)$periodStartPB->format('Y'), (int)$periodStartPB->format('n'), $dayToSetB);
                }
                $endB = (clone $periodStartPB)->modify('+1 month')->modify('-1 day');
                // cap by overall tanggal_akhir when provided
                if ($overallEndB !== null && $overallEndB < $endB) {
                    $endB = clone $overallEndB;
                }
                // skip if period starts after overall end
                if ($overallEndB !== null && $periodStartPB > $overallEndB) {
                    continue;
                }
                $months = [1=>'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
                $formatIndo = function($dt) use ($months) {
                    $d = (int)$dt->format('j');
                    $m = (int)$dt->format('n');
                    $y = $dt->format('Y');
                    return $d . ' ' . strtolower($months[$m]) . ' ' . $y;
                };
                $masaStr = $formatIndo($periodStartPB) . ' - ' . $formatIndo($endB);
                // compute tarif for variant B: try pricelist if dpp missing, then prorate
                // find indexes using normalized header map
                $dppIdx = $hmap['dpp'] ?? null;
                $tarifIdx = $hmap['tarif'] ?? null;
                $vendorIdx = $hmap['vendor'] ?? null;
                $sizeIdx = $hmap['size'] ?? ($hmap['ukuran'] ?? null);
                if ($dppIdx !== null && (!isset($row[$dppIdx]) || trim($row[$dppIdx]) === '')) {
                    $vendorVal = ($vendorIdx !== null && isset($row[$vendorIdx])) ? $row[$vendorIdx] : null;
                    $sizeVal = ($sizeIdx !== null && isset($row[$sizeIdx])) ? $row[$sizeIdx] : null;
                    try {
                        if ($vendorVal !== null && $sizeVal !== null) {
                            $pr = MasterPricelistSewaKontainer::where('vendor', $vendorVal)
                                ->where('ukuran_kontainer', (int)$sizeVal)
                                ->where('tanggal_harga_awal', '<=', $periodStartPB->format('Y-m-d'))
                                ->where(function($q) use ($periodStartPB) {
                                    $q->whereNull('tanggal_harga_akhir')->orWhere('tanggal_harga_akhir', '>=', $periodStartPB->format('Y-m-d'));
                                })
                                ->orderBy('tanggal_harga_awal', 'desc')
                                ->first();
                            if ($pr) {
                                $row3[$dppIdx] = (string)$pr->harga;
                            }
                        }
                    } catch (Exception $e) {}
                }
                $tarifVal = '';
                if ($dppIdx !== null && isset($row3[$dppIdx]) && trim($row3[$dppIdx]) !== '') {
                    $daysInPeriod = (int)$periodStartPB->diff($endB)->format('%a') + 1;
                    $daysInFullMonth = (int)$periodStartPB->format('t');
                    $tarifVal = ($daysInPeriod >= $daysInFullMonth) ? 'Bulanan' : 'Harian';
                }
                if ($tarifIdx === null) { $row3[] = $tarifVal; } else { $row3[$tarifIdx] = $tarifVal; }
                // ensure all expected indexes exist
                $maxIdx = max(array_values($hmap));
                for ($ii = 0; $ii <= $maxIdx; $ii++) { if (!isset($row3[$ii])) $row3[$ii] = ''; }
            }
            if ($masaIdx === null) { $row3[] = $masaStr; } else { $row3[$masaIdx] = $masaStr; }
            // final normalize row length
            $maxIdx = max(array_values($hmap));
            for ($ii = 0; $ii <= $maxIdx; $ii++) { if (!isset($row3[$ii])) $row3[$ii] = ''; }
            fputcsv($O2, $row3, ';');
            $rows2++;
            if (count($sample2) < 10) $sample2[] = [$vendor,$nomor,$group,$p,$masaStr];
        }
    }
}

fclose($FH);
fclose($O1);
fclose($O2);

echo "Wrote {$rows1} rows to {$out1} (start_period=1). Sample rows (vendor,nomor,group,periode):\n";
foreach ($sample1 as $s) echo implode(' | ', $s) . "\n";
echo "\nWrote {$rows2} rows to {$out2} (start_period=2). Sample rows (vendor,nomor,group,periode):\n";
foreach ($sample2 as $s) echo implode(' | ', $s) . "\n";
echo "\nDone.\n";
