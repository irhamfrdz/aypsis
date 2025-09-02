<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $exists = \Schema::hasTable('master_kegiatans');
    echo "table master_kegiatans exists: " . ($exists ? 'yes' : 'no') . "\n";
    if ($exists) {
        $count = \DB::table('master_kegiatans')->count();
        echo "count: $count\n";
        $rows = \DB::table('master_kegiatans')->orderBy('id','desc')->limit(10)->get();
        foreach ($rows as $r) {
            echo sprintf("id=%d kode=%s kegiatan=%s status=%s\n", $r->id, $r->kode, $r->kegiatan, $r->status);
        }
        echo "\ncolumns:\n";
        $cols = \DB::select("SHOW COLUMNS FROM master_kegiatans");
        foreach ($cols as $c) {
            echo sprintf("%s %s %s\n", $c->Field, $c->Type, $c->Null);
        }
    }
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
}
