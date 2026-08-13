<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$query = ['status' => '', 'tipe' => 'FCL'];
echo route('prospek.export-excel', $query);
