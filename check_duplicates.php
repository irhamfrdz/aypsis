<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Bl;

$voyage = 'AP11PJ26';
$duplicates = Bl::select('nomor_bl')
    ->where('no_voyage', $voyage)
    ->groupBy('nomor_bl')
    ->havingRaw('COUNT(id) > 1')
    ->get();

echo "Duplicate BLs for voyage {$voyage}:\n";
foreach ($duplicates as $dup) {
    $count = Bl::where('no_voyage', $voyage)->where('nomor_bl', $dup->nomor_bl)->count();
    echo "- {$dup->nomor_bl} (Count: {$count})\n";
    $records = Bl::where('no_voyage', $voyage)->where('nomor_bl', $dup->nomor_bl)->get();
    foreach ($records as $record) {
        echo "  ID: {$record->id}, Created at: {$record->created_at}\n";
    }
}
