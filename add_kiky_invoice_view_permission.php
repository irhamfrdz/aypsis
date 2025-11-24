<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\User;
use Illuminate\Database\Capsule\Manager as DB;

// Configurasi database  
$capsule = new DB;

$capsule->addConnection([
    'driver' => 'mysql',
    'host' => 'localhost',
    'database' => 'aypsis',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix' => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

echo "=== MEMBERIKAN PERMISSION TAGIHAN-KONTAINER-SEWA-VIEW KEPADA USER KIKY ===\n\n";

// Cari user kiky
$kiky = User::where('username', 'kiky')->first();

if (!$kiky) {
    echo "❌ User Kiky tidak ditemukan!\n";
    exit;
}

echo "✅ User ditemukan: {$kiky->username} (ID: {$kiky->id})\n\n";

// Cek apakah permission tagihan-kontainer-sewa-view sudah ada
$viewPermissionName = 'tagihan-kontainer-sewa-view';

// Coba cari permission di database
$existingPermission = DB::table('permissions')->where('name', $viewPermissionName)->first();

if (!$existingPermission) {
    echo "❌ Permission '{$viewPermissionName}' tidak ditemukan di database!\n";
    echo "Menambahkan permission baru...\n";
    
    // Insert permission baru
    $permissionId = DB::table('permissions')->insertGetId([
        'name' => $viewPermissionName,
        'description' => 'Melihat Tagihan Kontainer Sewa',
        'guard_name' => 'web',
        'created_at' => now(),
        'updated_at' => now()
    ]);
    
    echo "✅ Permission '{$viewPermissionName}' berhasil ditambahkan dengan ID: {$permissionId}\n";
} else {
    echo "✅ Permission '{$viewPermissionName}' sudah ada (ID: {$existingPermission->id})\n";
    $permissionId = $existingPermission->id;
}

// Cek apakah user sudah memiliki permission ini
$hasPermission = DB::table('model_has_permissions')
    ->where('model_type', 'App\\Models\\User')
    ->where('model_id', $kiky->id)
    ->where('permission_id', $permissionId)
    ->exists();

if ($hasPermission) {
    echo "✅ User Kiky sudah memiliki permission '{$viewPermissionName}'\n";
} else {
    echo "➕ Menambahkan permission '{$viewPermissionName}' kepada user Kiky...\n";
    
    // Berikan permission kepada user
    DB::table('model_has_permissions')->insert([
        'permission_id' => $permissionId,
        'model_type' => 'App\\Models\\User',
        'model_id' => $kiky->id
    ]);
    
    echo "✅ Permission '{$viewPermissionName}' berhasil diberikan kepada user Kiky!\n";
}

echo "\n=== VERIFIKASI PERMISSION ===\n";

// Verifikasi semua permission tagihan-kontainer-sewa yang dimiliki Kiky
$kikyPermissions = DB::table('model_has_permissions')
    ->join('permissions', 'model_has_permissions.permission_id', '=', 'permissions.id')
    ->where('model_has_permissions.model_type', 'App\\Models\\User')
    ->where('model_has_permissions.model_id', $kiky->id)
    ->where('permissions.name', 'like', '%tagihan-kontainer-sewa%')
    ->select('permissions.name', 'permissions.description')
    ->get();

echo "Permission tagihan-kontainer-sewa yang dimiliki user Kiky:\n";
foreach ($kikyPermissions as $perm) {
    echo "✅ {$perm->name} - {$perm->description}\n";
}

echo "\n🎉 Selesai! User Kiky seharusnya sudah bisa mengakses halaman show invoice tagihan sewa.\n";

function now() {
    return date('Y-m-d H:i:s');
}