<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Permission;

echo "=== SURAT JALAN PERMISSION VERIFICATION ===\n\n";

try {
    echo "1. Checking Surat Jalan permissions in database:\n";
    $suratJalanPermissions = Permission::where('name', 'like', 'surat-jalan%')->get();
    
    foreach ($suratJalanPermissions as $permission) {
        echo "   ✓ {$permission->name} - {$permission->description}\n";
    }
    
    echo "\n2. Checking admin user permissions:\n";
    $admin = User::find(1);
    
    if ($admin) {
        echo "   Admin user: {$admin->username}\n";
        
        $adminSuratJalanPerms = $admin->permissions()->where('name', 'like', 'surat-jalan%')->get();
        
        foreach ($adminSuratJalanPerms as $permission) {
            echo "   ✓ {$permission->name}\n";
        }
        
        echo "\n3. Testing hasPermissionTo method:\n";
        $testPermissions = ['surat-jalan-view', 'surat-jalan-create', 'surat-jalan-update', 'surat-jalan-delete'];
        
        foreach ($testPermissions as $permName) {
            $hasPermission = $admin->hasPermissionTo($permName);
            echo "   {$permName}: " . ($hasPermission ? "✓ GRANTED" : "✗ DENIED") . "\n";
        }
    } else {
        echo "   ❌ Admin user not found!\n";
    }
    
    echo "\n✅ Verification complete!\n";

} catch (Exception $e) {
    echo "❌ Error during verification: " . $e->getMessage() . "\n";
}