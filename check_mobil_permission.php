<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Spatie\Permission\Models\Permission;
use App\Models\User;

echo "=== CEK PERMISSION MASTER MOBIL ===\n\n";

// 1. Cek permission yang ada
echo "1. PERMISSION MOBIL DI DATABASE:\n";
$mobilePerms = Permission::where('name', 'like', '%mobil%')->get();
foreach ($mobilePerms as $perm) {
    echo "   - {$perm->name} (ID: {$perm->id})\n";
}

echo "\n2. USER YANG LOGIN SAAT INI:\n";
if (auth()->check()) {
    $user = auth()->user();
    echo "   Username: {$user->username}\n";
    echo "   ID: {$user->id}\n";
    
    echo "\n3. PERMISSION MASTER-MOBIL YANG DIMILIKI USER INI:\n";
    $userMobilPerms = $user->permissions()->where('name', 'like', '%mobil%')->get();
    if ($userMobilPerms->count() > 0) {
        foreach ($userMobilPerms as $perm) {
            echo "   ✓ {$perm->name}\n";
        }
    } else {
        echo "   ✗ TIDAK ADA PERMISSION MOBIL!\n";
    }
    
    echo "\n4. CEK PERMISSION 'master-mobil-view':\n";
    if ($user->hasPermissionTo('master-mobil-view')) {
        echo "   ✓ User MEMILIKI permission 'master-mobil-view'\n";
    } else {
        echo "   ✗ User TIDAK MEMILIKI permission 'master-mobil-view'\n";
    }
    
    echo "\n5. SEMUA PERMISSION USER:\n";
    $allUserPerms = $user->permissions;
    echo "   Total: {$allUserPerms->count()} permissions\n";
    
} else {
    echo "   ✗ Tidak ada user yang login\n";
}

echo "\n=== SELESAI ===\n";
