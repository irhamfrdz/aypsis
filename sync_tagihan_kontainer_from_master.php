<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Kontainer;

echo "===================================================================\n";
echo "  SYNC TAGIHAN KONTAINER SEWA FROM MASTER KONTAINERS\n";
echo "===================================================================\n\n";

// Parse command line arguments
$options = getopt('', ['container:', 'vendor:', 'dry-run', 'verbose', 'clean']);

$containerFilter = $options['container'] ?? null;
$vendorFilter = $options['vendor'] ?? null;
$dryRun = isset($options['dry-run']);
$verbose = isset($options['verbose']);
$cleanInvalid = isset($options['clean']);

if ($dryRun) {
    echo "[DRY RUN MODE] Tidak ada perubahan yang akan disimpan\n\n";
}

// Build query
$query = Kontainer::whereNotNull('tanggal_mulai_sewa');

if ($containerFilter) {
    $query->where('nomor_seri_gabungan', $containerFilter);
    echo "Filter: Kontainer = {$containerFilter}\n";
}

if ($vendorFilter) {
    $query->where('vendor', $vendorFilter);
    echo "Filter: Vendor = {$vendorFilter}\n";
}

$kontainers = $query->get();

echo "Ditemukan " . $kontainers->count() . " kontainer dengan tanggal_mulai_sewa\n\n";

if ($kontainers->isEmpty()) {
    echo "Tidak ada kontainer yang perlu diproses.\n";
    exit(0);
}

$stats = [
    'total_kontainer' => 0,
    'total_periode_created' => 0,
    'total_periode_updated' => 0,
    'total_periode_skipped' => 0,
    'total_periode_deleted' => 0,
    'errors' => 0,
];

foreach ($kontainers as $kontainer) {
    $stats['total_kontainer']++;
    
    echo "-------------------------------------------------------------------\n";
    echo "Kontainer: {$kontainer->nomor_seri_gabungan}\n";
    echo "  Vendor: {$kontainer->vendor}\n";
    echo "  Ukuran: {$kontainer->ukuran}\n";
    echo "  Tanggal Mulai: {$kontainer->tanggal_mulai_sewa}\n";
    echo "  Tanggal Selesai: " . ($kontainer->tanggal_selesai_sewa ?? 'NULL (masih berjalan)') . "\n";
    
    try {
        $tanggalMulai = Carbon::parse($kontainer->tanggal_mulai_sewa);
    } catch (Exception $e) {
        echo "  [ERROR] Invalid tanggal_mulai_sewa, skipping.\n\n";
        $stats['errors']++;
        continue;
    }
    
    // Tentukan tanggal akhir (selesai sewa atau hari ini)
    $tanggalSelesai = $kontainer->tanggal_selesai_sewa 
        ? Carbon::parse($kontainer->tanggal_selesai_sewa) 
        : Carbon::now();
    
    // Cari harga sewa dari data existing
    $existingTagihan = DB::table('daftar_tagihan_kontainer_sewa')
        ->where('nomor_kontainer', $kontainer->nomor_seri_gabungan)
        ->whereNotNull('dpp')
        ->where('dpp', '>', 0)
        ->orderBy('id', 'desc')
        ->first();
    
    $hargaSewa = $existingTagihan ? $existingTagihan->dpp : 0;
    
    // Jika tidak ada harga, coba cek dari master pricelist
    if ($hargaSewa == 0) {
        $pricelist = DB::table('master_pricelist_sewa_kontainers')
            ->where('vendor', $kontainer->vendor)
            ->where('ukuran_kontainer', $kontainer->ukuran)
            ->where('tarif', 'Bulanan')
            ->first();
        
        if ($pricelist) {
            $hargaSewa = $pricelist->harga;
        }
    }
    
    echo "  Harga Sewa: Rp " . number_format($hargaSewa, 0, ',', '.') . "\n";
    
    // Clean invalid periods if --clean flag is set
    if ($cleanInvalid) {
        $invalidQuery = DB::table('daftar_tagihan_kontainer_sewa')
            ->where('nomor_kontainer', $kontainer->nomor_seri_gabungan)
            ->whereNull('status_pranota');
        
        // Hapus periode yang tanggal_akhir melewati tanggal_selesai_sewa
        if ($kontainer->tanggal_selesai_sewa) {
            $invalidQuery->where(function($q) use ($tanggalMulai, $tanggalSelesai) {
                $q->where('tanggal_awal', '<', $tanggalMulai->format('Y-m-d'))
                  ->orWhere('tanggal_akhir', '>', $tanggalSelesai->format('Y-m-d'));
            });
        } else {
            $invalidQuery->where('tanggal_awal', '<', $tanggalMulai->format('Y-m-d'));
        }
        
        $invalidRecords = $invalidQuery->get();
        
        if ($invalidRecords->count() > 0) {
            if (!$dryRun) {
                $invalidQuery2 = DB::table('daftar_tagihan_kontainer_sewa')
                    ->where('nomor_kontainer', $kontainer->nomor_seri_gabungan)
                    ->whereNull('status_pranota');
                
                if ($kontainer->tanggal_selesai_sewa) {
                    $invalidQuery2->where(function($q) use ($tanggalMulai, $tanggalSelesai) {
                        $q->where('tanggal_awal', '<', $tanggalMulai->format('Y-m-d'))
                          ->orWhere('tanggal_akhir', '>', $tanggalSelesai->format('Y-m-d'));
                    });
                } else {
                    $invalidQuery2->where('tanggal_awal', '<', $tanggalMulai->format('Y-m-d'));
                }
                
                $invalidQuery2->delete();
                
                echo "  [DELETED] {$invalidRecords->count()} invalid period(s)\n";
            } else {
                echo "  [WILL DELETE] {$invalidRecords->count()} invalid period(s)\n";
            }
            $stats['total_periode_deleted'] += $invalidRecords->count();
        }
    }
    
    // Generate periode
    $currentStart = $tanggalMulai->copy();
    $periodeNum = 1;
    
    while ($currentStart->lte($tanggalSelesai)) {
        $currentEnd = $currentStart->copy()->addMonth()->subDay();
        
        // Jika currentEnd melewati tanggal_selesai_sewa, potong
        if ($kontainer->tanggal_selesai_sewa && $currentEnd->gt($tanggalSelesai)) {
            $currentEnd = $tanggalSelesai->copy();
        }
        
        // Cek apakah tagihan periode ini sudah ada
        $existing = DB::table('daftar_tagihan_kontainer_sewa')
            ->where('nomor_kontainer', $kontainer->nomor_seri_gabungan)
            ->where('periode', $periodeNum)
            ->whereNull('status_pranota')
            ->first();
        
        if ($existing) {
            // Update jika berbeda
            $needUpdate = false;
            $updates = [];
            
            // Format masa: "4 Mar 2025 - 3 Apr 2025"
            $masaString = $currentStart->format('j M Y') . ' - ' . $currentEnd->format('j M Y');
            
            if ($existing->tanggal_awal != $currentStart->format('Y-m-d')) {
                $updates['tanggal_awal'] = $currentStart->format('Y-m-d');
                $needUpdate = true;
            }
            
            if ($existing->tanggal_akhir != $currentEnd->format('Y-m-d')) {
                $updates['tanggal_akhir'] = $currentEnd->format('Y-m-d');
                $needUpdate = true;
            }
            
            if ($existing->masa != $masaString) {
                $updates['masa'] = $masaString;
                $needUpdate = true;
            }
            
            if ($hargaSewa > 0 && $existing->dpp != $hargaSewa) {
                $updates['dpp'] = $hargaSewa;
                $needUpdate = true;
            }
            
            if ($existing->vendor != $kontainer->vendor) {
                $updates['vendor'] = $kontainer->vendor;
                $needUpdate = true;
            }
            
            if ($existing->size != $kontainer->ukuran) {
                $updates['size'] = $kontainer->ukuran;
                $needUpdate = true;
            }
            
            if ($needUpdate && !$dryRun) {
                $updates['updated_at'] = now();
                DB::table('daftar_tagihan_kontainer_sewa')
                    ->where('id', $existing->id)
                    ->update($updates);
                
                if ($verbose) {
                    echo "  [UPDATED] Periode {$periodeNum}: {$currentStart->format('Y-m-d')} s/d {$currentEnd->format('Y-m-d')}\n";
                }
                $stats['total_periode_updated']++;
            } elseif ($needUpdate && $dryRun) {
                if ($verbose) {
                    echo "  [WILL UPDATE] Periode {$periodeNum}: {$currentStart->format('Y-m-d')} s/d {$currentEnd->format('Y-m-d')}\n";
                }
                $stats['total_periode_updated']++;
            } else {
                if ($verbose) {
                    echo "  [OK] Periode {$periodeNum}: {$currentStart->format('Y-m-d')} s/d {$currentEnd->format('Y-m-d')}\n";
                }
                $stats['total_periode_skipped']++;
            }
        } else {
            // Create new
            if (!$dryRun) {
                // Format masa: "4 Mar 2025 - 3 Apr 2025"
                $masaString = $currentStart->format('j M Y') . ' - ' . $currentEnd->format('j M Y');
                
                DB::table('daftar_tagihan_kontainer_sewa')->insert([
                    'vendor' => $kontainer->vendor,
                    'nomor_kontainer' => $kontainer->nomor_seri_gabungan,
                    'size' => $kontainer->ukuran,
                    'tanggal_awal' => $currentStart->format('Y-m-d'),
                    'tanggal_akhir' => $currentEnd->format('Y-m-d'),
                    'group' => '1',
                    'masa' => $masaString,
                    'tarif' => 'Bulanan',
                    'status' => 'active',
                    'dpp' => $hargaSewa,
                    'adjustment' => 0,
                    'adjustment_note' => null,
                    'periode' => $periodeNum,
                    'status_pranota' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                if ($verbose) {
                    echo "  [CREATED] Periode {$periodeNum}: {$currentStart->format('Y-m-d')} s/d {$currentEnd->format('Y-m-d')}\n";
                }
                $stats['total_periode_created']++;
            } else {
                if ($verbose) {
                    echo "  [WILL CREATE] Periode {$periodeNum}: {$currentStart->format('Y-m-d')} s/d {$currentEnd->format('Y-m-d')}\n";
                }
                $stats['total_periode_created']++;
            }
        }
        
        // Next period
        $currentStart = $currentEnd->copy()->addDay();
        $periodeNum++;
        
        // Safety break
        if ($periodeNum > 100) {
            echo "  [WARNING] Periode lebih dari 100, stopping...\n";
            break;
        }
    }
    
    echo "\n";
}

echo "===================================================================\n";
echo "SUMMARY:\n";
echo "===================================================================\n";
echo "Total Kontainer Diproses: {$stats['total_kontainer']}\n";
echo "Total Periode Created:    {$stats['total_periode_created']}\n";
echo "Total Periode Updated:    {$stats['total_periode_updated']}\n";
echo "Total Periode Deleted:    {$stats['total_periode_deleted']}\n";
echo "Total Periode Skipped:    {$stats['total_periode_skipped']}\n";
echo "Total Errors:             {$stats['errors']}\n";
echo "===================================================================\n";

if ($dryRun) {
    echo "\n[DRY RUN] Tidak ada perubahan yang disimpan.\n";
    echo "Jalankan tanpa --dry-run untuk menyimpan perubahan.\n";
}

echo "\nSELESAI\n";
