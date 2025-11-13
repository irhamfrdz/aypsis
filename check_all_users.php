<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "===========================================\n";
echo "CEK USER DAN PERMISSIONS\n";
echo "===========================================\n\n";

// Cek semua user
echo "Semua user di database:\n";
$users = DB::table('users')->get();

foreach ($users as $user) {
    echo "ID: {$user->id}\n";
    echo "  Username: {$user->username}\n";
    echo "  Role: {$user->role}\n";
    echo "  Role ID: {$user->role_id}\n";
    echo "  Status: {$user->status}\n";
    
    // Cek permissions user ini
    $userPerms = DB::table('permission_user')
        ->join('permissions', 'permission_user.permission_id', '=', 'permissions.id')
        ->where('permission_user.user_id', $user->id)
        ->where('permissions.name', 'LIKE', '%ob-bongkar%')
        ->select('permissions.name')
        ->get();
    
    if ($userPerms->count() > 0) {
        echo "  Permissions OB Bongkar:\n";
        foreach ($userPerms as $perm) {
            echo "    ✓ {$perm->name}\n";
        }
    } else {
        echo "  Permissions OB Bongkar: TIDAK ADA\n";
    }
    echo "\n";
}

echo "===========================================\n";
