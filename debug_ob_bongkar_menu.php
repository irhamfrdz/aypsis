<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "===========================================\n";
echo "DEBUG MENU OB BONGKAR\n";
echo "===========================================\n\n";

// 1. Cek tabel users
echo "1. Struktur tabel users:\n";
$userColumns = DB::select("SHOW COLUMNS FROM users");
echo "Kolom yang ada:\n";
foreach ($userColumns as $col) {
    echo "  - {$col->Field}\n";
}
echo "\n";

// 2. Cek semua user
echo "2. Daftar semua user:\n";
$users = DB::table('users')->get();
foreach ($users as $user) {
    echo "  - ID: {$user->id}, Username: {$user->username}, Email: {$user->email}\n";
}
echo "\n";

// 3. Cek role admin
$adminRole = DB::table('roles')->where('name', 'admin')->first();
echo "3. Role Admin:\n";
if ($adminRole) {
    echo "  ID: {$adminRole->id}, Name: {$adminRole->name}\n\n";
    
    // 4. Cek user yang punya role admin
    echo "4. User dengan role admin:\n";
    $adminUsers = DB::table('role_user')
        ->where('role_id', $adminRole->id)
        ->get();
    
    foreach ($adminUsers as $roleUser) {
        $user = DB::table('users')->where('id', $roleUser->user_id)->first();
        if ($user) {
            echo "  - User ID: {$user->id}, Username: {$user->username}\n";
            
            // Cek permissions
            $permissions = DB::table('permissions')
                ->join('permission_role', 'permissions.id', '=', 'permission_role.permission_id')
                ->where('permission_role.role_id', $adminRole->id)
                ->where('permissions.name', 'LIKE', '%ob-bongkar%')
                ->select('permissions.name')
                ->get();
            
            echo "    Permissions OB Bongkar:\n";
            if ($permissions->count() > 0) {
                foreach ($permissions as $perm) {
                    echo "      ✓ {$perm->name}\n";
                }
            } else {
                echo "      ❌ TIDAK ADA PERMISSION OB BONGKAR!\n";
            }
        }
    }
} else {
    echo "  ❌ Role admin tidak ditemukan!\n";
}

echo "\n";

// 5. Cek semua permissions ob-bongkar
echo "5. Semua permissions OB Bongkar:\n";
$obPerms = DB::table('permissions')
    ->where('name', 'LIKE', '%ob-bongkar%')
    ->get();

foreach ($obPerms as $perm) {
    echo "  - ID: {$perm->id}, Name: {$perm->name}\n";
    
    // Cek apakah di-assign ke admin role
    $assigned = DB::table('permission_role')
        ->where('permission_id', $perm->id)
        ->where('role_id', $adminRole->id)
        ->exists();
    
    echo "    Assigned to admin: " . ($assigned ? "✓ YES" : "❌ NO") . "\n";
}

echo "\n===========================================\n";
