<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/bootstrap/app.php';

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

// Simulasi login sebagai user yang akan menggunakan import
// Cek permission user untuk tagihan-kontainer-sewa

echo "Checking user permissions for import functionality...\n\n";

// Cek semua user dan permission mereka
$users = DB::table('users')->get();
echo "Available users:\n";
foreach($users as $user) {
    echo "- ID: {$user->id}, Username: {$user->username}, Name: {$user->name}\n";
}

echo "\n";

// Cek permission yang berhubungan dengan tagihan kontainer sewa
$permissions = DB::table('permissions')
    ->where('name', 'LIKE', '%tagihan-kontainer-sewa%')
    ->orWhere('name', 'LIKE', '%tagihan_kontainer_sewa%')
    ->get();

echo "Tagihan Kontainer Sewa related permissions:\n";
foreach($permissions as $permission) {
    echo "- {$permission->name}\n";
}

echo "\n";

// Cek user mana yang punya permission ini
$userPermissions = DB::table('user_permissions')
    ->join('permissions', 'user_permissions.permission_id', '=', 'permissions.id')
    ->join('users', 'user_permissions.user_id', '=', 'users.id')
    ->where('permissions.name', 'LIKE', '%tagihan-kontainer-sewa%')
    ->select('users.username', 'users.name', 'permissions.name as permission')
    ->get();

echo "Users with tagihan-kontainer-sewa permissions:\n";
foreach($userPermissions as $up) {
    echo "- User: {$up->username} ({$up->name}) - Permission: {$up->permission}\n";
}

// Khusus cek untuk admin
$adminPermissions = DB::table('user_permissions')
    ->join('permissions', 'user_permissions.permission_id', '=', 'permissions.id')
    ->join('users', 'user_permissions.user_id', '=', 'users.id')
    ->where('users.username', 'admin')
    ->where('permissions.name', 'LIKE', '%tagihan%')
    ->select('permissions.name as permission')
    ->get();

echo "\nAdmin user tagihan related permissions:\n";
foreach($adminPermissions as $perm) {
    echo "- {$perm->permission}\n";
}

echo "\nCheck completed!\n";
