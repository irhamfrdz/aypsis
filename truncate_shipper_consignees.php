<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    \Illuminate\Support\Facades\DB::table('shipper_consignees')->truncate();
    \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    
    echo "Success: Tabel shipper_consignees berhasil dikosongkan!\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
