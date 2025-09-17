<?php

/**
 * Debug Tagihan Kontainer Permissions
 * Usage: php debug_tagihan_permissions.php
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Permission;
use App\Models\User;

echo "==========================================\n";
echo "   Debug Tagihan Kontainer Permissions\n";
echo "==========================================\n";

try {
    // Check all permissions containing 'tagihan'
    echo "🔍 Searching for permissions containing 'tagihan':\n";
    $tagihanPerms = Permission::where('name', 'like', '%tagihan%')->get();

    if ($tagihanPerms->isEmpty()) {
        echo "❌ No permissions found containing 'tagihan'\n";
    } else {
        echo "✅ Found {$tagihanPerms->count()} permissions:\n";
        foreach ($tagihanPerms as $perm) {
            echo "  - {$perm->name} (ID: {$perm->id})\n";
        }
    }

    echo "\n🔍 Searching for permissions containing 'kontainer':\n";
    $kontainerPerms = Permission::where('name', 'like', '%kontainer%')->get();

    if ($kontainerPerms->isEmpty()) {
        echo "❌ No permissions found containing 'kontainer'\n";
    } else {
        echo "✅ Found {$kontainerPerms->count()} permissions:\n";
        foreach ($kontainerPerms as $perm) {
            echo "  - {$perm->name} (ID: {$perm->id})\n";
        }
    }

    echo "\n🔍 Searching for permissions containing 'tagihan-kontainer':\n";
    $tagihanKontainerPerms = Permission::where('name', 'like', '%tagihan-kontainer%')->get();

    if ($tagihanKontainerPerms->isEmpty()) {
        echo "❌ No permissions found containing 'tagihan-kontainer'\n";
    } else {
        echo "✅ Found {$tagihanKontainerPerms->count()} permissions:\n";
        foreach ($tagihanKontainerPerms as $perm) {
            echo "  - {$perm->name} (ID: {$perm->id})\n";
        }
    }

    // Check admin user's permissions
    echo "\n👤 Checking admin user's permissions:\n";
    $adminUser = User::with('permissions')->where('username', 'admin')->first();

    if (!$adminUser) {
        echo "❌ Admin user not found\n";
        exit(1);
    }

    $adminPerms = $adminUser->permissions->pluck('name')->toArray();
    echo "📊 Admin has {$adminUser->permissions->count()} permissions\n";

    $tagihanKontainerAdminPerms = array_filter($adminPerms, function($perm) {
        return strpos($perm, 'tagihan-kontainer') !== false;
    });

    if (empty($tagihanKontainerAdminPerms)) {
        echo "❌ Admin has NO tagihan-kontainer permissions\n";
    } else {
        echo "✅ Admin has these tagihan-kontainer permissions:\n";
        foreach ($tagihanKontainerAdminPerms as $perm) {
            echo "  - {$perm}\n";
        }
    }

    // Check if the expected permissions exist
    echo "\n🔍 Checking expected tagihan-kontainer permissions:\n";
    $expectedPerms = [
        'tagihan-kontainer.view',
        'tagihan-kontainer.create',
        'tagihan-kontainer.update',
        'tagihan-kontainer.delete'
    ];

    foreach ($expectedPerms as $permName) {
        $perm = Permission::where('name', $permName)->first();
        if ($perm) {
            echo "✅ {$permName} exists (ID: {$perm->id})\n";
        } else {
            echo "❌ {$permName} does NOT exist\n";
        }
    }

    // Check if admin has these permissions
    echo "\n🔍 Checking if admin has expected permissions:\n";
    foreach ($expectedPerms as $permName) {
        $hasPerm = $adminUser->permissions->where('name', $permName)->first();
        if ($hasPerm) {
            echo "✅ Admin has {$permName}\n";
        } else {
            echo "❌ Admin missing {$permName}\n";
        }
    }

    echo "\n==========================================\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
