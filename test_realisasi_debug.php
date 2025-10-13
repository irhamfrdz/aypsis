<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\MasterKegiatan;
use App\Http\Controllers\RealisasiUangMukaController;

echo "Testing RealisasiUangMukaController functionality...\n";

// Test if controller can be instantiated
$controller = new RealisasiUangMukaController();
echo "Controller instantiated successfully\n";

// Test kegiatan detection
$kegiatan = MasterKegiatan::where('nama_kegiatan', 'LIKE', '%amprahan%')->first();
if ($kegiatan) {
    echo "Found Amprahan kegiatan: " . $kegiatan->nama_kegiatan . "\n";

    // Test reflection to call private methods
    $reflection = new ReflectionClass($controller);

    $isMobilMethod = $reflection->getMethod('isMobilBasedActivity');
    $isMobilMethod->setAccessible(true);

    $isSupirMethod = $reflection->getMethod('isSupirBasedActivity');
    $isSupirMethod->setAccessible(true);

    $isMobil = $isMobilMethod->invoke($controller, $kegiatan);
    $isSupir = $isSupirMethod->invoke($controller, $kegiatan);

    echo "Is Mobil Activity: " . ($isMobil ? 'true' : 'false') . "\n";
    echo "Is Supir Activity: " . ($isSupir ? 'true' : 'false') . "\n";

    if (!$isMobil && !$isSupir) {
        echo "This is a Penerima-based activity (Amprahan)\n";
    }
} else {
    echo "No Amprahan kegiatan found\n";
}

echo "Test completed.\n";
