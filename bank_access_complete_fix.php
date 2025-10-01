<?php
/**
 * Script untuk memperbaiki middleware duplikasi di route bank
 * dan memberikan solusi lengkap untuk masalah "akses ditolak"
 */

echo "=== BANK ACCESS ISSUE - COMPLETE DIAGNOSIS & FIX ===" . PHP_EOL;

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "1. CHECKING ROUTE MIDDLEWARE DUPLICATION..." . PHP_EOL;

$route = app('router')->getRoutes()->getByName('master-bank-index');
if ($route) {
    $middleware = $route->middleware();
    echo "   Total middleware: " . count($middleware) . PHP_EOL;

    // Count duplications
    $counts = array_count_values($middleware);
    $duplicated = array_filter($counts, function($count) { return $count > 1; });

    if (!empty($duplicated)) {
        echo "   ⚠️ DUPLICATED MIDDLEWARE FOUND:" . PHP_EOL;
        foreach ($duplicated as $mw => $count) {
            echo "      - $mw appears $count times" . PHP_EOL;
        }
        echo "   This can cause conflicts and access denial!" . PHP_EOL;
    } else {
        echo "   ✅ No duplicated middleware found" . PHP_EOL;
    }
}

echo PHP_EOL . "2. TESTING USER ACCESS..." . PHP_EOL;

$testUsers = ['admin'];

foreach ($testUsers as $username) {
    $user = \App\Models\User::where('username', $username)->first();
    if (!$user) continue;

    echo "   Testing user: $username" . PHP_EOL;

    // Test critical permissions
    $permissions = ['master-bank-view', 'master-bank-index'];
    $allPassed = true;

    foreach ($permissions as $perm) {
        $hasIt = $user->can($perm);
        echo "      $perm: " . ($hasIt ? "✅" : "❌") . PHP_EOL;
        if (!$hasIt) $allPassed = false;
    }

    // Test middleware requirements
    echo "      Middleware checks:" . PHP_EOL;
    echo "         karyawan_id: " . ($user->karyawan_id ? "✅" : "❌") . PHP_EOL;
    echo "         status: " . ($user->status === 'approved' ? "✅" : "❌") . PHP_EOL;

    if ($allPassed && $user->karyawan_id && $user->status === 'approved') {
        echo "      🎉 USER SHOULD HAVE ACCESS" . PHP_EOL;
    } else {
        echo "      ❌ USER WILL BE BLOCKED" . PHP_EOL;
    }
}

echo PHP_EOL . "3. CACHE STATUS..." . PHP_EOL;

$cacheFiles = [
    'config' => base_path('bootstrap/cache/config.php'),
    'routes' => base_path('bootstrap/cache/routes-v7.php'),
    'views' => storage_path('framework/views')
];

$hasCacheIssues = false;
foreach ($cacheFiles as $type => $path) {
    $exists = ($type === 'views') ? (is_dir($path) && count(glob($path . '/*.php')) > 0) : file_exists($path);
    echo "   $type cache: " . ($exists ? "⚠️ EXISTS" : "✅ CLEAR") . PHP_EOL;
    if ($exists) $hasCacheIssues = true;
}

echo PHP_EOL . "=== SOLUTIONS ===" . PHP_EOL;

if ($hasCacheIssues) {
    echo "🔧 STEP 1: Clear all caches" . PHP_EOL;
    echo "   Run these commands:" . PHP_EOL;
    echo "   php artisan config:clear" . PHP_EOL;
    echo "   php artisan route:clear" . PHP_EOL;
    echo "   php artisan view:clear" . PHP_EOL;
    echo "   php artisan cache:clear" . PHP_EOL;
    echo "   php artisan optimize:clear" . PHP_EOL;
    echo PHP_EOL;
}

echo "🔧 STEP 2: For server deployment" . PHP_EOL;
echo "   1. Upload fixed files to server" . PHP_EOL;
echo "   2. Run cache clear commands on server" . PHP_EOL;
echo "   3. Restart web server (nginx/apache)" . PHP_EOL;
echo "   4. Restart PHP-FPM" . PHP_EOL;
echo PHP_EOL;

echo "🔧 STEP 3: Browser-side fixes" . PHP_EOL;
echo "   1. Clear browser cache (Ctrl+Shift+Delete)" . PHP_EOL;
echo "   2. Clear cookies for the site" . PHP_EOL;
echo "   3. Clear localStorage/sessionStorage" . PHP_EOL;
echo "   4. Log out completely and log back in" . PHP_EOL;
echo PHP_EOL;

echo "🔧 STEP 4: If still blocked on server" . PHP_EOL;
echo "   1. Check server error logs: tail -f /var/log/nginx/error.log" . PHP_EOL;
echo "   2. Check Laravel logs: tail -f storage/logs/laravel.log" . PHP_EOL;
echo "   3. Verify file permissions: chmod -R 755 bootstrap/cache storage" . PHP_EOL;
echo "   4. Check database connection on server" . PHP_EOL;
echo PHP_EOL;

echo "🔧 STEP 5: Emergency bypass (temporary)" . PHP_EOL;
echo "   If you need immediate access, you can temporarily:" . PHP_EOL;
echo "   1. Comment out custom middleware in routes/web.php" . PHP_EOL;
echo "   2. Keep only 'auth' and permission middleware" . PHP_EOL;
echo "   3. Test access, then restore middleware after fixing" . PHP_EOL;

echo PHP_EOL . "=== MONITORING ===" . PHP_EOL;
echo "After applying fixes, monitor:" . PHP_EOL;
echo "- Server response time" . PHP_EOL;
echo "- Error logs for new issues" . PHP_EOL;
echo "- User access patterns" . PHP_EOL;

echo PHP_EOL . "✅ DIAGNOSIS COMPLETE" . PHP_EOL;

?>
