<?php
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

echo "=== Checking user4 mobil permissions ===\n";

$user4 = User::where('username', 'user4')->first();

if (!$user4) {
    echo "User4 not found!\n";
    exit;
}

echo "User4 ID: {$user4->id}\n";

$mobilPermissions = $user4->permissions()->where('name', 'like', '%mobil%')->get();

if ($mobilPermissions->isEmpty()) {
    echo "User4 has no mobil permissions!\n";
} else {
    echo "User4 mobil permissions:\n";
    foreach ($mobilPermissions as $perm) {
        echo "- ID: {$perm->id}, Name: {$perm->name}\n";
    }
}

// Check specifically for master-mobil.view
$hasMasterMobilView = $user4->permissions()->where('name', 'master-mobil.view')->exists();
echo "\nHas master-mobil.view: " . ($hasMasterMobilView ? "YES" : "NO") . "\n";

// Check for the problematic fallback permission
$hasFallback = $user4->permissions()->where('name', 'master.mobil.index')->exists();
echo "Has master.mobil.index (fallback): " . ($hasFallback ? "YES" : "NO") . "\n";
?>
