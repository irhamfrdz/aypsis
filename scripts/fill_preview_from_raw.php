<?php
// Robust fill script: parse possibly multiline CSV records, compute per-period preview rows,
// and write output_preview_preview.csv

require __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel for Eloquent
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Carbon\Carbon;
use App\Models\MasterPricelistSewaKontainer;

$in = $argv[1] ?? __DIR__ . '/output_preview.csv';
if (!file_exists($in)) { echo "Input file not found: $in\n"; exit(1); }
$out = __DIR__ . '/output_preview_preview.csv';

$content = file_get_contents($in);
if ($content === false) { echo "Unable to read input\n"; exit(1); }
$content = str_replace(["\r\n","\r"], "\n", $content);

$records = [];
$len = strlen($content);
$inQuote = false;
$cur = '';
for ($i=0; $i<$len; $i++) {
    $ch = $content[$i];
    if ($ch === '"') {
        $next = ($i+1 < $len) ? $content[$i+1] : null;
        if ($inQuote && $next === '"') { $cur .= '"'; $i++; continue; }
        $inQuote = !$inQuote;
        $cur .= $ch;
        continue;
    }
    if ($ch === "\n" && !$inQuote) { $records[] = $cur; $cur = ''; continue; }
    $cur .= $ch;
}
if (strlen($cur) > 0) $records[] = $cur;

if (count($records) === 0) { echo "No records\n"; exit(1); }

$headerRaw = array_shift($records);
$header = str_getcsv($headerRaw, ';');
$header = array_map(function($h){ return trim(strtolower(str_replace("\xEF\xBB\xBF", '', $h))); }, $header);
$map = [];
foreach ($header as $i=>$h) $map[$h] = $i;

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

// output file
$FHout = fopen($out, 'w');
if (!$FHout) { echo "Unable to open output\n"; exit(1); }
fwrite($FHout, "\xEF\xBB\xBF");
fputcsv($FHout, ['vendor','nomor_kontainer','size','group','tanggal_awal','tanggal_akhir','periode','masa','tarif','dpp','dpp_nilai_lain','ppn','pph','grand_total','','','max_period'], ';');

// helper normalize
$indoMap = [
    'januari'=>'Jan','jan'=>'Jan','februari'=>'Feb','feb'=>'Feb','maret'=>'Mar','mar'=>'Mar','april'=>'Apr','apr'=>'Apr','mei'=>'May','jun'=>'Jun','juni'=>'Jun','jul'=>'Jul','juli'=>'Jul','agustus'=>'Aug','agu'=>'Aug','sep'=>'Sep','sept'=>'Sep','oktober'=>'Oct','okt'=>'Oct','november'=>'Nov','desember'=>'Dec','des'=>'Dec'
];
$normalize = function($s) use ($indoMap){
    $s = trim((string)$s);
    foreach ($indoMap as $k=>$v) $s = preg_replace('/\b'.preg_quote($k,'/').'\b/i', $v, $s);
    $s = preg_replace_callback('/(\D|^)(\d{2})(\D|$)/', function($m){ $y=(int)$m[2]; $full = ($y<=49)?(2000+$y):(1900+$y); return $m[1].$full.$m[3]; }, $s);
    return $s;
};

$months_id = [1=>'januari','februari','maret','april','mei','juni','juli','agustus','september','oktober','november','desember'];
$fmt = function($dt) use ($months_id){ return intval($dt->format('j')) . ' ' . $months_id[intval($dt->format('n'))] . ' ' . $dt->format('Y'); };

foreach ($records as $rec) {
    if (trim($rec) === '') continue;
    $row = str_getcsv($rec, ';');
    // skip if row empty
    $hasAny=false; foreach ($row as $c) { if (trim((string)$c)!=='') { $hasAny=true; break; } }
    if (!$hasAny) continue;

    $vendor = $vendorIdx!==null ? trim($row[$vendorIdx] ?? '') : '';
    $nomor = $nomorIdx!==null ? trim($row[$nomorIdx] ?? '') : '';
    $size = $sizeIdx!==null ? trim($row[$sizeIdx] ?? '') : '';
    $tanggal_awal_raw = $tanggalAwalIdx!==null ? trim($row[$tanggalAwalIdx] ?? '') : '';
    $tanggal_akhir_raw = $tanggalAkhirIdx!==null ? trim($row[$tanggalAkhirIdx] ?? '') : '';

    $tanggal_awal = null; $tanggal_akhir = null;
    try { $tanggal_awal = $tanggal_awal_raw ? Carbon::parse($normalize($tanggal_awal_raw))->toDateString() : null; } catch (Exception $e) { $tanggal_awal = null; }
    try { $tanggal_akhir = $tanggal_akhir_raw ? Carbon::parse($normalize($tanggal_akhir_raw))->toDateString() : null; } catch (Exception $e) { $tanggal_akhir = null; }

    if (empty($vendor) || empty($nomor)) continue;

    // group mapping
    $vendorKey = strtoupper(trim($vendor));
    if ($vendorKey === 'ZONA') $groupVal = 'Z001';
    elseif ($vendorKey === 'DPE') $groupVal = 'D002';
    else $groupVal = preg_replace('/\s+/', '', $vendorKey);

    // if input already expanded (has periode column), use that
    $isExpanded = ($periodeIdx !== null || $masaIdx !== null);
    if ($isExpanded) {
        $periode_raw = $periodeIdx !== null ? trim($row[$periodeIdx] ?? '') : '';
        $p = is_numeric($periode_raw) ? intval($periode_raw) : 1;
        if (!$tanggal_awal) continue;
        try { $baseStart = Carbon::parse($tanggal_awal)->startOfDay(); } catch (Exception $e) { $baseStart = Carbon::now()->startOfDay(); }
        $periodStart = $baseStart->copy()->addMonthsNoOverflow($p-1);
        $periodEnd = $periodStart->copy()->addMonthsNoOverflow(1)->subDay();
        if ($tanggal_akhir) { $endCap = Carbon::parse($tanggal_akhir)->startOfDay(); if ($periodEnd->gt($endCap)) $periodEnd = $endCap; }
        if ($periodEnd->lt($periodStart)) $periodEnd = $periodStart->copy();

        $daysInPeriod = $periodStart->diffInDays($periodEnd) + 1;
        $fullPeriodEnd = $periodStart->copy()->addMonthsNoOverflow(1)->subDay();
        $fullPeriodLength = $periodStart->diffInDays($fullPeriodEnd) + 1;
        $tarifLabel = ($daysInPeriod >= $fullPeriodLength) ? 'Bulanan' : 'Harian';

        // compute maxPeriod
        if ($tanggal_awal) {
            $s = Carbon::parse($tanggal_awal)->startOfDay();
            if ($tanggal_akhir) { $e = Carbon::parse($tanggal_akhir)->startOfDay(); $maxPeriod = $e->lt($s)?1:$s->diffInMonths($e)+1; } else { $maxPeriod = 1; }
        } else { $maxPeriod = 1; }

        // pricelist lookup
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
        if (!$pr) { $pr = MasterPricelistSewaKontainer::where('ukuran_kontainer', $size)->where('vendor', $vendor)->orderBy('tanggal_harga_awal','desc')->first(); }
        if (!$pr) { $pr = MasterPricelistSewaKontainer::where('ukuran_kontainer', $size)->orderBy('tanggal_harga_awal','desc')->first(); }

        $dppComputed = 0.0;
        if ($pr) {
            $harga = (float)$pr->harga; $prTarif = strtolower((string)$pr->tarif);
            if (strpos($prTarif, 'harian') !== false) { $dppComputed = round($harga * $daysInPeriod,2); $tarifLabel='Harian'; }
            else { if ($daysInPeriod >= $fullPeriodLength) { $dppComputed = round($harga,2); $tarifLabel='Bulanan'; } else { $dppComputed = round($harga * ($daysInPeriod/$fullPeriodLength),2); $tarifLabel='Harian'; } }
        }
        $dpp_nilai_lain = round($dppComputed * 11/12,2); $ppn = round($dpp_nilai_lain * 0.12,2); $pph = round($dppComputed * 0.02,2); $grand_total = round($dppComputed + $ppn - $pph,2);
        $masa_str = $fmt($periodStart) . ' - ' . $fmt($periodEnd);
        fputcsv($FHout, [$vendor,$nomor,$size,$groupVal,$tanggal_awal,$tanggal_akhir,$p,$masa_str,$tarifLabel,number_format($dppComputed,2,'.',''),number_format($dpp_nilai_lain,2,'.',''),number_format($ppn,2,'.',''),number_format($pph,2,'.',''),number_format($grand_total,2,'.',''),'','',$maxPeriod], ';');
        continue;
    }

    // not expanded input: compute maxPeriod and expand
    if (!$tanggal_awal) continue;
    $s = Carbon::parse($tanggal_awal)->startOfDay();
    if ($tanggal_akhir) { $e = Carbon::parse($tanggal_akhir)->startOfDay(); $maxPeriod = $e->lt($s)?1:$s->diffInMonths($e)+1; } else { $maxPeriod = 1; }

    try { $baseStart = Carbon::parse($tanggal_awal)->startOfDay(); } catch (Exception $e) { $baseStart = Carbon::now()->startOfDay(); }

    for ($p=1;$p<=$maxPeriod;$p++) {
        $periodStart = $baseStart->copy()->addMonthsNoOverflow($p-1);
        $periodEnd = $periodStart->copy()->addMonthsNoOverflow(1)->subDay();
        if ($tanggal_akhir) { $endCap = Carbon::parse($tanggal_akhir)->startOfDay(); if ($periodEnd->gt($endCap)) $periodEnd = $endCap; }
        if ($periodEnd->lt($periodStart)) $periodEnd = $periodStart->copy();
        $daysInPeriod = $periodStart->diffInDays($periodEnd) + 1;
        $fullPeriodEnd = $periodStart->copy()->addMonthsNoOverflow(1)->subDay();
        $fullPeriodLength = $periodStart->diffInDays($fullPeriodEnd) + 1;
        $tarifLabel = ($daysInPeriod >= $fullPeriodLength) ? 'Bulanan' : 'Harian';

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
        if (!$pr) { $pr = MasterPricelistSewaKontainer::where('ukuran_kontainer', $size)->where('vendor', $vendor)->orderBy('tanggal_harga_awal','desc')->first(); }
        if (!$pr) { $pr = MasterPricelistSewaKontainer::where('ukuran_kontainer', $size)->orderBy('tanggal_harga_awal','desc')->first(); }

        $dppComputed = 0.0;
        if ($pr) {
            $harga = (float)$pr->harga; $prTarif = strtolower((string)$pr->tarif);
            if (strpos($prTarif, 'harian') !== false) { $dppComputed = round($harga * $daysInPeriod,2); $tarifLabel='Harian'; }
            else { if ($daysInPeriod >= $fullPeriodLength) { $dppComputed = round($harga,2); $tarifLabel='Bulanan'; } else { $dppComputed = round($harga * ($daysInPeriod/$fullPeriodLength),2); $tarifLabel='Harian'; } }
        }
        $dpp_nilai_lain = round($dppComputed * 11/12,2); $ppn = round($dpp_nilai_lain * 0.12,2); $pph = round($dppComputed * 0.02,2); $grand_total = round($dppComputed + $ppn - $pph,2);
        $masa_str = $fmt($periodStart) . ' - ' . $fmt($periodEnd);
        fputcsv($FHout, [$vendor,$nomor,$size,$groupVal,$tanggal_awal,$tanggal_akhir,$p,$masa_str,$tarifLabel,number_format($dppComputed,2,'.',''),number_format($dpp_nilai_lain,2,'.',''),number_format($ppn,2,'.',''),number_format($pph,2,'.',''),number_format($grand_total,2,'.',''),'','',$maxPeriod], ';');
    }
}

fclose($FHout);

echo "Filled preview written to: $out\n";
