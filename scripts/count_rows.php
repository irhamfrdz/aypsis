<?php
$fh = fopen(__DIR__ . '/output_preview.csv', 'r');
if (!$fh) { echo "no file\n"; exit(1); }
$h = fgetcsv($fh, 0, ';');
$c = 0;
while (($r = fgetcsv($fh, 0, ';')) !== false) {
    $non = 0;
    foreach ($r as $col) { if (trim((string)$col) !== '') $non++; }
    if ($non > 0) $c++;
}
echo $c . "\n";
