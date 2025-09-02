<?php
// Simple preview script: finds the latest karyawan_export_*.csv and prints header + first 10 rows.
$paths = glob(__DIR__ . '/../public/exports/karyawan_export_*.csv');
if (!$paths) {
    echo "No karyawan export files found in public/exports\n";
    exit(1);
}
// sort by modified time desc
usort($paths, function($a, $b){
    return filemtime($b) - filemtime($a);
});
$path = $paths[0];
echo "Using file: $path\n\n";
$fp = fopen($path, 'r');
if (!$fp) {
    echo "Failed to open file\n";
    exit(1);
}
// read full content and detect BOM
$first = fgets($fp);
rewind($fp);
// attempt to detect delimiter (semicolon or comma)
$delim = substr_count($first, ';') > substr_count($first, ',') ? ';' : ',';
echo "Detected delimiter: '" . $delim . "'\n\n";
// read header
$header = fgetcsv($fp, 0, $delim);
if ($header === false) {
    echo "Failed to read header\n";
    exit(1);
}
// trim BOM from first header
$header[0] = preg_replace('/^\xEF\xBB\xBF/', '', $header[0]);
echo "Header columns (" . count($header) . "):\n";
foreach ($header as $i => $h) {
    echo sprintf("%3d: %s\n", $i+1, $h);
}
echo "\nSample rows:\n";
$max = 10; $i = 0;
while (($row = fgetcsv($fp, 0, $delim)) !== false && $i < $max) {
    // pad to header length
    $row = array_pad($row, count($header), '');
    echo ($i+1) . " | ";
    // show first 8 columns for brevity
    $vals = array_slice($row, 0, 8);
    echo implode(' ; ', array_map(function($v){ return strip_tags($v); }, $vals));
    echo "\n";
    $i++;
}
fclose($fp);
echo "\nDone. If this file looks correct, I can prepare an import script that runs within a DB transaction and validates before committing.\n";
