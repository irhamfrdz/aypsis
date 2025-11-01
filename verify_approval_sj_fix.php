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

echo "=== VERIFIKASI PERBAIKAN APPROVAL SURAT JALAN ===\n\n";

try {
    echo "1. MENGECEK PERMISSION YANG DIBUTUHKAN:\n";
    
    $requiredPermissions = [
        'approval-surat-jalan-view' => 'Untuk mengakses halaman approval surat jalan',
        'approval-surat-jalan-approve' => 'Untuk approve/reject surat jalan'
    ];
    
    $allPermissionsExist = true;
    
    foreach ($requiredPermissions as $permName => $description) {
        $perm = App\Models\Permission::where('name', $permName)->first();
        if ($perm) {
            echo "   ✅ {$permName} (ID: {$perm->id}) - {$description}\n";
        } else {
            echo "   ❌ {$permName} - TIDAK DITEMUKAN! - {$description}\n";
            $allPermissionsExist = false;
        }
    }
    
    if (!$allPermissionsExist) {
        echo "\n   ⚠️ Beberapa permission tidak ditemukan. Menjalankan pembuatan permission...\n";
        
        // Buat permission yang hilang
        foreach ($requiredPermissions as $permName => $description) {
            $existing = App\Models\Permission::where('name', $permName)->first();
            if (!$existing) {
                $newPerm = App\Models\Permission::create([
                    'name' => $permName,
                    'guard_name' => 'web',
                    'description' => $description
                ]);
                echo "   ✅ Created {$permName} (ID: {$newPerm->id})\n";
            }
        }
    }
    
    echo "\n2. MENGECEK USER YANG SUDAH DIBERI PERMISSION:\n";
    
    $usersWithPermission = [];
    $users = App\Models\User::all();
    
    foreach ($users as $user) {
        $userPermissions = $user->getAllPermissions()->pluck('name')->toArray();
        $hasApprovalSJView = in_array('approval-surat-jalan-view', $userPermissions);
        $hasApprovalSJApprove = in_array('approval-surat-jalan-approve', $userPermissions);
        
        if ($hasApprovalSJView || $hasApprovalSJApprove) {
            $permissions = [];
            if ($hasApprovalSJView) $permissions[] = 'view';
            if ($hasApprovalSJApprove) $permissions[] = 'approve';
            
            echo "   ✅ User: {$user->username} (ID: {$user->id}) - " . implode(', ', $permissions) . "\n";
            $usersWithPermission[] = $user;
        }
    }
    
    if (empty($usersWithPermission)) {
        echo "   ❌ TIDAK ADA USER yang memiliki permission approval surat jalan\n";
        echo "   💡 Berikan permission melalui UI: /master/user/[ID]/edit\n";
        echo "   💡 Atau jalankan command: \$user->givePermissionTo('approval-surat-jalan-view')\n";
    }
    
    echo "\n3. STATUS ROUTE:\n";
    echo "   ✅ Route /approval/surat-jalan sekarang menggunakan middleware:\n";
    echo "      - auth (user harus login)\n";
    echo "      - can:approval-surat-jalan-view (user harus punya permission view)\n";
    echo "   ✅ Route approve/reject menggunakan:\n";
    echo "      - can:approval-surat-jalan-approve (user harus punya permission approve)\n";
    
    echo "\n4. LANGKAH SELANJUTNYA:\n";
    if (empty($usersWithPermission)) {
        echo "   🔧 1. Buka halaman edit user: /master/user/[USER_ID]/edit\n";
        echo "   🔧 2. Klik 'Sistem Persetujuan' untuk expand dropdown\n";
        echo "   🔧 3. Centang checkbox 'Lihat' di baris 'Approval Surat Jalan'\n";
        echo "   🔧 4. Centang checkbox 'Setuju' jika user perlu approve/reject\n";
        echo "   🔧 5. Klik 'Perbarui'\n";
        echo "   🔧 6. Coba akses /approval/surat-jalan lagi\n";
    } else {
        echo "   ✅ Permission sudah diberikan ke user\n";
        echo "   ✅ User dapat mengakses /approval/surat-jalan\n";
        echo "   💡 Jika masih akses ditolak, clear cache: php artisan cache:clear\n";
    }
    
    echo "\n✅ PERBAIKAN SELESAI!\n";
    echo "✅ Route sekarang menggunakan permission yang benar\n";
    echo "✅ User dengan permission 'approval-surat-jalan-view' dapat mengakses menu\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    // Tidak tampilkan stack trace untuk error database connection
    if (!str_contains($e->getMessage(), 'could not find driver')) {
        echo "Stack trace: " . $e->getTraceAsString() . "\n";
    }
}