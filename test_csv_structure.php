<?php
echo "=== Testing CSV File Structure ===\n";

$file = 'TAGIHAN_DPE_IMPORT_READY.csv';

if (file_exists($file)) {
    $lines = file($file);
    echo "✅ File found: $file\n";
    echo "📊 Total lines: " . count($lines) . "\n";
    echo "📋 Header: " . trim($lines[0]) . "\n";
    echo "📝 Sample data (line 2): " . trim($lines[1]) . "\n";

    // Check delimiter
    $header = $lines[0];
    $semicolons = substr_count($header, ';');
    $commas = substr_count($header, ',');
    echo "🔍 Semicolons in header: $semicolons\n";
    echo "🔍 Commas in header: $commas\n";
    echo "✅ Delimiter detected: " . ($semicolons > $commas ? 'semicolon (;)' : 'comma (,)') . "\n";

    // Check if DPE format
    $isDpeFormat = strpos($header, 'Group;Kontainer;Awal;Akhir') !== false;
    echo "🏢 Format type: " . ($isDpeFormat ? 'DPE Format' : 'Standard Format') . "\n";

    echo "\n=== File Ready for Import! ===\n";
} else {
    echo "❌ File not found: $file\n";
}
?>
