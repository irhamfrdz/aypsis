<?php

use Illuminate\Support\Facades\Route;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🎯 FINAL VERIFICATION - TUJUAN KIRIM SYSTEM\n";
echo "============================================\n\n";

try {
    // 1. Check Route Exists
    echo "1. ✅ Route Check:\n";
    $routeExists = Route::has('tujuan-kirim.index');
    echo "   " . ($routeExists ? "✅" : "❌") . " Route 'tujuan-kirim.index' " . ($routeExists ? "EXISTS" : "NOT FOUND") . "\n";

    if ($routeExists) {
        $route = Route::getRoutes()->getByName('tujuan-kirim.index');
        echo "   📝 URI: " . $route->uri() . "\n";
        echo "   📝 Methods: " . implode(', ', $route->methods()) . "\n";
    }

    // 2. Check Permission Exists
    echo "\n2. ✅ Permission Check:\n";
    $permission = \Spatie\Permission\Models\Permission::where('name', 'master-tujuan-kirim-view')->first();
    if ($permission) {
        echo "   ✅ Permission 'master-tujuan-kirim-view' EXISTS (ID: {$permission->id})\n";
    } else {
        echo "   ❌ Permission 'master-tujuan-kirim-view' NOT FOUND!\n";
    }

    // 3. Check Admin User Has Permission
    echo "\n3. ✅ Admin User Check:\n";
    $adminUser = \App\Models\User::where('username', 'admin')->first();
    if ($adminUser) {
        echo "   ✅ Admin user found (ID: {$adminUser->id})\n";
        $hasPermission = $adminUser->hasPermissionTo('master-tujuan-kirim-view');
        echo "   " . ($hasPermission ? "✅" : "❌") . " Admin " . ($hasPermission ? "HAS" : "DOES NOT HAVE") . " tujuan-kirim permission\n";
    } else {
        echo "   ❌ Admin user NOT FOUND!\n";
    }

    // 4. Check Controller Exists
    echo "\n4. ✅ Controller Check:\n";
    $controllerExists = class_exists(\App\Http\Controllers\MasterTujuanKirimController::class);
    echo "   " . ($controllerExists ? "✅" : "❌") . " MasterTujuanKirimController " . ($controllerExists ? "EXISTS" : "NOT FOUND") . "\n";

    // 5. Check Model Exists
    echo "\n5. ✅ Model Check:\n";
    $modelExists = class_exists(\App\Models\MasterTujuanKirim::class);
    echo "   " . ($modelExists ? "✅" : "❌") . " MasterTujuanKirim Model " . ($modelExists ? "EXISTS" : "NOT FOUND") . "\n";

    // 6. Check Database Table Exists
    echo "\n6. ✅ Database Check:\n";
    try {
        $tableExists = \Illuminate\Support\Facades\Schema::hasTable('master_tujuan_kirim');
        echo "   " . ($tableExists ? "✅" : "❌") . " Table 'master_tujuan_kirim' " . ($tableExists ? "EXISTS" : "NOT FOUND") . "\n";

        if ($tableExists) {
            $count = \App\Models\MasterTujuanKirim::count();
            echo "   📊 Records in table: {$count}\n";
        }
    } catch (Exception $e) {
        echo "   ❌ Database error: " . $e->getMessage() . "\n";
    }

    // 7. Check View Files Exist
    echo "\n7. ✅ View Files Check:\n";
    $viewFiles = [
        'resources/views/master-tujuan-kirim/index.blade.php',
        'resources/views/master-tujuan-kirim/create.blade.php',
        'resources/views/master-tujuan-kirim/edit.blade.php',
        'resources/views/master-tujuan-kirim/show.blade.php'
    ];

    foreach ($viewFiles as $viewFile) {
        $exists = file_exists(__DIR__ . '/' . $viewFile);
        echo "   " . ($exists ? "✅" : "❌") . " " . basename($viewFile) . " " . ($exists ? "EXISTS" : "NOT FOUND") . "\n";
    }

    // 8. Check Layout File Menu
    echo "\n8. ✅ Layout Menu Check:\n";
    $layoutFile = __DIR__ . '/resources/views/layouts/app.blade.php';
    if (file_exists($layoutFile)) {
        $content = file_get_contents($layoutFile);
        $hasTujuanKirimMenu = strpos($content, 'master-tujuan-kirim-view') !== false &&
                              strpos($content, 'tujuan-kirim.index') !== false &&
                              strpos($content, 'Tujuan Kirim') !== false;

        echo "   " . ($hasTujuanKirimMenu ? "✅" : "❌") . " Sidebar menu " . ($hasTujuanKirimMenu ? "COMPLETE" : "INCOMPLETE") . "\n";
    } else {
        echo "   ❌ Layout file NOT FOUND!\n";
    }

    echo "\n🎉 FINAL RESULT:\n";
    echo "================\n";

    $allGood = $routeExists && $permission && $adminUser && $hasPermission &&
               $controllerExists && $modelExists && $tableExists && $hasTujuanKirimMenu;

    if ($allGood) {
        echo "✅ ALL SYSTEMS GO! Tujuan Kirim system is READY!\n\n";
        echo "🚀 NEXT STEPS:\n";
        echo "1. Close ALL browser tabs\n";
        echo "2. Open new browser window\n";
        echo "3. Go to: http://localhost:8000\n";
        echo "4. Login as admin\n";
        echo "5. Look for 'Master Data' in sidebar\n";
        echo "6. Click to expand Master Data dropdown\n";
        echo "7. Find 'Tujuan Kirim' menu item\n";
        echo "8. If still not visible, press Ctrl+F5 (hard refresh)\n";
    } else {
        echo "❌ Some components are missing. Please check the failed items above.\n";
    }

} catch (Exception $e) {
    echo "❌ Error during verification: " . $e->getMessage() . "\n";
}
