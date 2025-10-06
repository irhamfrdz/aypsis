<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    DB::statement('DROP TABLE IF EXISTS pembayaran_aktivitas_lainnya_items;');
    DB::statement('DROP TABLE IF EXISTS pembayaran_aktivitas_lainnya;');
    DB::statement('DROP TABLE IF EXISTS aktivitas_lainnya;');
    DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    echo "Tables dropped successfully\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

?>
