<?php
/**
 * Script untuk menganalisis permission di server Ubuntu
 * Khusus untuk menangani 691+ permissions
 */

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Models\Permission;

echo "╔═══════════════════════════════════════════════════════════════════════╗\n";
echo "║     ANALISIS PERMISSION SERVER UBUNTU (691 PERMISSIONS)               ║\n";
echo "╚═══════════════════════════════════════════════════════════════════════╝\n\n";

$allPermissions = Permission::orderBy('name')->get();
echo "📊 Total Permission di Server: " . $allPermissions->count() . "\n\n";

// 1. Analisis duplikasi permission (dot vs dash notation)
echo "🔍 Menganalisis duplikasi permission...\n";
$duplicates = [];
$uniqueBase = [];

foreach ($allPermissions as $perm) {
    // Convert to base format (normalize)
    $base = str_replace(['.', '-'], '_', $perm->name);

    if (!isset($uniqueBase[$base])) {
        $uniqueBase[$base] = [];
    }

    $uniqueBase[$base][] = [
        'id' => $perm->id,
        'name' => $perm->name,
        'description' => $perm->description
    ];
}

// Find duplicates
foreach ($uniqueBase as $base => $perms) {
    if (count($perms) > 1) {
        $duplicates[$base] = $perms;
    }
}

echo "   ❌ Found " . count($duplicates) . " sets of duplicate permissions\n";
echo "   📊 Total duplicate entries: " . array_sum(array_map('count', $duplicates)) . "\n\n";

// Show some examples
if (!empty($duplicates)) {
    echo "🔍 Contoh Duplikasi (10 pertama):\n";
    echo str_repeat("─", 75) . "\n";

    $count = 0;
    foreach ($duplicates as $base => $perms) {
        if ($count >= 10) break;

        echo "\n📂 Set " . ($count + 1) . ":\n";
        foreach ($perms as $perm) {
            echo "   - ID: " . str_pad($perm['id'], 4) . " │ " . $perm['name'] . "\n";
        }

        $count++;
    }
    echo "\n" . str_repeat("─", 75) . "\n\n";
}

// 2. Analisis permission yang digunakan di routes
echo "🔍 Menganalisis penggunaan di routes...\n";
$usedInRoutes = [];

foreach (Route::getRoutes() as $route) {
    $middleware = $route->middleware();

    foreach ($middleware as $mw) {
        if (strpos($mw, 'can:') === 0) {
            $permission = str_replace('can:', '', $mw);
            $usedInRoutes[$permission] = true;
        }
        if (strpos($mw, 'permission:') === 0) {
            $permission = str_replace('permission:', '', $mw);
            $usedInRoutes[$permission] = true;
        }
    }
}

echo "   ✅ Permission digunakan di routes: " . count($usedInRoutes) . "\n\n";

// 3. Analisis permission yang BENAR-BENAR digunakan
echo "🔍 Menganalisis permission yang AKTIF digunakan...\n";

$activePermissionIds = DB::table('user_permissions')
    ->select('permission_id')
    ->distinct()
    ->pluck('permission_id')
    ->toArray();

$activePermissionIdsFromRoles = DB::table('permission_role')
    ->select('permission_id')
    ->distinct()
    ->pluck('permission_id')
    ->toArray();

$allActiveIds = array_unique(array_merge($activePermissionIds, $activePermissionIdsFromRoles));

echo "   ✅ Permission yang assigned ke users: " . count($activePermissionIds) . "\n";
echo "   ✅ Permission yang assigned ke roles: " . count($activePermissionIdsFromRoles) . "\n";
echo "   ✅ Total permission yang AKTIF digunakan: " . count($allActiveIds) . "\n\n";

// 4. Permission yang TIDAK assigned ke siapapun
$unusedPermissions = Permission::whereNotIn('id', $allActiveIds)->get();

echo "╔═══════════════════════════════════════════════════════════════════════╗\n";
echo "║                    HASIL ANALISIS DETAIL                              ║\n";
echo "╚═══════════════════════════════════════════════════════════════════════╝\n\n";

echo "📊 STATISTIK:\n";
echo "   Total Permission:                    " . $allPermissions->count() . "\n";
echo "   Permission dengan duplikasi:         " . array_sum(array_map('count', $duplicates)) . "\n";
echo "   Permission digunakan di routes:      " . count($usedInRoutes) . "\n";
echo "   Permission assigned ke user/role:    " . count($allActiveIds) . "\n";
echo "   Permission TIDAK assigned:           " . $unusedPermissions->count() . "\n\n";

// 5. Kategori permission berdasarkan prefix
echo "📂 BREAKDOWN BY MODULE:\n";
echo str_repeat("─", 75) . "\n";

$modules = [];
foreach ($allPermissions as $perm) {
    $prefix = explode('-', $perm->name)[0];
    if (strpos($perm->name, '.') !== false) {
        $prefix = explode('.', $perm->name)[0];
    }

    if (!isset($modules[$prefix])) {
        $modules[$prefix] = 0;
    }
    $modules[$prefix]++;
}

arsort($modules);

foreach (array_slice($modules, 0, 20) as $module => $count) {
    echo "   " . str_pad($module, 30) . " : " . str_pad($count, 4, ' ', STR_PAD_LEFT) . " permissions\n";
}

if (count($modules) > 20) {
    echo "   ... dan " . (count($modules) - 20) . " module lainnya\n";
}

echo "\n" . str_repeat("─", 75) . "\n\n";

// 6. Rekomendasi pembersihan
echo "╔═══════════════════════════════════════════════════════════════════════╗\n";
echo "║                    REKOMENDASI PEMBERSIHAN                            ║\n";
echo "╚═══════════════════════════════════════════════════════════════════════╝\n\n";

$canDelete = $unusedPermissions->count();
$duplicateCount = array_sum(array_map('count', $duplicates)) - count($duplicates); // Keep 1 from each set

echo "✅ AMAN untuk dihapus:\n";
echo "   1. Permission yang TIDAK assigned: " . $canDelete . " permissions\n";
echo "   2. Permission duplikat: ~" . $duplicateCount . " permissions\n";
echo "   3. TOTAL yang bisa dihapus: ~" . ($canDelete + $duplicateCount) . " permissions\n\n";

$remaining = $allPermissions->count() - ($canDelete + $duplicateCount);
echo "📈 Permission yang akan tersisa: ~" . $remaining . " permissions\n\n";

echo "⚠️  CATATAN:\n";
echo "   - Selalu backup database sebelum menghapus\n";
echo "   - Permission duplikat perlu review manual\n";
echo "   - Prioritas: hapus yang TIDAK assigned dulu\n\n";

// 7. Export daftar untuk review
echo "💾 Membuat file export untuk review...\n";

// Export permission yang tidak assigned
$unassignedFile = base_path('server_unassigned_permissions.json');
file_put_contents($unassignedFile, json_encode($unusedPermissions->toArray(), JSON_PRETTY_PRINT));
echo "   ✅ " . basename($unassignedFile) . " - " . $unusedPermissions->count() . " permissions\n";

// Export permission duplikat
$duplicateFile = base_path('server_duplicate_permissions.json');
file_put_contents($duplicateFile, json_encode($duplicates, JSON_PRETTY_PRINT));
echo "   ✅ " . basename($duplicateFile) . " - " . count($duplicates) . " sets\n\n";

echo "✅ Analisis selesai!\n";
echo "\n📝 Langkah selanjutnya:\n";
echo "   1. Review file server_unassigned_permissions.json\n";
echo "   2. Review file server_duplicate_permissions.json\n";
echo "   3. Jalankan: php cleanup_server_permissions.php\n\n";
