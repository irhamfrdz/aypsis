<?php
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use App\Models\MasterPricelistSewaKontainer;
$in = 'c:\\Users\\amanda\\Downloads\\template_daftar_tagihan_kontainer_sewa_rev_parsed.csv';
if(!file_exists($in)){ echo "input not found: $in\n"; exit(1);}
$fh = fopen($in,'r'); $h = fgetcsv($fh,0,';'); $row = fgetcsv($fh,0,';');
print_r($h); print_r($row);
$header = $h;
// compute for p=1
$p=1;
// find indexes
$dppIdx=null;$tarifIdx=null;$vendorIdx=null;$sizeIdx=null;$tanggalIdx=null;
foreach($header as $i=>$c){ $lc=strtolower($c); if($lc==='dpp') $dppIdx=$i; if($lc==='tarif') $tarifIdx=$i; if($lc==='vendor') $vendorIdx=$i; if($lc==='size' || $lc==='ukuran') $sizeIdx=$i; if($lc==='tanggal_awal') $tanggalIdx=$i; }
$vendor = $row[$vendorIdx] ?? null; $size = $row[$sizeIdx] ?? null; $tanggal = $row[$tanggalIdx] ?? null;
$origStart = new DateTime($tanggal); $periodStartP = (clone $origStart)->modify('+' . ($p-1) . ' months');
$lastDay = (int)$periodStartP->format('t'); $dayToSet = min((int)$origStart->format('j'), $lastDay); $periodStartP->setDate((int)$periodStartP->format('Y'), (int)$periodStartP->format('n'), $dayToSet);
$end = (clone $periodStartP)->modify('+1 month')->modify('-1 day');
$found=null; try{ $pr = MasterPricelistSewaKontainer::where('vendor',$vendor)->where('ukuran_kontainer',(int)$size)->where('tanggal_harga_awal','<=',$periodStartP->format('Y-m-d'))->where(function($q) use($periodStartP){$q->whereNull('tanggal_harga_akhir')->orWhere('tanggal_harga_akhir','>=',$periodStartP->format('Y-m-d'));})->orderBy('tanggal_harga_awal','desc')->first(); if($pr) $found=$pr->harga;}catch(Exception $e){echo $e->getMessage();}
echo "vendor=$vendor size=$size tanggal=$tanggal periodStart=".$periodStartP->format('Y-m-d')." end=".$end->format('Y-m-d')." pricelist_harga=".($found===null?'<none>':$found)."\n";
if($found!==null){ $baseDpp = floatval($found); $daysInPeriod = (int)$periodStartP->diff($end)->format('%a')+1; $daysInFullMonth = (int)$periodStartP->format('t'); if($daysInPeriod>=$daysInFullMonth) $tarif = number_format(round($baseDpp,2),2,'.',''); else $tarif = number_format(round($baseDpp*($daysInPeriod/$daysInFullMonth),2),2,'.',''); echo "computed tarif=$tarif (daysInPeriod=$daysInPeriod fullMonth=$daysInFullMonth)\n"; }
fclose($fh);
