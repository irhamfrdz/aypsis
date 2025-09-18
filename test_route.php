<?php<?php<?php<?php<?php



require_once __DIR__ . '/vendor/autoload.php';



$app = require_once __DIR__ . '/bootstrap/app.php';require_once __DIR__ . '/vendor/autoload.php';



$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();



use Illuminate\Support\Facades\Route;$app = require_once __DIR__ . '/bootstrap/app.php';require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Http\Request;



echo "Testing route resolution...\n";

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {

    // Test route resolution

    $url = route('master-coa-download-template');

    echo "Route URL: $url\n";use Illuminate\Support\Facades\Route;$app = require_once __DIR__ . '/bootstrap/app.php';require_once __DIR__ . '/vendor/autoload.php';// Simple test to check if routes are loaded



    $routes = $app->router->getRoutes();use Illuminate\Http\Request;



    // Test controller instantiation$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

    $controller = app(\App\Http\Controllers\MasterCoaController::class);

    echo "Controller instantiated successfully\n";echo "Testing route resolution...\n";



    echo "Total routes loaded: " . count($routes) . "\n\n";require_once __DIR__ . '/vendor/autoload.php';



    // Test method calltry {

    $response = $controller->downloadTemplate();

    echo "Method executed successfully\n";    // Test route resolutionuse Illuminate\Support\Facades\Route;

    echo "Response type: " . get_class($response) . "\n";

    echo "Response status: " . $response->getStatusCode() . "\n";    $url = route('master-coa-download-template');



    // Test route resolution    echo "Route URL: $url\n";use Illuminate\Http\Request;$app = require_once __DIR__ . '/bootstrap/app.php';

    $masterRoutes = [];

    $found = false;



    foreach ($routes as $route) {    $routes = $app->router->getRoutes();

        $name = $route->getName();



        if ($name === 'master-divisi-index') {

            $found = true;    // Test controller instantiationecho "Testing route resolution...\n";$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();$app = require_once __DIR__ . '/bootstrap/app.php';

            echo "✅ Route 'master-divisi-index' found!\n";

            echo "URI: " . $route->uri() . "\n";    $controller = app(\App\Http\Controllers\MasterCoaController::class);

            echo "Methods: " . implode(', ', $route->methods()) . "\n";

        }    echo "Controller instantiated successfully\n";



        if (strpos($name, 'master-') === 0) {

            $masterRoutes[] = $name;

        }    echo "Total routes loaded: " . count($routes) . "\n\n";try {

    }



    if (!$found) {

        echo "❌ Route 'master-divisi-index' not found\n";    // Test method call    // Test route resolution

    }

    $response = $controller->downloadTemplate();

    echo "\n=== Master routes found ===\n";

    if (empty($masterRoutes)) {    echo "Method executed successfully\n";    $url = route('master.coa.download-template');use Illuminate\Support\Facades\Route;try {

        echo "No master routes found!\n";

    } else {    echo "Response type: " . get_class($response) . "\n";

        foreach (array_slice($masterRoutes, 0, 10) as $route) {

            echo "- " . $route . "\n";    echo "Response status: " . $response->getStatusCode() . "\n";    echo "Route URL: $url\n";

        }

        if (count($masterRoutes) > 10) {

            echo "... and " . (count($masterRoutes) - 10) . " more\n";

        }    // Test route resolutionuse Illuminate\Http\Request;    $routes = $app->router->getRoutes();

    }

    $masterRoutes = [];

} catch (Exception $e) {

    echo "❌ Error: " . $e->getMessage() . "\n";    $found = false;    // Test controller instantiation

    echo "File: " . $e->getFile() . "\n";

    echo "Line: " . $e->getLine() . "\n";

    echo "Trace:\n" . $e->getTraceAsString() . "\n";

}    foreach ($routes as $route) {    $controller = app(\App\Http\Controllers\MasterCoaController::class);

        $name = $route->getName();

    echo "Controller instantiated successfully\n";

        if ($name === 'master-divisi-index') {

            $found = true;echo "Testing route resolution...\n";    echo "Total routes loaded: " . count($routes) . "\n\n";

            echo "✅ Route 'master-divisi-index' found!\n";

            echo "URI: " . $route->uri() . "\n";    // Test method call

            echo "Methods: " . implode(', ', $route->methods()) . "\n";

        }    $response = $controller->downloadTemplate();



        if (strpos($name, 'master-') === 0) {    echo "Method executed successfully\n";

            $masterRoutes[] = $name;

        }    echo "Response type: " . get_class($response) . "\n";try {    $found = false;

    }

    echo "Response status: " . $response->getStatusCode() . "\n";

    if (!$found) {

        echo "❌ Route 'master-divisi-index' not found\n";    // Test route resolution    $masterRoutes = [];

    }

} catch (Exception $e) {

    echo "\n=== Master routes found ===\n";

    if (empty($masterRoutes)) {    echo "Error: " . $e->getMessage() . "\n";    $url = route('master.coa.download-template');    foreach ($routes as $route) {

        echo "No master routes found!\n";

    } else {    echo "File: " . $e->getFile() . "\n";

        foreach (array_slice($masterRoutes, 0, 10) as $route) {

            echo "- " . $route . "\n";    echo "Line: " . $e->getLine() . "\n";    echo "Route URL: $url\n";        $name = $route->getName();

        }

        if (count($masterRoutes) > 10) {    echo "Trace:\n" . $e->getTraceAsString() . "\n";

            echo "... and " . (count($masterRoutes) - 10) . " more\n";

        }}        if ($name === 'master.divisi.index') {

    }

    // Test controller instantiation            $found = true;

} catch (Exception $e) {

    echo "❌ Error: " . $e->getMessage() . "\n";    $controller = app(\App\Http\Controllers\MasterCoaController::class);            echo "✅ Route 'master.divisi.index' found!\n";

    echo "File: " . $e->getFile() . "\n";

    echo "Line: " . $e->getLine() . "\n";    echo "Controller instantiated successfully\n";            echo "URI: " . $route->uri() . "\n";

    echo "Trace:\n" . $e->getTraceAsString() . "\n";

}            echo "Methods: " . implode(', ', $route->methods()) . "\n";

    // Test method call        }

    $response = $controller->downloadTemplate();        if (strpos($name, 'master.') === 0) {

    echo "Method executed successfully\n";            $masterRoutes[] = $name;

    echo "Response type: " . get_class($response) . "\n";        }

    echo "Response status: " . $response->getStatusCode() . "\n";    }



} catch (Exception $e) {    if (!$found) {

    echo "Error: " . $e->getMessage() . "\n";        echo "❌ Route 'master.divisi.index' not found\n";

    echo "File: " . $e->getFile() . "\n";    }

    echo "Line: " . $e->getLine() . "\n";

    echo "Trace:\n" . $e->getTraceAsString() . "\n";    echo "\n=== Master routes found ===\n";

}    if (empty($masterRoutes)) {
        echo "No master routes found!\n";
    } else {
        foreach (array_slice($masterRoutes, 0, 10) as $route) {
            echo "- " . $route . "\n";
        }
        if (count($masterRoutes) > 10) {
            echo "... and " . (count($masterRoutes) - 10) . " more\n";
        }
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
