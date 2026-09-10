<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
print_r(Illuminate\Support\Facades\Schema::getColumnListing('invoice_aktivitas_lain'));
