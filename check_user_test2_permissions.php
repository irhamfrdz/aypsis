<?php

require_once 'vendor/autoload.php';

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

// Load Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== CHECK USER TEST 2 PERMISSIONS ===\n\n";

// Find user test 2
$user = User::where('name', 'test2')->first();

if (!$user) {
    echo "❌ User 'test 2' tidak ditemukan\n";
    exit(1);
}

echo "✅ User ditemukan: {$user->name} (ID: {$user->id})\n\n";

// Check specific permissions
$permissionsToCheck = [
    'permohonan',
    'master-permohonan',
    'dashboard',
    'tagihan-kontainer'
];

echo "=== PERMISSION CHECK ===\n";
foreach ($permissionsToCheck as $permission) {
    $hasPermission = $user->can($permission);
    $status = $hasPermission ? '✅ ALLOWED' : '❌ DENIED';
    echo "Permission '{$permission}': {$status}\n";
}

echo "\n=== ALL USER PERMISSIONS ===\n";
$userPermissions = $user->permissions->pluck('name')->toArray();
if (empty($userPermissions)) {
    echo "❌ User tidak memiliki permission apapun\n";
} else {
    echo "User memiliki " . count($userPermissions) . " permission(s):\n";
    foreach ($userPermissions as $perm) {
        echo "- {$perm}\n";
    }
}

echo "\n=== USER ROLES ===\n";
$userRoles = $user->roles->pluck('name')->toArray();
if (empty($userRoles)) {
    echo "❌ User tidak memiliki role apapun\n";
} else {
    echo "User memiliki " . count($userRoles) . " role(s):\n";
    foreach ($userRoles as $role) {
        echo "- {$role}\n";
    }
}
