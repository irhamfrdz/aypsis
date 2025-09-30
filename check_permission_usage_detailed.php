<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\DB;

echo "=== ANALISIS PENGGUNAAN PERMISSION ===\n";

$totalPermissions = Permission::count();
echo "Total permission di database: $totalPermissions\n\n";

// 1. Permission yang digunakan oleh user
$usedPermissions = DB::table('user_permissions')
    ->select('permission_id')
    ->distinct()
    ->pluck('permission_id')
    ->toArray();

$usedCount = count($usedPermissions);
$unusedCount = $totalPermissions - $usedCount;

echo "=== PENGGUNAAN PERMISSION ===\n";
echo "Permission yang digunakan user: $usedCount\n";
echo "Permission yang TIDAK digunakan: $unusedCount\n";
echo "Persentase penggunaan: " . round(($usedCount / $totalPermissions) * 100, 1) . "%\n\n";

// 2. Permission yang tidak pernah digunakan
if ($unusedCount > 0) {
    echo "=== PERMISSION YANG TIDAK DIGUNAKAN ===\n";
    $unusedPermissions = Permission::whereNotIn('id', $usedPermissions)
        ->orderBy('name')
        ->get();

    // Kelompokkan berdasarkan kategori
    $unusedCategories = [];
    foreach ($unusedPermissions as $perm) {
        $category = explode('-', $perm->name)[0];
        if (!isset($unusedCategories[$category])) {
            $unusedCategories[$category] = [];
        }
        $unusedCategories[$category][] = $perm->name;
    }

    foreach ($unusedCategories as $category => $perms) {
        echo "$category: " . count($perms) . " permissions\n";
        // Tampilkan beberapa contoh
        $examples = array_slice($perms, 0, 3);
        foreach ($examples as $example) {
            echo "  - $example\n";
        }
        if (count($perms) > 3) {
            echo "  ... dan " . (count($perms) - 3) . " lainnya\n";
        }
    }
    echo "\n";
}

// 3. Analisis permission per user
echo "=== PENGGUNAAN PERMISSION PER USER ===\n";
$users = User::with('permissions')->get();

$userPermissionStats = [];
foreach ($users as $user) {
    $permissionCount = $user->permissions->count();
    $userPermissionStats[] = [
        'username' => $user->username,
        'permission_count' => $permissionCount,
        'permissions' => $user->permissions->pluck('name')->toArray()
    ];
}

// Sort by permission count descending
usort($userPermissionStats, function($a, $b) {
    return $b['permission_count'] - $a['permission_count'];
});

echo "Top 5 users dengan permission terbanyak:\n";
for ($i = 0; $i < min(5, count($userPermissionStats)); $i++) {
    $user = $userPermissionStats[$i];
    echo "- {$user['username']}: {$user['permission_count']} permissions\n";
}

echo "\nUsers dengan permission paling sedikit:\n";
$leastPermitted = array_filter($userPermissionStats, function($user) {
    return $user['permission_count'] <= 5;
});

foreach ($leastPermitted as $user) {
    echo "- {$user['username']}: {$user['permission_count']} permissions\n";
}

// 4. Cek permission duplikat atau tidak valid
echo "\n=== CEK PERMISSION BERMASALAH ===\n";

// Permission dengan nama duplikat
$duplicateNames = DB::table('permissions')
    ->select('name')
    ->groupBy('name')
    ->havingRaw('COUNT(*) > 1')
    ->get();

echo "Permission dengan nama duplikat: " . $duplicateNames->count() . "\n";

if ($duplicateNames->count() > 0) {
    foreach ($duplicateNames as $dup) {
        $perms = Permission::where('name', $dup->name)->get();
        echo "- {$dup->name}: ada {$perms->count()} record\n";
    }
}

// 5. Permission yang mungkin deprecated (berdasarkan pola nama)
echo "\n=== PERMISSION YANG MUNGKIN DEPRECATED ===\n";
$deprecatedPatterns = [
    'old-',
    'temp-',
    'test-',
    'backup-',
    'deprecated-'
];

$deprecatedCount = 0;
foreach ($deprecatedPatterns as $pattern) {
    $count = Permission::where('name', 'like', $pattern . '%')->count();
    if ($count > 0) {
        echo "Pattern '$pattern': $count permissions\n";
        $deprecatedCount += $count;
    }
}

if ($deprecatedCount === 0) {
    echo "Tidak ada permission dengan pola deprecated yang jelas\n";
}

// 6. Ringkasan
echo "\n=== RINGKASAN ===\n";
echo "Total permission: $totalPermissions\n";
echo "Permission digunakan: $usedCount (" . round(($usedCount / $totalPermissions) * 100, 1) . "%)\n";
echo "Permission tidak digunakan: $unusedCount (" . round(($unusedCount / $totalPermissions) * 100, 1) . "%)\n";
echo "Permission duplikat: " . $duplicateNames->count() . "\n";
echo "Permission deprecated: $deprecatedCount\n";

if ($unusedCount > 100) {
    echo "\n⚠️  PERINGATAN: Ada $unusedCount permission yang tidak digunakan!\n";
    echo "   Ini bisa menjadi masalah keamanan dan maintenance.\n";
} elseif ($unusedCount > 0) {
    echo "\nℹ️  Ada beberapa permission yang belum digunakan, tapi masih dalam batas wajar.\n";
} else {
    echo "\n✅ Semua permission digunakan - sistem optimal!\n";
}
