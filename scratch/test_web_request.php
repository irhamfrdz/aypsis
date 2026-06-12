<?php

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

use Illuminate\Http\Request;

// Create a GET request mimicking the browser query params
$request = Request::create('/rekap-biaya-kapal/show', 'GET', [
    'kapal' => 'KM MERATUS KAMPAR',
    'voyage' => 'MERATUSWS114N',
]);

$response = $kernel->handle($request);

echo 'Response Status: '.$response->getStatusCode()."\n";

// Let's read the debug file now
$debugFile = __DIR__.'/debug_web.json';
if (file_exists($debugFile)) {
    echo "Debug JSON contents:\n";
    echo file_get_contents($debugFile)."\n";
} else {
    echo "Debug file not created!\n";
}
