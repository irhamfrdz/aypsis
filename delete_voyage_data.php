<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Bootstrap Laravel
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$voyage = 'SP04JB26';

try {
    DB::beginTransaction();

    echo "Deleting data for voyage: $voyage\n";

    // Delete from manifests
    $manifestCount = DB::table('manifests')->where('no_voyage', $voyage)->delete();
    echo "Deleted $manifestCount rows from manifests table.\n";

    // Delete from bls
    $blCount = DB::table('bls')->where('no_voyage', $voyage)->delete();
    echo "Deleted $blCount rows from bls table.\n";

    DB::commit();
    echo "Deletion completed successfully.\n";
} catch (\Exception $e) {
    DB::rollBack();
    echo "Error: " . $e->getMessage() . "\n";
}
