<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    $count = DB::table('pranota_perbaikan_kontainers')->count();
    echo 'Total pranota records: ' . $count . PHP_EOL;

    if($count > 0) {
        $latest = DB::table('pranota_perbaikan_kontainers')->latest('created_at')->first();
        echo 'Latest record details:' . PHP_EOL;
        echo '- ID: ' . $latest->id . PHP_EOL;
        echo '- Nomor Pranota: ' . ($latest->nomor_pranota ?? 'NULL') . PHP_EOL;
        echo '- Tanggal Pranota: ' . ($latest->tanggal_pranota ?? 'NULL') . PHP_EOL;
        echo '- Status: ' . ($latest->status ?? 'NULL') . PHP_EOL;
        echo '- Created at: ' . $latest->created_at . PHP_EOL;
    } else {
        echo 'No pranota records found in database.' . PHP_EOL;
    }

    // Also check perbaikan_kontainers table
    $perbaikanCount = DB::table('perbaikan_kontainers')->count();
    echo PHP_EOL . 'Total perbaikan records: ' . $perbaikanCount . PHP_EOL;

    if($perbaikanCount > 0) {
        $latestPerbaikan = DB::table('perbaikan_kontainers')->latest('created_at')->first();
        echo 'Latest perbaikan record:' . PHP_EOL;
        echo '- ID: ' . $latestPerbaikan->id . PHP_EOL;
        echo '- Nomor Kontainer: ' . ($latestPerbaikan->nomor_kontainer ?? 'NULL') . PHP_EOL;
        echo '- Status: ' . ($latestPerbaikan->status_perbaikan ?? 'NULL') . PHP_EOL;
    }

} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
}
?>