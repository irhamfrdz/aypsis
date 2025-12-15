<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== CEK SURAT JALAN YANG SEHARUSNYA MUNCUL ===\n\n";
echo "Masukkan nomor surat jalan yang tidak muncul (atau ketik 'semua' untuk cek semua): ";
$input = trim(fgets(STDIN));

if ($input === 'semua') {
    // Cek semua surat jalan yang belum ada uang jalan tapi tidak muncul
    $allSJ = \App\Models\SuratJalan::with(['order.pengirim'])
        ->whereDoesntHave('uangJalan')
        ->orderBy('created_at', 'desc')
        ->limit(50)
        ->get();
    
    echo "\nTotal surat jalan tanpa uang jalan: " . $allSJ->count() . "\n\n";
    
    foreach ($allSJ as $sj) {
        $alasan = [];
        
        if (!$sj->order_id) {
            $alasan[] = "❌ Tidak ada order_id";
        }
        
        if ($sj->status_pembayaran_uang_jalan && $sj->status_pembayaran_uang_jalan !== 'belum_ada') {
            $alasan[] = "❌ Status pembayaran: " . $sj->status_pembayaran_uang_jalan;
        }
        
        if ($sj->is_supir_customer) {
            $alasan[] = "❌ Supir customer";
        }
        
        $status = empty($alasan) ? "✅ SEHARUSNYA MUNCUL" : "⚠️ TIDAK MUNCUL";
        
        echo "ID: {$sj->id} | No: {$sj->no_surat_jalan}\n";
        echo "  Status: {$status}\n";
        if (!empty($alasan)) {
            echo "  Alasan: " . implode(", ", $alasan) . "\n";
        }
        echo "  ---\n";
    }
} else {
    // Cek surat jalan spesifik
    $sj = \App\Models\SuratJalan::with(['order.pengirim', 'uangJalan'])
        ->where('no_surat_jalan', $input)
        ->first();
    
    if (!$sj) {
        echo "❌ Surat jalan tidak ditemukan!\n";
        exit;
    }
    
    echo "\n=== DETAIL SURAT JALAN ===\n";
    echo "ID: {$sj->id}\n";
    echo "No Surat Jalan: {$sj->no_surat_jalan}\n";
    echo "Order ID: " . ($sj->order_id ?? 'NULL') . "\n";
    echo "Status Pembayaran UJ: " . ($sj->status_pembayaran_uang_jalan ?? 'NULL') . "\n";
    echo "Is Supir Customer: " . ($sj->is_supir_customer ? 'YA' : 'TIDAK') . "\n";
    echo "Status: " . ($sj->status ?? 'NULL') . "\n";
    echo "Created At: " . $sj->created_at . "\n";
    
    if ($sj->uangJalan) {
        echo "\n✅ SUDAH ADA UANG JALAN:\n";
        echo "  - ID: {$sj->uangJalan->id}\n";
        echo "  - Nomor: {$sj->uangJalan->nomor_uang_jalan}\n";
        echo "  - Total: Rp " . number_format($sj->uangJalan->total, 0, ',', '.') . "\n";
    } else {
        echo "\n❌ BELUM ADA UANG JALAN\n";
    }
    
    echo "\n=== ANALISIS ===\n";
    
    $akanMuncul = true;
    $alasan = [];
    
    if (!$sj->order_id) {
        $akanMuncul = false;
        $alasan[] = "Tidak ada order_id (surat jalan harus terhubung dengan order)";
    }
    
    if ($sj->status_pembayaran_uang_jalan && $sj->status_pembayaran_uang_jalan !== 'belum_ada') {
        $akanMuncul = false;
        $alasan[] = "Status pembayaran uang jalan: '{$sj->status_pembayaran_uang_jalan}' (harus 'belum_ada')";
    }
    
    if ($sj->is_supir_customer) {
        $akanMuncul = false;
        $alasan[] = "Merupakan supir customer (di-exclude dari daftar)";
    }
    
    if ($akanMuncul) {
        echo "✅ SURAT JALAN INI SEHARUSNYA MUNCUL DI DAFTAR!\n";
        echo "\nJika tidak muncul, kemungkinan:\n";
        echo "1. Cache browser (coba refresh dengan Ctrl+F5)\n";
        echo "2. Filter pencarian aktif\n";
        echo "3. Bug di query\n";
    } else {
        echo "❌ SURAT JALAN INI TIDAK AKAN MUNCUL\n";
        echo "\nAlasan:\n";
        foreach ($alasan as $i => $a) {
            echo ($i + 1) . ". " . $a . "\n";
        }
        
        echo "\nSOLUSI:\n";
        if (!$sj->order_id) {
            echo "- Hubungkan surat jalan dengan order terlebih dahulu\n";
        }
        if ($sj->status_pembayaran_uang_jalan && $sj->status_pembayaran_uang_jalan !== 'belum_ada') {
            echo "- Update status_pembayaran_uang_jalan menjadi 'belum_ada'\n";
            echo "  (Jika memang belum ada uang jalan yang dibuat)\n";
        }
        if ($sj->is_supir_customer) {
            echo "- Ini adalah supir customer, uang jalan dibayar oleh customer\n";
        }
    }
}

echo "\n";
