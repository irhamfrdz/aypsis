<?php

use Illuminate\Support\Facades\Route;

echo "=== TAGIHAN KONTAINER SEWA IMPORT ROUTES TEST ===\n\n";

// Get all routes related to tagihan kontainer sewa import
$routes = collect(Route::getRoutes())->filter(function($route) {
    $uri = $route->uri();
    return str_contains($uri, 'daftar-tagihan-kontainer-sewa') &&
           (str_contains($uri, 'import') || str_contains($route->getName() ?? '', 'import'));
});

echo "Found " . $routes->count() . " import-related routes:\n\n";

foreach ($routes as $route) {
    $methods = implode('|', $route->methods());
    $uri = $route->uri();
    $name = $route->getName();
    $action = $route->getActionName();

    echo "Method: {$methods}\n";
    echo "URI: {$uri}\n";
    echo "Name: {$name}\n";
    echo "Action: {$action}\n";
    echo "Middleware: " . implode(', ', $route->middleware()) . "\n";
    echo "---\n";
}

// Test if the controller methods exist
echo "\n=== CONTROLLER METHODS CHECK ===\n\n";

$controller = new \App\Http\Controllers\DaftarTagihanKontainerSewaController();

$methods = ['importPage', 'importCsv', 'processImport'];
foreach ($methods as $method) {
    if (method_exists($controller, $method)) {
        echo "✅ Method '{$method}' EXISTS\n";
    } else {
        echo "❌ Method '{$method}' MISSING\n";
    }
}

echo "\n=== ROUTE GENERATION TEST ===\n\n";

try {
    $importPageRoute = route('daftar-tagihan-kontainer-sewa.import');
    echo "✅ Import page route: {$importPageRoute}\n";
} catch (Exception $e) {
    echo "❌ Import page route error: " . $e->getMessage() . "\n";
}

try {
    $processRoute = route('daftar-tagihan-kontainer-sewa.import.process');
    echo "✅ Process import route: {$processRoute}\n";
} catch (Exception $e) {
    echo "❌ Process import route error: " . $e->getMessage() . "\n";
}

echo "\n=== PERMISSION CHECK ===\n\n";

try {
    $user = auth()->user();
    if ($user) {
        $hasCreatePerm = $user->can('tagihan-kontainer-sewa-create');
        $hasIndexPerm = $user->can('tagihan-kontainer-sewa-index');

        echo "Current User: " . $user->username ?? $user->name ?? 'Unknown' . "\n";
        echo "tagihan-kontainer-sewa-create: " . ($hasCreatePerm ? '✅' : '❌') . "\n";
        echo "tagihan-kontainer-sewa-index: " . ($hasIndexPerm ? '✅' : '❌') . "\n";
    } else {
        echo "❌ No authenticated user\n";
    }
} catch (Exception $e) {
    echo "❌ Permission check error: " . $e->getMessage() . "\n";
}
