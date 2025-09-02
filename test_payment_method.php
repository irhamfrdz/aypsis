<?php

use Illuminate\Http\Request;

// Test route directly
$request = new Request();
$controller = new \App\Http\Controllers\PranotaController();

try {
    echo "Testing showPaymentForm method...\n";
    $result = $controller->showPaymentForm($request);
    echo "Method executed successfully\n";
    echo "Result type: " . get_class($result) . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
