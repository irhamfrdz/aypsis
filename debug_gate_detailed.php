<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\Permission;

// Get admin user
$user = User::with('permissions')->where('username', 'admin')->first();
if (!$user) {
    echo "Admin user not found!\n";
    exit;
}

echo "=== DETAILED GATE DEBUGGING ===\n";

// Get the permission object
$permission = Permission::where('name', 'master-kode-nomor-view')->first();
if (!$permission) {
    echo "Permission 'master-kode-nomor-view' not found in database!\n";
    exit;
}

echo "Permission found: {$permission->name} (ID: {$permission->id})\n";

// Test the exact closure used in Gate definition
$gateClosure = function (User $user) use ($permission) {
    return $user->permissions()->where('name', $permission->name)->exists();
};

$closureResult = $gateClosure($user);
echo "Gate closure result: " . ($closureResult ? 'TRUE' : 'FALSE') . "\n";

// Test Gate::allows with different parameter formats
echo "\n=== TESTING Gate::allows ===\n";

$result1 = Gate::allows('master-kode-nomor-view');
echo "Gate::allows('master-kode-nomor-view'): " . ($result1 ? 'TRUE' : 'FALSE') . "\n";

$result2 = Gate::allows('master-kode-nomor-view', [$user]);
echo "Gate::allows('master-kode-nomor-view', [\$user]): " . ($result2 ? 'TRUE' : 'FALSE') . "\n";

$result3 = Gate::allows('master-kode-nomor-view', $user);
echo "Gate::allows('master-kode-nomor-view', \$user): " . ($result3 ? 'TRUE' : 'FALSE') . "\n";

// Test Gate::check
echo "\n=== TESTING Gate::check ===\n";

$result4 = Gate::check('master-kode-nomor-view');
echo "Gate::check('master-kode-nomor-view'): " . ($result4 ? 'TRUE' : 'FALSE') . "\n";

$result5 = Gate::check('master-kode-nomor-view', $user);
echo "Gate::check('master-kode-nomor-view', \$user): " . ($result5 ? 'TRUE' : 'FALSE') . "\n";

// Test with Auth::user() if available
echo "\n=== TESTING WITH AUTH ===\n";
use Illuminate\Support\Facades\Auth;

if (Auth::check()) {
    $authUser = Auth::user();
    echo "Auth user: {$authUser->username}\n";

    $result6 = Gate::allows('master-kode-nomor-view');
    echo "Gate::allows() with auth: " . ($result6 ? 'TRUE' : 'FALSE') . "\n";

    $result7 = $authUser->can('master-kode-nomor-view');
    echo "Auth user can(): " . ($result7 ? 'TRUE' : 'FALSE') . "\n";
} else {
    echo "No authenticated user\n";
}

echo "\n=== CONCLUSION ===\n";
if ($closureResult && !$result2) {
    echo "❌ Gate closure works but Gate::allows fails\n";
    echo "This suggests Gate definition or calling issue\n";
} elseif ($closureResult && $result2) {
    echo "✅ Everything works as expected\n";
} else {
    echo "❓ Unexpected results\n";
}
