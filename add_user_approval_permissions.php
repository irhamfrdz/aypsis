<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== MENAMBAHKAN PERMISSION PERSETUJUAN USER ===" . PHP_EOL;

// Permission yang diperlukan untuk persetujuan user
$userApprovalPermissions = [
    [
        'name' => 'user-approval',
        'description' => 'Melihat daftar user yang menunggu persetujuan'
    ],
    [
        'name' => 'user-approval-view',
        'description' => 'Melihat detail permohonan user'
    ],
    [
        'name' => 'user-approval-approve',
        'description' => 'Menyetujui permohonan user baru'
    ],
    [
        'name' => 'user-approval-reject',
        'description' => 'Menolak permohonan user baru'
    ],
    [
        'name' => 'user-approval-edit',
        'description' => 'Edit status permohonan user'
    ],
    [
        'name' => 'user-approval-history',
        'description' => 'Melihat riwayat persetujuan user'
    ],
    [
        'name' => 'master-user-approve',
        'description' => 'Menyetujui user dalam master user'
    ],
    [
        'name' => 'master-user-suspend',
        'description' => 'Menangguhkan user dalam master user'
    ],
    [
        'name' => 'master-user-activate',
        'description' => 'Mengaktifkan user dalam master user'
    ]
];

$addedCount = 0;

foreach ($userApprovalPermissions as $permData) {
    $existing = App\Models\Permission::where('name', $permData['name'])->first();
    
    if (!$existing) {
        $permission = App\Models\Permission::create([
            'name' => $permData['name'],
            'description' => $permData['description'],
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        echo "✅ Added: {$permission->name} (ID: {$permission->id}) - {$permission->description}" . PHP_EOL;
        $addedCount++;
    } else {
        echo "⚠️  Already exists: {$permData['name']} (ID: {$existing->id})" . PHP_EOL;
    }
}

echo PHP_EOL . "Total new permissions added: {$addedCount}" . PHP_EOL;

// Berikan permission ke admin user
echo PHP_EOL . "=== MEMBERIKAN USER APPROVAL PERMISSIONS KE ADMIN ===" . PHP_EOL;

$admin = App\Models\User::where('username', 'admin')->first();
if ($admin) {
    $userApprovalPermIds = App\Models\Permission::whereIn('name', array_column($userApprovalPermissions, 'name'))->pluck('id')->toArray();
    
    // Sync tanpa menghapus permission yang sudah ada
    $currentPerms = $admin->permissions()->pluck('permission_id')->toArray();
    $newPerms = array_unique(array_merge($currentPerms, $userApprovalPermIds));
    
    $admin->permissions()->sync($newPerms);
    
    echo "✅ User approval permissions telah diberikan ke admin user" . PHP_EOL;
    echo "   - Permission baru ditambahkan: " . count($userApprovalPermIds) . PHP_EOL;
    echo "   - Total permissions admin sekarang: " . count($newPerms) . PHP_EOL;
    
    // Tampilkan user approval permissions yang dimiliki admin
    echo PHP_EOL . "User approval permissions yang dimiliki admin sekarang:" . PHP_EOL;
    $adminUserApprovalPerms = $admin->permissions()
        ->where(function($query) {
            $query->where('name', 'LIKE', '%user-approval%')
                  ->orWhere('name', 'LIKE', '%master-user-approve%')
                  ->orWhere('name', 'LIKE', '%master-user-suspend%')
                  ->orWhere('name', 'LIKE', '%master-user-activate%');
        })->get();
    
    foreach ($adminUserApprovalPerms as $perm) {
        echo "  ✓ {$perm->name} - {$perm->description}" . PHP_EOL;
    }
} else {
    echo "❌ Admin user tidak ditemukan!" . PHP_EOL;
}

echo PHP_EOL . "=== SELESAI ===" . PHP_EOL;