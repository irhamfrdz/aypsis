<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Permission;

echo "🔍 Checking Admin User Operational Permissions\n";
echo "==============================================\n\n";

$admin = User::where('username', 'admin')->first();

if (!$admin) {
    echo "❌ Admin user not found!\n";
    exit(1);
}

echo "✅ Admin user found (ID: {$admin->id})\n";
echo "📊 Total permissions: " . $admin->permissions()->count() . "\n\n";

// Check operational permissions
$operationalModules = [
    'order-management',
    'surat-jalan', 
    'tanda-terima',
    'gate-in',
    'pranota-surat-jalan',
    'approval-surat-jalan'
];

$totalOperationalPerms = 0;

foreach ($operationalModules as $module) {
    echo "🔧 Module: $module\n";
    
    $modulePerms = $admin->permissions()
        ->where('name', 'like', $module . '%')
        ->get();
    
    echo "   Permissions: " . $modulePerms->count() . "\n";
    
    foreach ($modulePerms as $perm) {
        echo "   - {$perm->name}\n";
    }
    
    $totalOperationalPerms += $modulePerms->count();
    echo "\n";
}

echo "📈 Total operational permissions: $totalOperationalPerms\n";

if ($totalOperationalPerms > 0) {
    echo "✅ Admin user HAS operational permissions!\n";
    echo "✅ Permission saving is working correctly!\n";
} else {
    echo "❌ Admin user has NO operational permissions\n";
    echo "❌ There might be an issue with permission saving\n";
}

?>