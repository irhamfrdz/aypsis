<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Checking user permissions for Tanda Terima\n";
echo "=" . str_repeat("=", 70) . "\n\n";

echo "Enter username to check: ";
$username = trim(fgets(STDIN));

$user = DB::table('users')->where('username', $username)->first();

if (!$user) {
    echo "❌ User '{$username}' not found!\n";
    exit(1);
}

echo "\n✅ Found user: {$user->username} (ID: {$user->id})\n\n";

// Get user permissions
$userPermissions = DB::table('user_permissions')
    ->join('permissions', 'user_permissions.permission_id', '=', 'permissions.id')
    ->where('user_permissions.user_id', $user->id)
    ->where(function($query) {
        $query->where('permissions.name', 'LIKE', '%tanda-terima%')
              ->orWhere('permissions.name', 'LIKE', '%tanda_terima%');
    })
    ->select('permissions.id', 'permissions.name', 'permissions.description')
    ->get();

if ($userPermissions->isEmpty()) {
    echo "❌ User has NO tanda-terima permissions!\n\n";
    echo "User has total " . DB::table('user_permissions')->where('user_id', $user->id)->count() . " permissions.\n\n";
    
    echo "Available tanda-terima permissions that can be assigned:\n";
    $availablePerms = DB::table('permissions')
        ->where('name', 'LIKE', '%tanda-terima%')
        ->get(['id', 'name']);
    
    foreach ($availablePerms as $perm) {
        echo "  {$perm->id}. {$perm->name}\n";
    }
    
    echo "\n💡 To fix this, edit the user in admin panel and check tanda-terima permissions.\n";
} else {
    echo "✅ User has " . count($userPermissions) . " tanda-terima permissions:\n\n";
    foreach ($userPermissions as $perm) {
        echo "  ✓ {$perm->name}";
        if ($perm->description) {
            echo " - {$perm->description}";
        }
        echo "\n";
    }
    
    echo "\n" . str_repeat("=", 70) . "\n";
    echo "Checking required permissions for /tanda-terima page:\n\n";
    
    $hasView = $userPermissions->contains('name', 'tanda-terima-view');
    
    if ($hasView) {
        echo "  ✅ HAS tanda-terima-view permission\n";
        echo "  ✅ Should be able to access /tanda-terima page\n";
    } else {
        echo "  ❌ MISSING tanda-terima-view permission\n";
        echo "  ❌ Cannot access /tanda-terima page\n\n";
        echo "💡 Solution: Edit user and check 'View' checkbox for Tanda Terima\n";
    }
}

echo "\n" . str_repeat("=", 70) . "\n";
