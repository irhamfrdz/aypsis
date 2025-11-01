<?php
require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Config;

// Bootstrap Laravel application
$app = new Application(getcwd());
$app->singleton(\Illuminate\Contracts\Http\Kernel::class, \App\Http\Kernel::class);
$app->singleton(\Illuminate\Contracts\Console\Kernel::class, \App\Console\Kernel::class);
$app->singleton(\Illuminate\Contracts\Debug\ExceptionHandler::class, \App\Exceptions\Handler::class);
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== MEMPERBAIKI PERMISSION APPROVAL SURAT JALAN ===\n\n";

try {
    // Cek permission yang digunakan di route
    $routePermission = 'surat-jalan-approval-dashboard';
    $routePerm = App\Models\Permission::where('name', $routePermission)->first();
    
    echo "1. MENGECEK PERMISSION DI ROUTE:\n";
    if ($routePerm) {
        echo "   ✅ Permission '{$routePermission}' ditemukan (ID: {$routePerm->id})\n";
    } else {
        echo "   ❌ Permission '{$routePermission}' TIDAK DITEMUKAN!\n";
        echo "   💡 Route menggunakan: middleware(['auth', 'can:{$routePermission}'])\n";
    }
    
    // Cek permission yang kita buat
    echo "\n2. MENGECEK PERMISSION YANG KITA BUAT:\n";
    $ourPermissions = [
        'approval-surat-jalan-view',
        'approval-surat-jalan-approve',
        'approval-surat-jalan-reject',
        'approval-surat-jalan-print',
        'approval-surat-jalan-export'
    ];
    
    foreach ($ourPermissions as $permName) {
        $perm = App\Models\Permission::where('name', $permName)->first();
        if ($perm) {
            echo "   ✅ {$permName} (ID: {$perm->id})\n";
        } else {
            echo "   ❌ {$permName} - TIDAK DITEMUKAN!\n";
        }
    }
    
    echo "\n3. SOLUSI:\n";
    echo "   🔧 Opsi 1: Buat permission '{$routePermission}' dan berikan ke user\n";
    echo "   🔧 Opsi 2: Ubah route middleware untuk menggunakan 'approval-surat-jalan-view'\n\n";
    
    echo "4. MENJALANKAN SOLUSI OPSI 1 - MEMBUAT PERMISSION YANG DIBUTUHKAN:\n";
    
    if (!$routePerm) {
        // Buat permission yang dibutuhkan route
        $newPerm = App\Models\Permission::create([
            'name' => $routePermission,
            'guard_name' => 'web',
            'description' => 'Access to Surat Jalan Approval Dashboard'
        ]);
        
        echo "   ✅ Permission '{$routePermission}' berhasil dibuat (ID: {$newPerm->id})\n";
        
        // Berikan permission ini ke admin
        $adminUser = App\Models\User::where('username', 'admin')->first();
        if ($adminUser) {
            $adminUser->givePermissionTo($routePermission);
            echo "   ✅ Permission diberikan ke user 'admin'\n";
        } else {
            echo "   ⚠️ User 'admin' tidak ditemukan\n";
        }
    } else {
        echo "   ℹ️ Permission sudah ada, tidak perlu dibuat\n";
    }
    
    echo "\n5. ALTERNATIF: MEMBERIKAN PERMISSION KE USER LAIN:\n";
    echo "   💡 Untuk memberikan akses ke user lain, jalankan:\n";
    echo "   💡 \$user = App\\Models\\User::find([USER_ID]);\n";
    echo "   💡 \$user->givePermissionTo('{$routePermission}');\n\n";
    
    echo "6. CEK HASIL:\n";
    $usersWithRoutePermission = App\Models\User::permission($routePermission)->get();
    if ($usersWithRoutePermission->count() > 0) {
        echo "   ✅ Users yang memiliki permission '{$routePermission}':\n";
        foreach ($usersWithRoutePermission as $user) {
            echo "      - {$user->username} (ID: {$user->id})\n";
        }
    } else {
        echo "   ❌ Belum ada user yang memiliki permission '{$routePermission}'\n";
    }
    
    echo "\n✅ PERBAIKAN SELESAI!\n";
    echo "✅ Sekarang user dengan permission '{$routePermission}' dapat mengakses /approval/surat-jalan\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}