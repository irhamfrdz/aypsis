<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Permission;

// Create the approval-dashboard-ii permission if it doesn't exist
$permission = Permission::firstOrCreate(
    ['name' => 'approval-dashboard-ii'],
    ['description' => 'Akses Dashboard Approval Tugas II']
);

echo "Permission 'approval-dashboard-ii' berhasil dibuat atau sudah ada (ID: {$permission->id})\n";

// Give it to admin user (ID: 1)
$user = App\Models\User::find(1);
if ($user) {
    if (!$user->permissions()->where('name', 'approval-dashboard-ii')->exists()) {
        $user->permissions()->attach($permission);
        echo "Permission 'approval-dashboard-ii' berhasil diberikan ke user admin\n";
    } else {
        echo "User admin sudah memiliki permission 'approval-dashboard-ii'\n";
    }

    // Test permission
    echo "User can('approval-dashboard-ii'): " . ($user->can('approval-dashboard-ii') ? 'YES' : 'NO') . "\n";
} else {
    echo "User admin tidak ditemukan\n";
}
