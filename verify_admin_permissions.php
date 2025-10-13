<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "✅ VERIFYING ADMIN PERMISSIONS AFTER SEEDING\n";
echo "===========================================\n\n";

$admin = \App\Models\User::where('username', 'admin')->first();

if ($admin) {
    echo "👤 Admin User: {$admin->username} (ID: {$admin->id})\n\n";
    
    // Check specific permission
    $hasPermission = $admin->hasPermissionTo('master-tujuan-kirim-view');
    echo "🔍 Has master-tujuan-kirim-view: " . ($hasPermission ? "✅ YES" : "❌ NO") . "\n\n";
    
    // Get all tujuan-kirim permissions
    $tujuanKirimPerms = $admin->getAllPermissions()->filter(function($p) {
        return strpos($p->name, 'tujuan-kirim') !== false;
    });
    
    echo "📋 Total tujuan-kirim permissions: " . $tujuanKirimPerms->count() . "\n";
    
    if ($tujuanKirimPerms->count() > 0) {
        foreach($tujuanKirimPerms as $perm) {
            echo "   ✅ {$perm->name}\n";
        }
    } else {
        echo "   ❌ No tujuan-kirim permissions found\n";
    }
    
    echo "\n" . str_repeat("=", 50) . "\n";
    
    if ($hasPermission) {
        echo "🎉 SUCCESS! Admin now has tujuan-kirim permissions!\n";
        echo "📱 The menu should now appear in the sidebar.\n\n";
        echo "💡 NEXT STEPS:\n";
        echo "1. Clear browser cache (Ctrl+Shift+Delete)\n";
        echo "2. Or open browser in Incognito/Private mode\n";  
        echo "3. Login as admin\n";
        echo "4. Check sidebar for 'Master Data' → 'Tujuan Kirim'\n";
    } else {
        echo "❌ Something went wrong. Admin still doesn't have permissions.\n";
    }
    
} else {
    echo "❌ Admin user not found!\n";
}