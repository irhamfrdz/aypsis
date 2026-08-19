<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$data = DB::select("SELECT * FROM manifests WHERE nama_kapal LIKE 'BCA%' LIMIT 2");
echo "BCA Rows:\n";
echo json_encode($data, JSON_PRETTY_PRINT);
