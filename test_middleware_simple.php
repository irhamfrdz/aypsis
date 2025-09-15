<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    $middleware = app()->make('App\Http\Middleware\EnsurePermissionWithDetails');
    echo "✅ Middleware resolved successfully!\n";
} catch (Exception $e) {
    echo "❌ Middleware resolution failed: " . $e->getMessage() . "\n";
}
?>
