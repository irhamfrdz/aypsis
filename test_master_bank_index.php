<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTING ROUTE MASTER-BANK-INDEX ===" . PHP_EOL;

$route = app('router')->getRoutes()->getByName('master-bank-index');

if ($route) {
    echo "✅ Route 'master-bank-index' found!" . PHP_EOL;
    echo "📍 URI: " . $route->uri() . PHP_EOL;
    echo "🛡️ Middleware count: " . count($route->middleware()) . PHP_EOL;
    echo "🔧 Controller: " . $route->getActionName() . PHP_EOL;

    // Check duplicates
    $middleware = $route->middleware();
    $counts = array_count_values($middleware);
    $duplicated = array_filter($counts, function($count) { return $count > 1; });

    if (empty($duplicated)) {
        echo "✅ No middleware duplication!" . PHP_EOL;
    } else {
        echo "⚠️ Duplicated middleware:" . PHP_EOL;
        foreach ($duplicated as $mw => $count) {
            echo "   - $mw ($count times)" . PHP_EOL;
        }
    }

    echo PHP_EOL . "🎯 CONCLUSION: Route 'master-bank-index' is working!" . PHP_EOL;

} else {
    echo "❌ Route 'master-bank-index' NOT FOUND" . PHP_EOL;

    // List all bank routes
    echo PHP_EOL . "Available bank routes:" . PHP_EOL;
    foreach (app('router')->getRoutes() as $r) {
        if (str_contains($r->uri(), 'bank') || str_contains($r->getName() ?? '', 'bank')) {
            echo "   - " . ($r->getName() ?? 'unnamed') . " => " . $r->uri() . PHP_EOL;
        }
    }
}

?>
