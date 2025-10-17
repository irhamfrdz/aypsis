<?php
require_once __DIR__.'/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SuratJalan;
use App\Models\PranotaSuratJalan;
use Illuminate\Support\Facades\DB;

echo "=== TEST RELATIONSHIP ===\n";

// Test 1: Check if models exist
echo "1. Testing model instantiation...\n";
try {
    $suratJalan = new SuratJalan();
    echo "✓ SuratJalan model OK\n";
} catch (Exception $e) {
    echo "✗ SuratJalan error: " . $e->getMessage() . "\n";
}

try {
    $pranota = new PranotaSuratJalan();
    echo "✓ PranotaSuratJalan model OK\n";
} catch (Exception $e) {
    echo "✗ PranotaSuratJalan error: " . $e->getMessage() . "\n";
}

// Test 2: Check tables exist
echo "\n2. Testing table existence...\n";
try {
    $count = SuratJalan::count();
    echo "✓ surat_jalans table exists, count: $count\n";
} catch (Exception $e) {
    echo "✗ surat_jalans table error: " . $e->getMessage() . "\n";
}

try {
    $count = PranotaSuratJalan::count();
    echo "✓ pranota_surat_jalans table exists, count: $count\n";
} catch (Exception $e) {
    echo "✗ pranota_surat_jalans table error: " . $e->getMessage() . "\n";
}

// Test 3: Check pivot table
echo "\n3. Testing pivot table...\n";
try {
    $count = DB::table('pranota_surat_jalan_items')->count();
    echo "✓ pranota_surat_jalan_items table exists, count: $count\n";
} catch (Exception $e) {
    echo "✗ pranota_surat_jalan_items table error: " . $e->getMessage() . "\n";
}

// Test 4: Test relationship
echo "\n4. Testing relationships...\n";
try {
    $suratJalan = SuratJalan::first();
    if ($suratJalan) {
        $pranota = $suratJalan->pranotaSuratJalan();
        echo "✓ SuratJalan->pranotaSuratJalan() relationship OK\n";
    } else {
        echo "- No surat jalan found to test\n";
    }
} catch (Exception $e) {
    echo "✗ SuratJalan relationship error: " . $e->getMessage() . "\n";
}

try {
    $pranota = PranotaSuratJalan::first();
    if ($pranota) {
        $suratJalans = $pranota->suratJalans();
        echo "✓ PranotaSuratJalan->suratJalans() relationship OK\n";
    } else {
        echo "- No pranota found to test\n";
    }
} catch (Exception $e) {
    echo "✗ PranotaSuratJalan relationship error: " . $e->getMessage() . "\n";
}

echo "\n=== TEST COMPLETED ===\n";
?>
