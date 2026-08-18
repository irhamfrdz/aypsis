<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$vendorCount = \App\Models\SuratJalan::has('tagihanSupirVendor')->whereNotIn('status', ['cancelled', 'draft'])->whereNotNull('supir')->distinct('supir')->count('supir');
echo "Vendor Count: " . $vendorCount . "\n";
