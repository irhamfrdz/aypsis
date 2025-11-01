<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Checking dashboard permission in database:\n";

// Check if 'dashboard' permission exists
$dashboardPerm = App\Models\Permission::where('name', 'dashboard')->first();

if ($dashboardPerm) {
    echo "✅ Found 'dashboard' permission: {$dashboardPerm->description}\n";
} else {
    echo "❌ No 'dashboard' permission found\n";
}

echo "\nAll dashboard-related permissions:\n";
$allDashboard = App\Models\Permission::where('name', 'like', '%dashboard%')->get();
foreach ($allDashboard as $perm) {
    echo "- {$perm->name}: {$perm->description}\n";
}

// Check kiky's dashboard permissions
echo "\nUser kiky's dashboard permissions:\n";
$kiky = App\Models\User::where('username', 'kiky')->first();
if ($kiky) {
    $kikyDashboard = $kiky->permissions()->where('name', 'like', '%dashboard%')->get();
    foreach ($kikyDashboard as $perm) {
        echo "- {$perm->name}: {$perm->description}\n";
    }
    
    // Check if kiky has 'dashboard' permission specifically
    $hasDashboard = $kiky->permissions()->where('name', 'dashboard')->exists();
    echo "\nKiky has 'dashboard' permission: " . ($hasDashboard ? 'YES' : 'NO') . "\n";
}