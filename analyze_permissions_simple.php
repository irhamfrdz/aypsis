<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== ANALISIS PERMISSION SISTEM VS DATABASE ===" . PHP_EOL;

// Baca file routes
$routesFile = file_get_contents('routes/web.php');

// Extract semua permission dari middleware can: di routes
preg_match_all("/can:([a-zA-Z0-9\-\._]+)/", $routesFile, $matches);
$routePermissions = array_unique($matches[1]);

echo "📋 PERMISSIONS DARI ROUTES (" . count($routePermissions) . " total):" . PHP_EOL;
sort($routePermissions);

// Tampilkan beberapa contoh
$sampleCount = 0;
foreach ($routePermissions as $perm) {
    if ($sampleCount < 20) {
        echo "  - {$perm}" . PHP_EOL;
    }
    $sampleCount++;
}
if (count($routePermissions) > 20) {
    echo "  ... dan " . (count($routePermissions) - 20) . " lainnya" . PHP_EOL;
}

// Get all permissions from database
$dbPermissions = App\Models\Permission::pluck('name')->toArray();
sort($dbPermissions);

echo PHP_EOL . "🗄️  PERMISSIONS DARI DATABASE (" . count($dbPermissions) . " total)" . PHP_EOL . PHP_EOL;

echo "🔍 ANALISIS PERBEDAAN:" . PHP_EOL;

// Find permissions in routes but not in database
$missingInDb = array_diff($routePermissions, $dbPermissions);
echo "❌ MISSING IN DATABASE (" . count($missingInDb) . " permissions):" . PHP_EOL;
foreach ($missingInDb as $perm) {
    echo "  - {$perm}" . PHP_EOL;
}

echo PHP_EOL . "📊 SUMMARY:" . PHP_EOL;
echo "  - Routes Permissions: " . count($routePermissions) . PHP_EOL;
echo "  - Database Permissions: " . count($dbPermissions) . PHP_EOL;
echo "  - Missing in DB: " . count($missingInDb) . PHP_EOL;

if (count($missingInDb) > 0) {
    echo PHP_EOL . "⚠️  ADA PERMISSION YANG HILANG DI DATABASE!" . PHP_EOL;
    echo "Perlu menambahkan " . count($missingInDb) . " permission ke database." . PHP_EOL;
} else {
    echo PHP_EOL . "✅ SEMUA PERMISSION ROUTES SUDAH ADA DI DATABASE!" . PHP_EOL;
}

echo PHP_EOL . "=== SELESAI ===" . PHP_EOL;