<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Adding Biaya Kapal Print & Export Permissions ===\n\n";

// Check existing permissions
$existing = DB::table('permissions')
    ->where('name', 'like', '%biaya-kapal%')
    ->pluck('name')
    ->toArray();

echo "Existing permissions:\n";
foreach ($existing as $perm) {
    echo "  ✓ $perm\n";
}
echo "\n";

// Permissions to add
$newPermissions = [
    [
        'name' => 'biaya-kapal-print',
        'description' => 'Print Biaya Kapal',
        'created_at' => now(),
        'updated_at' => now()
    ],
    [
        'name' => 'biaya-kapal-export',
        'description' => 'Export Biaya Kapal',
        'created_at' => now(),
        'updated_at' => now()
    ]
];

$added = 0;
foreach ($newPermissions as $permission) {
    // Check if already exists
    $exists = DB::table('permissions')
        ->where('name', $permission['name'])
        ->exists();
    
    if (!$exists) {
        DB::table('permissions')->insert($permission);
        echo "✓ Added: {$permission['name']}\n";
        $added++;
    } else {
        echo "⊗ Already exists: {$permission['name']}\n";
    }
}

echo "\n=== Summary ===\n";
echo "Total permissions added: $added\n";

// Show all biaya-kapal permissions
echo "\nAll Biaya Kapal permissions now:\n";
$allPerms = DB::table('permissions')
    ->where('name', 'like', '%biaya-kapal%')
    ->orderBy('name')
    ->get(['id', 'name', 'description']);

foreach ($allPerms as $perm) {
    echo "  ID: {$perm->id} | {$perm->name}";
    if ($perm->description) {
        echo " | {$perm->description}";
    }
    echo "\n";
}

echo "\n✓ Script completed successfully!\n";
