<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $orphanRows = \DB::table('tagihan_kontainer_sewa_kontainers')
        ->whereNotIn('tagihan_id', function ($q) {
            $q->select('id')->from('tagihan_kontainer_sewa');
        })->get();

    $count = $orphanRows->count();
    echo "Found $count orphan pivot rows\n";
    if ($count > 0) {
        echo "Sample orphan ids (tagihan_id):\n";
        foreach ($orphanRows->pluck('tagihan_id')->unique()->take(50) as $id) {
            echo " - $id\n";
        }

        // Delete them
        $deleted = \DB::table('tagihan_kontainer_sewa_kontainers')
            ->whereNotIn('tagihan_id', function ($q) {
                $q->select('id')->from('tagihan_kontainer_sewa');
            })->delete();

        echo "Deleted $deleted orphan pivot rows\n";
    } else {
        echo "No orphan pivot rows to delete.\n";
    }
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
}
