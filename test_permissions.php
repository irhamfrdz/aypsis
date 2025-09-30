<?php<?php<?php



require_once 'vendor/autoload.php';



$app = require_once 'bootstrap/app.php';require_once 'vendor/autoload.php';require_once 'vendor/autoload.php';

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();



use App\Models\User;

$app = require_once 'bootstrap/app.php';use App\Models\Permission;

// Force fresh instance

$user = User::with('permissions')->find(30);$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();use Illuminate\Foundation\Application;



if (!$user) {

    echo "❌ User not found\n";

    exit;use App\Models\User;// Bootstrap Laravel

}

$app = require_once 'bootstrap/app.php';

echo "User: {$user->username}\n";

echo "Permissions loaded: " . $user->permissions->count() . "\n";// Force fresh instance$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);



$nomorTerakhirPerms = $user->permissions->filter(function($perm) {$user = User::with('permissions')->find(30);$kernel->bootstrap();

    return str_contains($perm->name, 'master-nomor-terakhir');

});



echo "\nNomor terakhir permissions:\n";if (!$user) {echo "Testing Permission Database\n";

if ($nomorTerakhirPerms->count() > 0) {

    foreach ($nomorTerakhirPerms as $perm) {    echo "❌ User not found\n";echo "==========================\n";

        echo "✅ {$perm->name}\n";

    }    exit;

} else {

    echo "❌ No nomor terakhir permissions found\n";}// Test basic permission lookup

}

$testNames = ['master-karyawan', 'master-kontainer', 'master-tujuan', 'master-kegiatan', 'master-permission', 'master-mobil', 'master-divisi', 'master-pajak', 'master-pekerjaan'];

// Test can() method

echo "\nTesting can() method:\n";echo "User: {$user->username}\n";

$testPerms = ['master-nomor-terakhir-view', 'master-nomor-terakhir-create'];

foreach ($testPerms as $perm) {echo "Permissions loaded: " . $user->permissions->count() . "\n";echo "Looking for permissions: " . implode(', ', $testNames) . "\n\n";

    $can = $user->can($perm);

    echo ($can ? "✅" : "❌") . " can('{$perm}') = " . ($can ? 'true' : 'false') . "\n";

}
$nomorTerakhirPerms = $user->permissions->filter(function($perm) {foreach ($testNames as $name) {

    return str_contains($perm->name, 'master-nomor-terakhir');    $permission = Permission::where('name', $name)->first();

});    if ($permission) {

        echo "✓ Found: $name (ID: {$permission->id})\n";

echo "\nNomor terakhir permissions:\n";    } else {

if ($nomorTerakhirPerms->count() > 0) {        echo "✗ Not found: $name\n";

    foreach ($nomorTerakhirPerms as $perm) {    }

        echo "✅ {$perm->name}\n";}

    }

} else {echo "\nChecking karyawan permissions:\n";

    echo "❌ No nomor terakhir permissions found\n";$karyawanPerms = Permission::where('name', 'like', '%karyawan%')->get();

}foreach ($karyawanPerms as $perm) {

    echo "  {$perm->id}: {$perm->name}\n";

// Test can() method}

echo "\nTesting can() method:\n";

$testPerms = ['master-nomor-terakhir-view', 'master-nomor-terakhir-create'];echo "\nTotal permissions in database: " . Permission::count() . "\n";

foreach ($testPerms as $perm) {

    $can = $user->can($perm);// Show some sample permissions

    echo ($can ? "✅" : "❌") . " can('{$perm}') = " . ($can ? 'true' : 'false') . "\n";echo "\nSample permissions from database:\n";

}$samplePermissions = Permission::take(10)->get();
foreach ($samplePermissions as $perm) {
    echo "  {$perm->id}: {$perm->name}\n";
}
