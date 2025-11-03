<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔧 Quick Fix: Assign Tanda Terima Permissions to User\n";
echo "=" . str_repeat("=", 70) . "\n\n";

echo "Enter username: ";
$username = trim(fgets(STDIN));

$user = DB::table('users')->where('username', $username)->first();

if (!$user) {
    echo "❌ User '{$username}' not found!\n";
    exit(1);
}

echo "\n✅ Found user: {$user->username} (ID: {$user->id})\n\n";

// Get all tanda-terima permissions
$permissions = DB::table('permissions')
    ->where('name', 'LIKE', '%tanda-terima%')
    ->whereNotIn('name', [
        'tanda-terima-tanpa-surat-jalan-view',
        'tanda-terima-tanpa-surat-jalan-create',
        'tanda-terima-tanpa-surat-jalan-update',
        'tanda-terima-tanpa-surat-jalan-delete'
    ])
    ->get();

echo "Found " . count($permissions) . " tanda-terima permissions:\n\n";

foreach ($permissions as $perm) {
    echo "  {$perm->id}. {$perm->name}\n";
}

echo "\n" . str_repeat("-", 70) . "\n";
echo "Assign which permissions? (comma-separated IDs, or 'all' for all):\n";
echo "Example: 127,128,129 or 'all'\n";
echo "Choice: ";
$choice = trim(fgets(STDIN));

$permissionIds = [];

if (strtolower($choice) === 'all') {
    $permissionIds = $permissions->pluck('id')->toArray();
} else {
    $permissionIds = array_map('trim', explode(',', $choice));
}

if (empty($permissionIds)) {
    echo "❌ No permissions selected!\n";
    exit(1);
}

echo "\n🔄 Assigning permissions...\n\n";

$assigned = 0;
$skipped = 0;

foreach ($permissionIds as $permId) {
    // Check if already assigned
    $exists = DB::table('user_permissions')
        ->where('user_id', $user->id)
        ->where('permission_id', $permId)
        ->exists();
    
    if ($exists) {
        $perm = DB::table('permissions')->find($permId);
        echo "  ⏭️  Skipped: {$perm->name} (already assigned)\n";
        $skipped++;
    } else {
        DB::table('user_permissions')->insert([
            'user_id' => $user->id,
            'permission_id' => $permId
        ]);
        
        $perm = DB::table('permissions')->find($permId);
        echo "  ✅ Assigned: {$perm->name}\n";
        $assigned++;
    }
}

echo "\n" . str_repeat("=", 70) . "\n";
echo "✅ Done! Assigned {$assigned} permissions, skipped {$skipped}\n";
echo "\n💡 User '{$username}' should now be able to access /tanda-terima page\n";
echo "🔄 Please logout and login again to refresh permissions\n";
