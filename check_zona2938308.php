<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\DaftarTagihanKontainerSewa;
use App\Models\MasterPricelistSewaKontainer;

echo "=== CHECKING KONTAINER ZONA2938308 ===\n\n";

// Cek kolom yang ada dulu
echo "Mengecek struktur tabel...\n";
$columns = DB::select('SHOW COLUMNS FROM daftar_tagihan_kontainer_sewa');
$columnNames = [];
$containerColumn = null;

echo "Kolom-kolom di tabel:\n";
foreach ($columns as $col) {
    echo "  - {$col->Field}\n";
    $columnNames[] = $col->Field;
    
    // Cari kolom yang berisi kata "kontainer" atau "container"
    if (strpos(strtolower($col->Field), 'kontainer') !== false || 
        strpos(strtolower($col->Field), 'container') !== false) {
        $containerColumn = $col->Field;
    }
}

if (!$containerColumn) {
    echo "\nTidak ditemukan kolom kontainer. Mencoba mencari dengan LIKE...\n";
    // Cari di semua record yang mengandung ZONA2938308
    $allData = DB::table('daftar_tagihan_kontainer_sewa')
        ->whereRaw('CONCAT_WS("|", ' . implode(', ', $columnNames) . ') LIKE ?', ['%ZONA2938308%'])
        ->limit(10)
        ->get();
    
    if ($allData->count() > 0) {
        echo "\nDitemukan {$allData->count()} record yang mengandung ZONA2938308:\n\n";
        foreach ($allData as $data) {
            echo "ID: {$data->id}\n";
            foreach ($data as $key => $value) {
                if ($value && strlen($value) < 200) {
                    echo "  {$key}: {$value}\n";
                }
            }
            echo "\n---\n";
        }
    } else {
        echo "Tidak ditemukan data dengan ZONA2938308\n";
    }
    exit;
}

echo "\nMenggunakan kolom: {$containerColumn}\n\n";

// Cari data kontainer ZONA2938308 periode April-Mei 2025
$tagihan = DB::table('daftar_tagihan_kontainer_sewa')
    ->where($containerColumn, 'ZONA2938308')
    ->where('tanggal_awal', '2025-04-25')
    ->where('tanggal_akhir', '2025-05-23')
    ->first();

if (!$tagihan) {
    echo "Data tidak ditemukan dengan masa April 2025.\n";
    echo "Mencoba mencari semua data ZONA2938308...\n\n";
    
    $allData = DB::table('daftar_tagihan_kontainer_sewa')
        ->where($containerColumn, 'ZONA2938308')
        ->get();
    
    if ($allData->count() > 0) {
        echo "Ditemukan {$allData->count()} record dengan ZONA2938308:\n\n";
        foreach ($allData as $data) {
            echo "ID: {$data->id}\n";
            foreach ($data as $key => $value) {
                if ($value && strlen($value) < 200) {
                    echo "  {$key}: {$value}\n";
                }
            }
            echo "\n---\n";
        }
    } else {
        echo "Tidak ditemukan data dengan ZONA2938308\n";
    }
    exit;
}

echo "Data ditemukan!\n";
echo "ID: {$tagihan->id}\n\n";

// Tampilkan semua field
echo "=== DATA TAGIHAN ===\n";
foreach ($tagihan as $key => $value) {
    if (strlen($value) < 200) {
        echo "{$key}: {$value}\n";
    }
}

echo "\n=== ANALISIS PERHITUNGAN DPP ===\n\n";

// Ambil data penting
$vendor = $tagihan->vendor ?? null;
$size = $tagihan->ukuran ?? $tagihan->size ?? $tagihan->ukuran_kontainer ?? null;
$tarif = $tagihan->tarif ?? null;
$periode = $tagihan->periode ?? null;
$masa = $tagihan->masa ?? null;
$dpp = $tagihan->dpp ?? 0;
$tanggal_awal = $tagihan->tanggal_awal ?? null;
$tanggal_akhir = $tagihan->tanggal_akhir ?? null;

echo "Vendor: {$vendor}\n";
echo "Size: {$size}\n";
echo "Tarif: {$tarif}\n";
echo "Periode: {$periode}\n";
echo "Masa: {$masa}\n";
echo "Tanggal Awal: {$tanggal_awal}\n";
echo "Tanggal Akhir: {$tanggal_akhir}\n";
echo "DPP Saat Ini: Rp " . number_format($dpp, 0, ',', '.') . "\n\n";

// Hitung jumlah hari
$jumlah_hari = 0;
if ($tanggal_awal && $tanggal_akhir) {
    $start = new DateTime($tanggal_awal);
    $end = new DateTime($tanggal_akhir);
    $jumlah_hari = $start->diff($end)->days + 1; // +1 untuk include hari pertama
    echo "Jumlah Hari (dari tanggal): {$jumlah_hari} hari\n";
} elseif (strpos($masa, ' - ') !== false) {
    $parts = explode(' - ', $masa);
    if (count($parts) === 2) {
        try {
            $start = new DateTime($parts[0]);
            $end = new DateTime($parts[1]);
            $jumlah_hari = $start->diff($end)->days + 1;
            echo "Jumlah Hari (dari masa): {$jumlah_hari} hari\n";
        } catch (Exception $e) {
            echo "Error parsing masa: {$e->getMessage()}\n";
        }
    }
}

// Cari pricelist
echo "\n=== PRICELIST ===\n";
$pricelist = DB::table('master_pricelist_sewa_kontainers')
    ->where('ukuran_kontainer', $size)
    ->where('vendor', $vendor)
    ->where('tarif', $tarif)
    ->first();

if ($pricelist) {
    echo "Pricelist ditemukan!\n";
    echo "Harga: Rp " . number_format($pricelist->harga, 0, ',', '.') . "\n";
    echo "Tarif: {$pricelist->tarif}\n\n";
    
    // Hitung DPP yang seharusnya
    echo "=== PERHITUNGAN DPP ===\n";
    if ($tarif === 'Harian') {
        $dpp_seharusnya = $pricelist->harga * $jumlah_hari;
        echo "Tarif Harian: Rp " . number_format($pricelist->harga, 0, ',', '.') . "\n";
        echo "Jumlah Hari: {$jumlah_hari}\n";
        echo "DPP Seharusnya: Rp " . number_format($pricelist->harga, 0, ',', '.') . " × {$jumlah_hari} = Rp " . number_format($dpp_seharusnya, 0, ',', '.') . "\n";
        echo "DPP Saat Ini: Rp " . number_format($dpp, 0, ',', '.') . "\n";
        echo "Selisih: Rp " . number_format($dpp_seharusnya - $dpp, 0, ',', '.') . "\n\n";
        
        if ($dpp != $dpp_seharusnya) {
            echo "❌ DPP TIDAK SESUAI!\n";
            echo "Kemungkinan penyebab:\n";
            echo "1. Jumlah hari yang digunakan salah\n";
            echo "2. Pricelist yang digunakan berbeda\n";
            echo "3. Ada adjustment atau nilai lain yang mempengaruhi\n";
            
            // Coba hitung mundur dari DPP yang ada
            if ($dpp > 0 && $pricelist->harga > 0) {
                $hari_terpakai = round($dpp / $pricelist->harga, 2);
                echo "\nJika DPP Rp " . number_format($dpp, 0, ',', '.') . " benar:\n";
                echo "Jumlah hari yang digunakan: {$hari_terpakai} hari\n";
                echo "Seharusnya: {$jumlah_hari} hari\n";
            }
        } else {
            echo "✓ DPP sudah benar!\n";
        }
    } elseif ($tarif === 'Bulanan') {
        $dpp_seharusnya = $pricelist->harga * $periode;
        echo "Tarif Bulanan: Rp " . number_format($pricelist->harga, 0, ',', '.') . "\n";
        echo "Periode: {$periode} bulan\n";
        echo "DPP Seharusnya: Rp " . number_format($pricelist->harga, 0, ',', '.') . " × {$periode} = Rp " . number_format($dpp_seharusnya, 0, ',', '.') . "\n";
        echo "DPP Saat Ini: Rp " . number_format($dpp, 0, ',', '.') . "\n";
        echo "Selisih: Rp " . number_format($dpp_seharusnya - $dpp, 0, ',', '.') . "\n";
    }
} else {
    echo "Pricelist tidak ditemukan untuk:\n";
    echo "  Vendor: {$vendor}\n";
    echo "  Size: {$size}\n";
    echo "  Tarif: {$tarif}\n\n";
    
    // Cari semua pricelist yang ada untuk vendor dan size ini
    echo "Mencari pricelist yang tersedia...\n";
    $available = DB::table('master_pricelist_sewa_kontainers')
        ->where('ukuran_kontainer', $size)
        ->where('vendor', $vendor)
        ->get();
    
    if ($available->count() > 0) {
        echo "Ditemukan {$available->count()} pricelist:\n";
        foreach ($available as $pl) {
            echo "  - Tarif: {$pl->tarif}, Harga: Rp " . number_format($pl->harga, 0, ',', '.') . "\n";
        }
    } else {
        echo "Tidak ada pricelist untuk vendor {$vendor} size {$size}\n";
    }
}

echo "\n=== SELESAI ===\n";
