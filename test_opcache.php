<?php
// Clear OPcache if enabled
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "OPcache cleared\n";
} else {
    echo "OPcache not enabled\n";
}

// Bootstrap Laravel
require_once __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Force reload class
if (class_exists('App\Models\Pranota')) {
    echo "Pranota class loaded\n";
} else {
    echo "Pranota class NOT loaded\n";
}

use App\Models\Pranota;

$p = Pranota::first();

echo "Raw status: " . $p->status . "\n";

// Check methods
$methods = get_class_methods($p);
echo "Has getStatusLabel: " . (in_array('getStatusLabel', $methods) ? 'YES' : 'NO') . "\n";
echo "Has getStatusLabelNew: " . (in_array('getStatusLabelNew', $methods) ? 'YES' : 'NO') . "\n";

if (method_exists($p, 'getStatusLabel')) {
    echo "Old method result: " . $p->getStatusLabel() . "\n";
}

if (method_exists($p, 'getStatusLabelNew')) {
    echo "New method result: " . $p->getStatusLabelNew() . "\n";
}

?>
