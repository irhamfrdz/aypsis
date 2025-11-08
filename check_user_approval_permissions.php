<?php

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== CEK USER APPROVAL PERMISSIONS ===\n";
echo "Tanggal: " . date('Y-m-d H:i:s') . "\n\n";

// 1. Cek user admin
echo "1. Mencari user admin...\n";
$admin = App\Models\User::where('username', 'admin')->first();

if (!$admin) {
    echo "❌ User admin tidak ditemukan!\n";
    exit(1);
}

echo "✅ User admin ditemukan: {$admin->name} (ID: {$admin->id})\n\n";

// 2. Cek semua permission user-approval di database
echo "2. Semua permission user-approval yang ada di database:\n";
$allUserApprovalPerms = App\Models\Permission::where('name', 'like', '%user-approval%')
    ->orderBy('name')
    ->pluck('name');

if ($allUserApprovalPerms->isEmpty()) {
    echo "❌ Tidak ada permission user-approval di database!\n";
} else {
    foreach ($allUserApprovalPerms as $perm) {
        echo "   - {$perm}\n";
    }
}
echo "Total: " . $allUserApprovalPerms->count() . " permission\n\n";

// 3. Cek permission user-approval yang dimiliki admin
echo "3. Permission user-approval yang dimiliki user admin:\n";
$adminUserApprovalPerms = $admin->permissions()
    ->where(function($query) {
        $query->where('name', 'like', '%user-approval%')
              ->orWhere('name', 'master-user');
    })
    ->orderBy('name')
    ->pluck('name');

if ($adminUserApprovalPerms->isEmpty()) {
    echo "❌ User admin tidak memiliki permission user-approval!\n";
} else {
    foreach ($adminUserApprovalPerms as $perm) {
        echo "   ✅ {$perm}\n";
    }
}
echo "Total: " . $adminUserApprovalPerms->count() . " permission\n\n";

// 4. Test permission check dengan method yang sama seperti controller
echo "4. Test akses permission seperti di UserApprovalController:\n";
$userPermissions = $admin->permissions->pluck('name')->toArray();

// Method check dari controller
$hasAccess = !empty(array_intersect($userPermissions, [
    'master-user',
    'user-approval', 
    'user-approval.view'
]));

echo "Permission yang dicek: ['master-user', 'user-approval', 'user-approval.view']\n";
echo "Has Access: " . ($hasAccess ? "✅ YA" : "❌ TIDAK") . "\n\n";

// 5. Cek permission spesifik satu per satu
echo "5. Cek permission spesifik:\n";
$permissionsToCheck = [
    'master-user',
    'user-approval',
    'user-approval.view',
    'user-approval-view',
    'user-approval-create',
    'user-approval-update', 
    'user-approval-delete',
    'user-approval-approve',
    'user-approval-reject'
];

foreach ($permissionsToCheck as $perm) {
    $hasPermission = $admin->hasPermissionTo($perm);
    $status = $hasPermission ? "✅" : "❌";
    echo "   {$status} {$perm}\n";
}

echo "\n=== SCRIPT SELESAI ===\n";