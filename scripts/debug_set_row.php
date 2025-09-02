<?php
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use App\Models\MasterPricelistSewaKontainer;
$path = __DIR__ . '/output_preview.csv';
$fh = fopen($path,'r');
$header = fgetcsv($fh,0,';');
$row = fgetcsv($fh,0,';');
print_r($header);
print_r($row);
$hmap = array_change_key_case(array_flip(array_map('strtolower',$header)));
print_r($hmap);
$vendor = $row[$hmap['vendor']];
$size = $row[$hmap['size']];
$tgl = $row[$hmap['tanggal_awal']];
$dppIdx = $hmap['dpp'] ?? null; $tarifIdx = $hmap['tarif'] ?? null;
$periodStartP = new DateTime($tgl);
try{
    $pr = MasterPricelistSewaKontainer::where('vendor',$vendor)->where('ukuran_kontainer',(int)$size)
        ->where('tanggal_harga_awal','<=',$periodStartP->format('Y-m-d'))
        ->where(function($q) use($periodStartP){$q->whereNull('tanggal_harga_akhir')->orWhere('tanggal_harga_akhir','>=',$periodStartP->format('Y-m-d'));})
        ->orderBy('tanggal_harga_awal','desc')->first();
    echo "pricelist: "; print_r($pr?->toArray());
    if($pr){ $row2 = $row; $row2[$dppIdx] = (string)$pr->harga; echo "after set dpp: "; print_r($row2); }
}catch(Exception $e){ echo $e->getMessage(); }
fclose($fh);
