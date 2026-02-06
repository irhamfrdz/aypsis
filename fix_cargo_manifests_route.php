<?php

/**
 * Route untuk membuat manifest yang hilang untuk kontainer CARGO
 * 
 * Tambahkan route ini ke routes/web.php:
 * 
 * Route::get('/admin/fix-cargo-manifests', function() {
 *     require base_path('fix_cargo_manifests_route.php');
 * })->middleware('auth');
 * 
 * Atau jalankan langsung dengan mengakses URL di browser
 */

use App\Models\NaikKapal;
use App\Models\Manifest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

// Cek authentication
if (!Auth::check()) {
    return response()->json(['error' => 'Unauthorized'], 401);
}

// Cek permission (optional, sesuaikan dengan permission sistem Anda)
if (!Auth::user()->can('ob-view')) {
    return response()->json(['error' => 'Tidak memiliki permission untuk menjalankan script ini'], 403);
}

try {
    DB::beginTransaction();
    
    $output = [];
    $output[] = "===== FIX CARGO MANIFESTS =====";
    $output[] = "User: " . Auth::user()->name;
    $output[] = "Waktu: " . now()->format('Y-m-d H:i:s');
    $output[] = "";
    $output[] = "Mulai memeriksa kontainer CARGO yang sudah OB...";
    $output[] = "";
    
    // Cari semua naik_kapal yang sudah OB
    $naikKapals = NaikKapal::where('sudah_ob', true)
        ->whereNotNull('nomor_kontainer')
        ->whereNotNull('nama_kapal')
        ->whereNotNull('no_voyage')
        ->with('prospek.tandaTerima')
        ->get();
    
    $output[] = "Ditemukan " . $naikKapals->count() . " kontainer yang sudah OB";
    $output[] = "";
    
    $created = 0;
    $skipped = 0;
    $errors = 0;
    $details = [];
    
    foreach ($naikKapals as $naikKapal) {
        $detail = [];
        $detail[] = "Kontainer: {$naikKapal->nomor_kontainer} ({$naikKapal->tipe_kontainer})";
        $detail[] = "Kapal: {$naikKapal->nama_kapal} / {$naikKapal->no_voyage}";
        
        // Cek apakah sudah ada manifest untuk kontainer ini
        $existingManifest = Manifest::where('nomor_kontainer', $naikKapal->nomor_kontainer)
            ->where('no_voyage', $naikKapal->no_voyage)
            ->where('nama_kapal', $naikKapal->nama_kapal)
            ->first();
        
        if ($existingManifest) {
            $detail[] = "Status: ✓ Manifest sudah ada (ID: {$existingManifest->id}, Nomor: {$existingManifest->nomor_bl})";
            $skipped++;
        } else {
            try {
                // Buat manifest baru
                $manifest = new Manifest();
                $manifest->nomor_kontainer = $naikKapal->nomor_kontainer;
                $manifest->no_seal = $naikKapal->no_seal;
                $manifest->tipe_kontainer = $naikKapal->tipe_kontainer;
                $manifest->size_kontainer = $naikKapal->size_kontainer;
                $manifest->nama_kapal = $naikKapal->nama_kapal;
                $manifest->no_voyage = $naikKapal->no_voyage;
                $manifest->nama_barang = $naikKapal->jenis_barang;
                $manifest->volume = $naikKapal->total_volume;
                $manifest->tonnage = $naikKapal->total_tonase;
                $manifest->pelabuhan_muat = $naikKapal->asal_kontainer;
                $manifest->pelabuhan_bongkar = $naikKapal->ke;
                $manifest->tanggal_berangkat = $naikKapal->tanggal_ob ?? now();
                
                // Data pengirim/penerima dari prospek jika ada
                if ($naikKapal->prospek_id && $naikKapal->prospek) {
                    $manifest->prospek_id = $naikKapal->prospek_id;
                    $manifest->pengirim = $naikKapal->prospek->pt_pengirim;
                    
                    $penerima = null;
                    if ($naikKapal->prospek->tandaTerima) {
                        $penerima = $naikKapal->prospek->tandaTerima->penerima;
                        $manifest->alamat_penerima = $naikKapal->prospek->tandaTerima->alamat_penerima;
                    }
                    $manifest->penerima = $penerima ?? $naikKapal->prospek->tujuan_pengiriman;
                }
                
                // Generate nomor manifest
                $lastManifest = Manifest::whereNotNull('nomor_bl')
                    ->orderBy('id', 'desc')
                    ->lockForUpdate() // Prevent race condition
                    ->first();
                
                if ($lastManifest && $lastManifest->nomor_bl) {
                    preg_match('/\d+/', $lastManifest->nomor_bl, $matches);
                    $lastNumber = isset($matches[0]) ? intval($matches[0]) : 0;
                    $nextNumber = str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
                    $manifest->nomor_bl = 'MNF-' . $nextNumber;
                } else {
                    $manifest->nomor_bl = 'MNF-000001';
                }
                
                $manifest->created_by = Auth::id();
                $manifest->updated_by = Auth::id();
                
                $manifest->save();
                
                $detail[] = "Status: ✓ Manifest berhasil dibuat (ID: {$manifest->id}, Nomor: {$manifest->nomor_bl})";
                $created++;
                
            } catch (\Exception $e) {
                $detail[] = "Status: ✗ Error - " . $e->getMessage();
                $errors++;
            }
        }
        
        $detail[] = "---";
        $details = array_merge($details, $detail);
    }
    
    DB::commit();
    
    $output[] = "";
    $output[] = "===== DETAIL =====";
    $output = array_merge($output, $details);
    $output[] = "";
    $output[] = "===== RINGKASAN =====";
    $output[] = "Total diperiksa: " . $naikKapals->count();
    $output[] = "Manifest dibuat: $created";
    $output[] = "Sudah ada (dilewati): $skipped";
    $output[] = "Error: $errors";
    $output[] = "";
    
    if ($created > 0) {
        $output[] = "✓ Berhasil membuat $created manifest baru!";
    } else {
        $output[] = "ℹ Tidak ada manifest yang perlu dibuat.";
    }
    
    // Return as HTML with pre-formatted text
    $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Fix CARGO Manifests</title>
    <style>
        body { 
            font-family: monospace; 
            padding: 20px; 
            background: #f5f5f5; 
        }
        pre { 
            background: #2d3748; 
            color: #48bb78; 
            padding: 20px; 
            border-radius: 8px;
            overflow-x: auto;
        }
        .success { color: #48bb78; }
        .error { color: #f56565; }
        .info { color: #4299e1; }
    </style>
</head>
<body>
    <h1>Fix CARGO Manifests - Results</h1>
    <pre>' . implode("\n", $output) . '</pre>
    <p><a href="javascript:history.back()">← Kembali</a></p>
</body>
</html>';
    
    return response($html);
    
} catch (\Exception $e) {
    DB::rollBack();
    
    return response()->json([
        'error' => true,
        'message' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ], 500);
}
