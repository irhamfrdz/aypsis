<?php

// Bootstrap Laravel application
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

try {
    echo "Testing Pranota class...\n";

    // Check if class exists
    if (class_exists('App\Models\Pranota')) {
        echo "✓ App\Models\Pranota class exists\n";

        // Try to instantiate
        $pranota = new App\Models\Pranota();
        echo "✓ Successfully instantiated Pranota class\n";

        // Check if it's a model
        if ($pranota instanceof Illuminate\Database\Eloquent\Model) {
            echo "✓ Pranota is an Eloquent Model\n";
        }

        // Check table name
        echo "Table name: " . $pranota->getTable() . "\n";

    } else {
        echo "✗ App\Models\Pranota class NOT found\n";

        // Check if autoloader is working
        echo "Checking autoloader...\n";
        $classes = get_declared_classes();
        $appModels = array_filter($classes, function($class) {
            return strpos($class, 'App\Models\\') === 0;
        });
        echo "Found " . count($appModels) . " App\Models classes:\n";
        foreach (array_slice($appModels, 0, 10) as $class) {
            echo "  - $class\n";
        }
    }

} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
