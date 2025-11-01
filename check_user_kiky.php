<?php

use Illuminate\Support\Facades\Artisan;

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Checking user 'kiky':\n";

// Get kiky user
$kiky = App\Models\User::where('username', 'kiky')->first();

if ($kiky) {
    echo "✅ User kiky found (ID: {$kiky->id})\n";
    
    // Get all permissions
    $totalPerms = $kiky->permissions()->count();
    echo "📊 Total permissions: {$totalPerms}\n";
    
    // Get dashboard permissions
    $dashboardPerms = $kiky->permissions()->where('name', 'like', '%dashboard%')->get();
    
    echo "🎛️ Dashboard permissions for kiky:\n";
    if ($dashboardPerms->count() > 0) {
        foreach ($dashboardPerms as $perm) {
            echo "   ✅ {$perm->name}: {$perm->description}\n";
        }
    } else {
        echo "   ❌ No dashboard permissions found\n";
    }
    
} else {
    echo "❌ User 'kiky' not found\n";
    
    // Show all users
    echo "\n📋 Available users:\n";
    $users = App\Models\User::select('id', 'username')->get();
    foreach ($users as $user) {
        echo "   - {$user->username} (ID: {$user->id})\n";
    }
}