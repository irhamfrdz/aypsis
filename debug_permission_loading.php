<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

// Get admin user with permissions loaded
$user = User::with('permissions')->where('username', 'admin')->first();
if (!$user) {
    echo "Admin user not found!\n";
    exit;
}

echo "=== TESTING PERMISSION LOADING ===\n";

// Check if permissions are loaded in collection
$permissionsLoaded = $user->permissions->isNotEmpty();
echo "Permissions loaded in collection: " . ($permissionsLoaded ? 'YES' : 'NO') . "\n";
echo "Number of permissions in collection: " . $user->permissions->count() . "\n";

// Check specific kode nomor permission
$hasKodeNomorView = $user->permissions->contains('name', 'master-kode-nomor-view');
echo "Has 'master-kode-nomor-view' in collection: " . ($hasKodeNomorView ? 'YES' : 'NO') . "\n";

// Test database query directly
$hasKodeNomorViewDb = $user->permissions()->where('name', 'master-kode-nomor-view')->exists();
echo "Has 'master-kode-nomor-view' in database: " . ($hasKodeNomorViewDb ? 'YES' : 'NO') . "\n";

echo "\n=== CONCLUSION ===\n";
if ($hasKodeNomorView && !$hasKodeNomorViewDb) {
    echo "❌ Inconsistency detected!\n";
    echo "Collection says YES, Database says NO\n";
    echo "This explains why Gate fails but can() works\n";
} elseif ($hasKodeNomorView && $hasKodeNomorViewDb) {
    echo "✅ Both collection and database agree\n";
} else {
    echo "❓ Unexpected state - both are false\n";
}
