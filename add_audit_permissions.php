<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== MEMBERIKAN AUDIT LOG PERMISSIONS KE ADMIN ===" . PHP_EOL;

// Cek audit log permissions yang ada
$auditLogPermissions = App\Models\Permission::where('name', 'LIKE', '%audit%')->get();

echo "Audit log permissions yang ditemukan:" . PHP_EOL;
foreach ($auditLogPermissions as $perm) {
    echo "  - {$perm->name} (ID: {$perm->id}) - {$perm->description}" . PHP_EOL;
}

// Jika tidak ada, buat audit log permissions
if ($auditLogPermissions->isEmpty()) {
    echo PHP_EOL . "⚠️  Tidak ada audit log permissions. Membuat permission baru..." . PHP_EOL;
    
    $newAuditPermissions = [
        [
            'name' => 'audit-log-view',
            'description' => 'Melihat log audit sistem'
        ],
        [
            'name' => 'audit-log-export',
            'description' => 'Export log audit sistem'
        ]
    ];
    
    foreach ($newAuditPermissions as $permData) {
        $permission = App\Models\Permission::create([
            'name' => $permData['name'],
            'description' => $permData['description'],
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        echo "✅ Created: {$permission->name} (ID: {$permission->id})" . PHP_EOL;
        $auditLogPermissions->push($permission);
    }
}

// Ambil admin user
$admin = App\Models\User::where('username', 'admin')->first();

if (!$admin) {
    echo "❌ Admin user tidak ditemukan!" . PHP_EOL;
    exit;
}

echo PHP_EOL . "Admin user ditemukan: {$admin->username} (ID: {$admin->id})" . PHP_EOL;

// Ambil permission IDs yang akan ditambahkan
$auditPermissionIds = $auditLogPermissions->pluck('id')->toArray();

// Ambil current permissions admin
$currentPermissionIds = $admin->permissions()->pluck('permission_id')->toArray();

// Gabungkan dengan permission baru
$newPermissionIds = array_unique(array_merge($currentPermissionIds, $auditPermissionIds));

// Sync permissions
$admin->permissions()->sync($newPermissionIds);

echo "✅ Audit log permissions berhasil ditambahkan ke admin!" . PHP_EOL;
echo "   - Permission baru ditambahkan: " . count($auditPermissionIds) . PHP_EOL;
echo "   - Total permissions admin sekarang: " . count($newPermissionIds) . PHP_EOL;

// Tampilkan audit permissions yang dimiliki admin
echo PHP_EOL . "Audit permissions yang dimiliki admin sekarang:" . PHP_EOL;
$adminAuditPerms = $admin->permissions()->where('name', 'LIKE', '%audit%')->get();
foreach ($adminAuditPerms as $perm) {
    echo "  ✓ {$perm->name} - {$perm->description}" . PHP_EOL;
}

echo PHP_EOL . "=== SELESAI ===" . PHP_EOL;