<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Pranota;

echo "=== Debug Model Loading ===\n\n";

try {
    // Check if model exists
    echo "Model class: " . Pranota::class . "\n";

    // Get reflection info
    $reflection = new ReflectionClass(Pranota::class);
    echo "File: " . $reflection->getFileName() . "\n";

    $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
    echo "\nPublic methods:\n";
    foreach ($methods as $method) {
        if ($method->getDeclaringClass()->getName() === Pranota::class) {
            echo "- " . $method->getName() . "()\n";
        }
    }

    // Try to get an instance
    $pranota = Pranota::where('no_invoice', 'PTK12509000004')->first();
    if ($pranota) {
        echo "\n✓ Successfully loaded pranota instance\n";
        echo "Instance class: " . get_class($pranota) . "\n";

        // Check available methods on instance
        $instanceMethods = get_class_methods($pranota);
        $customMethods = array_filter($instanceMethods, function($method) {
            return strpos($method, 'getTagihan') !== false || strpos($method, 'tagihan') !== false;
        });

        echo "Methods containing 'tagihan':\n";
        foreach ($customMethods as $method) {
            echo "- $method\n";
        }
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== Debug Complete ===\n";
