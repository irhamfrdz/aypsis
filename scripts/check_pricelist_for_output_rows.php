<?php
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use App\Models\MasterPricelistSewaKontainer;

$path = __DIR__ . '/output_preview.csv';
$fh = fopen($path,'r');
$h = fgetcsv($fh,0,';');
$count=0;
while(($row=fgetcsv($fh,0,';'))!==false && $count<20){
    $vendor=$row[0]; $size=$row[2]; $tgl=$row[4]; $dpp=$row[9];
    $found=null;
    try{
        $pr = MasterPricelistSewaKontainer::where('vendor',$vendor)
            ->where('ukuran_kontainer',(int)$size)
            ->where('tanggal_harga_awal','<=',$tgl)
            ->where(function($q) use ($tgl){ $q->whereNull('tanggal_harga_akhir')->orWhere('tanggal_harga_akhir','>=',$tgl); })
            ->orderBy('tanggal_harga_awal','desc')->first();
        if($pr) $found=$pr->harga;
    }catch(Exception $e){$found='err';}
    echo "$count: vendor=$vendor size=$size tgl=$tgl dpp_in_csv=".($dpp===''?'<EMPTY>':$dpp)." pricelist_harga=".($found===null?'<NONE>':$found)."\n";
    $count++;
}
fclose($fh);
