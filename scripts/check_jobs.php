<?php
require __DIR__ . '/../vendor/autoload.php';
use Illuminate\Database\Capsule\Manager as Capsule;
$capsule = new Capsule;
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => '127.0.0.1',
    'database' => 'aypsis',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();
try {
    $jobs = $capsule->getConnection()->table('jobs')->count();
    $failed = $capsule->getConnection()->table('failed_jobs')->count();
    echo "jobs:$jobs failed:$failed\n";
} catch (Throwable $e) {
    echo 'DB error: '.$e->getMessage();
}
