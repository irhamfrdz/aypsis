<?php

// Bootstrap Laravel
require_once __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Pranota;

$p = Pranota::first();

echo "Raw status: " . $p->status . "\n";
echo "Old method: " . $p->getStatusLabel() . "\n";
echo "New method: " . $p->getStatusLabelNew() . "\n";

?>
