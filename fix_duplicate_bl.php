<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Bl;
use Illuminate\Support\Facades\DB;

$voyage = 'AP11PJ26';

echo "Mengecek data double untuk voyage: {$voyage}...\n\n";

$duplicates = Bl::select('nomor_bl')
    ->where('no_voyage', $voyage)
    ->groupBy('nomor_bl')
    ->havingRaw('COUNT(id) > 1')
    ->get();

if ($duplicates->isEmpty()) {
    echo "Tidak ada data double yang ditemukan.\n";
    exit;
}

$deletedCount = 0;

DB::beginTransaction();
try {
    foreach ($duplicates as $dup) {
        // Ambil semua record untuk voyage dan nomor_bl ini, urutkan berdasarkan ID ASC
        // agar data pertama (yang paling awal dibuat) dipertahankan
        $records = Bl::where('no_voyage', $voyage)
            ->where('nomor_bl', $dup->nomor_bl)
            ->orderBy('id', 'asc')
            ->get();

        $first = true;
        foreach ($records as $record) {
            if ($first) {
                $first = false;
                echo "-> [TETAP] ID: {$record->id} | BL: {$dup->nomor_bl}\n";
                continue;
            }

            echo "   [HAPUS] ID: {$record->id} | BL: {$dup->nomor_bl}\n";
            $record->delete();
            $deletedCount++;
        }
    }
    
    DB::commit();
    echo "\nSelesai! Berhasil menghapus {$deletedCount} data double.\n";
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "\nTerjadi kesalahan: " . $e->getMessage() . "\n";
}
