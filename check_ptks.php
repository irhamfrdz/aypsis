<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$tables = ['pranotalist', 'pranota_tagihan_kontainer_sewa', 'pranota_tagihan_cat'];

foreach ($tables as $table) {
    try {
        $count = DB::table($table)->count();
        echo "$table: $count records\n";

        if ($count > 0) {
            $ptks = DB::table($table)->where('no_invoice', 'like', 'PTKS%')->orderBy('no_invoice', 'desc')->take(5)->get();
            if ($ptks->count() > 0) {
                echo "  PTKS records:\n";
                foreach ($ptks as $p) {
                    echo "    {$p->no_invoice}\n";
                }
            }
        }
    } catch (Exception $e) {
        echo "Error checking $table: " . $e->getMessage() . "\n";
    }
}
