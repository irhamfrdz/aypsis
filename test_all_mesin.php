<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$mesins = App\Models\Mesin::all();
foreach($mesins as $mesin) {
    echo 'IP: ' . $mesin->ip_address . ' - ';
    $zk = new App\Services\ZKLibrary($mesin->ip_address, (int)$mesin->port);
    if ($zk->connect()) {
        $users = $zk->getUser();
        echo 'Connected. Users: ' . (is_array($users) ? count($users) : 'Not array') . PHP_EOL;
        $zk->disconnect();
    } else {
        echo 'Failed to connect.' . PHP_EOL;
    }
}
