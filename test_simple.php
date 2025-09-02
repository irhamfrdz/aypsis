<?php

// Bootstrap Laravel
require_once __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing Pranota model...\n";

try {
    $p = \App\Models\Pranota::first();

    echo "Success! Raw status: " . $p->status . "\n";
    echo "getStatusLabel result: " . $p->getStatusLabel() . "\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

?>
