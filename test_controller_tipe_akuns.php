<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\TipeAkun;
use App\Http\Controllers\KodeNomorController;

echo "Testing KodeNomorController create method...\n";
echo "=============================================\n";

$controller = new KodeNomorController();

// Test method create
try {
    $response = $controller->create();

    // Extract data from view
    $viewData = $response->getData();

    if (isset($viewData['tipeAkuns'])) {
        $tipeAkuns = $viewData['tipeAkuns'];
        echo "✅ Data tipeAkuns berhasil dikirim ke view!\n";
        echo "Jumlah tipe akun: " . $tipeAkuns->count() . "\n\n";

        echo "Daftar tipe akun yang akan muncul di dropdown:\n";
        foreach ($tipeAkuns as $tipeAkun) {
            echo "- {$tipeAkun->tipe_akun}";
            if ($tipeAkun->catatan) {
                echo " - {$tipeAkun->catatan}";
            }
            echo "\n";
        }
    } else {
        echo "❌ Data tipeAkuns tidak ditemukan di view data!\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\nTest selesai!\n";
