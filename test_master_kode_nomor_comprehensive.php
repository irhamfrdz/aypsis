<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

echo "=== COMPREHENSIVE MASTER KODE NOMOR TEST ===\n\n";

// Test 1: Check if user is authenticated and has permissions
echo "1. TESTING USER AUTHENTICATION & PERMISSIONS\n";
echo "---------------------------------------------\n";

$user = \Illuminate\Support\Facades\Auth::user();
if ($user) {
    echo "✓ User authenticated: {$user->username} (ID: {$user->id})\n";

    // Test permission checking
    $permissions = [
        'master-kode-nomor-view',
        'master-kode-nomor-create',
        'master-kode-nomor-update',
        'master-kode-nomor-delete'
    ];

    foreach ($permissions as $perm) {
        // Check permission via database
        $hasPermission = DB::table('user_permissions')
            ->join('permissions', 'user_permissions.permission_id', '=', 'permissions.id')
            ->where('user_permissions.user_id', $user->id)
            ->where('permissions.name', $perm)
            ->exists();

        $status = $hasPermission ? '✓' : '✗';
        echo "$status Permission '$perm': " . ($hasPermission ? 'GRANTED' : 'DENIED') . "\n";
    }
} else {
    echo "✗ No authenticated user found!\n";
}

// Test 2: Check routes
echo "\n2. TESTING ROUTES\n";
echo "-----------------\n";

$routes = app('router')->getRoutes();
$masterKodeNomorRoutes = [];

foreach ($routes as $route) {
    $name = $route->getName();
    if ($name && strpos($name, 'master.kode-nomor') !== false) {
        $masterKodeNomorRoutes[] = [
            'name' => $name,
            'uri' => $route->uri(),
            'methods' => implode('|', $route->methods())
        ];
    }
}

if (count($masterKodeNomorRoutes) > 0) {
    echo "✓ Found " . count($masterKodeNomorRoutes) . " master kode nomor routes:\n";
    foreach ($masterKodeNomorRoutes as $route) {
        echo "  - {$route['name']} ({$route['methods']}): {$route['uri']}\n";
    }
} else {
    echo "✗ No master kode nomor routes found!\n";
}

// Test 3: Check if route exists and is accessible
echo "\n3. TESTING ROUTE ACCESSIBILITY\n";
echo "-------------------------------\n";

try {
    $route = Route::getRoutes()->getByName('master.kode-nomor.index');
    if ($route) {
        echo "✓ Route 'master.kode-nomor.index' exists\n";
        echo "  URI: {$route->uri()}\n";
        echo "  Methods: " . implode('|', $route->methods()) . "\n";

        // Test if route can be generated
        try {
            $url = route('master.kode-nomor.index');
            echo "✓ Route URL generated successfully: $url\n";
        } catch (Exception $e) {
            echo "✗ Failed to generate route URL: " . $e->getMessage() . "\n";
        }
    } else {
        echo "✗ Route 'master.kode-nomor.index' not found!\n";
    }
} catch (Exception $e) {
    echo "✗ Error checking route: " . $e->getMessage() . "\n";
}

// Test 4: Check controller
echo "\n4. TESTING CONTROLLER\n";
echo "---------------------\n";

$controllerPath = app_path('Http/Controllers/KodeNomorController.php');
if (file_exists($controllerPath)) {
    echo "✓ KodeNomorController exists at: $controllerPath\n";

    // Check if controller class exists
    if (class_exists('\App\Http\Controllers\KodeNomorController')) {
        echo "✓ KodeNomorController class can be loaded\n";

        // Check if index method exists
        $controller = new \App\Http\Controllers\KodeNomorController();
        if (method_exists($controller, 'index')) {
            echo "✓ index() method exists in controller\n";
        } else {
            echo "✗ index() method NOT found in controller\n";
        }
    } else {
        echo "✗ KodeNomorController class cannot be loaded\n";
    }
} else {
    echo "✗ KodeNomorController file NOT found\n";
}

// Test 5: Check views
echo "\n5. TESTING VIEWS\n";
echo "----------------\n";

$viewPaths = [
    resource_path('views/master/kode-nomor/index.blade.php'),
    resource_path('views/master/kode-nomor/create.blade.php'),
    resource_path('views/master/kode-nomor/edit.blade.php'),
    resource_path('views/master/kode-nomor/show.blade.php')
];

foreach ($viewPaths as $viewPath) {
    if (file_exists($viewPath)) {
        echo "✓ View exists: " . basename($viewPath) . "\n";
    } else {
        echo "✗ View NOT FOUND: " . basename($viewPath) . "\n";
    }
}

// Test 6: Check sidebar layout
echo "\n6. TESTING SIDEBAR LAYOUT\n";
echo "--------------------------\n";

$sidebarPath = resource_path('views/layouts/app.blade.php');
if (file_exists($sidebarPath)) {
    echo "✓ Sidebar layout exists: $sidebarPath\n";

    $sidebarContent = file_get_contents($sidebarPath);

    // Check if kode nomor menu exists in sidebar
    if (strpos($sidebarContent, 'master-kode-nomor-view') !== false) {
        echo "✓ Kode nomor permission check found in sidebar\n";
    } else {
        echo "✗ Kode nomor permission check NOT found in sidebar\n";
    }

    if (strpos($sidebarContent, 'master.kode-nomor.index') !== false) {
        echo "✓ Kode nomor route link found in sidebar\n";
    } else {
        echo "✗ Kode nomor route link NOT found in sidebar\n";
    }

    if (strpos($sidebarContent, 'Kode Nomor') !== false) {
        echo "✓ 'Kode Nomor' text found in sidebar\n";
    } else {
        echo "✗ 'Kode Nomor' text NOT found in sidebar\n";
    }
} else {
    echo "✗ Sidebar layout file NOT found\n";
}

// Test 7: Check database permissions
echo "\n7. TESTING DATABASE PERMISSIONS\n";
echo "--------------------------------\n";

try {
    $permissions = DB::table('permissions')
        ->whereIn('name', [
            'master-kode-nomor-view',
            'master-kode-nomor-create',
            'master-kode-nomor-update',
            'master-kode-nomor-delete'
        ])
        ->get();

    if ($permissions->count() > 0) {
        echo "✓ Found " . $permissions->count() . " kode nomor permissions in database:\n";
        foreach ($permissions as $perm) {
            echo "  - {$perm->name} (ID: {$perm->id})\n";
        }
    } else {
        echo "✗ No kode nomor permissions found in database!\n";
    }

    // Check user permissions
    if ($user) {
        $userPermissions = DB::table('user_permissions')
            ->join('permissions', 'user_permissions.permission_id', '=', 'permissions.id')
            ->where('user_permissions.user_id', $user->id)
            ->whereIn('permissions.name', [
                'master-kode-nomor-view',
                'master-kode-nomor-create',
                'master-kode-nomor-update',
                'master-kode-nomor-delete'
            ])
            ->pluck('permissions.name')
            ->toArray();

        if (count($userPermissions) > 0) {
            echo "✓ User has " . count($userPermissions) . " kode nomor permissions assigned:\n";
            foreach ($userPermissions as $perm) {
                echo "  - $perm\n";
            }
        } else {
            echo "✗ User has NO kode nomor permissions assigned!\n";
        }
    }
} catch (Exception $e) {
    echo "✗ Database error: " . $e->getMessage() . "\n";
}

// Test 8: Simulate sidebar rendering
echo "\n8. SIMULATING SIDEBAR RENDERING\n";
echo "--------------------------------\n";

if ($user) {
    $canViewKodeNomor = DB::table('user_permissions')
        ->join('permissions', 'user_permissions.permission_id', '=', 'permissions.id')
        ->where('user_permissions.user_id', $user->id)
        ->where('permissions.name', 'master-kode-nomor-view')
        ->exists();

    echo "User can view kode nomor: " . ($canViewKodeNomor ? 'YES' : 'NO') . "\n";

    if ($canViewKodeNomor) {
        echo "✓ Menu should be VISIBLE in sidebar\n";
    } else {
        echo "✗ Menu should be HIDDEN from sidebar (no permission)\n";
    }
} else {
    echo "✗ Cannot simulate sidebar rendering (no authenticated user)\n";
}

echo "\n=== TEST COMPLETED ===\n";
echo "If menu is still not visible, check:\n";
echo "1. User authentication status\n";
echo "2. User permissions assignment\n";
echo "3. Browser cache (try Ctrl+F5)\n";
echo "4. Laravel cache (run: php artisan view:clear, route:clear, config:clear)\n";
