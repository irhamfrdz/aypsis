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

echo "=== DIAGNOSIS: AKSES DITOLAK APPROVAL SURAT JALAN ===\n\n";

try {
    // Ambil semua user untuk cek siapa yang punya permission approval surat jalan
    $users = App\Models\User::all();
    
    echo "1. MENGECEK USER YANG MEMILIKI PERMISSION APPROVAL SURAT JALAN:\n";
    
    $approvalSJPermissions = [
        'approval-surat-jalan-view',
        'approval-surat-jalan-approve', 
        'approval-surat-jalan-reject',
        'approval-surat-jalan-print',
        'approval-surat-jalan-export'
    ];
    
    $usersWithApprovalSJ = [];
    
    foreach ($users as $user) {
        $userPermissions = $user->getAllPermissions()->pluck('name')->toArray();
        $hasApprovalSJ = array_intersect($approvalSJPermissions, $userPermissions);
        
        if (!empty($hasApprovalSJ)) {
            $usersWithApprovalSJ[$user->id] = [
                'username' => $user->username,
                'permissions' => $hasApprovalSJ
            ];
            
            echo "   ✅ User: {$user->username} (ID: {$user->id})\n";
            echo "      📋 Permissions: " . implode(', ', $hasApprovalSJ) . "\n";
        }
    }
    
    if (empty($usersWithApprovalSJ)) {
        echo "   ❌ TIDAK ADA USER yang memiliki permission approval-surat-jalan\n";
        echo "   💡 Ini adalah penyebab akses ditolak!\n\n";
    } else {
        echo "\n";
    }
    
    echo "2. MENGECEK PERMISSION DI DATABASE:\n";
    foreach ($approvalSJPermissions as $permName) {
        $perm = App\Models\Permission::where('name', $permName)->first();
        if ($perm) {
            echo "   ✅ {$permName} (ID: {$perm->id})\n";
        } else {
            echo "   ❌ {$permName} - TIDAK DITEMUKAN!\n";
        }
    }
    
    echo "\n3. MENGECEK ROUTE PROTECTION:\n";
    echo "   📋 Route approval surat jalan biasanya dilindungi dengan middleware seperti:\n";
    echo "      - middleware('permission:approval-surat-jalan-view')\n";
    echo "      - middleware('permission:approval-surat-jalan-approve')\n";
    echo "   💡 Pastikan permission yang digunakan di route sesuai dengan yang ada di database\n\n";
    
    echo "4. SOLUSI YANG DISARANKAN:\n";
    if (empty($usersWithApprovalSJ)) {
        echo "   🔧 LANGKAH 1: Berikan permission approval-surat-jalan kepada user\n";
        echo "      - Buka /master/user/[USER_ID]/edit\n";
        echo "      - Expand 'Sistem Persetujuan'\n";
        echo "      - Centang checkbox pada 'Approval Surat Jalan'\n";
        echo "      - Klik 'Perbarui'\n\n";
    } else {
        echo "   🔧 LANGKAH 1: Cek apakah user yang login memiliki permission\n";
        echo "   🔧 LANGKAH 2: Cek route protection di web.php atau controller\n";
        echo "   🔧 LANGKAH 3: Cek apakah nama permission di route sama dengan di database\n\n";
    }
    
    // Tampilkan user mana yang sedang login (jika ada session)
    echo "5. UNTUK DEBUG LEBIH LANJUT:\n";
    echo "   📝 Jalankan script ini dengan user ID spesifik:\n";
    echo "   📝 php check_user_approval_permissions.php [USER_ID]\n";
    echo "   📝 Contoh: php check_user_approval_permissions.php 1\n\n";
    
    echo "6. CEK APAKAH ADA ROUTE YANG MENGGUNAKAN PERMISSION INI:\n";
    echo "   📂 Cek file routes/web.php\n";
    echo "   📂 Cek controller yang menangani approval surat jalan\n";
    echo "   🔍 Cari middleware yang menggunakan 'approval-surat-jalan'\n\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}