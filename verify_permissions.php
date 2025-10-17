<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Verifikasi Permission Surat Jalan Approval ===\n";

// Cek permissions
echo "\nPermissions yang telah dibuat:\n";
$permissions = DB::table('permissions')
    ->where('name', 'like', 'surat-jalan-approval%')
    ->get(['name', 'description']);

if ($permissions->count() > 0) {
    foreach ($permissions as $permission) {
        echo "✅ {$permission->name}: {$permission->description}\n";
    }
} else {
    echo "❌ Tidak ada permission surat jalan approval yang ditemukan\n";
}

// Cek user admin permissions
echo "\nPermissions yang diberikan ke user admin:\n";
$adminPermissions = DB::table('user_permissions')
    ->join('permissions', 'user_permissions.permission_id', '=', 'permissions.id')
    ->join('users', 'user_permissions.user_id', '=', 'users.id')
    ->where('users.username', 'admin')
    ->where('permissions.name', 'like', 'surat-jalan-approval%')
    ->get(['permissions.name']);

if ($adminPermissions->count() > 0) {
    foreach ($adminPermissions as $permission) {
        echo "✅ {$permission->name}\n";
    }
} else {
    echo "❌ User admin belum memiliki permission surat jalan approval\n";
}

echo "\nTotal: " . $permissions->count() . " permissions untuk surat jalan approval\n";
echo "=== Verifikasi Selesai ===\n";
