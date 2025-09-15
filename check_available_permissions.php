<?php

require_once 'vendor/autoload.php';

use App\Models\User;
use Illuminate\Support\Facades\DB;

// Initialize Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Checking Available Permissions\n";
echo "==============================\n\n";

// Get all permissions from database
$permissions = DB::table('permissions')->get();

echo "Total permissions found: " . $permissions->count() . "\n\n";

echo "All Permissions:\n";
foreach ($permissions as $permission) {
    echo "  - {$permission->name} (ID: {$permission->id})\n";
}

echo "\nPranota-related permissions:\n";
$pranotaPermissions = $permissions->filter(function($perm) {
    return strpos($perm->name, 'pranota') !== false;
});

if ($pranotaPermissions->count() > 0) {
    foreach ($pranotaPermissions as $perm) {
        echo "  - {$perm->name} (ID: {$perm->id})\n";
    }
} else {
    echo "  ❌ No pranota-related permissions found\n";
}

echo "\nChecking for pranota-create specifically:\n";
$pranotaCreate = DB::table('permissions')->where('name', 'pranota-create')->first();
if ($pranotaCreate) {
    echo "  ✅ pranota-create exists (ID: {$pranotaCreate->id})\n";
} else {
    echo "  ❌ pranota-create does NOT exist\n";
    echo "  💡 This permission needs to be created first\n";
}

echo "\nTest completed!\n";
