<?php

require_once 'bootstrap/app.php';

$app = new Illuminate\Foundation\Application(
    $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
);

$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    App\Http\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::createFromGlobals()
);

// Now we can use Laravel
use App\Models\User;
use App\Models\Permission;
use App\Http\Controllers\UserController;

echo "=== DEBUG: Pranota Rit Kenek Permission Save Issue ===\n\n";

// 1. Check if pranota-rit-kenek permissions exist in database
echo "1. Checking pranota-rit-kenek permissions in database:\n";
$pranotaRitKenekPermissions = Permission::where('name', 'like', 'pranota-rit-kenek%')->orderBy('name')->get();

if ($pranotaRitKenekPermissions->count() > 0) {
    foreach ($pranotaRitKenekPermissions as $permission) {
        echo "   - ID: {$permission->id}, Name: {$permission->name}\n";
    }
} else {
    echo "   ERROR: No pranota-rit-kenek permissions found in database!\n";
}
echo "\n";

// 2. Test admin user current permissions
echo "2. Checking admin user current permissions:\n";
$adminUser = User::where('username', 'admin')->first();

if ($adminUser) {
    echo "   Admin user found (ID: {$adminUser->id})\n";
    
    $adminPermissions = $adminUser->permissions()->where('name', 'like', 'pranota-rit%')->orderBy('name')->get();
    echo "   Current pranota-rit related permissions:\n";
    
    if ($adminPermissions->count() > 0) {
        foreach ($adminPermissions as $permission) {
            echo "     - ID: {$permission->id}, Name: {$permission->name}\n";
        }
    } else {
        echo "     No pranota-rit related permissions found for admin\n";
    }
} else {
    echo "   ERROR: Admin user not found!\n";
}
echo "\n";

// 3. Test UserController matrix conversion
echo "3. Testing UserController matrix conversion:\n";

// Simulate form input for pranota-rit-kenek permissions
$testMatrixPermissions = [
    'pranota-rit-kenek' => [
        'view' => '1',
        'create' => '1',
        'update' => '1',
        'delete' => '1',
        'print' => '1',
        'export' => '1',
        'approve' => '1'
    ]
];

echo "   Simulating form input:\n";
foreach ($testMatrixPermissions['pranota-rit-kenek'] as $action => $value) {
    echo "     permissions[pranota-rit-kenek][{$action}] = {$value}\n";
}
echo "\n";

// Create UserController instance and test conversion
$userController = new UserController();

try {
    // Call the public test method we added to UserController
    $convertedIds = $userController->testConvertMatrixPermissionsToIds($testMatrixPermissions);
    
    echo "   Converted permission IDs:\n";
    if (!empty($convertedIds)) {
        foreach ($convertedIds as $id) {
            $permission = Permission::find($id);
            if ($permission) {
                echo "     - ID: {$id}, Name: {$permission->name}\n";
            } else {
                echo "     - ID: {$id}, Permission not found!\n";
            }
        }
    } else {
        echo "     ERROR: No permission IDs returned from conversion!\n";
    }
} catch (Exception $e) {
    echo "   ERROR during conversion: " . $e->getMessage() . "\n";
}
echo "\n";

// 4. Test permission lookup individually
echo "4. Testing individual permission lookups:\n";
$expectedPermissions = [
    'pranota-rit-kenek-view',
    'pranota-rit-kenek-create',
    'pranota-rit-kenek-edit',
    'pranota-rit-kenek-update',
    'pranota-rit-kenek-delete',
    'pranota-rit-kenek-print',
    'pranota-rit-kenek-export',
    'pranota-rit-kenek-approve'
];

foreach ($expectedPermissions as $permName) {
    $permission = Permission::where('name', $permName)->first();
    if ($permission) {
        echo "   ✓ Found: {$permName} (ID: {$permission->id})\n";
    } else {
        echo "   ✗ Missing: {$permName}\n";
    }
}
echo "\n";

// 5. Test the reverse: check how existing permissions are converted to matrix
echo "5. Testing reverse conversion (permissions to matrix):\n";
if ($adminUser) {
    $currentPermissionNames = $adminUser->permissions->pluck('name')->toArray();
    $matrixResult = $userController->testConvertPermissionsToMatrix($currentPermissionNames);
    
    if (isset($matrixResult['pranota-rit-kenek'])) {
        echo "   Current pranota-rit-kenek matrix state:\n";
        foreach ($matrixResult['pranota-rit-kenek'] as $action => $state) {
            $status = $state ? 'CHECKED' : 'UNCHECKED';
            echo "     {$action}: {$status}\n";
        }
    } else {
        echo "   No pranota-rit-kenek matrix found in current permissions\n";
    }
}

echo "\n=== DEBUG COMPLETE ===\n";