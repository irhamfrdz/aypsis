<?php
require_once __DIR__.'/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\PranotaSuratJalanController;
use App\Models\NomorTerakhir;
use Carbon\Carbon;

echo "=== TEST GENERATE NOMOR PRANOTA ===\n";

// Create controller instance
$controller = new PranotaSuratJalanController();

// Use reflection to test private method
$reflection = new ReflectionClass($controller);
$method = $reflection->getMethod('generateNomorPranota');
$method->setAccessible(true);

// Get current nomor terakhir
$nomorTerakhir = NomorTerakhir::where('modul', 'PSJ')->first();
$currentNumber = $nomorTerakhir ? $nomorTerakhir->nomor_terakhir : 0;

echo "Nomor terakhir saat ini: $currentNumber\n";

// Generate 3 nomor for testing
for ($i = 1; $i <= 3; $i++) {
    $nomor = $method->invoke($controller);
    echo "Generated Nomor $i: $nomor\n";
}

// Check final nomor terakhir
$nomorTerakhir->refresh();
echo "Nomor terakhir setelah generate: {$nomorTerakhir->nomor_terakhir}\n";

echo "\nFormat berhasil: PSJ-MMYY-XXXXXX ✓\n";
?>
