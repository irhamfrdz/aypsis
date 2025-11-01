<?php

require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Prospek;
use App\Models\TandaTerima;
use App\Models\SuratJalan;

echo "=== TESTING AUTO-LINKING FUNCTIONALITY ===\n\n";

// Test 1: Buat TandaTerima baru, lalu buat Prospek baru dengan surat_jalan_id yang sama
echo "1. TESTING: Create TandaTerima then Prospek with same surat_jalan_id\n";

// Cari surat jalan yang ada
$suratJalan = SuratJalan::where('term', '!=', null)->first();
if (!$suratJalan) {
    echo "No SuratJalan with term found, creating test data...\n";
    $suratJalan = SuratJalan::create([
        'no_surat_jalan' => 'TEST-SJ-' . time(),
        'tanggal_surat_jalan' => now(),
        'term' => 'TEST TERM - AUTO LINK',
        'kegiatan' => 'Test',
        'pengirim' => 'Test Pengirim',
        'jenis_barang' => 'Test Barang',
        'tujuan_pengiriman' => 'Test Tujuan',
        'supir' => 'Test Supir',
        'no_plat' => 'TEST123',
        'input_by' => 1,
        'input_date' => now(),
        'status' => 'approved',
    ]);
}

echo "Using SuratJalan ID: {$suratJalan->id}, No: {$suratJalan->no_surat_jalan}, Term: {$suratJalan->term}\n";

// Buat TandaTerima baru
$newTandaTerima = TandaTerima::create([
    'surat_jalan_id' => $suratJalan->id,
    'no_surat_jalan' => $suratJalan->no_surat_jalan,
    'tanggal_surat_jalan' => $suratJalan->tanggal_surat_jalan,
    'term' => $suratJalan->term,
    'kegiatan' => $suratJalan->kegiatan,
    'pengirim' => $suratJalan->pengirim,
    'jenis_barang' => $suratJalan->jenis_barang,
    'tujuan_pengiriman' => $suratJalan->tujuan_pengiriman,
    'supir' => $suratJalan->supir,
    'created_by' => 1,
]);

echo "Created TandaTerima ID: {$newTandaTerima->id}\n";

// Buat Prospek baru dengan surat_jalan_id yang sama
$newProspek = Prospek::create([
    'tanggal' => now(),
    'nama_supir' => 'Test Supir Auto',
    'barang' => 'Test Barang Auto',
    'pt_pengirim' => 'Test PT Auto',
    'ukuran' => '20',
    'tipe' => 'test',
    'no_surat_jalan' => $suratJalan->no_surat_jalan,
    'surat_jalan_id' => $suratJalan->id,
    'nomor_kontainer' => 'TEST123456',
    'tujuan_pengiriman' => 'Test Tujuan Auto',
    'total_ton' => 10.5,
    'total_volume' => 25.0,
    'status' => 'aktif',
    'created_by' => 1,
]);

echo "Created Prospek ID: {$newProspek->id}\n";

// Refresh dan check apakah auto-linking berhasil
$newProspek->refresh();
echo "Prospek tanda_terima_id after auto-link: " . ($newProspek->tanda_terima_id ?? 'NULL') . "\n";

if ($newProspek->tanda_terima_id == $newTandaTerima->id) {
    echo "✅ AUTO-LINKING SUCCESS! Prospek terlink dengan TandaTerima\n";
    
    // Test term data access
    if ($newProspek->tandaTerima && $newProspek->tandaTerima->term) {
        echo "✅ TERM DATA ACCESSIBLE: {$newProspek->tandaTerima->term}\n";
    } else {
        echo "❌ TERM DATA NOT ACCESSIBLE\n";
    }
} else {
    echo "❌ AUTO-LINKING FAILED\n";
}

echo "\n";

// Test 2: Buat Prospek dulu, lalu TandaTerima dengan surat_jalan_id yang sama
echo "2. TESTING: Create Prospek then TandaTerima with same surat_jalan_id\n";

// Buat SuratJalan baru
$suratJalan2 = SuratJalan::create([
    'no_surat_jalan' => 'TEST-SJ2-' . time(),
    'tanggal_surat_jalan' => now(),
    'term' => 'TEST TERM 2 - AUTO LINK',
    'kegiatan' => 'Test 2',
    'pengirim' => 'Test Pengirim 2',
    'jenis_barang' => 'Test Barang 2',
    'tujuan_pengiriman' => 'Test Tujuan 2',
    'supir' => 'Test Supir 2',
    'no_plat' => 'TEST456',
    'input_by' => 1,
    'input_date' => now(),
    'status' => 'approved',
]);

echo "Using SuratJalan2 ID: {$suratJalan2->id}, No: {$suratJalan2->no_surat_jalan}, Term: {$suratJalan2->term}\n";

// Buat Prospek dulu
$newProspek2 = Prospek::create([
    'tanggal' => now(),
    'nama_supir' => 'Test Supir Auto 2',
    'barang' => 'Test Barang Auto 2',
    'pt_pengirim' => 'Test PT Auto 2',
    'ukuran' => '40',
    'tipe' => 'test2',
    'no_surat_jalan' => $suratJalan2->no_surat_jalan,
    'surat_jalan_id' => $suratJalan2->id,
    'nomor_kontainer' => 'TEST789012',
    'tujuan_pengiriman' => 'Test Tujuan Auto 2',
    'total_ton' => 15.5,
    'total_volume' => 35.0,
    'status' => 'aktif',
    'created_by' => 1,
]);

echo "Created Prospek2 ID: {$newProspek2->id}\n";
echo "Prospek2 tanda_terima_id before TandaTerima creation: " . ($newProspek2->tanda_terima_id ?? 'NULL') . "\n";

// Buat TandaTerima dengan surat_jalan_id yang sama
$newTandaTerima2 = TandaTerima::create([
    'surat_jalan_id' => $suratJalan2->id,
    'no_surat_jalan' => $suratJalan2->no_surat_jalan,
    'tanggal_surat_jalan' => $suratJalan2->tanggal_surat_jalan,
    'term' => $suratJalan2->term,
    'kegiatan' => $suratJalan2->kegiatan,
    'pengirim' => $suratJalan2->pengirim,
    'jenis_barang' => $suratJalan2->jenis_barang,
    'tujuan_pengiriman' => $suratJalan2->tujuan_pengiriman,
    'supir' => $suratJalan2->supir,
    'created_by' => 1,
]);

echo "Created TandaTerima2 ID: {$newTandaTerima2->id}\n";

// Refresh dan check apakah auto-linking berhasil
$newProspek2->refresh();
echo "Prospek2 tanda_terima_id after TandaTerima creation: " . ($newProspek2->tanda_terima_id ?? 'NULL') . "\n";

if ($newProspek2->tanda_terima_id == $newTandaTerima2->id) {
    echo "✅ AUTO-LINKING SUCCESS! Prospek2 terlink dengan TandaTerima2\n";
    
    // Test term data access
    if ($newProspek2->tandaTerima && $newProspek2->tandaTerima->term) {
        echo "✅ TERM DATA ACCESSIBLE: {$newProspek2->tandaTerima->term}\n";
    } else {
        echo "❌ TERM DATA NOT ACCESSIBLE\n";
    }
} else {
    echo "❌ AUTO-LINKING FAILED\n";
}

echo "\n=== AUTO-LINKING TEST COMPLETE ===\n";