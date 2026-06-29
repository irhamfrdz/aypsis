<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Manifest;

$voyage = 'SP10BJ26';
$count = Manifest::where('no_voyage', $voyage)->count();

echo "Found {$count} manifests with voyage {$voyage}.\n";

if ($count > 0) {
    $deleted = Manifest::where('no_voyage', $voyage)->delete();
    echo "Successfully deleted {$deleted} manifests with voyage {$voyage}.\n";
} else {
    echo "No manifests deleted.\n";
}
