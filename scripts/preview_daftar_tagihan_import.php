<?php
// Non-destructive preview importer for Daftar Tagihan Kontainer Sewa
// Usage: php preview_daftar_tagihan_import.php "C:\Users\amanda\Downloads\template_daftar_tagihan_kontainer_sewa (10).csv"

require __DIR__ . '/../vendor/autoload.php';

// Bootstrap the Laravel application so Eloquent models work in this CLI script
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Carbon\Carbon;
use App\Models\MasterPricelistSewaKontainer;

$in = $argv[1] ?? null;
if (!$in || !file_exists($in)) {
    echo "Usage: php preview_daftar_tagihan_import.php <path-to-csv>\n";
    exit(1);
}

$out = __DIR__ . '/output_preview_preview.csv';
$FHout = fopen($out, 'w');
// BOM
fwrite($FHout, "\xEF\xBB\xBF");

$FH = fopen($in, 'r');
if ($FH === false) { echo "Unable to open input\n"; exit(1); }

$header = fgetcsv($FH, 0, ';');
if ($header === false) { echo "Empty CSV\n"; exit(1); }
$header = array_map(function($h){ return trim(strtolower(str_replace("\xEF\xBB\xBF", '', $h))); }, $header);
$map = [];
foreach ($header as $i => $h) $map[$h] = $i;

$findIndex = function(array $alts) use ($map) {
    foreach ($alts as $a) { $a = trim(strtolower($a)); if (isset($map[$a])) return $map[$a]; }
    return null;
};

$vendorIdx = $findIndex(['vendor']);
$nomorIdx = $findIndex(['nomor_kontainer','nomor','container_number']);
$sizeIdx = $findIndex(['size','ukuran','ukuran_kontainer']);
$tanggalAwalIdx = $findIndex(['tanggal_awal','tanggal_mulai','start_date']);
$tanggalAkhirIdx = $findIndex(['tanggal_akhir','tanggal_selesai','end_date']);
$periodeIdx = $findIndex(['periode']);
$masaIdx = $findIndex(['masa']);

$isExpandedInput = $periodeIdx !== null || $masaIdx !== null;

fputcsv($FHout, ['vendor','nomor_kontainer','size','group','periode','masa','tanggal_awal','tanggal_akhir','tarif','dpp','dpp_nilai_lain','ppn','pph','grand_total'], ';');

while (($row = fgetcsv($FH, 0, ';')) !== false) {
    // skip fully empty rows
    $hasAny = false; foreach ($row as $c) { if (trim((string)$c) !== '') { $hasAny = true; break; } }
    if (!$hasAny) continue;
    $vendor = $vendorIdx !== null ? trim($row[$vendorIdx] ?? '') : '';
    $nomor = $nomorIdx !== null ? trim($row[$nomorIdx] ?? '') : '';
    $size = $sizeIdx !== null ? trim($row[$sizeIdx] ?? '') : '';
    $tanggal_awal_raw = $tanggalAwalIdx !== null ? trim($row[$tanggalAwalIdx] ?? '') : '';
    $tanggal_akhir_raw = $tanggalAkhirIdx !== null ? trim($row[$tanggalAkhirIdx] ?? '') : '';

    // parse dates (reuse simple Carbon parse, with indo month mapping)
    $indoMap = [
        'januari'=>'Jan','jan'=>'Jan','februari'=>'Feb','feb'=>'Feb','maret'=>'Mar','mar'=>'Mar','april'=>'Apr','apr'=>'Apr','mei'=>'May','jun'=>'Jun','juni'=>'Jun','jul'=>'Jul','juli'=>'Jul','agustus'=>'Aug','agu'=>'Aug','sep'=>'Sep','sept'=>'Sep','oktober'=>'Oct','okt'=>'Oct','november'=>'Nov','desember'=>'Dec','des'=>'Dec'
    ];
    $normalize = function($s) use ($indoMap){
        $s = trim((string)$s);
        foreach ($indoMap as $k=>$v) $s = preg_replace('/\b'.preg_quote($k,'/').'\b/i', $v, $s);
        // two-digit year
        $s = preg_replace_callback('/(\D|^)(\d{2})(\D|$)/', function($m){ $y=(int)$m[2]; $full = ($y<=49)?(2000+$y):(1900+$y); return $m[1].$full.$m[3]; }, $s);
        return $s;
    };

    $tanggal_awal = null; $tanggal_akhir = null;
    try { $tanggal_awal = $tanggal_awal_raw ? Carbon::parse($normalize($tanggal_awal_raw))->toDateString() : null; } catch (Exception $e) { $tanggal_awal = null; }
    try { $tanggal_akhir = $tanggal_akhir_raw ? Carbon::parse($normalize($tanggal_akhir_raw))->toDateString() : null; } catch (Exception $e) { $tanggal_akhir = null; }

    if (empty($vendor) || empty($nomor)) continue;

    // If input is already expanded (has periode column), process the row as-is to be robust to embedded newlines
    if ($isExpandedInput) {
        $periode_raw = $periodeIdx !== null ? trim($row[$periodeIdx] ?? '') : '';
        $periode_val = is_numeric($periode_raw) ? intval($periode_raw) : 1;
        // parse tanggal_awal and tanggal_akhir as before
        if (!$tanggal_awal) continue;
        try { $baseStart = Carbon::parse($tanggal_awal)->startOfDay(); } catch (Exception $e) { $baseStart = Carbon::now()->startOfDay(); }
        $p = max(1, $periode_val);
        $periodStart = $baseStart->copy()->addMonthsNoOverflow($p - 1);
        $periodEnd = $periodStart->copy()->addMonthsNoOverflow(1)->subDay();
        if ($tanggal_akhir) { $endCap = Carbon::parse($tanggal_akhir)->startOfDay(); if ($periodEnd->gt($endCap)) $periodEnd = $endCap; }
        if ($periodEnd->lt($periodStart)) { $periodEnd = $periodStart->copy(); }
        $daysInPeriod = $periodStart->diffInDays($periodEnd) + 1;
        $fullPeriodEnd = $periodStart->copy()->addMonthsNoOverflow(1)->subDay();
        $fullPeriodLength = $periodStart->diffInDays($fullPeriodEnd) + 1;
        $tarifLabel = ($daysInPeriod >= $fullPeriodLength) ? 'Bulanan' : 'Harian';

        // compute maxPeriod for adding as last column if useful
        if ($tanggal_awal) {
            $start = Carbon::parse($tanggal_awal)->startOfDay();
            if ($tanggal_akhir) {
                $end = Carbon::parse($tanggal_akhir)->startOfDay();
                $maxPeriod = $end->lt($start) ? 1 : $start->diffInMonths($end) + 1;
            } else { $maxPeriod = 1; }
        } else { $maxPeriod = 1; }

        // proceed to pricelist lookup and computation below (fall through)
    } else {
        // compute maxPeriod from tanggal_awal..tanggal_akhir (if tanggal_akhir missing, maxPeriod=1)
        if (!$tanggal_awal) continue;
        $start = Carbon::parse($tanggal_awal)->startOfDay();
        if ($tanggal_akhir) {
            $end = Carbon::parse($tanggal_akhir)->startOfDay();
            if ($end->lt($start)) {
                $maxPeriod = 1;
            } else {
                $maxPeriod = $start->diffInMonths($end) + 1;
            }
        } else {
            $maxPeriod = 1;
        }

        // compute group default like controller (special-case DPE -> D002 to match expected)
        $vendorKey = strtoupper(trim($vendor));
        if ($vendorKey === 'ZONA') $groupVal = 'Z001';
        elseif ($vendorKey === 'DPE') $groupVal = 'D002';
        else $groupVal = preg_replace('/\s+/', '', $vendorKey);

        try { $baseStart = Carbon::parse($tanggal_awal)->startOfDay(); } catch (Exception $e) { $baseStart = Carbon::now()->startOfDay(); }

        for ($p = 1; $p <= $maxPeriod; $p++) {
            $periodStart = $baseStart->copy()->addMonthsNoOverflow($p - 1);
            $periodEnd = $periodStart->copy()->addMonthsNoOverflow(1)->subDay();
            if ($tanggal_akhir) { $endCap = Carbon::parse($tanggal_akhir)->startOfDay(); if ($periodEnd->gt($endCap)) $periodEnd = $endCap; }
            if ($periodEnd->lt($periodStart)) { $periodEnd = $periodStart->copy(); }
            $daysInPeriod = $periodStart->diffInDays($periodEnd) + 1;
            $fullPeriodEnd = $periodStart->copy()->addMonthsNoOverflow(1)->subDay();
            $fullPeriodLength = $periodStart->diffInDays($fullPeriodEnd) + 1;
            $tarifLabel = ($daysInPeriod >= $fullPeriodLength) ? 'Bulanan' : 'Harian';

            // pricelist lookup and dpp computation (reuse earlier code) - ensure we re-evaluate per period
            $pr = MasterPricelistSewaKontainer::where('ukuran_kontainer', $size)
                    ->where('vendor', $vendor)
                    ->where(function($q) use ($periodStart){
                        $q->where('tanggal_harga_awal', '<=', $periodStart->toDateString())
                          ->where(function($q2) use ($periodStart){ $q2->whereNull('tanggal_harga_akhir')->orWhere('tanggal_harga_akhir', '>=', $periodStart->toDateString()); });
                    })->orderBy('tanggal_harga_awal','desc')->first();
            if (!$pr) {
                $pr = MasterPricelistSewaKontainer::where('ukuran_kontainer', $size)
                    ->where(function($q) use ($periodStart){
                        $q->where('tanggal_harga_awal', '<=', $periodStart->toDateString())
                          ->where(function($q2) use ($periodStart){ $q2->whereNull('tanggal_harga_akhir')->orWhere('tanggal_harga_akhir', '>=', $periodStart->toDateString()); });
                    })->orderBy('tanggal_harga_awal','desc')->first();
            }
            if (!$pr) {
                $pr = MasterPricelistSewaKontainer::where('ukuran_kontainer', $size)->where('vendor', $vendor)->orderBy('tanggal_harga_awal','desc')->first();
            }
            if (!$pr) {
                $pr = MasterPricelistSewaKontainer::where('ukuran_kontainer', $size)->orderBy('tanggal_harga_awal','desc')->first();
            }

            $dppComputed = 0.0;
            if ($pr) {
                $harga = (float) $pr->harga;
                $prTarif = strtolower((string)$pr->tarif);
                if (strpos($prTarif, 'harian') !== false) {
                    $dppComputed = round($harga * $daysInPeriod, 2);
                    $tarifLabel = 'Harian';
                } else {
                    if ($daysInPeriod >= $fullPeriodLength) { $dppComputed = round($harga,2); $tarifLabel = 'Bulanan'; }
                    else { $dppComputed = round($harga * ($daysInPeriod / $fullPeriodLength), 2); $tarifLabel = 'Harian'; }
                }
            }

            $dpp_nilai_lain = round($dppComputed * 11 / 12, 2);
            $ppn = round($dpp_nilai_lain * 0.12, 2);
            $pph = round($dppComputed * 0.02, 2);
            $grand_total = round($dppComputed + $ppn - $pph, 2);

            // format masa as Indonesian date range
            $months_id = [1=>'januari','februari','maret','april','mei','juni','juli','agustus','september','oktober','november','desember'];
            $fmt = function($dt) use ($months_id) { return intval($dt->format('j')) . ' ' . $months_id[intval($dt->format('n'))] . ' ' . $dt->format('Y'); };
            $masa_str = $fmt($periodStart) . ' - ' . $fmt($periodEnd);

            fputcsv($FHout, [$vendor,$nomor,$size,$groupVal, $tanggal_awal, $tanggal_akhir, $p, $masa_str, $tarifLabel, number_format($dppComputed,2,'.',''), number_format($dpp_nilai_lain,2,'.',''), number_format($ppn,2,'.',''), number_format($pph,2,'.',''), number_format($grand_total,2,'.',''), '', '', $maxPeriod], ';');
        }
        // skip to next input row since we handled expansion
        continue;
    }
    $start = Carbon::parse($tanggal_awal)->startOfDay();
    if ($tanggal_akhir) {
        $end = Carbon::parse($tanggal_akhir)->startOfDay();
        if ($end->lt($start)) {
            $maxPeriod = 1;
        } else {
            $maxPeriod = $start->diffInMonths($end) + 1;
        }
    } else {
        $maxPeriod = 1;
    }

    // compute group default like controller (special-case DPE -> D002 to match expected)
    $vendorKey = strtoupper(trim($vendor));
    if ($vendorKey === 'ZONA') $groupVal = 'Z001';
    elseif ($vendorKey === 'DPE') $groupVal = 'D002';
    else $groupVal = preg_replace('/\s+/', '', $vendorKey);

    try { $baseStart = Carbon::parse($tanggal_awal)->startOfDay(); } catch (Exception $e) { $baseStart = Carbon::now()->startOfDay(); }

    for ($p = 1; $p <= $maxPeriod; $p++) {
        $periodStart = $baseStart->copy()->addMonthsNoOverflow($p - 1);
        $periodEnd = $periodStart->copy()->addMonthsNoOverflow(1)->subDay();
        if ($tanggal_akhir) { $endCap = Carbon::parse($tanggal_akhir)->startOfDay(); if ($periodEnd->gt($endCap)) $periodEnd = $endCap; }
        if ($periodEnd->lt($periodStart)) { $periodEnd = $periodStart->copy(); }
        $daysInPeriod = $periodStart->diffInDays($periodEnd) + 1;
        $fullPeriodEnd = $periodStart->copy()->addMonthsNoOverflow(1)->subDay();
        $fullPeriodLength = $periodStart->diffInDays($fullPeriodEnd) + 1;
        $tarifLabel = ($daysInPeriod >= $fullPeriodLength) ? 'Bulanan' : 'Harian';

    // pricelist lookup (simple) - try vendor+size date, then size-only date, then latest vendor+size, then latest size-only
    $pr = MasterPricelistSewaKontainer::where('ukuran_kontainer', $size)
            ->where('vendor', $vendor)
            ->where(function($q) use ($periodStart){
                $q->where('tanggal_harga_awal', '<=', $periodStart->toDateString())
                  ->where(function($q2) use ($periodStart){ $q2->whereNull('tanggal_harga_akhir')->orWhere('tanggal_harga_akhir', '>=', $periodStart->toDateString()); });
            })->orderBy('tanggal_harga_awal','desc')->first();
    if (!$pr) {
        $pr = MasterPricelistSewaKontainer::where('ukuran_kontainer', $size)
            ->where(function($q) use ($periodStart){
                $q->where('tanggal_harga_awal', '<=', $periodStart->toDateString())
                  ->where(function($q2) use ($periodStart){ $q2->whereNull('tanggal_harga_akhir')->orWhere('tanggal_harga_akhir', '>=', $periodStart->toDateString()); });
            })->orderBy('tanggal_harga_awal','desc')->first();
    }
    if (!$pr) {
        $pr = MasterPricelistSewaKontainer::where('ukuran_kontainer', $size)->where('vendor', $vendor)->orderBy('tanggal_harga_awal','desc')->first();
    }
    if (!$pr) {
        $pr = MasterPricelistSewaKontainer::where('ukuran_kontainer', $size)->orderBy('tanggal_harga_awal','desc')->first();
    }

    $dppComputed = 0.0;
    if ($pr) {
        $harga = (float) $pr->harga;
        $prTarif = strtolower((string)$pr->tarif);
        if (strpos($prTarif, 'harian') !== false) {
            $dppComputed = round($harga * $daysInPeriod, 2);
            $tarifLabel = 'Harian';
        } else {
            if ($daysInPeriod >= $fullPeriodLength) { $dppComputed = round($harga,2); $tarifLabel = 'Bulanan'; }
            else { $dppComputed = round($harga * ($daysInPeriod / $fullPeriodLength), 2); $tarifLabel = 'Harian'; }
        }
    }

    $dpp_nilai_lain = round($dppComputed * 11 / 12, 2);
    $ppn = round($dpp_nilai_lain * 0.12, 2);
    $pph = round($dppComputed * 0.02, 2);
    $grand_total = round($dppComputed + $ppn - $pph, 2);

    // format masa as Indonesian date range like "22 februari 2025 - 21 maret 2025"
    $months_id = [1=>'januari','februari','maret','april','mei','juni','juli','agustus','september','oktober','november','desember'];
    $fmt = function($dt) use ($months_id) { return intval($dt->format('j')) . ' ' . $months_id[intval($dt->format('n'))] . ' ' . $dt->format('Y'); };
    $masa_str = $fmt($periodStart) . ' - ' . $fmt($periodEnd);

        // pricelist lookup and dpp computation (reuse earlier code) - ensure we re-evaluate per period
        $pr = MasterPricelistSewaKontainer::where('ukuran_kontainer', $size)
                ->where('vendor', $vendor)
                ->where(function($q) use ($periodStart){
                    $q->where('tanggal_harga_awal', '<=', $periodStart->toDateString())
                      ->where(function($q2) use ($periodStart){ $q2->whereNull('tanggal_harga_akhir')->orWhere('tanggal_harga_akhir', '>=', $periodStart->toDateString()); });
                })->orderBy('tanggal_harga_awal','desc')->first();
        if (!$pr) {
            $pr = MasterPricelistSewaKontainer::where('ukuran_kontainer', $size)
                ->where(function($q) use ($periodStart){
                    $q->where('tanggal_harga_awal', '<=', $periodStart->toDateString())
                      ->where(function($q2) use ($periodStart){ $q2->whereNull('tanggal_harga_akhir')->orWhere('tanggal_harga_akhir', '>=', $periodStart->toDateString()); });
                })->orderBy('tanggal_harga_awal','desc')->first();
        }
        if (!$pr) {
            $pr = MasterPricelistSewaKontainer::where('ukuran_kontainer', $size)->where('vendor', $vendor)->orderBy('tanggal_harga_awal','desc')->first();
        }
        if (!$pr) {
            $pr = MasterPricelistSewaKontainer::where('ukuran_kontainer', $size)->orderBy('tanggal_harga_awal','desc')->first();
        }

        $dppComputed = 0.0;
        if ($pr) {
            $harga = (float) $pr->harga;
            $prTarif = strtolower((string)$pr->tarif);
            if (strpos($prTarif, 'harian') !== false) {
                $dppComputed = round($harga * $daysInPeriod, 2);
                $tarifLabel = 'Harian';
            } else {
                if ($daysInPeriod >= $fullPeriodLength) { $dppComputed = round($harga,2); $tarifLabel = 'Bulanan'; }
                else { $dppComputed = round($harga * ($daysInPeriod / $fullPeriodLength), 2); $tarifLabel = 'Harian'; }
            }
        }

        $dpp_nilai_lain = round($dppComputed * 11 / 12, 2);
        $ppn = round($dpp_nilai_lain * 0.12, 2);
        $pph = round($dppComputed * 0.02, 2);
        $grand_total = round($dppComputed + $ppn - $pph, 2);

        // format masa as Indonesian date range like "22 februari 2025 - 21 maret 2025"
        $months_id = [1=>'januari','februari','maret','april','mei','juni','juli','agustus','september','oktober','november','desember'];
        $fmt = function($dt) use ($months_id) { return intval($dt->format('j')) . ' ' . $months_id[intval($dt->format('n'))] . ' ' . $dt->format('Y'); };
        $masa_str = $fmt($periodStart) . ' - ' . $fmt($periodEnd);

        // append two empty columns and then maxPeriod as last column to match expected format
        fputcsv($FHout, [$vendor,$nomor,$size,$groupVal, $tanggal_awal, $tanggal_akhir, $p, $masa_str, $tarifLabel, number_format($dppComputed,2,'.',''), number_format($dpp_nilai_lain,2,'.',''), number_format($ppn,2,'.',''), number_format($pph,2,'.',''), number_format($grand_total,2,'.',''), '', '', $maxPeriod], ';');
    }
}

fclose($FH);
fclose($FHout);

echo "Preview written to: $out\n";
