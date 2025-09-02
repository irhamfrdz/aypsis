<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "Wipe Tagihan DB - START\n";
    $beforeTagihan = \DB::table('tagihan_kontainer_sewa')->count();
    $beforePivots = \DB::table('tagihan_kontainer_sewa_kontainers')->count();
    echo "Before: tagihan=$beforeTagihan, pivots=$beforePivots\n";

    // delete pivot rows first
    $deletedPivots = \DB::table('tagihan_kontainer_sewa_kontainers')->delete();
    $deletedTagihan = \DB::table('tagihan_kontainer_sewa')->delete();

    // reset auto-increment (MySQL)
    try {
        \DB::statement("ALTER TABLE tagihan_kontainer_sewa AUTO_INCREMENT = 1");
        \DB::statement("ALTER TABLE tagihan_kontainer_sewa_kontainers AUTO_INCREMENT = 1");
    } catch (Exception $e) {
        echo "Warning: failed to reset AUTO_INCREMENT: " . $e->getMessage() . "\n";
    }

    $afterTagihan = \DB::table('tagihan_kontainer_sewa')->count();
    $afterPivots = \DB::table('tagihan_kontainer_sewa_kontainers')->count();

    echo "Deleted: pivots_removed=$deletedPivots, tagihan_removed=$deletedTagihan\n";
    echo "After: tagihan=$afterTagihan, pivots=$afterPivots\n";
    echo "Wipe Tagihan DB - DONE\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
