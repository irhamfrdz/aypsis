<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;

echo "🔍 ANALISIS CONTROLLER DAN ROUTE USAGE\n";
echo "======================================\n\n";

// Get all controllers in the directory
$controllerDir = app_path('Http/Controllers');
$controllerFiles = collect(File::files($controllerDir))
    ->map(fn($file) => $file->getFilenameWithoutExtension())
    ->filter(fn($name) => $name !== 'Controller' && str_ends_with($name, 'Controller'))
    ->sort();

echo "📁 CONTROLLER YANG TERSEDIA:\n";
echo "============================\n";
foreach ($controllerFiles as $controller) {
    echo "  📄 $controller\n";
}
echo "\nTotal: " . $controllerFiles->count() . " controllers\n\n";

// Get all routes and their controllers
$routes = collect(Route::getRoutes());
$usedControllers = collect();

$routes->each(function($route) use ($usedControllers) {
    $action = $route->getAction('controller');
    if ($action && str_contains($action, '@')) {
        $controller = class_basename(explode('@', $action)[0]);
        if (!$usedControllers->contains($controller)) {
            $usedControllers->push($controller);
        }
    }
});

echo "🎯 CONTROLLER YANG DIGUNAKAN DI ROUTE:\n";
echo "======================================\n";
$usedControllers = $usedControllers->sort();
foreach ($usedControllers as $controller) {
    $routeCount = $routes->filter(function($route) use ($controller) {
        $action = $route->getAction('controller');
        return $action && str_contains($action, class_basename($controller));
    })->count();
    echo "  ✅ $controller ($routeCount routes)\n";
}
echo "\nTotal: " . $usedControllers->count() . " controllers in use\n\n";

// Find unused controllers
$unusedControllers = $controllerFiles->diff($usedControllers);

echo "❌ CONTROLLER YANG TIDAK DIGUNAKAN:\n";
echo "===================================\n";
if ($unusedControllers->count() > 0) {
    foreach ($unusedControllers as $controller) {
        echo "  🗑️  $controller\n";
    }
    echo "\nTotal: " . $unusedControllers->count() . " unused controllers\n\n";
} else {
    echo "  ✅ Semua controller terpakai!\n\n";
}

// Analyze route patterns for potential consolidation
echo "🔄 ANALISIS POLA ROUTE UNTUK KONSOLIDASI:\n";
echo "==========================================\n\n";

// Group routes by similar patterns
$routePatterns = [];
$routes->each(function($route) use (&$routePatterns) {
    $name = $route->getName();
    if ($name) {
        // Extract base pattern (before the last dot)
        $parts = explode('.', $name);
        if (count($parts) > 1) {
            $baseName = implode('.', array_slice($parts, 0, -1));
            if (!isset($routePatterns[$baseName])) {
                $routePatterns[$baseName] = [];
            }
            $routePatterns[$baseName][] = $name;
        }
    }
});

// Find patterns that could be consolidated into resource routes
$resourceCandidates = [];
foreach ($routePatterns as $pattern => $routes) {
    if (count($routes) >= 4) { // At least 4 routes suggest it could be a resource
        $hasIndex = in_array($pattern . '.index', $routes);
        $hasCreate = in_array($pattern . '.create', $routes);
        $hasStore = in_array($pattern . '.store', $routes);
        $hasShow = in_array($pattern . '.show', $routes);
        $hasEdit = in_array($pattern . '.edit', $routes);
        $hasUpdate = in_array($pattern . '.update', $routes);
        $hasDestroy = in_array($pattern . '.destroy', $routes);

        $resourceMethods = collect([$hasIndex, $hasCreate, $hasStore, $hasShow, $hasEdit, $hasUpdate, $hasDestroy])
            ->filter()->count();

        if ($resourceMethods >= 4) {
            $resourceCandidates[$pattern] = [
                'routes' => $routes,
                'resource_methods' => $resourceMethods,
                'total_routes' => count($routes)
            ];
        }
    }
}

echo "🏗️  KANDIDAT RESOURCE CONTROLLER:\n";
echo "=================================\n";
foreach ($resourceCandidates as $pattern => $info) {
    echo "  📦 $pattern:\n";
    echo "      - Total routes: {$info['total_routes']}\n";
    echo "      - Resource methods: {$info['resource_methods']}/7\n";
    echo "      - Additional routes: " . ($info['total_routes'] - $info['resource_methods']) . "\n";

    if ($info['resource_methods'] >= 6) {
        echo "      ✅ RECOMMENDED: Convert to resource route\n";
    } else {
        echo "      ⚠️  PARTIAL: Consider resource route with 'only' option\n";
    }
    echo "\n";
}

// Analyze duplicate functionality
echo "🔍 ANALISIS DUPLIKASI FUNGSIONALITAS:\n";
echo "====================================\n\n";

// Find similar route names that might indicate duplication
$suspiciousPairs = [];
$routeNames = collect($routes)->map(fn($route) => $route->getName())->filter();

foreach ($routeNames as $name1) {
    foreach ($routeNames as $name2) {
        if ($name1 !== $name2) {
            // Check for similar patterns
            $similarity = similar_text($name1, $name2, $percent);
            if ($percent > 75 && $similarity > 10) {
                $key = implode(' <-> ', sorted([$name1, $name2]));
                if (!isset($suspiciousPairs[$key])) {
                    $suspiciousPairs[$key] = round($percent, 1);
                }
            }
        }
    }
}

echo "⚠️  ROUTE DENGAN NAMA MIRIP (KEMUNGKINAN DUPLIKAT):\n";
if (count($suspiciousPairs) > 0) {
    arsort($suspiciousPairs);
    $count = 0;
    foreach ($suspiciousPairs as $pair => $similarity) {
        if ($count++ < 20) { // Show top 20
            echo "  🔄 $pair ($similarity% similar)\n";
        }
    }
    if (count($suspiciousPairs) > 20) {
        echo "  ... and " . (count($suspiciousPairs) - 20) . " more similar pairs\n";
    }
} else {
    echo "  ✅ Tidak ada route dengan nama yang mencurigakan\n";
}

echo "\n📊 RINGKASAN ANALYSIS:\n";
echo "======================\n";
echo "Total Routes: " . $routes->count() . "\n";
echo "Total Controllers: " . $controllerFiles->count() . "\n";
echo "Used Controllers: " . $usedControllers->count() . "\n";
echo "Unused Controllers: " . $unusedControllers->count() . "\n";
echo "Resource Candidates: " . count($resourceCandidates) . "\n";
echo "Suspicious Duplicates: " . count($suspiciousPairs) . "\n\n";

// Final recommendations
echo "💡 REKOMENDASI AKSI:\n";
echo "====================\n";

if ($unusedControllers->count() > 0) {
    echo "1️⃣ HAPUS CONTROLLER YANG TIDAK TERPAKAI:\n";
    foreach ($unusedControllers as $controller) {
        echo "   rm app/Http/Controllers/$controller.php\n";
    }
    echo "\n";
}

if (count($resourceCandidates) > 0) {
    echo "2️⃣ KONVERSI KE RESOURCE ROUTES:\n";
    $topCandidates = array_slice($resourceCandidates, 0, 5, true);
    foreach ($topCandidates as $pattern => $info) {
        if ($info['resource_methods'] >= 6) {
            echo "   Route::resource('$pattern', 'SomeController');\n";
        }
    }
    echo "\n";
}

echo "3️⃣ AUDIT MANUAL DIPERLUKAN UNTUK:\n";
echo "   - Route dengan fungsi serupa\n";
echo "   - Middleware yang berlebihan\n";
echo "   - Permission yang tidak konsisten\n\n";

function sorted($array) {
    sort($array);
    return $array;
}
