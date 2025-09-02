<?php
$fh = fopen(__DIR__ . '/output_preview.csv','r');
$h = fgetcsv($fh,0,';');
$hmap = array_flip(array_map('strtolower',$h));
$dppIdx = $hmap['dpp'] ?? null;
if ($dppIdx === null) { echo "no dpp column\n"; exit(1); }
$cnt = 0; $total = 0;
while(($r = fgetcsv($fh,0,';')) !== false) { $total++; if(!isset($r[$dppIdx]) || trim($r[$dppIdx]) === '') $cnt++; }
echo "total=$total empty_dpp=$cnt\n";
