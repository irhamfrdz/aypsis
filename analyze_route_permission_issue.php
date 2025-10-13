<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\DB;

echo "=== Checking Route Registration and Permission Format ===\n";

$user = User::find(1);
echo "User: {$user->username}\n\n";

// Check if the routes are correctly registered with proper naming
echo "=== Route Name Check ===\n";
$routes = [
    'master.tujuan.index',
    'master.tujuan-kegiatan-utama.index'
];

foreach ($routes as $routeName) {
    try {
        $url = route($routeName);
        echo "✅ Route {$routeName}: {$url}\n";
    } catch (Exception $e) {
        echo "❌ Route {$routeName}: ERROR - {$e->getMessage()}\n";
    }
}

echo "\n=== Permission Format Analysis ===\n";
echo "The issue might be permission format inconsistency:\n";
echo "- Master Tujuan uses: master-tujuan-view (dash format)\n";
echo "- Master Tujuan Kegiatan Utama uses: master-tujuan-kegiatan-utama-view (dash format)\n";

// Check database for exact permission names
echo "\n=== Database Permission Check ===\n";
$tujuanPerms = DB::table('permissions')
    ->where('name', 'like', 'master-tujuan%')
    ->where('name', 'not like', '%kegiatan%')
    ->pluck('name');

echo "Master Tujuan permissions in DB:\n";
foreach ($tujuanPerms as $perm) {
    echo "- {$perm}\n";
}

$tujuanKegiatanUtamaPerms = DB::table('permissions')
    ->where('name', 'like', 'master-tujuan-kegiatan-utama%')
    ->pluck('name');

echo "\nMaster Tujuan Kegiatan Utama permissions in DB:\n";
foreach ($tujuanKegiatanUtamaPerms as $perm) {
    echo "- {$perm}\n";
}

// Check user's actual permissions
echo "\n=== User's Actual Permissions ===\n";
$userPerms = DB::table('user_permissions')
    ->join('permissions', 'user_permissions.permission_id', '=', 'permissions.id')
    ->where('user_permissions.user_id', $user->id)
    ->where('permissions.name', 'like', 'master-tujuan%')
    ->pluck('permissions.name');

echo "User has these tujuan-related permissions:\n";
foreach ($userPerms as $perm) {
    echo "- {$perm}\n";
}

echo "\n=== Recommendation ===\n";
echo "Try accessing these URLs directly in browser:\n";
echo "1. http://localhost/master/tujuan (should work)\n";
echo "2. http://localhost/master/tujuan-kegiatan-utama (currently blocked)\n";
echo "\nCheck the exact error message and HTTP status code.\n";