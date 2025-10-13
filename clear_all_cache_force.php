<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

echo "🔄 FORCE CLEAR ALL CACHE - TUJUAN KIRIM DEBUG\n";
echo "==============================================\n\n";

try {
    // Clear all caches
    echo "1. ✅ Clearing Route Cache...\n";
    \Illuminate\Support\Facades\Artisan::call('route:clear');
    
    echo "2. ✅ Clearing View Cache...\n";
    \Illuminate\Support\Facades\Artisan::call('view:clear');
    
    echo "3. ✅ Clearing Config Cache...\n";
    \Illuminate\Support\Facades\Artisan::call('config:clear');
    
    echo "4. ✅ Clearing Application Cache...\n";
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    
    echo "5. ✅ Optimizing Application...\n";
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
    
    echo "\n🎯 DEBUGGING MENU TUJUAN KIRIM\n";
    echo "================================\n";
    
    // Test route exists
    echo "6. 🔍 Testing Route Exists:\n";
    try {
        $routes = \Illuminate\Support\Facades\Route::getRoutes();
        $tujuanKirimRoutes = [];
        
        foreach ($routes as $route) {
            $name = $route->getName();
            if ($name && strpos($name, 'tujuan-kirim') !== false) {
                $tujuanKirimRoutes[] = "   ✅ " . $name . " -> " . $route->uri();
            }
        }
        
        if (!empty($tujuanKirimRoutes)) {
            echo "   📝 Found Tujuan Kirim Routes:\n";
            foreach ($tujuanKirimRoutes as $routeInfo) {
                echo $routeInfo . "\n";
            }
        } else {
            echo "   ❌ NO Tujuan Kirim routes found!\n";
        }
        
    } catch (Exception $e) {
        echo "   ❌ Error checking routes: " . $e->getMessage() . "\n";
    }
    
    echo "\n7. 🔍 Testing Permission Exists:\n";
    try {
        $permission = \Spatie\Permission\Models\Permission::where('name', 'master-tujuan-kirim-view')->first();
        if ($permission) {
            echo "   ✅ Permission 'master-tujuan-kirim-view' exists (ID: {$permission->id})\n";
            
            // Check if admin user has this permission
            $adminUser = \App\Models\User::where('username', 'admin')->first();
            if ($adminUser) {
                $hasPermission = $adminUser->hasPermissionTo('master-tujuan-kirim-view');
                echo "   " . ($hasPermission ? "✅" : "❌") . " Admin user " . ($hasPermission ? "HAS" : "DOES NOT HAVE") . " permission\n";
                
                // List all permissions for admin
                $permissions = $adminUser->getAllPermissions()->pluck('name')->toArray();
                $tujuanKirimPerms = array_filter($permissions, function($perm) {
                    return strpos($perm, 'tujuan-kirim') !== false;
                });
                
                if (!empty($tujuanKirimPerms)) {
                    echo "   📝 Admin's Tujuan Kirim permissions: " . implode(', ', $tujuanKirimPerms) . "\n";
                } else {
                    echo "   ❌ Admin has NO tujuan-kirim permissions\n";
                }
            } else {
                echo "   ❌ Admin user not found!\n";
            }
        } else {
            echo "   ❌ Permission 'master-tujuan-kirim-view' NOT found!\n";
        }
    } catch (Exception $e) {
        echo "   ❌ Error checking permissions: " . $e->getMessage() . "\n";
    }
    
    echo "\n8. 🔍 Testing Layout File Content:\n";
    $layoutFile = __DIR__ . '/resources/views/layouts/app.blade.php';
    if (file_exists($layoutFile)) {
        $content = file_get_contents($layoutFile);
        
        // Check for tujuan-kirim menu
        if (strpos($content, 'master-tujuan-kirim-view') !== false) {
            echo "   ✅ Found 'master-tujuan-kirim-view' in layout file\n";
        } else {
            echo "   ❌ 'master-tujuan-kirim-view' NOT found in layout file\n";
        }
        
        if (strpos($content, 'tujuan-kirim.index') !== false) {
            echo "   ✅ Found 'tujuan-kirim.index' route in layout file\n";
        } else {
            echo "   ❌ 'tujuan-kirim.index' route NOT found in layout file\n";
        }
        
        if (strpos($content, 'Tujuan Kirim') !== false) {
            echo "   ✅ Found 'Tujuan Kirim' text in layout file\n";
        } else {
            echo "   ❌ 'Tujuan Kirim' text NOT found in layout file\n";
        }
    } else {
        echo "   ❌ Layout file not found!\n";
    }
    
    echo "\n🎉 ALL CACHE CLEARED SUCCESSFULLY!\n";
    echo "📱 Now try these steps:\n";
    echo "   1. Close ALL browser tabs for your app\n";
    echo "   2. Open a new browser window\n";
    echo "   3. Go to your app URL\n";
    echo "   4. Login as admin\n";
    echo "   5. Check sidebar for 'Tujuan Kirim' menu\n";
    echo "   6. If still not visible, press Ctrl+F5 (hard refresh)\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}