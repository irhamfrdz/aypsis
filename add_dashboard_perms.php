<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== MENAMBAHKAN DASHBOARD PERMISSIONS ===" . PHP_EOL;

// Permission yang perlu ditambahkan untuk dashboard
$dashboardPermissions = [
    [
        'name' => 'dashboard',
        'description' => 'Akses halaman dashboard utama sistem'
    ],
    [
        'name' => 'dashboard-view',
        'description' => 'Melihat halaman dashboard'
    ]
];

$addedCount = 0;

foreach ($dashboardPermissions as $permData) {
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

echo PHP_EOL . "Total permissions added: {$addedCount}" . PHP_EOL;

// Berikan permission dashboard ke admin user
echo PHP_EOL . "=== MEMBERIKAN DASHBOARD PERMISSIONS KE ADMIN ===" . PHP_EOL;

$admin = App\Models\User::where('username', 'admin')->first();
if ($admin) {
    $dashboardPerms = App\Models\Permission::whereIn('name', ['dashboard', 'dashboard-view'])->pluck('id')->toArray();
    
    // Sync tanpa menghapus permission yang sudah ada
    $currentPerms = $admin->permissions()->pluck('permission_id')->toArray();
    $newPerms = array_unique(array_merge($currentPerms, $dashboardPerms));
    
    $admin->permissions()->sync($newPerms);
    
    echo "✅ Dashboard permissions telah diberikan ke admin user" . PHP_EOL;
    echo "   - Total permissions admin sekarang: " . count($newPerms) . PHP_EOL;
} else {
    echo "❌ Admin user tidak ditemukan!" . PHP_EOL;
}

echo PHP_EOL . "=== SELESAI ===" . PHP_EOL;