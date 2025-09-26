<?php<?php



require_once 'vendor/autoload.php';require_once 'vendor/autoload.php';



// Bootstrap Laraveluse Illuminate\Http\Request;

$app = require_once 'bootstrap/app.php';use App\Models\User;

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();use App\Models\Permission;

use App\Http\Controllers\UserController;

use App\Http\Controllers\UserController;

use App\Models\User;// Initialize Laravel

use Illuminate\Http\Request;$app = require_once 'bootstrap/app.php';

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== TESTING FORM SUBMISSION WITH UNCHECKED PEMBAYARAN-PRANAOTA-CAT ===\n\n";

echo "Testing form submission simulation\n";

// Get a test userecho "==================================\n\n";

$user = User::find(1);

if (!$user) {// Test data - user test4

    echo "❌ Test user not found\n";$user = User::where('username', 'test4')->first();

    exit(1);if (!$user) {

}    echo "❌ User test4 not found\n";

    exit(1);

echo "✅ Found test user: {$user->name} (ID: {$user->id})\n\n";}



// First, give the user pembayaran-pranota-cat permissionsecho "User: {$user->username} (ID: {$user->id})\n\n";

$user->permissions()->sync([1213, 1214, 1215, 1216, 1217, 1218]); // All pembayaran-pranota-cat permissions

echo "✅ Gave user all pembayaran-pranota-cat permissions\n\n";// Simulate form data that would be sent when user checks all tagihan-kontainer permissions

$formData = [

// Now simulate form submission WITHOUT pembayaran-pranota-cat in the permissions matrix    'username' => 'test4',

$formData = [    'karyawan_id' => $user->karyawan_id,

    'name' => $user->name,    'permissions' => [

    'username' => $user->username,        'tagihan-kontainer' => [

    'karyawan_id' => $user->karyawan_id,            'view' => '1',

    'permissions' => [            'create' => '1',

        // Intentionally NOT including pembayaran-pranota-cat            'update' => '1',

        'master-user' => [            'delete' => '1',

            'view' => '1',            'approve' => '1',

            'create' => '1'            'print' => '1',

        ]            'export' => '1'

    ]        ],

];        'master-pranota-tagihan-kontainer' => [

            'access' => '1'

$request = new Request();        ]

$request->merge($formData);    ]

];

// Create controller and call update

$controller = new UserController();echo "Simulated form data:\n";

$reflection = new ReflectionClass($controller);print_r($formData);

$updateMethod = $reflection->getMethod('update');echo "\n";

$updateMethod->setAccessible(true);

// Create a mock request

echo "🔄 Calling update method with form data that excludes pembayaran-pranota-cat...\n";$request = new Request();

$response = $updateMethod->invoke($controller, $request, $user);$request->merge($formData);

echo "✅ Update method executed\n\n";

// Test convertMatrixPermissionsToIds

// Check if user still has pembayaran-pranota-cat permissions$controller = new UserController();

$userPermissions = $user->permissions()->where('name', 'like', 'pembayaran-pranota-cat%')->get();$reflection = new ReflectionClass($controller);

echo "🔍 Checking user permissions after update:\n";$method = $reflection->getMethod('convertMatrixPermissionsToIds');

if ($userPermissions->count() > 0) {$method->setAccessible(true);

    echo "❌ User still has " . $userPermissions->count() . " pembayaran-pranota-cat permissions:\n";

    foreach ($userPermissions as $perm) {$permissionIds = $method->invoke($controller, $formData['permissions']);

        echo "  - {$perm->name}\n";

    }echo "Converted Permission IDs:\n";

    echo "\n❌ This means the permissions were NOT removed as expected!\n";foreach ($permissionIds as $id) {

} else {    $perm = Permission::find($id);

    echo "✅ User has no pembayaran-pranota-cat permissions (correctly removed)\n";    if ($perm) {

}        echo "  - ID {$id}: {$perm->name}\n";

    } else {

// Now test the opposite - form submission WITH pembayaran-pranota-cat checked        echo "  - ID {$id}: NOT FOUND\n";

echo "\n=== TESTING FORM SUBMISSION WITH CHECKED PEMBAYARAN-PRANAOTA-CAT ===\n\n";    }

}

$formData2 = [echo "\n";

    'name' => $user->name,

    'username' => $user->username,// Check if these are the expected permissions

    'karyawan_id' => $user->karyawan_id,$expectedPermissions = [

    'permissions' => [    'tagihan-kontainer-view',

        'master-user' => [    'tagihan-kontainer-create',

            'view' => '1',    'tagihan-kontainer-update',

            'create' => '1'    'tagihan-kontainer-delete',

        ],    'tagihan-kontainer-approve',

        'pembayaran-pranota-cat' => [    'tagihan-kontainer-print',

            'view' => '1',    'tagihan-kontainer-export',

            'create' => '1',    'master-pranota-tagihan-kontainer'

            'update' => '1',];

            'delete' => '1',

            'print' => '1',$actualPermissions = [];

            'export' => '1'foreach ($permissionIds as $id) {

        ]    $perm = Permission::find($id);

    ]    if ($perm) {

];        $actualPermissions[] = $perm->name;

    }

$request2 = new Request();}

$request2->merge($formData2);

echo "Expected permissions:\n";

echo "🔄 Calling update method with pembayaran-pranota-cat checked...\n";foreach ($expectedPermissions as $perm) {

$response2 = $updateMethod->invoke($controller, $request2, $user);    echo "  - $perm\n";

echo "✅ Update method executed\n\n";}



// Check if user now has pembayaran-pranota-cat permissionsecho "\nActual permissions from conversion:\n";

$userPermissions2 = $user->permissions()->where('name', 'like', 'pembayaran-pranota-cat%')->get();foreach ($actualPermissions as $perm) {

echo "🔍 Checking user permissions after update:\n";    echo "  - $perm\n";

if ($userPermissions2->count() > 0) {}

    echo "✅ User has " . $userPermissions2->count() . " pembayaran-pranota-cat permissions:\n";

    foreach ($userPermissions2 as $perm) {$missing = array_diff($expectedPermissions, $actualPermissions);

        echo "  - {$perm->name}\n";$extra = array_diff($actualPermissions, $expectedPermissions);

    }

} else {if (empty($missing) && empty($extra)) {

    echo "❌ User has no pembayaran-pranota-cat permissions\n";    echo "\n✅ All permissions converted correctly!\n";

}} else {
    if (!empty($missing)) {
        echo "\n❌ Missing permissions:\n";
        foreach ($missing as $perm) {
            echo "  - $perm\n";
        }
    }
    if (!empty($extra)) {
        echo "\n❌ Extra permissions:\n";
        foreach ($extra as $perm) {
            echo "  - $perm\n";
        }
    }
}

echo "\nTest completed!\n";
