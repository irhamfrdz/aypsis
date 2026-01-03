<?php

/**
 * Script untuk menghapus data naik_kapal dengan nomor voyage SR18JB25
 * 
 * Usage: php delete_naik_kapal_sr18jb25.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\NaikKapal;
use Illuminate\Support\Facades\DB;

try {
    echo "=================================================\n";
    echo "Script Hapus Data Naik Kapal - Voyage SR18JB25\n";
    echo "=================================================\n\n";

    // Get data yang akan dihapus
    $naikKapals = NaikKapal::where('no_voyage', 'SR18JB25')->get();
    
    if ($naikKapals->isEmpty()) {
        echo "❌ Tidak ada data naik_kapal dengan nomor voyage SR18JB25\n";
        exit(0);
    }

    echo "📊 Ditemukan " . $naikKapals->count() . " data naik_kapal dengan voyage SR18JB25:\n";
    echo "─────────────────────────────────────────────────\n";
    
    foreach ($naikKapals as $index => $nk) {
        echo ($index + 1) . ". ID: {$nk->id}\n";
        echo "   - Nomor Kontainer: " . ($nk->nomor_kontainer ?: '-') . "\n";
        echo "   - Kapal: " . ($nk->nama_kapal ?: '-') . "\n";
        echo "   - Voyage: {$nk->no_voyage}\n";
        echo "   - Prospek ID: " . ($nk->prospek_id ?: '-') . "\n";
        echo "   - Created: " . ($nk->created_at ? $nk->created_at->format('d/m/Y H:i') : '-') . "\n";
        echo "\n";
    }

    echo "─────────────────────────────────────────────────\n";
    echo "⚠️  PERINGATAN: Anda akan menghapus {$naikKapals->count()} data!\n";
    echo "Apakah Anda yakin ingin melanjutkan? (yes/no): ";
    
    $handle = fopen("php://stdin", "r");
    $line = fgets($handle);
    $confirmation = trim(strtolower($line));
    fclose($handle);
    
    if ($confirmation !== 'yes' && $confirmation !== 'y') {
        echo "\n❌ Operasi dibatalkan.\n";
        exit(0);
    }

    echo "\n🔄 Memulai proses penghapusan...\n\n";

    DB::beginTransaction();
    
    try {
        $deletedCount = 0;
        
        foreach ($naikKapals as $index => $nk) {
            echo "Menghapus data #" . ($index + 1) . " (ID: {$nk->id})... ";
            $nk->delete();
            $deletedCount++;
            echo "✓ Berhasil\n";
        }
        
        DB::commit();
        
        echo "\n=================================================\n";
        echo "✅ SUKSES!\n";
        echo "=================================================\n";
        echo "Total data yang berhasil dihapus: {$deletedCount}\n";
        echo "Voyage: SR18JB25\n";
        echo "Waktu: " . date('d/m/Y H:i:s') . "\n";
        echo "=================================================\n";
        
    } catch (\Exception $e) {
        DB::rollBack();
        throw $e;
    }

} catch (\Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    exit(1);
}
