<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$columns = array_map(function($c) { return $c->getName(); }, \Schema::getConnection()->getDoctrineSchemaManager()->listTableColumns('stock_bans'));
echo implode("\n", $columns);
