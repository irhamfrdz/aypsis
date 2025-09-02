<?php

// Simple direct test without Laravel
require_once __DIR__ . '/app/Models/Pranota.php';

echo "=== Direct Class Test ===\n\n";

// Check class methods directly
$reflection = new ReflectionClass(\App\Models\Pranota::class);
$methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);

echo "Methods defined in Pranota class:\n";
foreach ($methods as $method) {
    if ($method->getDeclaringClass()->getName() === \App\Models\Pranota::class) {
        echo "- " . $method->getName() . "()\n";
    }
}

echo "\n=== Direct Test Complete ===\n";
