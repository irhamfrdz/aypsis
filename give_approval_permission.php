<?php
require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\DB;

$app = new Application(getcwd());
$app->singleton(\Illuminate\Contracts\Http\Kernel::class, \App\Http\Kernel::class);
$app->singleton(\Illuminate\Contracts\Console\Kernel::class, \App\Console\Kernel::class);
$app->singleton(\Illuminate\Contracts\Debug\ExceptionHandler::class, \App\Exceptions\Handler::class);
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== PERBAIKAN APPROVAL SURAT JALAN ===\n\n";

try {
    echo "1. CEK USER ADMIN:\n";
    $user = App\Models\User::find(1);
    if ($user) {
        echo "   ✅ User ditemukan: {$user->username}\n";
        
        echo "\n2. CEK PERMISSION SAAT INI:\n";
        $currentPerms = $user->getAllPermissions()->pluck('name')->toArray();
        $approvalPerms = array_filter($currentPerms, function($perm) {
            return str_contains($perm, 'approval-surat-jalan');
        });
        
        if (!empty($approvalPerms)) {
            echo "   ✅ User sudah memiliki approval surat jalan permissions:\n";
            foreach ($approvalPerms as $perm) {
                echo "      - {$perm}\n";
            }
        } else {
            echo "   ❌ User TIDAK memiliki approval surat jalan permissions\n";
        }
        
        echo "\n3. MEMBERIKAN PERMISSION:\n";
        $requiredPerms = [
            'approval-surat-jalan-view',
            'approval-surat-jalan-approve'
        ];
        
        foreach ($requiredPerms as $permName) {
            if (!in_array($permName, $currentPerms)) {
                echo "   🔧 Memberikan permission: {$permName}\n";
                $user->givePermissionTo($permName);
                echo "   ✅ Permission diberikan!\n";
            } else {
                echo "   ℹ️ Permission {$permName} sudah ada\n";
            }
        }
        
        echo "\n4. VERIFIKASI HASIL:\n";
        $user->refresh(); // Reload user
        $finalPerms = $user->getAllPermissions()->pluck('name')->toArray();
        $finalApprovalPerms = array_filter($finalPerms, function($perm) {
            return str_contains($perm, 'approval-surat-jalan');
        });
        
        if (!empty($finalApprovalPerms)) {
            echo "   ✅ BERHASIL! User sekarang memiliki:\n";
            foreach ($finalApprovalPerms as $perm) {
                echo "      - {$perm}\n";
            }
            
            echo "\n5. TEST AKSES:\n";
            if ($user->hasPermissionTo('approval-surat-jalan-view')) {
                echo "   ✅ User dapat mengakses /approval/surat-jalan\n";
            } else {
                echo "   ❌ User masih tidak dapat mengakses /approval/surat-jalan\n";
            }
            
        } else {
            echo "   ❌ Gagal memberikan permission\n";
        }
        
    } else {
        echo "   ❌ User tidak ditemukan\n";
    }
    
    echo "\n✅ SELESAI! Coba akses /approval/surat-jalan sekarang\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}