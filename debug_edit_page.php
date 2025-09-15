<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Route;

// Simulate accessing the edit user page
echo '🔍 Testing direct access to user edit page...' . PHP_EOL;
echo PHP_EOL;

// Get user admin
$user = User::find(1);

if (!$user) {
    echo '❌ User admin not found' . PHP_EOL;
    exit;
}

echo '👤 User: ' . $user->username . PHP_EOL;
echo '📧 Email: ' . $user->email . PHP_EOL;
echo '🔐 Permission count: ' . $user->permissions->count() . PHP_EOL;
echo PHP_EOL;

// Check if routes are registered
$routes = Route::getRoutes();
$editRoute = null;

foreach ($routes as $route) {
    if ($route->getName() === 'master.user.edit') {
        $editRoute = $route;
        break;
    }
}

if ($editRoute) {
    echo '✅ Route master.user.edit is registered' . PHP_EOL;
    echo '📍 URI: ' . $editRoute->uri() . PHP_EOL;
    echo '🎯 Action: ' . $editRoute->getActionName() . PHP_EOL;
} else {
    echo '❌ Route master.user.edit is NOT registered' . PHP_EOL;
}

echo PHP_EOL;
echo '🔍 Checking view file existence...' . PHP_EOL;

// Check if view file exists
$viewPath = resource_path('views/master-user/edit.blade.php');
if (file_exists($viewPath)) {
    echo '✅ View file exists: ' . $viewPath . PHP_EOL;
    echo '📄 File size: ' . filesize($viewPath) . ' bytes' . PHP_EOL;
    echo '📅 Last modified: ' . date('Y-m-d H:i:s', filemtime($viewPath)) . PHP_EOL;
} else {
    echo '❌ View file does NOT exist: ' . $viewPath . PHP_EOL;
}

echo PHP_EOL;
echo '🔍 Checking template content for pranota-supir...' . PHP_EOL;

// Read the template file and check for pranota-supir checkbox
$content = file_get_contents($viewPath);
$pranotaPattern = '/permissions\[pranota-supir\]\[view\]/';
$pembayaranPattern = '/permissions\[pembayaran-pranota-supir\]\[view\]/';

if (preg_match($pranotaPattern, $content)) {
    echo '✅ pranota-supir view checkbox found in template' . PHP_EOL;
} else {
    echo '❌ pranota-supir view checkbox NOT found in template' . PHP_EOL;
}

if (preg_match($pembayaranPattern, $content)) {
    echo '✅ pembayaran-pranota-supir view checkbox found in template' . PHP_EOL;
} else {
    echo '❌ pembayaran-pranota-supir view checkbox NOT found in template' . PHP_EOL;
}

echo PHP_EOL;
echo '💡 POSSIBLE ISSUES:' . PHP_EOL;
echo '1. Browser cache - Try clearing browser cache and hard refresh (Ctrl+F5)' . PHP_EOL;
echo '2. Laravel view cache - Run: php artisan view:clear' . PHP_EOL;
echo '3. Laravel config cache - Run: php artisan config:clear' . PHP_EOL;
echo '4. JavaScript interference - Check if any JS is modifying the checkboxes' . PHP_EOL;
echo '5. Permission data not loading - Check if user permissions are actually loaded' . PHP_EOL;

echo PHP_EOL;
echo '🔧 RECOMMENDED FIXES:' . PHP_EOL;
echo '1. Clear all Laravel caches:' . PHP_EOL;
echo '   php artisan view:clear' . PHP_EOL;
echo '   php artisan config:clear' . PHP_EOL;
echo '   php artisan cache:clear' . PHP_EOL;
echo '   php artisan route:clear' . PHP_EOL;
echo PHP_EOL;
echo '2. Hard refresh browser (Ctrl+F5)' . PHP_EOL;
echo PHP_EOL;
echo '3. Check browser developer tools for JavaScript errors' . PHP_EOL;
