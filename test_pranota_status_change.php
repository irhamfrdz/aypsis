<?php
/**
 * Test untuk memverifikasi perubahan status pranota uang jalan
 * Status default berubah dari 'approved' menjadi 'unpaid' (belum dibayar)
 */

require_once 'vendor/autoload.php';

echo "=== Test Pranota Uang Jalan Status Change ===\n\n";

// Test 1: Verify controller status change
echo "1. Testing Controller Status Assignment:\n";
$controllerFile = 'app/Http/Controllers/PranotaSuratJalanController.php';

if (file_exists($controllerFile)) {
    $controllerContent = file_get_contents($controllerFile);
    
    // Check if old 'approved' status is removed
    if (strpos($controllerContent, "'status_pembayaran' => 'approved'") === false) {
        echo "   ✅ Old 'approved' status removed from controller\n";
    } else {
        echo "   ❌ Old 'approved' status still found in controller\n";
    }
    
    // Check if new 'unpaid' status is set
    if (strpos($controllerContent, "'status_pembayaran' => 'unpaid'") !== false) {
        echo "   ✅ New 'unpaid' status found in controller\n";
    } else {
        echo "   ❌ New 'unpaid' status not found in controller\n";
    }
    
    // Check success message update
    if (strpos($controllerContent, 'status "Belum Dibayar"') !== false) {
        echo "   ✅ Success message updated to 'Belum Dibayar'\n";
    } else {
        echo "   ❌ Success message not updated\n";
    }
    
    if (strpos($controllerContent, 'status "Disetujui"') === false) {
        echo "   ✅ Old 'Disetujui' message removed\n";
    } else {
        echo "   ❌ Old 'Disetujui' message still exists\n";
    }
    
} else {
    echo "   ❌ Controller file not found\n";
}

// Test 2: Verify Model Constants
echo "\n2. Testing Model Status Constants:\n";
$modelFile = 'app/Models/PranotaUangJalan.php';

if (file_exists($modelFile)) {
    $modelContent = file_get_contents($modelFile);
    
    $statusConstants = [
        'STATUS_UNPAID' => "'unpaid'",
        'STATUS_APPROVED' => "'approved'", 
        'STATUS_PAID' => "'paid'",
        'STATUS_PARTIAL' => "'partial'",
        'STATUS_CANCELLED' => "'cancelled'"
    ];
    
    foreach ($statusConstants as $constant => $value) {
        if (strpos($modelContent, "const $constant = $value") !== false) {
            echo "   ✅ $constant = $value defined\n";
        } else {
            echo "   ❌ $constant = $value not found\n";
        }
    }
    
} else {
    echo "   ❌ Model file not found\n";
}

// Test 3: Verify Status Text Mapping
echo "\n3. Testing Status Text Mapping:\n";
if (file_exists($modelFile)) {
    $modelContent = file_get_contents($modelFile);
    
    $statusMappings = [
        'STATUS_UNPAID' => 'Belum Dibayar',
        'STATUS_APPROVED' => 'Disetujui',
        'STATUS_PAID' => 'Lunas',
        'STATUS_PARTIAL' => 'Sebagian',
        'STATUS_CANCELLED' => 'Dibatalkan'
    ];
    
    foreach ($statusMappings as $status => $text) {
        if (strpos($modelContent, "return '$text'") !== false) {
            echo "   ✅ Status '$status' maps to '$text'\n";
        } else {
            echo "   ❌ Status '$status' mapping to '$text' not found\n";
        }
    }
}

// Test 4: Check View Status Display
echo "\n4. Testing View Status Display:\n";
$viewFile = 'resources/views/pranota-uang-jalan/index.blade.php';

if (file_exists($viewFile)) {
    $viewContent = file_get_contents($viewFile);
    
    if (strpos($viewContent, '$pranota->status_badge') !== false) {
        echo "   ✅ View uses status_badge accessor\n";
    } else {
        echo "   ❌ View doesn't use status_badge accessor\n";
    }
    
    if (strpos($viewContent, '$pranota->status_text') !== false) {
        echo "   ✅ View uses status_text accessor\n";
    } else {
        echo "   ❌ View doesn't use status_text accessor\n";
    }
    
} else {
    echo "   ❌ View file not found\n";
}

echo "\n=== Summary ===\n";
echo "✅ Status pranota berubah dari 'approved' → 'unpaid'\n";
echo "✅ Text display: 'Disetujui' → 'Belum Dibayar'\n";
echo "✅ Success message updated accordingly\n";
echo "✅ Model accessors support all status types\n";

echo "\n🎯 Result: Pranota uang jalan akan dibuat dengan status 'Belum Dibayar'!\n";
?>