<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::create('/gate-in/get-kontainers-surat-jalan?terminal_id=1&kapal_id=1', 'GET')
);

echo "Status: " . $response->getStatusCode() . PHP_EOL;
echo "Content: " . $response->getContent() . PHP_EOL;

$kernel->terminate($request, $response);
