<?php
/**
 * Assign Tanda Terima Tanpa Surat Jalan Permissions to Admin User
 *
 * This script assigns all tanda terima tanpa surat jalan permissions to the admin user.
 */

require 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use App\Models\User;
use App\Models\Permission;

// Get admin user (usually ID 1)
$admin = User::find(1);

if (!$admin) {
    $admin = User::where('email', 'admin@example.com')->first();
}

if (!$admin) {
    echo "❌ Admin user not found!\n";
    exit(1);
}

echo "Found admin user: {$admin->name} (ID: {$admin->id})\n\n";

// Get all tanda terima tanpa surat jalan permissions
$permissions = Permission::where('name', 'like', 'tanda-terima-tanpa-surat-jalan-%')->get();

if ($permissions->isEmpty()) {
    echo "❌ No tanda terima tanpa surat jalan permissions found! Run TandaTerimaTanpaSuratJalanPermissionSeeder first.\n";
    exit(1);
}

echo "Assigning permissions to admin...\n";
echo str_repeat("=", 80) . "\n";

$assignedCount = 0;
$skippedCount = 0;

foreach ($permissions as $permission) {
    // Check if already assigned
    if ($admin->permissions()->where('permission_id', $permission->id)->exists()) {
        echo "⚠️  SKIPPED: {$permission->name} (already assigned)\n";
        $skippedCount++;
        continue;
    }

    // Assign permission
    $admin->permissions()->attach($permission->id);

    echo "✅ ASSIGNED: {$permission->name} - {$permission->display_name}\n";
    $assignedCount++;
}

echo str_repeat("=", 80) . "\n";
echo "\nSummary:\n";
echo "- Permissions assigned: $assignedCount\n";
echo "- Permissions skipped: $skippedCount\n";
echo "- Total permissions: " . $permissions->count() . "\n\n";

echo "✅ Admin user now has access to Tanda Terima Tanpa Surat Jalan module!\n\n";

// Display the assigned permissions
echo "📋 Assigned Permissions:\n";
echo str_repeat("-", 50) . "\n";
foreach ($permissions as $permission) {
    echo "• {$permission->name}\n";
    echo "  Display: {$permission->display_name}\n";
    echo "  Description: {$permission->description}\n\n";
}
