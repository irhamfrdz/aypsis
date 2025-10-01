<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/bootstrap/app.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

// Load Laravel application
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Create test HTTP request
$request = Illuminate\Http\Request::create('/master/bank', 'GET');

// Simulate authenticated user (admin)
$user = \App\Models\User::find(1); // Admin user
if (!$user) {
    echo "❌ Admin user not found!\n";
    exit;
}

// Auth::login manually
auth()->login($user);

echo "🧪 TESTING BANK ACCESS AFTER MIDDLEWARE GROUP FIX\n";
echo "================================================\n";

echo "👤 Current User: " . $user->username . " (ID: {$user->id})\n";
echo "🔐 User Permissions:\n";

// Check specific permissions
$bankViewPermission = $user->can('master-bank-view');
echo "   - master-bank-view: " . ($bankViewPermission ? "✅ YES" : "❌ NO") . "\n";

echo "\n🛤️  ROUTE TESTING:\n";

// Test route resolution
try {
    $route = \Illuminate\Support\Facades\Route::getRoutes()->match($request);
    echo "✅ Route resolved: " . $route->getName() . "\n";
    echo "📝 Controller: " . $route->getActionName() . "\n";

    // Get middleware
    $middleware = $route->middleware();
    echo "🛡️  Route Middleware:\n";
    foreach ($middleware as $m) {
        echo "   - $m\n";
    }

} catch (Exception $e) {
    echo "❌ Route error: " . $e->getMessage() . "\n";
}

echo "\n🔍 MIDDLEWARE SIMULATION:\n";

// Simulate middleware checks
echo "1. Auth Middleware: " . (auth()->check() ? "✅ PASS" : "❌ FAIL") . "\n";

// Check if user has karyawan record (EnsureKaryawanPresent)
$karyawan = \App\Models\Karyawan::where('user_id', $user->id)->first();
echo "2. EnsureKaryawanPresent: " . ($karyawan ? "✅ PASS" : "❌ FAIL") . "\n";

// Check if user is approved (EnsureUserApproved)
echo "3. EnsureUserApproved: " . ($user->is_approved ? "✅ PASS" : "❌ FAIL") . "\n";

// Check crew checklist (EnsureCrewChecklistComplete)
if ($karyawan && $karyawan->divisi === 'CREW') {
    $crewChecklist = \App\Models\CrewChecklist::where('karyawan_id', $karyawan->id)->first();
    $crewCheckPassed = $crewChecklist && $crewChecklist->is_complete;
} else {
    $crewCheckPassed = true; // Not crew, so passes
}
echo "4. EnsureCrewChecklistComplete: " . ($crewCheckPassed ? "✅ PASS" : "❌ FAIL") . "\n";

echo "\n📊 FINAL ANALYSIS:\n";
$allMiddlewarePassed = auth()->check() && $karyawan && $user->is_approved && $crewCheckPassed;
$hasPermission = $bankViewPermission;

echo "Middleware Status: " . ($allMiddlewarePassed ? "✅ ALL PASSED" : "❌ BLOCKED") . "\n";
echo "Permission Status: " . ($hasPermission ? "✅ AUTHORIZED" : "❌ UNAUTHORIZED") . "\n";

if ($allMiddlewarePassed && $hasPermission) {
    echo "\n🎉 SUCCESS: User should be able to access /master/bank\n";
} else {
    echo "\n⚠️  BLOCKED: User cannot access /master/bank\n";
    echo "   Reasons:\n";
    if (!auth()->check()) echo "   - Not authenticated\n";
    if (!$karyawan) echo "   - No karyawan record\n";
    if (!$user->is_approved) echo "   - User not approved\n";
    if (!$crewCheckPassed) echo "   - Crew checklist incomplete\n";
    if (!$hasPermission) echo "   - Missing master-bank-view permission\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
