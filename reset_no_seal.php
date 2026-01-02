<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Resetting all no_seal to null...\n";
DB::table('bls')->update(['no_seal' => null, 'updated_at' => now()]);
echo "Done! All no_seal values have been reset.\n";
