<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== MENAMBAHKAN 5 PERMISSION YANG HILANG ===" . PHP_EOL;

// Permission yang hilang dari database
$missingPermissions = [
    'approval-tugas-1.view' => 'Melihat approval tugas level 1',
    'master-kapal.create' => 'Membuat data master kapal',
    'master-kapal.delete' => 'Menghapus data master kapal',
    'master-kapal.edit' => 'Mengedit data master kapal',
    'master-kapal.view' => 'Melihat data master kapal'
];

$addedCount = 0;

foreach ($missingPermissions as $name => $description) {
    $existing = App\Models\Permission::where('name', $name)->first();
    
    if (!$existing) {
        $permission = App\Models\Permission::create([
            'name' => $name,
            'description' => $description,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        echo "✅ Added: {$permission->name} (ID: {$permission->id})" . PHP_EOL;
        $addedCount++;
    } else {
        echo "⚠️  Already exists: {$name} (ID: {$existing->id})" . PHP_EOL;
    }
}

echo PHP_EOL . "Total new permissions added: {$addedCount}" . PHP_EOL;

// Berikan permission ke admin user
if ($addedCount > 0) {
    echo PHP_EOL . "=== MEMBERIKAN PERMISSION BARU KE ADMIN ===" . PHP_EOL;

    $admin = App\Models\User::where('username', 'admin')->first();
    if ($admin) {
        $newPermissionIds = App\Models\Permission::whereIn('name', array_keys($missingPermissions))->pluck('id')->toArray();
        
        // Sync tanpa menghapus permission yang sudah ada
        $currentPerms = $admin->permissions()->pluck('permission_id')->toArray();
        $newPerms = array_unique(array_merge($currentPerms, $newPermissionIds));
        
        $admin->permissions()->sync($newPerms);
        
        echo "✅ Permission baru telah diberikan ke admin user" . PHP_EOL;
        echo "   - Total permissions admin sekarang: " . count($newPerms) . PHP_EOL;
    }
}

echo PHP_EOL . "=== SELESAI ===" . PHP_EOL;