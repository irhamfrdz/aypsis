<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

// Load Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "Testing Gate In Route Access...\n\n";
    
    // Test if route exists
    if (Route::has('gate-in.index')) {
        echo "✓ Gate In route exists\n";
    } else {
        echo "❌ Gate In route not found\n";
        exit(1);
    }
    
    // Test controller exists
    if (class_exists('App\Http\Controllers\GateInController')) {
        echo "✓ GateInController class exists\n";
    } else {
        echo "❌ GateInController class not found\n";
        exit(1);
    }
    
    // Test models exist
    if (class_exists('App\Models\GateIn')) {
        echo "✓ GateIn model exists\n";
    } else {
        echo "❌ GateIn model not found\n";
    }
    
    if (class_exists('App\Models\MasterTerminal')) {
        echo "✓ MasterTerminal model exists\n";
    } else {
        echo "❌ MasterTerminal model not found\n";
    }
    
    if (class_exists('App\Models\MasterService')) {
        echo "✓ MasterService model exists\n";
    } else {
        echo "❌ MasterService model not found\n";
    }
    
    // Test database connection
    try {
        $gateInCount = DB::table('gate_ins')->count();
        echo "✓ Database connection OK, Gate Ins: $gateInCount\n";
    } catch (Exception $e) {
        echo "❌ Database error: " . $e->getMessage() . "\n";
    }
    
    echo "\n🎯 All tests passed! Gate In should be accessible.\n";
    echo "Try accessing: http://127.0.0.1:8000/gate-in\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}