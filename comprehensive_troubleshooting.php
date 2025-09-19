<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Permission;

echo "=== COMPREHENSIVE TROUBLESHOOTING ===\n\n";

// 1. Check if user is logged in
echo "1. AUTHENTICATION CHECK:\n";
if (Auth::check()) {
    $user = Auth::user();
    echo "✅ User logged in: {$user->username} (ID: {$user->id})\n";

    // Check if user has karyawan
    if ($user->karyawan) {
        echo "✅ User has karyawan: {$user->karyawan->nama_lengkap}\n";
    } else {
        echo "❌ User missing karyawan record\n";
    }
} else {
    echo "❌ No user logged in\n";
    exit;
}

echo "\n2. PERMISSIONS CHECK:\n";
// Check user's permissions
$userPermissions = $user->permissions->pluck('name')->toArray();
echo "User has " . count($userPermissions) . " permissions\n";

$requiredPermissions = [
    'master-kode-nomor-view',
    'master-kode-nomor',
    'master-kode-nomor.view'
];

foreach ($requiredPermissions as $perm) {
    $hasPermission = in_array($perm, $userPermissions);
    echo ($hasPermission ? "✅" : "❌") . " Has permission: $perm\n";
}

echo "\n3. ROUTE ACCESSIBILITY CHECK:\n";
// Test if route is accessible
try {
    $route = Route::getRoutes()->getByName('master.kode-nomor.index');
    if ($route) {
        echo "✅ Route exists: master.kode-nomor.index\n";
        echo "   URI: {$route->uri()}\n";
        echo "   Methods: " . implode(', ', $route->methods()) . "\n";

        // Check middleware
        $middleware = $route->middleware();
        echo "   Middleware: " . implode(', ', $middleware) . "\n";
    } else {
        echo "❌ Route not found\n";
    }
} catch (Exception $e) {
    echo "❌ Route error: " . $e->getMessage() . "\n";
}

echo "\n4. CONTROLLER CHECK:\n";
// Test if controller exists and is accessible
$controllerClass = 'App\\Http\\Controllers\\KodeNomorController';
if (class_exists($controllerClass)) {
    echo "✅ Controller class exists: $controllerClass\n";

    // Check if index method exists
    if (method_exists($controllerClass, 'index')) {
        echo "✅ Index method exists\n";
    } else {
        echo "❌ Index method missing\n";
    }
} else {
    echo "❌ Controller class not found\n";
}

echo "\n5. VIEW CHECK:\n";
// Check if view files exist
$viewPaths = [
    'master.kode-nomor.index',
    'master.kode-nomor.create',
    'master.kode-nomor.show',
    'master.kode-nomor.edit'
];

foreach ($viewPaths as $viewPath) {
    $viewFile = resource_path("views/" . str_replace('.', '/', $viewPath) . ".blade.php");
    if (file_exists($viewFile)) {
        echo "✅ View exists: $viewPath\n";
    } else {
        echo "❌ View missing: $viewPath\n";
    }
}

echo "\n6. DATABASE CHECK:\n";
// Check database table
try {
    $tableExists = DB::getSchemaBuilder()->hasTable('kode_nomor');
    echo ($tableExists ? "✅" : "❌") . " Table 'kode_nomor' exists\n";

    if ($tableExists) {
        $count = DB::table('kode_nomor')->count();
        echo "   Records: $count\n";
    }
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
}

echo "\n7. MODEL CHECK:\n";
// Check if model exists and works
$modelClass = 'App\\Models\\KodeNomor';
if (class_exists($modelClass)) {
    echo "✅ Model class exists: $modelClass\n";

    try {
        // Try to create a query
        $query = $modelClass::query();
        echo "✅ Model query works\n";
    } catch (Exception $e) {
        echo "❌ Model query error: " . $e->getMessage() . "\n";
    }
} else {
    echo "❌ Model class not found\n";
}

echo "\n=== FINAL RECOMMENDATIONS ===\n";
echo "1. Hard refresh browser (Ctrl+F5 or Cmd+Shift+R)\n";
echo "2. Clear browser cache completely\n";
echo "3. Logout and login again\n";
echo "4. Check if you're logged in as admin user\n";
echo "5. Verify user has 'master-kode-nomor-view' permission\n";
echo "6. Try accessing URL directly: /master/kode-nomor\n";
