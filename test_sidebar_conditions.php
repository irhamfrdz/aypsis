<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

echo "=== TESTING SIDEBAR CONDITIONS FOR USER ADMIN ===\n";

$userAdmin = User::where('username', 'user_admin')->first();

if ($userAdmin) {
    echo "User: " . $userAdmin->username . "\n";

    // Simulate the same conditions as in sidebar
    $user = $userAdmin;
    $hasKaryawan = $user && $user->karyawan;
    $isAdmin = $user && method_exists($user, 'hasRole') && $user->hasRole('admin');

    echo "Has karyawan: " . ($hasKaryawan ? 'YES' : 'NO') . "\n";
    echo "Is admin: " . ($isAdmin ? 'YES' : 'NO') . "\n";

    // Check master permissions
    $hasMasterPermissions = $user && (
        $user->can('master-permission-view') ||
        $user->can('master-cabang-view') ||
        $user->can('master-pengirim-view') ||
        $user->can('master-jenis-barang-view') ||
        $user->can('master-term-view') ||
        $user->can('master-coa-view') ||
        $user->can('master-kode-nomor-view') ||
        $user->can('master-nomor-terakhir-view') ||
        $user->can('master-tipe-akun-view') ||
        $user->can('master-tujuan-view') ||
        $user->can('master-tujuan-kirim-view') ||
        $user->can('master-kegiatan-view')
    );

    echo "Has master permissions: " . ($hasMasterPermissions ? 'YES' : 'NO') . "\n";

    $showSidebar = $hasKaryawan || $isAdmin || $user;
    echo "Show sidebar: " . ($showSidebar ? 'YES' : 'NO') . "\n";

    $showMasterSection = $isAdmin || $hasMasterPermissions;
    echo "Show master section: " . ($showMasterSection ? 'YES' : 'NO') . "\n";

    // Test specific permission
    $canViewTujuanKirim = $user && $user->can('master-tujuan-kirim-view');
    echo "Can view tujuan kirim: " . ($canViewTujuanKirim ? 'YES' : 'NO') . "\n";

    // Check individual permissions
    echo "\n=== INDIVIDUAL PERMISSION CHECKS ===\n";
    $permissions = [
        'master-permission-view',
        'master-cabang-view',
        'master-tujuan-kirim-view',
        'master-tujuan-view',
        'master-kegiatan-view'
    ];

    foreach ($permissions as $perm) {
        $has = $user->can($perm);
        echo "{$perm}: " . ($has ? 'YES' : 'NO') . "\n";
    }

} else {
    echo "User admin not found\n";
}
