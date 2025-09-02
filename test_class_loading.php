<?php

echo "Testing class loading...\n";

// Bootstrap Laravel
require_once __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Laravel bootstrapped.\n";

// Test class exists
if (class_exists('App\Models\Pranota')) {
    echo "Pranota class exists!\n";

    $pranota = \App\Models\Pranota::first();
    echo "Pranota instance created. Status: {$pranota->status}\n";
    echo "getStatusLabel result: {$pranota->getStatusLabel()}\n";

} else {
    echo "Pranota class NOT FOUND!\n";
}

?>
