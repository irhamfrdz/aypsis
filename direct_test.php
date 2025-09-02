<?php

echo "Direct file test...\n";

// Test load file directly
require_once __DIR__.'/app/Models/Pranota.php';

echo "File loaded directly.\n";

// Test namespace
use App\Models\Pranota;

echo "Namespace used.\n";

// Bootstrap Laravel
require_once __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Laravel bootstrapped.\n";

// Test class
$pranota = new Pranota();
echo "New instance created.\n";

// Test database
$first = Pranota::first();
echo "Database query successful.\n";
echo "Status: {$first->status}\n";
echo "getStatusLabel: {$first->getStatusLabel()}\n";

?>
