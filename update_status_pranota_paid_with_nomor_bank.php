<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;
use Illuminate\Support\Facades\DB;

echo "=== Update Status Pranota to 'paid' untuk items yang memiliki nomor_bank ===\n\n";

try {
    DB::beginTransaction();

    // Get all items yang memiliki nomor_bank dan status_pranota belum 'paid'
    $tagihans = DaftarTagihanKontainerSewa::whereNotNull('nomor_bank')
        ->where('nomor_bank', '!=', '')
        ->where(function($query) {
            $query->whereNull('status_pranota')
                  ->orWhere('status_pranota', '!=', 'paid');
        })
        ->get();

    echo "Ditemukan " . $tagihans->count() . " items dengan nomor_bank yang perlu diupdate\n\n";

    if ($tagihans->count() === 0) {
        echo "Tidak ada data yang perlu diupdate.\n";
        DB::rollBack();
        exit(0);
    }

    $updated = 0;
    foreach ($tagihans as $tagihan) {
        echo "Updating ID: {$tagihan->id} | Kontainer: {$tagihan->nomor_kontainer} | Nomor Bank: {$tagihan->nomor_bank} | Status Lama: " . ($tagihan->status_pranota ?? 'null') . "\n";
        
        $tagihan->update(['status_pranota' => 'paid']);
        $updated++;
    }

    DB::commit();

    echo "\n=== Selesai ===\n";
    echo "Total items yang diupdate: {$updated}\n";
    echo "Status pranota berubah menjadi: 'paid'\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
