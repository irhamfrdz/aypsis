<?php

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

echo "=== FINAL TEST: USER ADMIN ACCESS PERMISSIONS ===\n\n";

$admin = User::where('username', 'admin')->with('permissions')->first();

if ($admin) {
    echo "Testing access to key menu items for user admin:\n\n";

    $menuTests = [
        'Dashboard' => 'dashboard',
        'Master Cabang (View)' => 'master-cabang-view',
        'Master Cabang (Create)' => 'master-cabang-create',
        'Master Cabang (Update)' => 'master-cabang-update',
        'Master Cabang (Delete)' => 'master-cabang-delete',
        'Master COA (View)' => 'master-coa-view',
        'Master COA (Create)' => 'master-coa-create',
        'Master COA (Update)' => 'master-coa-update',
        'Master COA (Delete)' => 'master-coa-delete',
        'Master Karyawan (View)' => 'master-karyawan-view',
        'Master User (View)' => 'master-user-view',
        'Master Bank (View)' => 'master-bank-view',
        'Master Divisi (View)' => 'master-divisi-view'
    ];

    $totalTests = count($menuTests);
    $passedTests = 0;

    foreach ($menuTests as $menuName => $permission) {
        $canAccess = $admin->can($permission);
        $status = $canAccess ? '✅ ACCESS GRANTED' : '❌ ACCESS DENIED';
        echo sprintf("%-30s: %s\n", $menuName, $status);

        if ($canAccess) {
            $passedTests++;
        }
    }

    echo "\n" . str_repeat("=", 50) . "\n";
    echo "SUMMARY: {$passedTests}/{$totalTests} tests passed\n";

    if ($passedTests === $totalTests) {
        echo "🎉 ALL PERMISSIONS WORKING! User admin can access all required menus.\n";
    } else {
        echo "⚠️  Some permissions still need attention.\n";
    }

    echo "\nUser admin has " . $admin->permissions->count() . " total permissions.\n";

} else {
    echo "❌ User admin not found!\n";
}
