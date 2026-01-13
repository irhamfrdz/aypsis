<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Bl;
use App\Models\NaikKapal;

$voyage = 'SR01JB26';

echo "=== FIX MISSING BLS FOR VOYAGE {$voyage} ===\n\n";

// Get all naik_kapal that have sudah_ob = 1 but no corresponding BLS
$naikKapalOb = DB::table('naik_kapal')
    ->where('no_voyage', $voyage)
    ->where('sudah_ob', 1)
    ->get();

$createdCount = 0;
$skippedCount = 0;
$errorCount = 0;

foreach ($naikKapalOb as $nk) {
    // Check if BLS already exists
    $exists = DB::table('bls')
        ->where('no_voyage', $voyage)
        ->where('nama_kapal', $nk->nama_kapal)
        ->where('nomor_kontainer', $nk->nomor_kontainer)
        ->exists();
    
    if ($exists) {
        $skippedCount++;
        continue;
    }
    
    try {
        // Create BLS record
        $bl = new Bl();
        
        // Copy data from naik_kapal
        $bl->nomor_kontainer = $nk->nomor_kontainer;
        $bl->no_seal = $nk->no_seal;
        $bl->nama_barang = $nk->jenis_barang;
        $bl->tipe_kontainer = $nk->tipe_kontainer;
        $bl->size_kontainer = $nk->size_kontainer;
        $bl->nama_kapal = $nk->nama_kapal;
        $bl->no_voyage = $nk->no_voyage;
        $bl->asal_kontainer = $nk->asal_kontainer;
        $bl->ke = $nk->ke;
        $bl->pelabuhan_asal = $nk->pelabuhan_asal;
        $bl->pelabuhan_tujuan = $nk->pelabuhan_tujuan;
        $bl->tonnage = $nk->total_tonase;
        $bl->volume = $nk->total_volume;
        $bl->kuantitas = $nk->kuantitas;
        
        // Set prospek_id if available
        if ($nk->prospek_id) {
            $bl->prospek_id = $nk->prospek_id;
        }
        
        // Set OB status
        $bl->sudah_ob = true;
        $bl->supir_id = $nk->supir_id;
        $bl->tanggal_ob = $nk->tanggal_ob ?? now();
        $bl->catatan_ob = $nk->catatan_ob ?? 'Auto-fixed missing BLS';
        
        // Set TL status if applicable
        if ($nk->is_tl) {
            $bl->sudah_tl = true;
        }
        
        // Generate nomor BL
        $lastBl = Bl::whereNotNull('nomor_bl')
            ->orderBy('id', 'desc')
            ->first();
        
        if ($lastBl && $lastBl->nomor_bl) {
            preg_match('/\d+/', $lastBl->nomor_bl, $matches);
            $lastNumber = isset($matches[0]) ? intval($matches[0]) : 0;
            $nextNumber = str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
            $bl->nomor_bl = 'BL-' . $nextNumber;
        } else {
            $bl->nomor_bl = 'BL-000001';
        }
        
        $bl->created_by = 1; // Admin
        $bl->updated_by = 1;
        
        $bl->save();
        
        $createdCount++;
        $tlFlag = $nk->is_tl ? ' [TL]' : '';
        echo "✅ Created BLS for: {$nk->nomor_kontainer} ({$bl->nomor_bl}){$tlFlag}\n";
        
    } catch (\Exception $e) {
        $errorCount++;
        echo "❌ Error for {$nk->nomor_kontainer}: {$e->getMessage()}\n";
    }
}

echo "\n=== SUMMARY ===\n";
echo "Created: {$createdCount}\n";
echo "Skipped (already exists): {$skippedCount}\n";
echo "Errors: {$errorCount}\n";

// Verify final count
$finalCount = DB::table('bls')->where('no_voyage', $voyage)->count();
echo "\nTotal BLS for {$voyage} now: {$finalCount}\n";
