<?php
require_once 'vendor/autoload.php';

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== CHECKING PERMISSIONS FOR MENU ===\n\n";

try {
    // 1. Pembayaran Uang Muka Permissions
    echo "1. MENU PEMBAYARAN UANG MUKA:\n";
    $pembayaranPermissions = DB::table('permissions')
        ->where('name', 'like', '%pembayaran-uang-muka%')
        ->orderBy('name')
        ->get();
        
    if ($pembayaranPermissions->count() > 0) {
        foreach ($pembayaranPermissions as $perm) {
            echo "   ✓ {$perm->name}\n";
        }
    } else {
        echo "   ✗ Tidak ada permission ditemukan untuk pembayaran-uang-muka\n";
    }
    
    // 2. Realisasi Uang Muka Permissions
    echo "\n2. MENU REALISASI UANG MUKA:\n";
    $realisasiPermissions = DB::table('permissions')
        ->where('name', 'like', '%realisasi-uang-muka%')
        ->orderBy('name')
        ->get();
        
    if ($realisasiPermissions->count() > 0) {
        foreach ($realisasiPermissions as $perm) {
            echo "   ✓ {$perm->name}\n";
        }
    } else {
        echo "   ✗ Tidak ada permission ditemukan untuk realisasi-uang-muka\n";
    }
    
    // 3. Check Permission Pattern
    echo "\n3. POLA PERMISSION YANG DIGUNAKAN:\n";
    echo "   Pembayaran Uang Muka:\n";
    echo "   - pembayaran-uang-muka-view (untuk melihat daftar dan detail)\n";
    echo "   - pembayaran-uang-muka-create (untuk menambah data)\n";
    echo "   - pembayaran-uang-muka-edit (untuk mengubah data)\n";
    echo "   - pembayaran-uang-muka-delete (untuk menghapus data)\n";
    
    echo "\n   Realisasi Uang Muka:\n";
    echo "   - realisasi-uang-muka-view (untuk melihat daftar dan detail)\n";
    echo "   - realisasi-uang-muka-create (untuk menambah data)\n";
    echo "   - realisasi-uang-muka-edit (untuk mengubah data)\n";
    echo "   - realisasi-uang-muka-delete (untuk menghapus data)\n";
    
    // 4. Check Admin Role Permissions
    echo "\n4. CHECKING ADMIN ROLE PERMISSIONS:\n";
    $adminRole = DB::table('roles')->where('name', 'Admin')->first();
    
    if ($adminRole) {
        echo "   Admin Role ID: {$adminRole->id}\n";
        
        // Check pembayaran permissions for admin
        $adminPembayaranPerms = DB::table('permission_role')
            ->join('permissions', 'permission_role.permission_id', '=', 'permissions.id')
            ->where('permission_role.role_id', $adminRole->id)
            ->where('permissions.name', 'like', '%pembayaran-uang-muka%')
            ->pluck('permissions.name')
            ->toArray();
            
        echo "   Admin Pembayaran Permissions: " . (count($adminPembayaranPerms) > 0 ? implode(', ', $adminPembayaranPerms) : 'None') . "\n";
        
        // Check realisasi permissions for admin
        $adminRealisasiPerms = DB::table('permission_role')
            ->join('permissions', 'permission_role.permission_id', '=', 'permissions.id')
            ->where('permission_role.role_id', $adminRole->id)
            ->where('permissions.name', 'like', '%realisasi-uang-muka%')
            ->pluck('permissions.name')
            ->toArray();
            
        echo "   Admin Realisasi Permissions: " . (count($adminRealisasiPerms) > 0 ? implode(', ', $adminRealisasiPerms) : 'None') . "\n";
    } else {
        echo "   ✗ Admin role tidak ditemukan\n";
    }
    
    // 5. Summary
    echo "\n5. SUMMARY:\n";
    $totalPembayaran = $pembayaranPermissions->count();
    $totalRealisasi = $realisasiPermissions->count();
    
    echo "   - Total Pembayaran Uang Muka permissions: $totalPembayaran\n";
    echo "   - Total Realisasi Uang Muka permissions: $totalRealisasi\n";
    
    if ($totalPembayaran == 0 || $totalRealisasi == 0) {
        echo "   ⚠️  MASALAH: Ada permission yang hilang, perlu dibuat seeder\n";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

echo "\n=== CHECK COMPLETED ===\n";