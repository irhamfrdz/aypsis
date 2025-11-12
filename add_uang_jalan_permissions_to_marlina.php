<?php

/**
 * Script untuk memberikan permission Uang Jalan dan Pranota Uang Jalan ke user Marlina
 * 
 * Permissions yang akan diberikan:
 * - Uang Jalan: view, create, update, delete, approve, print, export
 * - Pranota Uang Jalan: view, create, update, delete, approve, print, export
 * 
 * Total: 14 permissions
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

echo "\n";
echo "=================================================================\n";
echo "  Script: Add Uang Jalan Permissions to Marlina\n";
echo "=================================================================\n";
echo "\n";

try {
    // Find user Marlina
    echo "🔍 Mencari user Marlina...\n";
    
    // Try to find by name first
    $user = User::where('name', 'LIKE', '%marlina%')->first();
    
    if (!$user) {
        // Try to find by email
        $user = User::where('email', 'LIKE', '%marlina%')->first();
    }
    
    if (!$user) {
        echo "❌ User Marlina tidak ditemukan!\n";
        echo "💡 Silakan cek nama user yang benar di database.\n\n";
        
        echo "Daftar user yang tersedia:\n";
        $users = User::select('id', 'name', 'email')->get();
        foreach ($users as $u) {
            echo "  - ID: {$u->id}, Name: {$u->name}, Email: {$u->email}\n";
        }
        exit(1);
    }
    
    echo "✅ User ditemukan:\n";
    echo "   - ID: {$user->id}\n";
    echo "   - Name: {$user->name}\n";
    echo "   - Email: {$user->email}\n";
    echo "\n";
    
    // Define permissions to add
    $permissionsToAdd = [
        // Uang Jalan permissions
        'uang-jalan-view',
        'uang-jalan-create',
        'uang-jalan-update',
        'uang-jalan-delete',
        'uang-jalan-approve',
        'uang-jalan-print',
        'uang-jalan-export',
        
        // Pranota Uang Jalan permissions
        'pranota-uang-jalan-view',
        'pranota-uang-jalan-create',
        'pranota-uang-jalan-update',
        'pranota-uang-jalan-delete',
        'pranota-uang-jalan-approve',
        'pranota-uang-jalan-print',
        'pranota-uang-jalan-export',
    ];
    
    echo "📋 Permissions yang akan diberikan:\n";
    echo "   Total: " . count($permissionsToAdd) . " permissions\n\n";
    
    // Check which permissions exist
    $existingPermissions = [];
    $missingPermissions = [];
    
    foreach ($permissionsToAdd as $permName) {
        $permission = Permission::where('name', $permName)->first();
        if ($permission) {
            $existingPermissions[] = $permName;
        } else {
            $missingPermissions[] = $permName;
        }
    }
    
    // Show missing permissions warning
    if (!empty($missingPermissions)) {
        echo "⚠️  WARNING: Beberapa permissions belum ada di database:\n";
        foreach ($missingPermissions as $permName) {
            echo "   ❌ {$permName}\n";
        }
        echo "\n";
        echo "💡 Silakan jalankan seeder terlebih dahulu:\n";
        echo "   php artisan db:seed --class=UangJalanPermissionSeeder\n";
        echo "\n";
        
        if (empty($existingPermissions)) {
            echo "❌ Tidak ada permissions yang bisa diberikan. Script dihentikan.\n";
            exit(1);
        }
        
        echo "⏩ Melanjutkan dengan permissions yang ada...\n\n";
    }
    
    // Give permissions to user
    $addedCount = 0;
    $skippedCount = 0;
    
    echo "🔄 Memberikan permissions ke user...\n\n";
    
    foreach ($existingPermissions as $permName) {
        if ($user->hasPermissionTo($permName)) {
            echo "   ⏭️  {$permName} (sudah ada)\n";
            $skippedCount++;
        } else {
            $user->givePermissionTo($permName);
            echo "   ✅ {$permName} (berhasil ditambahkan)\n";
            $addedCount++;
        }
    }
    
    echo "\n";
    echo "=================================================================\n";
    echo "  SUMMARY\n";
    echo "=================================================================\n";
    echo "User: {$user->name} ({$user->email})\n";
    echo "Permissions ditambahkan: {$addedCount}\n";
    echo "Permissions sudah ada: {$skippedCount}\n";
    echo "Total permissions: " . ($addedCount + $skippedCount) . "\n";
    
    if (!empty($missingPermissions)) {
        echo "Permissions yang tidak ada: " . count($missingPermissions) . "\n";
    }
    
    echo "\n";
    echo "✅ Script selesai dijalankan!\n";
    echo "\n";
    echo "📝 Next steps:\n";
    echo "   1. User Marlina sekarang bisa akses modul Uang Jalan\n";
    echo "   2. User Marlina sekarang bisa akses modul Pranota Uang Jalan\n";
    echo "   3. Silakan login dan test fungsionalitas\n";
    echo "\n";
    
} catch (\Exception $e) {
    echo "\n";
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "\n";
    echo "Stack trace:\n";
    echo $e->getTraceAsString() . "\n";
    echo "\n";
    exit(1);
}
