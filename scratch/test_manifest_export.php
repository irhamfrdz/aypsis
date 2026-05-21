<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Exports\ManifestTableExport;
use App\Models\Manifest;
use Maatwebsite\Excel\Facades\Excel;

try {
    echo "Running manifest export test...\n";

    // Fetch some manifest records
    $manifests = Manifest::limit(50)->get();
    echo 'Fetched '.$manifests->count()." manifests.\n";

    if ($manifests->isEmpty()) {
        echo "No manifests found in database! Creating a dummy one...\n";
        // We won't save it, but let's see
    }

    $export = new ManifestTableExport($manifests);
    $path = 'scratch/manifest_test_output.xlsx';

    echo "Storing excel to $path...\n";
    Excel::store($export, $path, 'local');

    echo "Excel stored successfully!\n";

} catch (\Exception $e) {
    echo 'Error occurred: '.$e->getMessage()."\n";
    echo $e->getTraceAsString()."\n";
}
