<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$statuses = \App\Models\AlatBerat::pluck('status')->unique();
echo "STATUSES: " . json_encode($statuses) . "\n";
echo "COUNT: " . \App\Models\AlatBerat::count() . "\n";
