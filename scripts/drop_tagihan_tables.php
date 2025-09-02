<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$tables = ['tagihan_kontainer_sewa_kontainers', 'tagihan_kontainer_sewa'];

foreach ($tables as $t) {
    if (!Schema::hasTable($t)) {
        echo "MISSING $t\n";
        continue;
    }

    $rows = DB::table($t)->get()->toArray();
    $path = __DIR__ . '/../backups/' . $t . '.json';
    file_put_contents($path, json_encode($rows));
    echo "BACKED_UP $t -> $path (" . count($rows) . " rows)\n";
}

foreach ($tables as $t) {
    if (!Schema::hasTable($t)) {
        echo "SKIP_DROP_MISSING $t\n";
        continue;
    }

    try {
        DB::statement("DROP TABLE IF EXISTS `$t`");
        echo "DROPPED $t\n";
    } catch (\Exception $e) {
        echo "ERROR_DROPPING $t: " . $e->getMessage() . "\n";
    }
}

echo "DONE\n";
