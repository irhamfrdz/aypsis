<?php
// CLI importer: parse CSV and insert tagihan rows into DB idempotently.
// Usage: php import_daftar_tagihan_cli.php [path-to-csv]

require __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel app
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Carbon\Carbon;
use App\Models\MasterPricelistSewaKontainer;
use App\Models\DaftarTagihanKontainerSewa;

$in = $argv[1] ?? __DIR__ . '/output_preview.csv';
if (!file_exists($in)) { echo "Input not found: $in\n"; exit(1); }

echo "Importing from: $in\n";

$content = file_get_contents($in);
$content = str_replace(["\r\n","\r"], "\n", $content);

// split into logical CSV records respecting quotes
$records = [];
$len = strlen($content);
$inQuote = false;
$cur = '';
for ($i=0;$i<$len;$i++) {
    $ch = $content[$i];
    if ($ch === '"') {
        $next = ($i+1 < $len) ? $content[$i+1] : null;
        if ($inQuote && $next === '"') { $cur .= '"'; $i++; continue; }
        $inQuote = !$inQuote; $cur .= $ch; continue;
    }
    if ($ch === "\n" && !$inQuote) { $records[] = $cur; $cur = ''; continue; }
    $cur .= $ch;
}
if (strlen($cur) > 0) $records[] = $cur;
if (count($records) === 0) { echo "No records found\n"; exit(1); }

$header = str_getcsv(array_shift($records), ';');
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
$groupIdx = $findIndex(['group']);
$tanggalAwalIdx = $findIndex(['tanggal_awal','tanggal_mulai','start_date']);
$tanggalAkhirIdx = $findIndex(['tanggal_akhir','tanggal_selesai','end_date']);
$periodeIdx = $findIndex(['periode']);
$masaIdx = $findIndex(['masa']);
$tarifIdx = $findIndex(['tarif']);
$dppIdx = $findIndex(['dpp']);

// normalizers
$indoMap = [
    'januari'=>'Jan','jan'=>'Jan','februari'=>'Feb','feb'=>'Feb','maret'=>'Mar','mar'=>'Mar','april'=>'Apr','apr'=>'Apr','mei'=>'May','jun'=>'Jun','juni'=>'Jun','jul'=>'Jul','juli'=>'Jul','agustus'=>'Aug','agu'=>'Aug','sep'=>'Sep','sept'=>'Sep','oktober'=>'Oct','okt'=>'Oct','november'=>'Nov','desember'=>'Dec','des'=>'Dec'
];
$normalize = function($s) use ($indoMap){
    $s = trim((string)$s);
    foreach ($indoMap as $k=>$v) $s = preg_replace('/\b'.preg_quote($k,'/').'\b/i', $v, $s);
    $s = preg_replace_callback('/(\D|^)(\d{2})(\D|$)/', function($m){ $y=(int)$m[2]; $full = ($y<=49)?(2000+$y):(1900+$y); return $m[1].$full.$m[3]; }, $s);
    return $s;
};

$fmtMonths = [1=>'januari','februari','maret','april','mei','juni','juli','agustus','september','oktober','november','desember'];
$fmt = function($dt) use ($fmtMonths){ return intval($dt->format('j')) . ' ' . $fmtMonths[intval($dt->format('n'))] . ' ' . $dt->format('Y'); };

$created = 0;
$skipped = 0;
$totalRows = 0;

foreach ($records as $rec) {
    if (trim($rec) === '') continue;
    $row = str_getcsv($rec, ';');
    $totalRows++;
    $vendor = $vendorIdx!==null ? trim($row[$vendorIdx] ?? '') : '';
    $nomor = $nomorIdx!==null ? trim($row[$nomorIdx] ?? '') : '';
    $size = $sizeIdx!==null ? trim($row[$sizeIdx] ?? '') : '';
    if (empty($vendor) || empty($nomor)) { $skipped++; continue; }

    $tanggal_awal_raw = $tanggalAwalIdx!==null ? trim($row[$tanggalAwalIdx] ?? '') : '';
    $tanggal_akhir_raw = $tanggalAkhirIdx!==null ? trim($row[$tanggalAkhirIdx] ?? '') : '';
    try { $tanggal_awal = $tanggal_awal_raw ? Carbon::parse($normalize($tanggal_awal_raw))->toDateString() : null; } catch (Exception $e) { $tanggal_awal = null; }
    try { $tanggal_akhir = $tanggal_akhir_raw ? Carbon::parse($normalize($tanggal_akhir_raw))->toDateString() : null; } catch (Exception $e) { $tanggal_akhir = null; }

    $groupVal = '';
    if ($groupIdx !== null && !empty($row[$groupIdx])) $groupVal = trim($row[$groupIdx]);
    else { $vendorKey = strtoupper(trim($vendor)); if ($vendorKey==='ZONA') $groupVal='Z001'; elseif ($vendorKey==='DPE') $groupVal='D002'; else $groupVal = preg_replace('/\s+/','', $vendorKey); }

    // determine if input row is already expanded (contains periode)
    $isExpanded = ($periodeIdx !== null || $masaIdx !== null);
    if ($isExpanded) {
        $periode_raw = $periodeIdx!==null ? trim($row[$periodeIdx] ?? '') : '';
        $p = is_numeric($periode_raw) ? intval($periode_raw) : 1;
        if (!$tanggal_awal) { $skipped++; continue; }
        try { $baseStart = Carbon::parse($tanggal_awal)->startOfDay(); } catch (Exception $e) { $baseStart = Carbon::now()->startOfDay(); }
        $periodStart = $baseStart->copy()->addMonthsNoOverflow($p-1);
        $periodEnd = $periodStart->copy()->addMonthsNoOverflow(1)->subDay();
        if ($tanggal_akhir) { $endCap = Carbon::parse($tanggal_akhir)->startOfDay(); if ($periodEnd->gt($endCap)) $periodEnd = $endCap; }
        if ($periodEnd->lt($periodStart)) $periodEnd = $periodStart->copy();

        $daysInPeriod = $periodStart->diffInDays($periodEnd) + 1;
        $fullMonth = $periodStart->copy()->addMonthsNoOverflow(1)->subDay();
        $fullLen = $periodStart->diffInDays($fullMonth) + 1;

        // pricelist lookup
        $pr = MasterPricelistSewaKontainer::where('ukuran_kontainer', $size)
            ->where('vendor', $vendor)
            ->where(function($q) use ($periodStart){
                $q->where('tanggal_harga_awal', '<=', $periodStart->toDateString())
                  ->where(function($q2) use ($periodStart){ $q2->whereNull('tanggal_harga_akhir')->orWhere('tanggal_harga_akhir','>=',$periodStart->toDateString()); });
            })->orderBy('tanggal_harga_awal','desc')->first();
        if (!$pr) {
            $pr = MasterPricelistSewaKontainer::where('ukuran_kontainer', $size)
                ->where(function($q) use ($periodStart){
                    $q->where('tanggal_harga_awal', '<=', $periodStart->toDateString())
                      ->where(function($q2) use ($periodStart){ $q2->whereNull('tanggal_harga_akhir')->orWhere('tanggal_harga_akhir','>=',$periodStart->toDateString()); });
                })->orderBy('tanggal_harga_awal','desc')->first();
        }
        if (!$pr) { $pr = MasterPricelistSewaKontainer::where('ukuran_kontainer', $size)->where('vendor',$vendor)->orderBy('tanggal_harga_awal','desc')->first(); }
        if (!$pr) { $pr = MasterPricelistSewaKontainer::where('ukuran_kontainer', $size)->orderBy('tanggal_harga_awal','desc')->first(); }

        $dppComputed = 0.0;
        if ($pr) {
            $harga = (float)$pr->harga; $prTarif = strtolower((string)$pr->tarif);
            if (strpos($prTarif,'harian')!==false) { $dppComputed = round($harga * $daysInPeriod,2); $tarifLabel='Harian'; }
            else { if ($daysInPeriod >= $fullLen) { $dppComputed = round($harga,2); $tarifLabel='Bulanan'; } else { $dppComputed = round($harga * ($daysInPeriod/$fullLen),2); $tarifLabel='Harian'; } }
        }

        $dpp_nilai_lain = round($dppComputed * 11/12,2); $ppn = round($dpp_nilai_lain * 0.12,2); $pph = round($dppComputed * 0.02,2); $grand_total = round($dppComputed + $ppn - $pph,2);

        $attrs = ['vendor'=>$vendor,'nomor_kontainer'=>$nomor,'tanggal_awal'=>$periodStart->toDateString(),'periode'=>$p];
        $rowData = [
            'vendor'=>$vendor,'nomor_kontainer'=>$nomor,'size'=>$size,'group'=>$groupVal,
            'tanggal_awal'=>$periodStart->toDateString(),'tanggal_akhir'=>$periodEnd->toDateString(),'periode'=>$p,'masa'=>$daysInPeriod,
            'tarif'=>$tarifLabel,'dpp'=>$dppComputed,'dpp_nilai_lain'=>$dpp_nilai_lain,'ppn'=>$ppn,'pph'=>$pph,'grand_total'=>$grand_total,'status'=>'Tersedia'
        ];

        $m = DaftarTagihanKontainerSewa::firstOrCreate($attrs, array_merge($attrs,$rowData));
        if ($m->wasRecentlyCreated) $created++; else $skipped++;
        continue;
    }

    // not expanded: compute periods from start..end
    if (!$tanggal_awal) { $skipped++; continue; }
    $start = Carbon::parse($tanggal_awal)->startOfDay();
    if ($tanggal_akhir) { $end = Carbon::parse($tanggal_akhir)->startOfDay(); $maxPeriod = $end->lt($start)?1:$start->diffInMonths($end)+1; } else { $maxPeriod = 1; }

    try { $baseStart = Carbon::parse($tanggal_awal)->startOfDay(); } catch (Exception $e) { $baseStart = Carbon::now()->startOfDay(); }
    for ($p=1;$p<=$maxPeriod;$p++) {
        $periodStart = $baseStart->copy()->addMonthsNoOverflow($p-1);
        $periodEnd = $periodStart->copy()->addMonthsNoOverflow(1)->subDay();
        if ($tanggal_akhir) { $endCap = Carbon::parse($tanggal_akhir)->startOfDay(); if ($periodEnd->gt($endCap)) $periodEnd = $endCap; }
        if ($periodEnd->lt($periodStart)) $periodEnd = $periodStart->copy();
        $daysInPeriod = $periodStart->diffInDays($periodEnd) + 1;
        $fullMonth = $periodStart->copy()->addMonthsNoOverflow(1)->subDay(); $fullLen = $periodStart->diffInDays($fullMonth) + 1;

        // pricelist lookup
        $pr = MasterPricelistSewaKontainer::where('ukuran_kontainer', $size)
            ->where('vendor', $vendor)
            ->where(function($q) use ($periodStart){
                $q->where('tanggal_harga_awal', '<=', $periodStart->toDateString())
                  ->where(function($q2) use ($periodStart){ $q2->whereNull('tanggal_harga_akhir')->orWhere('tanggal_harga_akhir','>=',$periodStart->toDateString()); });
            })->orderBy('tanggal_harga_awal','desc')->first();
        if (!$pr) {
            $pr = MasterPricelistSewaKontainer::where('ukuran_kontainer', $size)
                ->where(function($q) use ($periodStart){
                    $q->where('tanggal_harga_awal', '<=', $periodStart->toDateString())
                      ->where(function($q2) use ($periodStart){ $q2->whereNull('tanggal_harga_akhir')->orWhere('tanggal_harga_akhir','>=',$periodStart->toDateString()); });
                })->orderBy('tanggal_harga_awal','desc')->first();
        }
        if (!$pr) { $pr = MasterPricelistSewaKontainer::where('ukuran_kontainer', $size)->where('vendor',$vendor)->orderBy('tanggal_harga_awal','desc')->first(); }
        if (!$pr) { $pr = MasterPricelistSewaKontainer::where('ukuran_kontainer', $size)->orderBy('tanggal_harga_awal','desc')->first(); }

        $dppComputed = 0.0;
        if ($pr) {
            $harga = (float)$pr->harga; $prTarif = strtolower((string)$pr->tarif);
            if (strpos($prTarif,'harian')!==false) { $dppComputed = round($harga * $daysInPeriod,2); $tarifLabel='Harian'; }
            else { if ($daysInPeriod >= $fullLen) { $dppComputed = round($harga,2); $tarifLabel='Bulanan'; } else { $dppComputed = round($harga * ($daysInPeriod/$fullLen),2); $tarifLabel='Harian'; } }
        }

        $dpp_nilai_lain = round($dppComputed * 11/12,2); $ppn = round($dpp_nilai_lain * 0.12,2); $pph = round($dppComputed * 0.02,2); $grand_total = round($dppComputed + $ppn - $pph,2);

        $attrs = ['vendor'=>$vendor,'nomor_kontainer'=>$nomor,'tanggal_awal'=>$periodStart->toDateString(),'periode'=>$p];
        $rowData = [
            'vendor'=>$vendor,'nomor_kontainer'=>$nomor,'size'=>$size,'group'=>$groupVal,
            'tanggal_awal'=>$periodStart->toDateString(),'tanggal_akhir'=>$periodEnd->toDateString(),'periode'=>$p,'masa'=>$daysInPeriod,
            'tarif'=>$tarifLabel,'dpp'=>$dppComputed,'dpp_nilai_lain'=>$dpp_nilai_lain,'ppn'=>$ppn,'pph'=>$pph,'grand_total'=>$grand_total,'status'=>'Tersedia'
        ];

        $m = DaftarTagihanKontainerSewa::firstOrCreate($attrs, array_merge($attrs,$rowData));
        if ($m->wasRecentlyCreated) $created++; else $skipped++;
    }
}

echo "Import finished. Total input rows: $totalRows. Created: $created. Skipped(existing): $skipped\n";

