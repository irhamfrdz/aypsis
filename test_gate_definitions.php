<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Permission;
use Illuminate\Support\Facades\Gate;

echo '=== TESTING GATE DEFINITIONS ===' . PHP_EOL;

$user = User::where('username', 'test4')->first();

if (!$user) {
    echo 'User test4 not found' . PHP_EOL;
    exit(1);
}

echo 'Gate defined for "permohonan": ' . (Gate::has('permohonan') ? '✅ YES' : '❌ NO') . PHP_EOL;
echo 'Gate defined for "dashboard": ' . (Gate::has('dashboard') ? '✅ YES' : '❌ NO') . PHP_EOL;

echo PHP_EOL;
echo '=== TESTING GATE RESULTS ===' . PHP_EOL;
echo 'Gate::allows("dashboard"): ' . (Gate::allows('dashboard') ? '✅ YES' : '❌ NO') . PHP_EOL;
echo 'Gate::allows("permohonan"): ' . (Gate::allows('permohonan') ? '✅ YES' : '❌ NO') . PHP_EOL;

$user = User::first();

echo PHP_EOL;
if (!$user) {
    echo '=== TESTING USER-SPECIFIC GATES ===' . PHP_EOL;
    echo "❌ No users found in database\n";
    exit;
}

echo "Testing with user: {$user->name} (ID: {$user->id})\n\n";

echo 'Gate::forUser($user)->allows("dashboard"): ' . (Gate::forUser($user)->allows('dashboard') ? '✅ YES' : '❌ NO') . PHP_EOL;
echo 'Gate::forUser($user)->allows("permohonan"): ' . (Gate::forUser($user)->allows('permohonan') ? '✅ YES' : '❌ NO') . PHP_EOL;

echo PHP_EOL;
echo '=== TESTING can() METHOD ===' . PHP_EOL;
echo '$user->can("dashboard"): ' . ($user->can('dashboard') ? '✅ YES' : '❌ NO') . PHP_EOL;
echo '$user->can("permohonan"): ' . ($user->can('permohonan') ? '✅ YES' : '❌ NO') . PHP_EOL;

echo PHP_EOL;
echo '=== TESTING GATE RESULTS ===' . PHP_EOL;
$gateDefined = Gate::has('dashboard');
echo "Gate 'dashboard' defined: " . ($gateDefined ? '✅ YES' : '❌ NO') . "\n";
echo 'Gate::allows("dashboard"): ' . (Gate::allows('dashboard') ? '✅ YES' : '❌ NO') . PHP_EOL;
$gateDefinedMasterUser = Gate::has('master-user');
echo 'Gate::allows("permohonan"): ' . (Gate::allows('permohonan') ? '✅ YES' : '❌ NO') . PHP_EOL;
echo "Gate 'master-user' defined: " . ($gateDefinedMasterUser ? '✅ YES' : '❌ NO') . "\n";

echo PHP_EOL;
echo '=== TESTING USER-SPECIFIC GATES ===' . PHP_EOL;
$dashboardCheck = Gate::check('dashboard', $user);
echo "Gate::check('dashboard'): " . ($dashboardCheck ? '✅ ALLOWED' : '❌ DENIED') . "\n";
echo 'Gate::forUser($user)->allows("dashboard"): ' . (Gate::forUser($user)->allows('dashboard') ? '✅ YES' : '❌ NO') . PHP_EOL;
$masterUserCheck = Gate::check('master-user', $user);
echo 'Gate::forUser($user)->allows("permohonan"): ' . (Gate::forUser($user)->allows('permohonan') ? '✅ YES' : '❌ NO') . PHP_EOL;
echo "Gate::check('master-user'): " . ($masterUserCheck ? '✅ ALLOWED' : '❌ DENIED') . "\n";

echo PHP_EOL;
echo "\n=== COMPARING Gate::allows vs Gate::check ===\n";
echo '$user->can("dashboard"): ' . ($user->can('dashboard') ? '✅ YES' : '❌ NO') . PHP_EOL;
echo "Gate::allows('dashboard', \$user): " . (Gate::allows('dashboard', $user) ? '✅ ALLOWED' : '❌ DENIED') . "\n";
echo '$user->can("permohonan"): ' . ($user->can('permohonan') ? '✅ YES' : '❌ NO') . PHP_EOL;
echo "Gate::check('dashboard', \$user): " . (Gate::check('dashboard', $user) ? '✅ ALLOWED' : '❌ DENIED') . "\n";

// Test the Gate callback directly
echo "\n=== TESTING GATE CALLBACK ===\n";
try {
    $gateCallback = Gate::getCallback('dashboard');
    if ($gateCallback) {
        $result = $gateCallback($user);
        echo "Gate callback for 'dashboard' result: " . ($result ? '✅ ALLOWED' : '❌ DENIED') . "\n";
    } else {
        echo "❌ No callback found for gate 'dashboard'\n";
    }
} catch (Exception $e) {
    echo "❌ Exception in gate callback: " . $e->getMessage() . "\n";
}
