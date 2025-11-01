<?php



require 'vendor/autoload.php';require_once 'vendor/autoload.php';



$app = require_once 'bootstrap/app.php';use App\Models\Permission;

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Initialize Laravel

echo "=== ANALISIS PERMISSION SISTEM VS DATABASE ===" . PHP_EOL;$app = require_once 'bootstrap/app.php';

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Baca file routes

$routesFile = file_get_contents('routes/web.php');$permissions = Permission::all()->pluck('name')->toArray();

echo 'Total permissions: ' . count($permissions) . PHP_EOL;

// Extract semua permission dari middleware can: di routesecho PHP_EOL;

preg_match_all("/can:([a-zA-Z0-9\-\._]+)/", $routesFile, $matches);

$routePermissions = array_unique($matches[1]);echo '=== DAFTAR LENGKAP PERMISSION ===' . PHP_EOL;

foreach ($permissions as $index => $permission) {

echo "📋 PERMISSIONS DARI ROUTES (" . count($routePermissions) . " total):" . PHP_EOL;    echo str_pad($index + 1, 3, ' ', STR_PAD_LEFT) . '. ' . $permission . PHP_EOL;

sort($routePermissions);}



// Group permissions by module for better analysisecho PHP_EOL;

$groupedRoutePermissions = [];echo '=== ANALISIS KATEGORI PERMISSION ===' . PHP_EOL;

foreach ($routePermissions as $perm) {

    $module = explode('-', $perm)[0];// Kategorikan permission berdasarkan pola

    if (strpos($perm, '.') !== false) {$categories = [

        $module = explode('.', $perm)[0];    'master' => [],

    }    'tagihan' => [],

    $groupedRoutePermissions[$module][] = $perm;    'pranota' => [],

}    'pembayaran' => [],

    'permohonan' => [],

$moduleCount = 0;    'user' => [],

foreach ($groupedRoutePermissions as $module => $perms) {    'dashboard' => [],

    if ($moduleCount < 10) { // Limit display    'other' => []

        echo "  📂 {$module} (" . count($perms) . " permissions)" . PHP_EOL;];

        $permCount = 0;

        foreach ($perms as $perm) {foreach ($permissions as $permission) {

            if ($permCount < 5) {    if (strpos($permission, 'master-') === 0) {

                echo "    - {$perm}" . PHP_EOL;        $categories['master'][] = $permission;

            }    } elseif (strpos($permission, 'tagihan-') === 0) {

            $permCount++;        $categories['tagihan'][] = $permission;

        }    } elseif (strpos($permission, 'pranota') !== false) {

        if (count($perms) > 5) {        $categories['pranota'][] = $permission;

            echo "    ... dan " . (count($perms) - 5) . " lainnya" . PHP_EOL;    } elseif (strpos($permission, 'pembayaran') !== false) {

        }        $categories['pembayaran'][] = $permission;

        echo PHP_EOL;    } elseif (strpos($permission, 'permohonan') !== false) {

    }        $categories['permohonan'][] = $permission;

    $moduleCount++;    } elseif (strpos($permission, 'user') !== false) {

}        $categories['user'][] = $permission;

    } elseif (strpos($permission, 'dashboard') !== false) {

if ($moduleCount > 10) {        $categories['dashboard'][] = $permission;

    echo "  ... dan " . ($moduleCount - 10) . " module lainnya" . PHP_EOL . PHP_EOL;    } else {

}        $categories['other'][] = $permission;

    }

// Get all permissions from database}

$dbPermissions = App\Models\Permission::pluck('name')->toArray();

sort($dbPermissions);foreach ($categories as $category => $perms) {

    echo strtoupper($category) . ': ' . count($perms) . ' permissions' . PHP_EOL;

echo "🗄️  PERMISSIONS DARI DATABASE (" . count($dbPermissions) . " total)" . PHP_EOL . PHP_EOL;    if (count($perms) <= 10) { // Show all if <= 10

        foreach ($perms as $perm) {

echo "🔍 ANALISIS PERBEDAAN:" . PHP_EOL;            echo '  - ' . $perm . PHP_EOL;

        }

// Find permissions in routes but not in database    } else { // Show first 5 and last 5 if > 10

$missingInDb = array_diff($routePermissions, $dbPermissions);        for ($i = 0; $i < 5; $i++) {

echo "❌ MISSING IN DATABASE (" . count($missingInDb) . " permissions):" . PHP_EOL;            echo '  - ' . $perms[$i] . PHP_EOL;

foreach ($missingInDb as $perm) {        }

    echo "  - {$perm}" . PHP_EOL;        echo '  ... (' . (count($perms) - 10) . ' more permissions) ...' . PHP_EOL;

}        for ($i = count($perms) - 5; $i < count($perms); $i++) {

            echo '  - ' . $perms[$i] . PHP_EOL;

echo PHP_EOL . "📊 SUMMARY:" . PHP_EOL;        }

echo "  - Routes Permissions: " . count($routePermissions) . PHP_EOL;    }

echo "  - Database Permissions: " . count($dbPermissions) . PHP_EOL;    echo PHP_EOL;

echo "  - Missing in DB: " . count($missingInDb) . PHP_EOL;}


if (count($missingInDb) > 0) {
    echo PHP_EOL . "⚠️  ADA PERMISSION YANG HILANG DI DATABASE!" . PHP_EOL;
    echo "Perlu menambahkan " . count($missingInDb) . " permission ke database." . PHP_EOL;
} else {
    echo PHP_EOL . "✅ SEMUA PERMISSION ROUTES SUDAH ADA DI DATABASE!" . PHP_EOL;
}

echo PHP_EOL . "=== SELESAI ===" . PHP_EOL;