<?php

use Illuminate\Support\Facades\Artisan;

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Adding dashboard-view permission to user kiky:\n";

// Get kiky user
$kiky = App\Models\User::where('username', 'kiky')->first();

if ($kiky) {
    // Get dashboard-view permission
    $dashboardViewPerm = App\Models\Permission::where('name', 'dashboard-view')->first();
    
    if ($dashboardViewPerm) {
        // Check if user already has this permission
        if (!$kiky->permissions()->where('permission_id', $dashboardViewPerm->id)->exists()) {
            $kiky->permissions()->attach($dashboardViewPerm->id);
            echo "✅ Added 'dashboard-view' permission to kiky\n";
        } else {
            echo "ℹ️ User kiky already has 'dashboard-view' permission\n";
        }
        
        // Show updated dashboard permissions
        echo "\n🎛️ Updated dashboard permissions for kiky:\n";
        $dashboardPerms = $kiky->permissions()->where('name', 'like', '%dashboard%')->get();
        foreach ($dashboardPerms as $perm) {
            echo "   ✅ {$perm->name}: {$perm->description}\n";
        }
        
    } else {
        echo "❌ dashboard-view permission not found in database\n";
    }
    
} else {
    echo "❌ User 'kiky' not found\n";
}