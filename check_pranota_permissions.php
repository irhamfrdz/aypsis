<?php

require_once 'vendor/autoload.php';

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Permission;

// Initialize Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Checking Pranota Permissions\n";
echo "===========================\n\n";

// Find all permissions containing 'pranota'
$pranotaPerms = Permission::where('name', 'like', '%pranota%')->get();
echo "All permissions containing 'pranota':\n";
foreach ($pranotaPerms as $perm) {
    echo "  {$perm->id}: {$perm->name}\n";
}
echo "\n";

// Check what user marlina has
$user = User::where('username', 'marlina')->first();
if (!$user) {
    echo "❌ User marlina not found\n";
    exit(1);
}

echo "User marlina permissions containing 'pranota':\n";
foreach ($user->permissions as $perm) {
    if (strpos($perm->name, 'pranota') !== false) {
        echo "  {$perm->id}: {$perm->name}\n";
    }
}
echo "\n";

// Check specific permissions used in sidebar
$sidebarChecks = [
    'pranota.view',
    'pranota-kontainer-sewa-view',
    'pranota-kontainer-sewa.index'
];

echo "Sidebar permission checks:\n";
foreach ($sidebarChecks as $permName) {
    $hasPerm = $user->hasPermissionTo($permName);
    echo "  - $permName: " . ($hasPerm ? "✅ HAS" : "❌ MISSING") . "\n";
}

?>
