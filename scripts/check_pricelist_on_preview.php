<?php
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use App\Models\MasterPricelistSewaKontainer;
$in = __DIR__ . '/output_preview.csv';
if (!file_exists($in)) { echo "preview not found: $in\n"; exit(1); }
$fh = fopen($in,'r'); $header = fgetcsv($fh,0,';');
// normalize header
$header = array_map(function($c){ if($c===null) return '';
    // strip Unicode BOM (U+FEFF) and any leading UTF-8 BOM bytes
    $c = preg_replace('/^\x{FEFF}/u','',$c);
    if (substr($c,0,3) === "\xEF\xBB\xBF") $c = substr($c,3);
    // remove other invisible/control chars
    $c = preg_replace('/[\x00-\x1F\x7F]/u','',$c);
    return trim($c);
}, $header);
$hmap = array_flip(array_map('strtolower',$header));
// debug: print header and header map
echo "Normalized header:\n"; print_r($header);
echo "Header map keys:\n"; print_r($hmap);

// compute indexes once
$vendorIdx = $hmap['vendor'] ?? null;
$sizeIdx = $hmap['size'] ?? ($hmap['ukuran'] ?? null);
$periodeIdx = $hmap['periode'] ?? null;
$tanggalIdx = $hmap['tanggal_awal'] ?? null;

// debug presence checks + raw bytes of keys
echo "Header map raw keys with hex:\n";
foreach (array_keys($hmap) as $k) {
    $hex = bin2hex($k);
    echo "key='" . $k . "' hex={$hex} idx={$hmap[$k]}\n";
}
echo "\nisset(hmap['vendor'])=" . (isset($hmap['vendor']) ? 'true' : 'false') . "\n";
echo "array_key_exists('vendor', hmap)=" . (array_key_exists('vendor', $hmap) ? 'true' : 'false') . "\n";
echo "vendorIdx raw: "; var_export($vendorIdx); echo "\n";
echo "tanggalIdx raw: "; var_export($tanggalIdx); echo "\n";

if ($vendorIdx === null || $tanggalIdx === null) {
    echo "Header missing required columns; aborting.\n";
    exit(1);
}

$cnt = 0;
while(($row = fgetcsv($fh,0,';')) !== false && $cnt < 40) {
    $cnt++;
    if (!is_array($row) || !isset($row[$vendorIdx]) || !isset($row[$tanggalIdx])) {
        echo "Row #$cnt missing expected columns (count=".count((array)$row).") - skipping\n";
        print_r($row);
        continue;
    }
    $vendor = $row[$vendorIdx];
    $size = $sizeIdx !== null && isset($row[$sizeIdx]) ? $row[$sizeIdx] : '';
    $periode = $periodeIdx !== null && isset($row[$periodeIdx]) ? $row[$periodeIdx] : '1';
    $tanggal_awal = $row[$tanggalIdx] ?? '';
    if ($tanggal_awal === '') { echo "$cnt: no tanggal_awal\n"; continue; }
    $p = intval($periode) > 0 ? intval($periode) : 1;
    try { $origStart = new DateTime($tanggal_awal); } catch(Exception $e) { echo "$cnt: invalid tanggal_awal $tanggal_awal\n"; continue; }
    $periodStart = (clone $origStart)->modify('+' . ($p-1) . ' months');
    // adjust day
    $day = (int)$origStart->format('j'); $last = (int)$periodStart->format('t'); $dayToSet = min($day,$last); $periodStart->setDate((int)$periodStart->format('Y'), (int)$periodStart->format('n'), $dayToSet);
    // lookup pricelist
    $pr = MasterPricelistSewaKontainer::where('vendor',$vendor)
        ->where('ukuran_kontainer',(int)$size)
        ->where('tanggal_harga_awal','<=',$periodStart->format('Y-m-d'))
        ->where(function($q) use ($periodStart){ $q->whereNull('tanggal_harga_akhir')->orWhere('tanggal_harga_akhir','>=',$periodStart->format('Y-m-d')); })
        ->orderBy('tanggal_harga_awal','desc')
        ->first();
    $price = $pr ? $pr->harga : null;
    // inspect CSV dpp/tarif values if present
    $tarifCsv = isset($row[8]) ? $row[8] : null;
    $dppCsv = isset($row[9]) ? $row[9] : null;
    echo sprintf("%02d: vendor=%s size=%s periode=%s start=%s pricelist=%s csv_dpp='%s' csv_tarif='%s'\n", $cnt, $vendor, $size, $p, $periodStart->format('Y-m-d'), $price===null?'<none>':$price, $dppCsv, $tarifCsv);
}
fclose($fh);
