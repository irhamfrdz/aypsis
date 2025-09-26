<?php

require_once 'vendor/autoload.php';

use App\Models\PranotaTagihanKontainerSewa;

echo "Checking pranota_tagihan_kontainer_sewa table...\n";

try {
    $count = PranotaTagihanKontainerSewa::count();
    echo "Total records: " . $count . "\n";

    if ($count > 0) {
        $first = PranotaTagihanKontainerSewa::first();
        echo "First record: " . json_encode($first->toArray()) . "\n";
    } else {
        echo "No records found in the table.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
