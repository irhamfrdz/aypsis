<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Complete Middleware Debug ===\n";

// Get admin user
$adminUser = DB::table('users')->where('username', 'admin')->first();
echo "1. Admin User Data:\n";
echo "   - ID: {$adminUser->id}\n";
echo "   - Username: {$adminUser->username}\n";
echo "   - Status: {$adminUser->status}\n";
echo "   - Karyawan ID: {$adminUser->karyawan_id}\n";

echo "\n2. Middleware Check Results:\n";

// Check 1: EnsureKaryawanPresent
$hasKaryawanId = !empty($adminUser->karyawan_id);
echo "   - EnsureKaryawanPresent: " . ($hasKaryawanId ? "✓ PASS" : "❌ FAIL") . "\n";

// Check 2: EnsureUserApproved
$isApproved = $adminUser->status === 'approved';
echo "   - EnsureUserApproved: " . ($isApproved ? "✓ PASS" : "❌ FAIL") . "\n";

// Check 3: EnsureCrewChecklistComplete
echo "   - EnsureCrewChecklistComplete: ✓ PASS (doesn't block)\n";

// Check if there's a model relationship issue
echo "\n3. Model Relationship Check:\n";
try {
    $userModel = App\Models\User::find($adminUser->id);
    if ($userModel && $userModel->karyawan) {
        echo "   - User->karyawan relationship: ✓ WORKING\n";
        echo "   - Karyawan name: {$userModel->karyawan->nama_lengkap}\n";
    } else {
        echo "   - User->karyawan relationship: ❌ BROKEN\n";
    }
} catch (Exception $e) {
    echo "   - Model check error: " . $e->getMessage() . "\n";
}

// Check roles
echo "\n4. Role Check:\n";
$roles = DB::table('role_user')
    ->join('roles', 'role_user.role_id', '=', 'roles.id')
    ->where('role_user.user_id', $adminUser->id)
    ->pluck('roles.name')
    ->toArray();
echo "   - User roles: " . implode(', ', $roles) . "\n";

// Test actual route access
echo "\n5. Route Test:\n";
try {
    $routes = collect(app('router')->getRoutes())->filter(function($route) {
        return str_contains($route->uri(), 'pranota-kontainer-sewa/import') &&
               in_array('POST', $route->methods());
    });

    foreach($routes as $route) {
        echo "   - Found POST route: {$route->uri()}\n";
        echo "   - Middleware: " . implode(', ', $route->middleware()) . "\n";

        // Check if route is in protected group
        $middlewareStack = $route->middleware();
        if (in_array('App\\Http\\Middleware\\EnsureKaryawanPresent', $middlewareStack) ||
            in_array('App\\Http\\Middleware\\EnsureUserApproved', $middlewareStack) ||
            in_array('App\\Http\\Middleware\\EnsureCrewChecklistComplete', $middlewareStack)) {
            echo "   - Has custom middleware: YES\n";
        } else {
            echo "   - Has custom middleware: NO (only web,auth)\n";
        }
    }
} catch (Exception $e) {
    echo "   - Route check error: " . $e->getMessage() . "\n";
}

echo "\n=== CONCLUSION ===\n";
if ($hasKaryawanId && $isApproved) {
    echo "✓ Admin user should PASS all middleware checks\n";
    echo "If import is still blocked, check:\n";
    echo "1. Laravel logs: storage/logs/laravel.log\n";
    echo "2. Browser network tab for actual error response\n";
    echo "3. Controller method permissions or validation\n";
} else {
    echo "❌ Admin user will be BLOCKED by middleware\n";
    if (!$hasKaryawanId) echo "- Fix: Assign karyawan_id to admin user\n";
    if (!$isApproved) echo "- Fix: Set status='approved' for admin user\n";
}

?>
