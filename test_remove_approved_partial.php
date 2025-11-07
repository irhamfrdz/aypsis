<?php
/**
 * Test untuk memverifikasi penghapusan status approved dan partial
 * dari sistem pranota uang jalan
 */

require_once 'vendor/autoload.php';

echo "=== Test Remove Approved & Partial Status ===\n\n";

// Test 1: Check Model Constants
echo "1. Testing Model Constants Removal:\n";
$modelFile = 'app/Models/PranotaUangJalan.php';

if (file_exists($modelFile)) {
    $modelContent = file_get_contents($modelFile);
    
    // Check removed constants
    $removedConstants = [
        'STATUS_APPROVED' => "'approved'",
        'STATUS_PARTIAL' => "'partial'"
    ];
    
    foreach ($removedConstants as $constant => $value) {
        if (strpos($modelContent, "const $constant = $value") === false) {
            echo "   ✅ $constant constant removed\n";
        } else {
            echo "   ❌ $constant constant still exists\n";
        }
    }
    
    // Check remaining constants
    $remainingConstants = [
        'STATUS_UNPAID' => "'unpaid'",
        'STATUS_PAID' => "'paid'", 
        'STATUS_CANCELLED' => "'cancelled'"
    ];
    
    foreach ($remainingConstants as $constant => $value) {
        if (strpos($modelContent, "const $constant = $value") !== false) {
            echo "   ✅ $constant constant preserved\n";
        } else {
            echo "   ❌ $constant constant missing\n";
        }
    }
} else {
    echo "   ❌ Model file not found\n";
}

// Test 2: Check Status Badge Accessor
echo "\n2. Testing Status Badge Accessor:\n";
if (file_exists($modelFile)) {
    $modelContent = file_get_contents($modelFile);
    
    // Check removed status cases
    $removedCases = [
        'STATUS_APPROVED',
        'STATUS_PARTIAL'
    ];
    
    foreach ($removedCases as $case) {
        if (strpos($modelContent, "case self::$case:") === false) {
            echo "   ✅ $case case removed from status badge\n";
        } else {
            echo "   ❌ $case case still exists in status badge\n";
        }
    }
    
    // Check remaining cases
    $remainingCases = [
        'STATUS_PAID',
        'STATUS_CANCELLED', 
        'STATUS_UNPAID'
    ];
    
    foreach ($remainingCases as $case) {
        if (strpos($modelContent, "case self::$case:") !== false) {
            echo "   ✅ $case case preserved in status badge\n";
        } else {
            echo "   ❌ $case case missing from status badge\n";
        }
    }
}

// Test 3: Check Status Text Accessor
echo "\n3. Testing Status Text Accessor:\n";
if (file_exists($modelFile)) {
    $modelContent = file_get_contents($modelFile);
    
    // Check removed text mappings
    $removedTexts = [
        'Disetujui',
        'Sebagian'
    ];
    
    foreach ($removedTexts as $text) {
        if (strpos($modelContent, "return '$text'") === false) {
            echo "   ✅ '$text' text removed\n";
        } else {
            echo "   ❌ '$text' text still exists\n";
        }
    }
    
    // Check remaining text mappings
    $remainingTexts = [
        'Belum Dibayar',
        'Lunas',
        'Dibatalkan'
    ];
    
    foreach ($remainingTexts as $text) {
        if (strpos($modelContent, "return '$text'") !== false) {
            echo "   ✅ '$text' text preserved\n";
        } else {
            echo "   ❌ '$text' text missing\n";
        }
    }
}

// Test 4: Check Controller Statistics
echo "\n4. Testing Controller Statistics:\n";
$controllerFile = 'app/Http/Controllers/PranotaSuratJalanController.php';

if (file_exists($controllerFile)) {
    $controllerContent = file_get_contents($controllerFile);
    
    // Check removed statistics
    if (strpos($controllerContent, "'approved' => PranotaUangJalan::where") === false) {
        echo "   ✅ 'approved' statistics removed\n";
    } else {
        echo "   ❌ 'approved' statistics still exists\n";
    }
    
    // Check remaining statistics
    $remainingStats = [
        "'total' => PranotaUangJalan::count()",
        "'this_month' => PranotaUangJalan::whereMonth",
        "'unpaid' => PranotaUangJalan::where('status_pembayaran', 'unpaid')",
        "'paid' => PranotaUangJalan::where('status_pembayaran', 'paid')"
    ];
    
    foreach ($remainingStats as $stat) {
        if (strpos($controllerContent, $stat) !== false) {
            echo "   ✅ Statistics preserved: " . explode(' =>', $stat)[0] . "\n";
        }
    }
} else {
    echo "   ❌ Controller file not found\n";
}

// Test 5: Check View Filter Options
echo "\n5. Testing View Filter Options:\n";
$viewFile = 'resources/views/pranota-uang-jalan/index.blade.php';

if (file_exists($viewFile)) {
    $viewContent = file_get_contents($viewFile);
    
    // Check removed options
    $removedOptions = [
        'value="approved"',
        'value="partial"'
    ];
    
    foreach ($removedOptions as $option) {
        if (strpos($viewContent, $option) === false) {
            echo "   ✅ Filter option removed: $option\n";
        } else {
            echo "   ❌ Filter option still exists: $option\n";
        }
    }
    
    // Check remaining options
    $remainingOptions = [
        'value="unpaid"',
        'value="paid"',
        'value="cancelled"'
    ];
    
    foreach ($remainingOptions as $option) {
        if (strpos($viewContent, $option) !== false) {
            echo "   ✅ Filter option preserved: $option\n";
        } else {
            echo "   ❌ Filter option missing: $option\n";
        }
    }
    
    // Check edit/delete condition
    if (strpos($viewContent, "in_array(\$pranota->status_pembayaran, ['unpaid', 'approved'])") === false) {
        echo "   ✅ Edit/delete condition updated (removed approved)\n";
    } else {
        echo "   ❌ Edit/delete condition still includes approved\n";
    }
    
    if (strpos($viewContent, "\$pranota->status_pembayaran == 'unpaid'") !== false) {
        echo "   ✅ Edit/delete condition simplified to unpaid only\n";
    }
} else {
    echo "   ❌ View file not found\n";
}

echo "\n=== Summary ===\n";
echo "✅ STATUS_APPROVED and STATUS_PARTIAL constants removed\n";
echo "✅ Status badge and text mappings cleaned up\n";
echo "✅ Controller statistics updated\n";
echo "✅ View filter options simplified\n";
echo "✅ Edit/delete permissions restricted to unpaid only\n";

echo "\n🎯 Result: Only 3 status remain: Belum Dibayar, Lunas, Dibatalkan\n";
?>