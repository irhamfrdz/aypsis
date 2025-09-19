<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Gate;
use App\Models\User;

// Get admin user
$user = User::where('username', 'admin')->first();
if (!$user) {
    echo "Admin user not found!\n";
    exit;
}

echo "=== CHECKING GATE REGISTRATION ===\n";

// Check if Gate is defined for kode nomor permission
$gateDefined = Gate::has('master-kode-nomor-view');
echo "Gate 'master-kode-nomor-view' defined: " . ($gateDefined ? 'YES' : 'NO') . "\n";

// Test Gate directly
if ($gateDefined) {
    $gateResult = Gate::allows('master-kode-nomor-view', [$user]);
    echo "Gate::allows('master-kode-nomor-view', \$user): " . ($gateResult ? 'TRUE' : 'FALSE') . "\n";

    $gateResult2 = Gate::check('master-kode-nomor-view', $user);
    echo "Gate::check('master-kode-nomor-view', \$user): " . ($gateResult2 ? 'TRUE' : 'FALSE') . "\n";

    $gateResult3 = $user->can('master-kode-nomor-view');
    echo "\$user->can('master-kode-nomor-view'): " . ($gateResult3 ? 'TRUE' : 'FALSE') . "\n";
}

// Check all gates related to kode nomor
echo "\n=== ALL GATES WITH 'kode-nomor' ===\n";
$allGates = array_keys(Gate::abilities());
foreach ($allGates as $gate) {
    if (strpos($gate, 'kode-nomor') !== false) {
        echo "- $gate\n";
    }
}

echo "\n=== CONCLUSION ===\n";
if ($gateDefined && $gateResult) {
    echo "✅ Gate is properly registered and working\n";
    echo "The issue might be elsewhere in the sidebar logic\n";
} else {
    echo "❌ Gate is not working properly\n";
    echo "This could be why the menu doesn't show in sidebar\n";
}
