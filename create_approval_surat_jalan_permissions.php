<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== CREATING APPROVAL SURAT JALAN PERMISSIONS ===\n\n";

// Required permissions for approval surat jalan
$permissions = [
    [
        'name' => 'approval-surat-jalan-view',
        'description' => 'Lihat halaman approval surat jalan'
    ],
    [
        'name' => 'approval-surat-jalan-approve',
        'description' => 'Approve surat jalan'
    ],
    [
        'name' => 'approval-surat-jalan-reject',
        'description' => 'Reject surat jalan'
    ],
    [
        'name' => 'approval-surat-jalan-print',
        'description' => 'Cetak approval surat jalan'
    ],
    [
        'name' => 'approval-surat-jalan-export',
        'description' => 'Export approval surat jalan'
    ]
];

$created = 0;
$skipped = 0;

foreach ($permissions as $permData) {
    // Check if permission already exists
    $existing = App\Models\Permission::where('name', $permData['name'])->first();
    
    if ($existing) {
        echo "SKIP: {$permData['name']} already exists\n";
        $skipped++;
    } else {
        // Create new permission
        $permission = App\Models\Permission::create([
            'name' => $permData['name'],
            'description' => $permData['description'],
            'guard_name' => 'web'
        ]);
        
        echo "✅ CREATED: {$permData['name']} (ID: {$permission->id})\n";
        $created++;
    }
}

echo "\n=== SUMMARY ===\n";
echo "Created: $created permissions\n";
echo "Skipped: $skipped permissions\n";
echo "Total: " . ($created + $skipped) . " permissions processed\n";

echo "\n=== TESTING AFTER CREATION ===\n";

// Test if permissions can be found now
$testPermissions = [
    'approval-surat-jalan-view',
    'approval-surat-jalan-approve',
    'approval-surat-jalan-reject',
    'approval-surat-jalan-print',
    'approval-surat-jalan-export'
];

foreach ($testPermissions as $permName) {
    $permission = App\Models\Permission::where('name', $permName)->first();
    if ($permission) {
        echo "✅ FOUND: $permName (ID: {$permission->id})\n";
    } else {
        echo "❌ NOT FOUND: $permName\n";
    }
}

echo "\n=== PERMISSION CREATION COMPLETE ===\n";