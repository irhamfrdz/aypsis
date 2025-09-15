<?php

require_once 'vendor/autoload.php';

use Illuminate\Http\Request;
use App\Models\Permission;

// Initialize Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Permission lama yang masih digunakan oleh user:\n";
echo "==============================================\n\n";

$oldPermissions = Permission::where('name', 'like', 'master.%')->with('users')->get();
foreach($oldPermissions as $perm) {
    $userCount = $perm->users->count();
    if($userCount > 0) {
        echo "⚠️  {$perm->name} (ID: {$perm->id}) - Digunakan oleh {$userCount} user\n";

        // Tampilkan username user yang menggunakannya
        foreach($perm->users as $user) {
            echo "    - {$user->username}\n";
        }
        echo "\n";
    }
}

echo "Permission baru yang digunakan oleh user:\n";
echo "=========================================\n\n";

$newPermissions = Permission::where('name', 'like', 'master-%')->with('users')->get();
foreach($newPermissions as $perm) {
    $userCount = $perm->users->count();
    if($userCount > 0) {
        echo "✅ {$perm->name} (ID: {$perm->id}) - Digunakan oleh {$userCount} user\n";

        // Tampilkan username user yang menggunakannya
        foreach($perm->users as $user) {
            echo "    - {$user->username}\n";
        }
        echo "\n";
    }
}
