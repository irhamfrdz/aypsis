<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use Illuminate\Support\Facades\DB;
$rows = DB::select('SHOW COLUMNS FROM kontainers');
foreach ($rows as $r) {
    echo "{$r->Field} => {$r->Type}\n";
}
