<?php
$env = getenv('DB_DATABASE') ?: 'database/database.sqlite';
// Ensure we use the file DB
putenv('DB_DATABASE=' . $env);
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $count = \DB::table('users')->count();
    echo "USERS_COUNT=" . $count . "\n";
    $rows = \DB::table('users')->get();
    foreach ($rows as $r) {
        echo "USER: id={$r->id}, username={$r->username}, name={$r->name}\n";
    }
} catch (\Throwable $e) {
    echo 'ERR: ' . $e->getMessage() . "\n";
}
