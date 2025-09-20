<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Permission;

echo "=== CHECKING ADMIN USER PERMISSIONS ===\n\n";

$user = User::where('username', 'admin')->first();

if (!$user) {
    echo "Admin user not found!\n";
    exit(1);
}

echo "Admin user found: {$user->username}\n";
echo "User ID: {$user->id}\n\n";

echo "=== ALL ADMIN PERMISSIONS ===\n";
$permissions = $user->permissions()->orderBy('name')->get();
foreach ($permissions as $perm) {
    echo "- {$perm->name} (ID: {$perm->id})\n";
}

echo "\n=== CHECKING SPECIFIC PERMISSIONS ===\n";
echo "Has 'permohonan' permission: " . ($user->can('permohonan') ? 'YES' : 'NO') . "\n";
echo "Has 'approval.view' permission: " . ($user->can('approval.view') ? 'YES' : 'NO') . "\n";
echo "Has 'approval.dashboard' permission: " . ($user->can('approval.dashboard') ? 'YES' : 'NO') . "\n";
echo "Has 'approval' permission: " . ($user->can('approval') ? 'YES' : 'NO') . "\n";

echo "\n=== CHECKING APPROVAL-RELATED PERMISSIONS ===\n";
$approvalPerms = $permissions->filter(function($perm) {
    return strpos($perm->name, 'approval') !== false;
});

if ($approvalPerms->count() > 0) {
    echo "Found {$approvalPerms->count()} approval-related permissions:\n";
    foreach ($approvalPerms as $perm) {
        echo "- {$perm->name}\n";
    }
} else {
    echo "No approval-related permissions found!\n";
}

echo "\n=== CHECKING PERMOHONAN-RELATED PERMISSIONS ===\n";
$permohonanPerms = $permissions->filter(function($perm) {
    return strpos($perm->name, 'permohonan') !== false;
});

if ($permohonanPerms->count() > 0) {
    echo "Found {$permohonanPerms->count()} permohonan-related permissions:\n";
    foreach ($permohonanPerms as $perm) {
        echo "- {$perm->name}\n";
    }
} else {
    echo "No permohonan-related permissions found!\n";
}

echo "\nTest completed.\n";
