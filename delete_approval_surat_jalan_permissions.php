<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\DB;

echo "=== SCRIPT HAPUS PERMISSIONS APPROVAL SURAT JALAN ===\n\n";

try {
    // Cari semua permissions yang terkait approval-surat-jalan
    $permissions = Permission::where('name', 'like', 'approval-surat-jalan%')->get();
    
    if ($permissions->isEmpty()) {
        echo "✅ Tidak ada permission approval-surat-jalan yang ditemukan.\n";
        exit(0);
    }
    
    echo "Ditemukan " . $permissions->count() . " permission(s) approval-surat-jalan:\n";
    foreach ($permissions as $permission) {
        echo "  - {$permission->name} (ID: {$permission->id})\n";
    }
    
    echo "\nApakah Anda yakin ingin menghapus semua permission ini? (yes/no): ";
    $handle = fopen("php://stdin", "r");
    $line = fgets($handle);
    $confirmation = trim(strtolower($line));
    fclose($handle);
    
    if ($confirmation !== 'yes') {
        echo "\n❌ Operasi dibatalkan.\n";
        exit(0);
    }
    
    echo "\nMemulai proses penghapusan...\n\n";
    
    DB::beginTransaction();
    
    try {
        foreach ($permissions as $permission) {
            echo "Menghapus permission: {$permission->name}...\n";
            
            // Hapus relasi di pivot table model_has_permissions
            DB::table('model_has_permissions')
                ->where('permission_id', $permission->id)
                ->delete();
            echo "  ✓ Relasi di model_has_permissions dihapus\n";
            
            // Hapus relasi di pivot table role_has_permissions
            DB::table('role_has_permissions')
                ->where('permission_id', $permission->id)
                ->delete();
            echo "  ✓ Relasi di role_has_permissions dihapus\n";
            
            // Hapus permission itu sendiri
            $permission->delete();
            echo "  ✓ Permission dihapus\n\n";
        }
        
        DB::commit();
        
        echo "✅ BERHASIL! Semua permission approval-surat-jalan telah dihapus.\n";
        echo "\nTotal permission dihapus: " . $permissions->count() . "\n";
        
        // Clear cache
        echo "\nMembersihkan cache...\n";
        \Artisan::call('cache:clear');
        \Artisan::call('config:clear');
        echo "✅ Cache dibersihkan.\n";
        
    } catch (\Exception $e) {
        DB::rollBack();
        throw $e;
    }
    
} catch (\Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
