<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    DB::beginTransaction();

    // Check the target row
    $row = DB::table('biaya_kapal_barang')->where('id', 553)->first();
    if ($row) {
        echo "Before Fix:\n";
        echo "ID: {$row->id}, jumlah: {$row->jumlah}, tarif: {$row->tarif}, subtotal: {$row->subtotal}\n";

        // Update row
        $newJumlah = 523.912;
        $newSubtotal = $newJumlah * $row->tarif; // 4191296

        DB::table('biaya_kapal_barang')->where('id', 553)->update([
            'jumlah' => $newJumlah,
            'subtotal' => $newSubtotal
        ]);

        $rowAfter = DB::table('biaya_kapal_barang')->where('id', 553)->first();
        echo "After Fix:\n";
        echo "ID: {$rowAfter->id}, jumlah: {$rowAfter->jumlah}, tarif: {$rowAfter->tarif}, subtotal: {$rowAfter->subtotal}\n";
    } else {
        echo "Row with ID 553 not found!\n";
    }

    DB::commit();
} catch (\Exception $e) {
    DB::rollBack();
    echo "Error: " . $e->getMessage() . "\n";
}
