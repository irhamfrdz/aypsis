<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== VERIFIKASI USER ADMIN ===" . PHP_EOL;

// Cek user admin
$admin = App\Models\User::where('username', 'admin')->first();

if ($admin) {
    echo "✅ User admin ditemukan!" . PHP_EOL;
    echo "   - User ID: " . $admin->id . PHP_EOL;
    echo "   - Username: " . $admin->username . PHP_EOL;
    echo "   - Status: " . $admin->status . PHP_EOL;
    echo "   - Created: " . $admin->created_at . PHP_EOL;
    
    // Hitung total permissions
    $totalPerms = DB::table('user_permissions')->where('user_id', $admin->id)->count();
    echo "   - Total Permissions: " . $totalPerms . PHP_EOL;
    
    // Ambil beberapa permissions contoh
    $samplePerms = DB::table('user_permissions')
        ->join('permissions', 'user_permissions.permission_id', '=', 'permissions.id')
        ->where('user_permissions.user_id', $admin->id)
        ->select('permissions.name')
        ->limit(5)
        ->get();
        
    echo "   - Sample Permissions:" . PHP_EOL;
    foreach ($samplePerms as $perm) {
        echo "     * " . $perm->name . PHP_EOL;
    }
    
    echo PHP_EOL . "🎉 Admin user berhasil dibuat dengan " . $totalPerms . " permissions!" . PHP_EOL;
    echo "📝 Login dengan:" . PHP_EOL;
    echo "   Username: admin" . PHP_EOL;
    echo "   Password: admin123" . PHP_EOL;
    
} else {
    echo "❌ User admin tidak ditemukan!" . PHP_EOL;
}

echo PHP_EOL . "=== SELESAI ===" . PHP_EOL;