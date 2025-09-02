<?php
$path = __DIR__ . '/output_preview.csv';
$fh = fopen($path, 'r');
$h = fgetcsv($fh, 0, ';');
$row = fgetcsv($fh, 0, ';');
echo count($h) . " cols, " . count($row) . " rowcols\n";
foreach ($h as $i => $c) echo "$i:$c\n";
echo "---\n";
foreach ($row as $i => $v) echo "$i:" . ($v === '' ? '<EMPTY>' : $v) . "\n";
fclose($fh);
