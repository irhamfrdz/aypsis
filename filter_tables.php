<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Schema;

foreach(Schema::getTableListing() as $t) {
    if(stripos($t, 'gudang') !== false || stripos($t, 'move') !== false || stripos($t, 'mutasi') !== false) {
        echo $t . "\n";
    }
}
