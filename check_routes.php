<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Route;

$routes = Route::getRoutes();
$pranotaRoutes = [];

foreach($routes as $route) {
    if(str_contains($route->uri(), 'pranota-perbaikan-kontainer')) {
        $pranotaRoutes[] = [
            'uri' => $route->uri(),
            'methods' => $route->methods(),
            'middleware' => $route->middleware(),
            'action' => $route->getActionName()
        ];
    }
}

echo 'Pranota Perbaikan Kontainer Routes:' . PHP_EOL;
foreach($pranotaRoutes as $route) {
    echo '- ' . implode('|', $route['methods']) . ' ' . $route['uri'] . PHP_EOL;
    echo '  Middleware: ' . implode(', ', $route['middleware']) . PHP_EOL;
    echo '  Action: ' . $route['action'] . PHP_EOL;
    echo PHP_EOL;
}
?>
