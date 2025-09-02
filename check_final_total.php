<?php

require 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as DB;

// Setup database connection
$capsule = new DB;
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => 'localhost',
    'database' => 'aypsis',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

$pranota = DB::table('pranotalist')->where('no_invoice', 'PTK12509000001')->first();
echo "Pranota: {$pranota->no_invoice}\n";
echo "Total Amount: Rp " . number_format($pranota->total_amount, 2) . "\n";
echo "Status: {$pranota->status}\n";
