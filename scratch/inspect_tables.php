<?php

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "--- naik_kapal columns ---\n";
print_r(Schema::getColumnListing('naik_kapal'));

echo "--- bls columns ---\n";
print_r(Schema::getColumnListing('bls'));

echo "--- sample naik_kapal ---\n";
print_r(DB::table('naik_kapal')->whereNotNull('no_voyage')->where('no_voyage', '!=', '')->first());

echo "--- sample bls ---\n";
print_r(DB::table('bls')->whereNotNull('no_voyage')->where('no_voyage', '!=', '')->first());
