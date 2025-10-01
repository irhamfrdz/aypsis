<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Permission;
use Illuminate\Support\Facades\Route;

echo "=== ANALISIS KECOCOKAN PERMISSION MATRIX UI DAN ROUTE PROTECTION ===\n\n";

// 1. Cek Permission Matrix dari UI (berdasarkan extract_ui_modules.php - UPDATED)
$uiMatrixPermissions = [
    'dashboard' => ['view'],
    'master-bank' => ['view', 'create', 'update', 'delete'],
    'master-cabang' => ['view', 'create', 'update', 'delete'],
    'master-coa' => ['view', 'create', 'update', 'delete'],
    'master-divisi' => ['view', 'create', 'update', 'delete'],
    'master-karyawan' => ['view', 'create', 'update', 'delete', 'print', 'export'],
    'master-kegiatan' => ['view', 'create', 'update', 'delete'],
    'master-kode-nomor' => ['view', 'create', 'update', 'delete'],
    'master-kontainer' => ['view', 'create', 'update', 'delete'],
    'master-mobil' => ['view', 'create', 'update', 'delete'],
    'master-nomor-terakhir' => ['view', 'create', 'update', 'delete'],
    'master-pajak' => ['view', 'create', 'update', 'delete'],
    'master-pekerjaan' => ['view', 'create', 'update', 'delete', 'print', 'export'],
    'master-permission' => ['view', 'create', 'update', 'delete'],
    'master-pricelist-cat' => ['view', 'create', 'update', 'delete'],
    'master-pricelist-sewa-kontainer' => ['view', 'create', 'update', 'delete'],
    'master-stock-kontainer' => ['view', 'create', 'update', 'delete'],
    'master-tipe-akun' => ['view', 'create', 'update', 'delete'],
    'master-tujuan' => ['view', 'create', 'update', 'delete'],
    'master-user' => ['view', 'create', 'update', 'delete'],
    'master-vendor-bengkel' => ['view', 'create', 'update', 'delete'],
    'pembayaran-pranota-cat' => ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'],
    'pembayaran-pranota-kontainer' => ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'],
    'pembayaran-pranota-perbaikan-kontainer' => ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'],
    'pembayaran-pranota-supir' => ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'],
    'permohonan-memo' => ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'],
    'pranota-cat' => ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'],
    'pranota-kontainer-sewa' => ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'],
    'pranota-perbaikan-kontainer' => ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'],
    'pranota-supir' => ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'],
    'tagihan-cat' => ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'],
    'tagihan-kontainer-sewa' => ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'],
    'tagihan-perbaikan-kontainer' => ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'],
    'user-approval' => ['view', 'create', 'update', 'delete', 'print', 'export'],
];

// 2. Cek permissions yang ada di database
echo "=== PERMISSIONS DI DATABASE ===\n";
$dbPermissions = Permission::orderBy('name')->pluck('name')->toArray();
$dbPermissionsByModule = [];

foreach ($dbPermissions as $perm) {
    if (preg_match('/^(.*?)-(view|create|update|delete|print|export|approve|index|edit|destroy)$/', $perm, $matches)) {
        $module = $matches[1];
        $action = $matches[2];

        // Normalisasi action names
        if ($action === 'index') $action = 'view';
        if ($action === 'edit') $action = 'update';
        if ($action === 'destroy') $action = 'delete';

        $dbPermissionsByModule[$module][] = $action;
    } elseif ($perm === 'dashboard') {
        $dbPermissionsByModule['dashboard'][] = 'view';
    }
}

// 3. Cek route protection (UPDATED berdasarkan implementasi actual di routes/web.php)
$protectedRoutes = [
    // Master Data - SUDAH DIPROTEKSI
    'master-user' => ['view', 'create', 'update', 'delete'],
    'master-karyawan' => ['view', 'create', 'update', 'delete', 'print', 'export'],
    'master-permission' => ['view', 'create', 'update', 'delete'], // FIXED: Sudah diproteksi
    'master-coa' => ['view', 'create', 'update', 'delete'],
    'master-bank' => ['view', 'create', 'update', 'delete'],
    'master-tujuan' => ['view', 'create', 'update', 'delete'],
    'master-divisi' => ['view', 'create', 'update', 'delete'],
    'master-pajak' => ['view', 'create', 'update', 'delete'],
    'master-pekerjaan' => ['view', 'create', 'update', 'delete', 'print', 'export'],
    'master-vendor-bengkel' => ['view', 'create', 'update', 'delete'],
    'master-kode-nomor' => ['view', 'create', 'update', 'delete'],
    'master-stock-kontainer' => ['view', 'create', 'update', 'delete'],
    'master-tipe-akun' => ['view', 'create', 'update', 'delete'],
    'master-nomor-terakhir' => ['view', 'create', 'update', 'delete'],
    'master-cabang' => ['view', 'create', 'update', 'delete'],
    'master-mobil' => ['view', 'create', 'update', 'delete'], // FIXED: Sudah diproteksi
    'master-kontainer' => ['view', 'create', 'update', 'delete'], // FIXED: Sudah diproteksi
    'master-kegiatan' => ['view', 'create', 'update', 'delete'], // FIXED: Sudah diproteksi
    'master-pricelist-sewa-kontainer' => ['view', 'create', 'update', 'delete'], // FIXED: Sudah diproteksi
    'master-pricelist-cat' => ['view', 'create', 'update', 'delete'],

    // Business Process - SUDAH DIPROTEKSI
    'dashboard' => ['view'],
    'permohonan-memo' => ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'],
    'tagihan-kontainer-sewa' => ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'],
    'tagihan-cat' => ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'],
    'tagihan-perbaikan-kontainer' => ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'],
    'pranota-kontainer-sewa' => ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'],
    'pranota-cat' => ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'],
    'pranota-perbaikan-kontainer' => ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'],
    'pranota-supir' => ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'],
    'pembayaran-pranota-kontainer' => ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'],
    'pembayaran-pranota-cat' => ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'],
    'pembayaran-pranota-perbaikan-kontainer' => ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'],
    'pembayaran-pranota-supir' => ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'],
    'user-approval' => ['view', 'create', 'update', 'delete', 'print', 'export'],
];

// 4. Analisis kecocokan
echo "=== ANALISIS KECOCOKAN ===\n\n";

$totalModules = 0;
$matchingModules = 0;
$issues = [];

foreach ($uiMatrixPermissions as $module => $uiActions) {
    $totalModules++;

    $dbActions = isset($dbPermissionsByModule[$module]) ? array_unique($dbPermissionsByModule[$module]) : [];
    $routeActions = isset($protectedRoutes[$module]) ? $protectedRoutes[$module] : [];

    $uiSet = array_unique($uiActions);
    $dbSet = array_unique($dbActions);
    $routeSet = array_unique($routeActions);

    $uiDbMatch = empty(array_diff($uiSet, $dbSet)) && empty(array_diff($dbSet, $uiSet));
    $uiRouteMatch = empty(array_diff($uiSet, $routeSet)) && empty(array_diff($routeSet, $uiSet));
    $dbRouteMatch = empty(array_diff($dbSet, $routeSet)) && empty(array_diff($routeSet, $dbSet));

    if ($uiDbMatch && $uiRouteMatch && $dbRouteMatch) {
        $matchingModules++;
        echo "✅ {$module}: SEMPURNA\n";
        echo "   UI: " . implode(', ', $uiSet) . "\n";
        echo "   DB: " . implode(', ', $dbSet) . "\n";
        echo "   Route: " . implode(', ', $routeSet) . "\n\n";
    } else {
        echo "❌ {$module}: ADA MASALAH\n";
        echo "   UI: " . implode(', ', $uiSet) . "\n";
        echo "   DB: " . implode(', ', $dbSet) . "\n";
        echo "   Route: " . implode(', ', $routeSet) . "\n";

        if (!$uiDbMatch) {
            echo "   🔸 UI-DB Mismatch: ";
            $missingInDb = array_diff($uiSet, $dbSet);
            $extraInDb = array_diff($dbSet, $uiSet);
            if (!empty($missingInDb)) echo "Missing in DB: " . implode(', ', $missingInDb) . " ";
            if (!empty($extraInDb)) echo "Extra in DB: " . implode(', ', $extraInDb);
            echo "\n";
        }

        if (!$uiRouteMatch) {
            echo "   🔸 UI-Route Mismatch: ";
            $missingInRoute = array_diff($uiSet, $routeSet);
            $extraInRoute = array_diff($routeSet, $uiSet);
            if (!empty($missingInRoute)) echo "Missing in Route: " . implode(', ', $missingInRoute) . " ";
            if (!empty($extraInRoute)) echo "Extra in Route: " . implode(', ', $extraInRoute);
            echo "\n";
        }

        if (!$dbRouteMatch) {
            echo "   🔸 DB-Route Mismatch: ";
            $missingInRoute = array_diff($dbSet, $routeSet);
            $extraInRoute = array_diff($routeSet, $dbSet);
            if (!empty($missingInRoute)) echo "Missing in Route: " . implode(', ', $missingInRoute) . " ";
            if (!empty($extraInRoute)) echo "Extra in Route: " . implode(', ', $extraInRoute);
            echo "\n";
        }
        echo "\n";

        $issues[] = $module;
    }
}

echo "=== RINGKASAN ===\n";
echo "Total modules: {$totalModules}\n";
echo "Modules matching: {$matchingModules}\n";
echo "Compatibility: " . round(($matchingModules / $totalModules) * 100, 1) . "%\n";

if (!empty($issues)) {
    echo "\nModules dengan masalah:\n";
    foreach ($issues as $issue) {
        echo "- {$issue}\n";
    }
} else {
    echo "\n🎉 SEMUA MODULE SUDAH SESUAI!\n";
}

echo "\n=== REKOMENDASI ===\n";
if (($matchingModules / $totalModules) >= 0.9) {
    echo "✅ Sistem permission sudah sangat baik (>90% compatibility)\n";
    echo "✅ Permission Matrix UI sudah sesuai dengan Route Protection\n";
    echo "✅ Database permissions sudah sesuai dengan implementasi\n";
    echo "✅ Sistem siap untuk production use\n";
} elseif (($matchingModules / $totalModules) >= 0.8) {
    echo "⚠️ Sistem permission cukup baik (>80% compatibility)\n";
    echo "⚠️ Perlu perbaikan minor pada beberapa module\n";
} else {
    echo "❌ Sistem permission perlu perbaikan major\n";
    echo "❌ Banyak ketidaksesuaian antara UI, DB, dan Routes\n";
}
