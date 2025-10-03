<?php

$csvPath = 'C:\\Users\\amanda\\Downloads\\export_tagihan_kontainer_sewa_2025-10-02_153813.csv';

echo "=== DEBUG CSV PARSING ===\n\n";

$handle = fopen($csvPath, 'r');
$delimiter = ';'; // From the file

$rowNum = 0;
while (($row = fgetcsv($handle, 1000, $delimiter)) !== false && $rowNum < 5) {
    $rowNum++;

    if ($rowNum == 1) {
        echo "HEADERS (Row 1):\n";
        foreach ($row as $idx => $header) {
            $cleaned = trim($header, " \t\n\r\0\x0B\"'"); // Remove quotes and whitespace
            echo "  [$idx] = '$cleaned' (raw: '$header')\n";
        }
        echo "\n";
        $headers = array_map(function($h) {
            return trim($h, " \t\n\r\0\x0B\"'");
        }, $row);
    } else {
        echo "DATA ROW $rowNum:\n";
        foreach ($row as $idx => $value) {
            $headerName = $headers[$idx] ?? "Unknown";
            echo "  [$idx] $headerName = '$value'\n";
        }
        echo "\n";
    }
}

fclose($handle);

echo "=== END DEBUG ===\n";
