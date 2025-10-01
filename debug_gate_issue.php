<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Permission;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

echo "=== DEBUG GATE DEFINITION MASALAH ===\n";

$admin = User::where('username', 'admin')->first();
if (!$admin) {
    echo "❌ Admin user not found\n";
    exit(1);
}

// Login as admin for Gate context
Auth::login($admin);

echo "✅ Admin user logged in: " . $admin->username . "\n";

// Check permission existence
$viewBankPerm = Permission::where('name', 'master-bank-view')->first();
if (!$viewBankPerm) {
    echo "❌ master-bank-view permission not found in database\n";
    exit(1);
}

echo "✅ Permission exists: " . $viewBankPerm->name . " (ID: " . $viewBankPerm->id . ")\n";

// Check user has permission
$hasPermission = $admin->permissions->contains('id', $viewBankPerm->id);
echo "User has permission: " . ($hasPermission ? 'YES' : 'NO') . "\n";

if (!$hasPermission) {
    echo "❌ Admin doesn't have the permission, exiting\n";
    exit(1);
}

echo "\n=== GATE DEFINITION CHECK ===\n";

// Check if Gate is defined
$gateDefined = Gate::has('master-bank-view');
echo "Gate defined: " . ($gateDefined ? 'YES' : 'NO') . "\n";

if ($gateDefined) {
    // Get the actual gate callback
    $gates = Gate::abilities();
    $gateCallback = $gates['master-bank-view'] ?? null;

    if ($gateCallback) {
        echo "Gate callback exists\n";

        // Test the callback directly
        try {
            $callbackResult = $gateCallback($admin);
            echo "Direct callback result: " . ($callbackResult ? 'TRUE' : 'FALSE') . "\n";
        } catch (Exception $e) {
            echo "Callback error: " . $e->getMessage() . "\n";
        }

        // Test various Gate methods
        echo "\n=== GATE METHOD TESTS ===\n";

        // 1. Gate::allows without user param (should use Auth::user())
        $allows1 = Gate::allows('master-bank-view');
        echo "Gate::allows('master-bank-view'): " . ($allows1 ? 'TRUE' : 'FALSE') . "\n";

        // 2. Gate::allows with user param
        $allows2 = Gate::allows('master-bank-view', $admin);
        echo "Gate::allows('master-bank-view', \$admin): " . ($allows2 ? 'TRUE' : 'FALSE') . "\n";

        // 3. Gate::check without user param
        $check1 = Gate::check('master-bank-view');
        echo "Gate::check('master-bank-view'): " . ($check1 ? 'TRUE' : 'FALSE') . "\n";

        // 4. Gate::check with user param
        $check2 = Gate::check('master-bank-view', $admin);
        echo "Gate::check('master-bank-view', \$admin): " . ($check2 ? 'TRUE' : 'FALSE') . "\n";

        // 5. User->can method
        $userCan = $admin->can('master-bank-view');
        echo "\$admin->can('master-bank-view'): " . ($userCan ? 'TRUE' : 'FALSE') . "\n";

        // 6. Check Auth::user() context
        $authUser = Auth::user();
        if ($authUser) {
            echo "\nAuth::user() ID: " . $authUser->id . "\n";
            echo "Same as admin: " . ($authUser->id === $admin->id ? 'YES' : 'NO') . "\n";
        } else {
            echo "❌ Auth::user() is null\n";
        }

    } else {
        echo "❌ Gate callback not found\n";
    }
} else {
    echo "❌ Gate not defined\n";

    // Check AppServiceProvider boot method
    echo "\n=== CHECKING APPSERVICEPROVIDER ===\n";

    // Try to manually define the gate like AppServiceProvider should do
    try {
        Gate::define('master-bank-view', function (User $user) use ($viewBankPerm) {
            return $user->permissions()->where('name', $viewBankPerm->name)->exists();
        });

        echo "✅ Gate manually defined\n";

        $testResult = Gate::allows('master-bank-view');
        echo "Gate test after manual definition: " . ($testResult ? 'TRUE' : 'FALSE') . "\n";

    } catch (Exception $e) {
        echo "❌ Error manually defining gate: " . $e->getMessage() . "\n";
    }
}

echo "\n=== PERMISSION RELATIONSHIP CHECK ===\n";

// Check the actual database relationship
$permissionCheck = $admin->permissions()->where('name', 'master-bank-view')->exists();
echo "DB relationship check: " . ($permissionCheck ? 'EXISTS' : 'NOT EXISTS') . "\n";

// Check pivot table directly
try {
    $pivotExists = \Illuminate\Support\Facades\DB::table('user_permissions')
        ->where('user_id', $admin->id)
        ->where('permission_id', $viewBankPerm->id)
        ->exists();
    echo "Pivot table check: " . ($pivotExists ? 'EXISTS' : 'NOT EXISTS') . "\n";
} catch (Exception $e) {
    echo "Pivot check error: " . $e->getMessage() . "\n";
}
