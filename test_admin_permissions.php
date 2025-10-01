<?php

// Script untuk test permission user admin setelah perbaikan

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

echo "=== TESTING USER ADMIN PERMISSIONS (AFTER FIX) ===\n\n";

$admin = User::where('username', 'admin')->with('permissions')->first();

if ($admin) {
    echo "Testing permission checks for user admin:\n\n";

    // Test cases yang sering bermasalah
    $testPermissions = [
        'master-cabang-view',
        'master-cabang-create',
        'master-cabang-update',
        'master-cabang-delete',
        'master-coa-view',
        'master-coa-create',
        'master-coa-update',
        'master-coa-delete',
        'dashboard',
        'master-karyawan-view',
        'master-user-view'
    ];

    foreach ($testPermissions as $permission) {
        $hasPermission = $admin->can($permission);
        $status = $hasPermission ? '✅ GRANTED' : '❌ DENIED';
        echo "- {$permission}: {$status}\n";

        // Debug info - show what actual permissions we have that might match
        $matchingPerms = $admin->permissions->filter(function($perm) use ($permission) {
            return str_contains($perm->name, str_replace('-view', '', $permission)) ||
                   str_contains($perm->name, str_replace('-create', '', $permission)) ||
                   str_contains($perm->name, str_replace('-update', '', $permission)) ||
                   str_contains($perm->name, str_replace('-delete', '', $permission));
        });

        if ($matchingPerms->count() > 0) {
            echo "  Related permissions: " . $matchingPerms->pluck('name')->join(', ') . "\n";
        }
    }

} else {
    echo "User admin tidak ditemukan!\n";
}
