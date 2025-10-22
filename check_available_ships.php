<?php

require_once 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule;

$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => 'localhost',
    'database'  => 'aypsis',
    'username'  => 'root',
    'password'  => '',
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

// Get first 5 kapals
$kapals = Capsule::table('master_kapals')
    ->select('nama_kapal', 'nickname')
    ->limit(5)
    ->get();

echo "📋 Available Ships:\n";
foreach($kapals as $k) {
    echo "- {$k->nama_kapal} => Nickname: {$k->nickname}\n";
}
