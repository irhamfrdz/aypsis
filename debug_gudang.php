<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Gudang;

try {
    echo "Check Gudang Data:\n";
    $countTotal = Gudang::count();
    echo "Total Gudang: " . $countTotal . "\n";
    
    $activeGudangs = Gudang::where('status', 'Active')->orderBy('nama_gudang')->get();
    echo "Active Gudangs: " . $activeGudangs->count() . "\n";

    if ($activeGudangs->count() > 0) {
        foreach ($activeGudangs as $g) {
            echo "- " . $g->nama_gudang . " (Status: " . $g->status . ")\n";
        }
    } else {
        echo "No active gudangs found.\n";
        $allGudangs = Gudang::all();
        echo "All Gudangs list:\n";
        foreach ($allGudangs as $g) {
            echo "- " . $g->nama_gudang . " (Status: '" . $g->status . "')\n"; // Quote status to check for hidden chars or different case
        }
    }

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
