<?php

// Bootstrap Laravel
require_once __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== METHOD REFLECTION DEBUG ===\n\n";

try {
    $pranota = \App\Models\Pranota::first();
    $reflection = new ReflectionClass($pranota);

    echo "1. Class hierarchy:\n";
    echo "   - Class: " . $reflection->getName() . "\n";
    echo "   - Parent: " . $reflection->getParentClass()->getName() . "\n";
    echo "   - File: " . $reflection->getFileName() . "\n";

    echo "\n2. getStatusLabel method details:\n";
    if ($reflection->hasMethod('getStatusLabel')) {
        $method = $reflection->getMethod('getStatusLabel');
        echo "   - Declaring class: " . $method->getDeclaringClass()->getName() . "\n";
        echo "   - File: " . $method->getFileName() . "\n";
        echo "   - Start line: " . $method->getStartLine() . "\n";
        echo "   - End line: " . $method->getEndLine() . "\n";
        echo "   - Is final: " . ($method->isFinal() ? 'YES' : 'NO') . "\n";
        echo "   - Is static: " . ($method->isStatic() ? 'YES' : 'NO') . "\n";
        echo "   - Is abstract: " . ($method->isAbstract() ? 'YES' : 'NO') . "\n";
    } else {
        echo "   - Method NOT found in class\n";
    }

    echo "\n3. All methods in class:\n";
    $methods = $reflection->getMethods();
    foreach ($methods as $method) {
        if (strpos($method->getName(), 'Status') !== false) {
            echo "   - {$method->getName()} (in {$method->getDeclaringClass()->getName()})\n";
        }
    }

    echo "\n4. Check for __call method:\n";
    echo "   - Has __call: " . ($reflection->hasMethod('__call') ? 'YES' : 'NO') . "\n";
    if ($reflection->hasMethod('__call')) {
        $callMethod = $reflection->getMethod('__call');
        echo "   - __call in: " . $callMethod->getDeclaringClass()->getName() . "\n";
    }

    echo "\n5. Traits used:\n";
    $traits = $reflection->getTraits();
    foreach ($traits as $trait) {
        echo "   - " . $trait->getName() . "\n";
    }

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

echo "\n=== END REFLECTION DEBUG ===\n";
?>
