<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    $controller = new App\Http\Controllers\NomorTerakhirController();
    echo "✅ Controller instantiated successfully\n";

    $model = new App\Models\NomorTerakhir();
    echo "✅ Model instantiated successfully\n";

    $count = App\Models\NomorTerakhir::count();
    echo "✅ Found $count records in nomor_terakhir table\n";

} catch(Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
