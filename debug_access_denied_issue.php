<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Route;

echo "=== Debugging Access Denied Issue for Master Tujuan Kegiatan Utama ===\n\n";

$user = User::find(1);
if (!$user) {
    echo "❌ User with ID 1 not found\n";
    exit;
}

echo "User: {$user->username}\n\n";

// Compare route middleware for both modules
echo "=== Route Comparison ===\n";

// Master Tujuan routes
$tujuanIndexRoute = Route::getRoutes()->getByName('master.tujuan.index');
if ($tujuanIndexRoute) {
    echo "✅ Master Tujuan Index Route:\n";
    echo "   URL: " . $tujuanIndexRoute->uri() . "\n";
    echo "   Middlewares: " . implode(', ', $tujuanIndexRoute->middleware()) . "\n\n";
} else {
    echo "❌ Master Tujuan Index Route not found\n\n";
}

// Master Tujuan Kegiatan Utama routes
$tujuanKegiatanUtamaIndexRoute = Route::getRoutes()->getByName('master.tujuan-kegiatan-utama.index');
if ($tujuanKegiatanUtamaIndexRoute) {
    echo "✅ Master Tujuan Kegiatan Utama Index Route:\n";
    echo "   URL: " . $tujuanKegiatanUtamaIndexRoute->uri() . "\n";
    echo "   Middlewares: " . implode(', ', $tujuanKegiatanUtamaIndexRoute->middleware()) . "\n\n";
} else {
    echo "❌ Master Tujuan Kegiatan Utama Index Route not found\n\n";
}

// Check permission differences
echo "=== Permission Check ===\n";
$tujuanPerms = [
    'master-tujuan-view',
    'master-tujuan.view'
];

$tujuanKegiatanUtamaPerms = [
    'master-tujuan-kegiatan-utama-view',
    'master-tujuan-kegiatan-utama.view'
];

echo "Master Tujuan permissions:\n";
foreach ($tujuanPerms as $perm) {
    $hasPerm = $user->can($perm);
    echo ($hasPerm ? "✅" : "❌") . " {$perm}: " . ($hasPerm ? "YES" : "NO") . "\n";
}

echo "\nMaster Tujuan Kegiatan Utama permissions:\n";
foreach ($tujuanKegiatanUtamaPerms as $perm) {
    $hasPerm = $user->can($perm);
    echo ($hasPerm ? "✅" : "❌") . " {$perm}: " . ($hasPerm ? "YES" : "NO") . "\n";
}

echo "\n=== Analyzing the Issue ===\n";
echo "Possible causes of access denied:\n";
echo "1. Route middleware mismatch\n";
echo "2. Permission format differences\n";
echo "3. Route not properly registered\n";
echo "4. Cache issues\n";
echo "5. Middleware execution order\n";

// Check the exact URLs being accessed
echo "\n=== URL Access Test ===\n";
echo "Master Tujuan URL: /master/tujuan\n";
echo "Master Tujuan Kegiatan Utama URL: /master/tujuan-kegiatan-utama\n";

echo "\n=== Recommendation ===\n";
echo "Check browser network tab to see the exact HTTP status code (403, 404, etc.)\n";
echo "Check Laravel logs for detailed error messages\n";