<?php

// Bootstrap Laravel
require_once __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Pranota;

$p = Pranota::first();

echo "Raw status: " . $p->status . "\n";

// Check methods
$methods = get_class_methods($p);
echo "Has getStatusLabel: " . (in_array('getStatusLabel', $methods) ? 'YES' : 'NO') . "\n";
echo "Has getStatusText: " . (in_array('getStatusText', $methods) ? 'YES' : 'NO') . "\n";

if (method_exists($p, 'getStatusLabel')) {
    echo "getStatusLabel result: " . $p->getStatusLabel() . "\n";
}

if (method_exists($p, 'getStatusText')) {
    echo "getStatusText result: " . $p->getStatusText() . "\n";
}

// Check class hierarchy
echo "\nClass info:\n";
echo "Class: " . get_class($p) . "\n";
echo "Parent: " . get_parent_class($p) . "\n";

// Check file path
$reflection = new ReflectionClass($p);
echo "File: " . $reflection->getFileName() . "\n";

if (method_exists($p, 'getStatusLabel')) {
    $method = $reflection->getMethod('getStatusLabel');
    echo "Method file: " . $method->getFileName() . "\n";
    echo "Method line: " . $method->getStartLine() . "-" . $method->getEndLine() . "\n";
}

?>
