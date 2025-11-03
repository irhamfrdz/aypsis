<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Checking tanda-terima permissions in database:\n";
echo "=" . str_repeat("=", 70) . "\n\n";

$permissions = DB::table('permissions')
    ->where('name', 'LIKE', '%tanda-terima%')
    ->orWhere('name', 'LIKE', '%tanda_terima%')
    ->get(['id', 'name', 'description']);

if ($permissions->isEmpty()) {
    echo "❌ NO tanda-terima permissions found in database!\n\n";
    echo "Available permissions with 'terima' keyword:\n";
    $allPerms = DB::table('permissions')
        ->where('name', 'LIKE', '%terima%')
        ->get(['id', 'name']);
    
    foreach ($allPerms as $perm) {
        echo "  {$perm->id}. {$perm->name}\n";
    }
} else {
    echo "✅ Found " . count($permissions) . " tanda-terima permissions:\n\n";
    foreach ($permissions as $perm) {
        echo "  {$perm->id}. {$perm->name}";
        if ($perm->description) {
            echo " - {$perm->description}";
        }
        echo "\n";
    }
}

echo "\n" . str_repeat("=", 70) . "\n";
echo "🔍 Checking routes that use tanda-terima permissions:\n\n";

// Check routes
$routes = Route::getRoutes();
$tandaTerimaRoutes = [];

foreach ($routes as $route) {
    $middleware = $route->gatherMiddleware();
    foreach ($middleware as $m) {
        if (is_string($m) && (str_contains($m, 'tanda-terima') || str_contains($m, 'tanda_terima'))) {
            $tandaTerimaRoutes[] = [
                'uri' => $route->uri(),
                'name' => $route->getName(),
                'middleware' => $m,
                'methods' => implode('|', $route->methods())
            ];
        }
    }
}

if (empty($tandaTerimaRoutes)) {
    echo "❌ NO routes found with tanda-terima permissions!\n";
} else {
    echo "✅ Found " . count($tandaTerimaRoutes) . " routes:\n\n";
    foreach ($tandaTerimaRoutes as $r) {
        echo "  [{$r['methods']}] {$r['uri']}\n";
        echo "    Name: {$r['name']}\n";
        echo "    Permission: {$r['middleware']}\n\n";
    }
}
