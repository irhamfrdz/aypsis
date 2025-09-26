<?php



require_once 'vendor/autoload.php';require_once 'vendor/autoload.php';



$app = require_once 'bootstrap/app.php';$app = require_once 'bootstrap/app.php';

$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();$app->boot();



use App\Models\Permission;

use App\Models\User;

echo "Checking master-tipe-akun permissions after seeder...\n";

echo "=== Available Permissions ===\n";

$permissions = Permission::where('name', 'like', 'master-tipe-akun%')->get();$permissions = Permission::pluck('name')->toArray();

foreach($permissions as $permission) {

echo "Found: " . $permissions->count() . " permissions\n";    echo "- " . $permission . "\n";

}

if ($permissions->count() > 0) {

    foreach ($permissions as $permission) {echo "\n=== Current User Permissions ===\n";

        echo "- " . $permission->name . " (ID: " . $permission->id . ")\n";$user = User::find(1); // Assuming user ID 1 is admin

    }if($user) {

} else {    echo "User: " . $user->name . "\n";

    echo "No permissions found!\n";    echo "Roles: " . $user->roles->pluck('name')->implode(', ') . "\n";

}    echo "Permissions: " . $user->getAllPermissions()->pluck('name')->implode(', ') . "\n";



echo "Done!\n";    echo "\n=== Checking specific permissions ===\n";
    echo "master-pranota: " . ($user->can('master-pranota') ? 'YES' : 'NO') . "\n";
    echo "master-pranota-tagihan-kontainer: " . ($user->can('master-pranota-tagihan-kontainer') ? 'YES' : 'NO') . "\n";
}
