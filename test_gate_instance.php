<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;

echo "=== TESTING GATE INSTANCE DIRECTLY ===\n";

// Get first user
$user = User::first();
if (!$user) {
    echo "❌ No users found in database\n";
    exit;
}

echo "Testing with user: {$user->name} (ID: {$user->id})\n\n";

// Get Gate instance directly
$gate = app(GateContract::class);
echo "Gate instance type: " . get_class($gate) . "\n";

// Test with direct Gate instance
$result = $gate->check('dashboard', $user);
echo "Direct Gate instance check: " . ($result ? '✅ ALLOWED' : '❌ DENIED') . "\n";

// Test facade
$result2 = Gate::check('dashboard', $user);
echo "Gate facade check: " . ($result2 ? '✅ ALLOWED' : '❌ DENIED') . "\n";

// Test allows
$result3 = Gate::allows('dashboard', $user);
echo "Gate facade allows: " . ($result3 ? '✅ ALLOWED' : '❌ DENIED') . "\n";

// Test user can
$result4 = $user->can('dashboard');
echo "User can: " . ($result4 ? '✅ ALLOWED' : '❌ DENIED') . "\n";

// Check if gate is defined
$defined = Gate::has('dashboard');
echo "Gate defined: " . ($defined ? '✅ YES' : '❌ NO') . "\n";

// Try to inspect the gate callback
try {
    $reflection = new ReflectionClass($gate);
    $gatesProperty = $reflection->getProperty('gates');
    $gatesProperty->setAccessible(true);
    $gates = $gatesProperty->getValue($gate);

    if (isset($gates['dashboard'])) {
        echo "Dashboard gate callback exists in gates array\n";
        $callback = $gates['dashboard'];
        echo "Callback type: " . gettype($callback) . "\n";

        if (is_callable($callback)) {
            echo "Callback is callable\n";
            $callbackResult = $callback($user);
            echo "Direct callback execution: " . ($callbackResult ? '✅ ALLOWED' : '❌ DENIED') . "\n";
        }
    } else {
        echo "Dashboard gate not found in gates array\n";
    }
} catch (Exception $e) {
    echo "Reflection error: " . $e->getMessage() . "\n";
}
