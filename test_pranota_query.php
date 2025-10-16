<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\SuratJalan;
use App\Models\PranotaSuratJalan;

echo "=== Testing Pranota Surat Jalan Query ===\n\n";

try {
    // Test query untuk mendapatkan approved surat jalan
    echo "1. Testing approved surat jalans query...\n";
    $approvedSuratJalans = SuratJalan::where('status', 'fully_approved')
        ->whereDoesntHave('pranotaSuratJalan')
        ->orderBy('tanggal_surat_jalan', 'desc')
        ->get();

    echo "✅ Found " . $approvedSuratJalans->count() . " approved surat jalans without pranota\n\n";

    // Test relationship
    echo "2. Testing relationships...\n";
    $testPranota = PranotaSuratJalan::first();
    if ($testPranota) {
        echo "✅ Found test pranota: " . $testPranota->nomor_pranota . "\n";
        echo "✅ Related surat jalans: " . $testPranota->suratJalans->count() . "\n";
    } else {
        echo "ℹ️  No existing pranota found (this is normal for new installation)\n";
    }

    echo "\n✅ All queries working correctly!\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
