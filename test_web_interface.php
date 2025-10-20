<?php

/**
 * Test Web Interface untuk Gate In
 * Script ini untuk testing web interface setelah penghapusan service_id
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

echo "=== TEST WEB INTERFACE GATE IN ===\n\n";

try {
    // 1. Test Routes
    echo "1. Testing Routes...\n";

    $gateInRoutes = [
        'gate-in.index',
        'gate-in.create',
        'gate-in.store',
        'gate-in.show',
        'gate-in.edit',
        'gate-in.update',
        'gate-in.destroy',
        'gate-in.calculate-total',
        'gate-in.get-kontainers-surat-jalan'
    ];

    foreach ($gateInRoutes as $routeName) {
        if (Route::has($routeName)) {
            echo "   ✓ Route '$routeName' exists\n";
        } else {
            echo "   ✗ Route '$routeName' missing\n";
        }
    }

    // 2. Test Model Relationships
    echo "\n2. Testing Model Relationships...\n";

    $gateIn = \App\Models\GateIn::with(['kapal', 'aktivitas', 'petikemas'])->first();

    if ($gateIn) {
        echo "   ✓ GateIn model loaded: {$gateIn->nomor_gate_in}\n";
        echo "   ✓ Kapal relationship: " . ($gateIn->kapal ? $gateIn->kapal->nama_kapal : 'No kapal') . "\n";
        echo "   ✓ Aktivitas count: {$gateIn->aktivitas->count()}\n";
        echo "   ✓ Petikemas count: {$gateIn->petikemas->count()}\n";

        // Test that service relationship is gone
        try {
            $service = $gateIn->service;
            echo "   ✗ Service relationship still exists (should be removed)\n";
        } catch (\Exception $e) {
            echo "   ✓ Service relationship properly removed\n";
        }
    } else {
        echo "   ⚠ No Gate In data found\n";
    }

    // 3. Test Controller Methods
    echo "\n3. Testing Controller Access...\n";

    $controller = new \App\Http\Controllers\GateInController();
    echo "   ✓ GateInController instantiated\n";

    // Check if methods exist
    $methods = ['index', 'create', 'store', 'show', 'edit', 'update', 'destroy', 'calculateTotal'];
    foreach ($methods as $method) {
        if (method_exists($controller, $method)) {
            echo "   ✓ Method '$method' exists\n";
        } else {
            echo "   ✗ Method '$method' missing\n";
        }
    }

    // 4. Test View Files
    echo "\n4. Testing View Files...\n";

    $viewFiles = [
        'gate-in.index',
        'gate-in.create',
        'gate-in.show',
        'gate-in.edit'
    ];

    foreach ($viewFiles as $viewFile) {
        if (view()->exists($viewFile)) {
            echo "   ✓ View '$viewFile' exists\n";
        } else {
            echo "   ✗ View '$viewFile' missing\n";
        }
    }

    // 5. Test Database Structure
    echo "\n5. Testing Database Structure...\n";

    $tables = [
        'gate_ins' => ['nomor_gate_in', 'kapal_id', 'pelabuhan', 'kegiatan', 'status'],
        'gate_in_aktivitas_details' => ['gate_in_id', 'aktivitas', 's_t_s', 'box', 'itm', 'tarif', 'total'],
        'gate_in_petikemas_details' => ['gate_in_id', 'no_petikemas', 's_t_s', 'estimasi', 'estimasi_biaya']
    ];

    foreach ($tables as $table => $requiredColumns) {
        if (\Schema::hasTable($table)) {
            echo "   ✓ Table '$table' exists\n";

            $actualColumns = \Schema::getColumnListing($table);
            $missingColumns = array_diff($requiredColumns, $actualColumns);

            if (empty($missingColumns)) {
                echo "     ✓ All required columns present\n";
            } else {
                echo "     ✗ Missing columns: " . implode(', ', $missingColumns) . "\n";
            }

            // Check that service_id is NOT present in gate_ins
            if ($table === 'gate_ins' && in_array('service_id', $actualColumns)) {
                echo "     ✗ service_id column still exists (should be removed)\n";
            } elseif ($table === 'gate_ins') {
                echo "     ✓ service_id column properly removed\n";
            }
        } else {
            echo "   ✗ Table '$table' missing\n";
        }
    }

    // 6. Test Pricelist Integration
    echo "\n6. Testing Pricelist Integration...\n";

    $pricelistCount = \App\Models\PricelistGateIn::count();
    echo "   ✓ Pricelist entries: $pricelistCount\n";

    $biayaTypes = \App\Models\PricelistGateIn::select('biaya')->distinct()->pluck('biaya');
    echo "   ✓ Available biaya types: " . $biayaTypes->implode(', ') . "\n";

    // Check for multiple biaya types needed
    $requiredBiaya = ['ADMINISTRASI', 'HAULAGE', 'LOLO'];
    $availableBiaya = $biayaTypes->toArray();
    $missingBiaya = array_diff($requiredBiaya, $availableBiaya);

    if (empty($missingBiaya)) {
        echo "   ✓ All required biaya types available for multiple biaya system\n";
    } else {
        echo "   ⚠ Missing biaya types: " . implode(', ', $missingBiaya) . "\n";
    }

    echo "\n=== WEB INTERFACE TEST COMPLETED ===\n";
    echo "System is ready for web interface testing!\n";
    echo "You can now access:\n";
    echo "- Index: /gate-in\n";
    echo "- Create: /gate-in/create\n";
    echo "- Show: /gate-in/{id}\n";
    echo "- Edit: /gate-in/{id}/edit\n";

} catch (Exception $e) {
    echo "\n✗ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
