<?php

// Bootstrap Laravel
require_once __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$p = \App\Models\Pranota::first();

echo "Raw status: " . $p->status . "\n";

// Get reflection to check actual method content
$reflection = new ReflectionClass($p);
$method = $reflection->getMethod('getStatusLabel');

// Get method source
$file = $method->getFileName();
$startLine = $method->getStartLine();
$endLine = $method->getEndLine();

echo "\nMethod location: $file:$startLine-$endLine\n";

// Read actual file content
$lines = file($file);
$methodLines = array_slice($lines, $startLine - 1, $endLine - $startLine + 1);

echo "\nActual method code:\n";
foreach ($methodLines as $i => $line) {
    echo ($startLine + $i) . ": " . $line;
}

// Call method directly through reflection
echo "\nDirect reflection call: ";
try {
    echo $method->invoke($p) . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Check for accessor pattern
echo "\nChecking accessor pattern...\n";
echo "Has getStatusLabelAttribute: " . (method_exists($p, 'getStatusLabelAttribute') ? 'YES' : 'NO') . "\n";

// Check attributes
echo "\nModel attributes:\n";
foreach ($p->getAttributes() as $key => $value) {
    if (strpos($key, 'status') !== false) {
        echo "$key: $value\n";
    }
}

?>
