<?php
// Debug parsed CSV records: show first 20 parsed rows from output_preview.csv
$in = __DIR__ . '/output_preview.csv';
$content = file_get_contents($in);
$content = str_replace(["\r\n","\r"],"\n", $content);
$records = [];
$len = strlen($content);
$inQuote = false;
$cur='';
for ($i=0;$i<$len;$i++){
    $ch = $content[$i];
    if ($ch === '"'){
        $next = ($i+1<$len)?$content[$i+1]:null;
        if ($inQuote && $next==='"'){ $cur.='"'; $i++; continue; }
        $inQuote = !$inQuote; $cur.=$ch; continue;
    }
    if ($ch==="\n" && !$inQuote){ $records[]=$cur; $cur=''; continue; }
    $cur.=$ch;
}
if (strlen($cur)>0) $records[]=$cur;

$header = str_getcsv(array_shift($records), ';');
echo "HEADER: ".implode('|',$header)."\n\n";
$count=0;
foreach ($records as $r){
    $cols = str_getcsv($r, ';');
    echo ($count+1) . ') ' . implode(' | ', array_slice($cols,0,10)) . "\n";
    $count++; if ($count>=20) break;
}
echo "Total records parsed: " . count($records) . "\n";
