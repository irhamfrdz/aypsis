<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== CEK & TAMBAH PERMISSION BL (BILL OF LADING) ===" . PHP_EOL;

// Cek permission BL yang sudah ada
$blPermissions = App\Models\Permission::where('name', 'LIKE', '%bl%')->get();

echo "Permission BL yang sudah ada:" . PHP_EOL;
foreach ($blPermissions as $perm) {
    echo "  - {$perm->name} (ID: {$perm->id}) - {$perm->description}" . PHP_EOL;
}

// Permission BL yang diperlukan (hanya yang belum ada)
$requiredBlPermissions = [
    [
        'name' => 'bl-update',
        'description' => 'Memperbarui Bill of Lading'
    ],
    [
        'name' => 'bl-delete',
        'description' => 'Menghapus Bill of Lading'
    ],
    [
        'name' => 'bl-print',
        'description' => 'Mencetak Bill of Lading'
    ],
    [
        'name' => 'bl-export',
        'description' => 'Export Bill of Lading'
    ],
    [
        'name' => 'bl-approve',
        'description' => 'Menyetujui Bill of Lading'
    ]
];

$addedCount = 0;

echo PHP_EOL . "Menambahkan permission BL yang belum ada:" . PHP_EOL;

foreach ($requiredBlPermissions as $permData) {
    $existing = App\Models\Permission::where('name', $permData['name'])->first();
    
    if (!$existing) {
        $permission = App\Models\Permission::create([
            'name' => $permData['name'],
            'description' => $permData['description'],
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        echo "✅ Added: {$permission->name} (ID: {$permission->id})" . PHP_EOL;
        $addedCount++;
    } else {
        echo "⚠️  Already exists: {$permData['name']} (ID: {$existing->id})" . PHP_EOL;
    }
}

echo PHP_EOL . "Total permission BL baru ditambahkan: {$addedCount}" . PHP_EOL;

// Berikan permission ke admin user
if ($addedCount > 0) {
    echo PHP_EOL . "=== MEMBERIKAN BL PERMISSIONS KE ADMIN ===" . PHP_EOL;

    $admin = App\Models\User::where('username', 'admin')->first();
    if ($admin) {
        $blPermissionIds = App\Models\Permission::whereIn('name', array_column($requiredBlPermissions, 'name'))->pluck('id')->toArray();
        
        // Sync tanpa menghapus permission yang sudah ada
        $currentPerms = $admin->permissions()->pluck('permission_id')->toArray();
        $newPerms = array_unique(array_merge($currentPerms, $blPermissionIds));
        
        $admin->permissions()->sync($newPerms);
        
        echo "✅ BL permissions telah diberikan ke admin user" . PHP_EOL;
        echo "   - Total permissions admin sekarang: " . count($newPerms) . PHP_EOL;
    }
}

echo PHP_EOL . "=== SELESAI ===" . PHP_EOL;