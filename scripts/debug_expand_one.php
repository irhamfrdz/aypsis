<?php
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use App\Models\MasterPricelistSewaKontainer;
$in = 'c:\\Users\\amanda\\Downloads\\template_daftar_tagihan_kontainer_sewa_rev_parsed.csv';
$fh = fopen($in,'r'); $header = fgetcsv($fh,0,';');
$header = array_map(function($c){ if($c===null) return ''; $c = preg_replace('/^\xEF\xBB\xBF/','',$c); $c = preg_replace('/[\x00-\x1F\x7F]/u','',$c); return trim($c); }, $header);
$normalizedLowerHeader = array_map('strtolower', $header);
$headerMap = array_flip($normalizedLowerHeader);
echo "Header: \n"; print_r($header); echo "map: \n"; print_r($headerMap);
$row = fgetcsv($fh,0,';');
echo "Original row: \n"; print_r($row);
// emulate first loop iteration
$get = function($name) use ($row, $headerMap) { $k = strtolower($name); if(isset($headerMap[$k])) return $row[$headerMap[$k]]; return ''; };
$vendor = $get('vendor'); $nomor = $get('nomor_kontainer'); $tanggal_awal = $get('tanggal_awal'); $tanggal_akhir = $get('tanggal_akhir'); $computed = $get('computed_periode'); $computed = ($computed===''?1:(int)$computed);
$periodeIdx=null;$masaIdx=null; foreach($header as $i=>$col){ $lc=strtolower($col); if($lc==='periode') $periodeIdx=$i; if($lc==='masa') $masaIdx=$i; }
$origStart = new DateTime($tanggal_awal); $origDay=(int)$origStart->format('j'); $overallEnd = new DateTime($tanggal_akhir);
$p=1; $row2 = $row;
// compute periodStart
$periodStartP = (clone $origStart)->modify('+'.($p-1).' months'); $lastDay=(int)$periodStartP->format('t'); $dayToSet=min($origDay,$lastDay); $periodStartP->setDate((int)$periodStartP->format('Y'),(int)$periodStartP->format('n'),$dayToSet);
$end=(clone $periodStartP)->modify('+1 month')->modify('-1 day'); if($overallEnd < $end) $end = clone $overallEnd; if($periodStartP > $overallEnd){ echo "period starts after overall end\n"; exit; }
$months = [1=>'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
$formatIndo = function($dt) use($months){ $d=(int)$dt->format('j'); $m=(int)$dt->format('n'); $y=$dt->format('Y'); return $d.' '.strtolower($months[$m]).' '.$y; };
$masaStr = $formatIndo($periodStartP).' - '.$formatIndo($end);
// find indexes
$dppIdx=null;$tarifIdx=null;$vendorIdx=null;$sizeIdx=null; foreach($header as $i=>$col){ $lc=strtolower($col); if($lc==='dpp') $dppIdx=$i; if($lc==='tarif') $tarifIdx=$i; if($lc==='vendor') $vendorIdx=$i; if($lc==='size'||$lc==='ukuran') $sizeIdx=$i; }
echo "indexes: dpp=$dppIdx tarif=$tarifIdx vendor=$vendorIdx size=$sizeIdx\n";
if ($dppIdx !== null && (!isset($row[$dppIdx]) || trim($row[$dppIdx]) === '')) {
    $vendorVal = ($vendorIdx !== null && isset($row[$vendorIdx])) ? $row[$vendorIdx] : null;
    $sizeVal = ($sizeIdx !== null && isset($row[$sizeIdx])) ? $row[$sizeIdx] : null;
    echo "vendorVal=[$vendorVal] sizeVal=[$sizeVal]\n";
    $pr = MasterPricelistSewaKontainer::where('vendor',$vendorVal)->where('ukuran_kontainer',(int)$sizeVal)->where('tanggal_harga_awal','<=',$periodStartP->format('Y-m-d'))->where(function($q) use($periodStartP){$q->whereNull('tanggal_harga_akhir')->orWhere('tanggal_harga_akhir','>=',$periodStartP->format('Y-m-d'));})->orderBy('tanggal_harga_awal','desc')->first();
    echo "pr found? "; var_export($pr ? $pr->harga : null); echo "\n";
    if($pr){ $row2[$dppIdx] = (string)$pr->harga; }
}
// compute tarif
$tarifVal=''; if ($dppIdx !== null && isset($row2[$dppIdx]) && trim($row2[$dppIdx]) !== '') { $baseDpp = floatval(str_replace([',',' '],['','.'],$row2[$dppIdx])); $daysInPeriod = (int)$periodStartP->diff($end)->format('%a')+1; $daysInFullMonth=(int)$periodStartP->format('t'); if($daysInPeriod>=$daysInFullMonth) $tarifVal = number_format(round($baseDpp,2),2,'.',''); else $tarifVal = number_format(round($baseDpp*($daysInPeriod/$daysInFullMonth),2),2,'.',''); }
if ($tarifIdx === null) { $row2[] = $tarifVal; } else { $row2[$tarifIdx] = $tarifVal; }
if ($masaIdx === null) { $row2[] = $masaStr; } else { $row2[$masaIdx] = $masaStr; }

echo "After assignment row2: \n"; print_r($row2);

fclose($fh);
