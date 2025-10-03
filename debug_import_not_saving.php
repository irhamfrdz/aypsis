<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;
use Illuminate\Support\Facades\DB;

echo "=== DEBUGGING: Mengapa Import Tidak Tersimpan ===\n\n";

// 1. Check jumlah data di database
echo "1. Jumlah Data di Database:\n";
$totalData = DaftarTagihanKontainerSewa::count();
echo "   Total: {$totalData} records\n\n";

// 2. Check data dari CSV yang seharusnya diimport
echo "2. Check Sample Data dari CSV (CBHU3952697):\n";
$sampleContainer = 'CBHU3952697';
$dataFromDb = DaftarTagihanKontainerSewa::where('nomor_kontainer', $sampleContainer)->get();

if ($dataFromDb->count() > 0) {
    echo "   ✅ Data DITEMUKAN di database ({$dataFromDb->count()} records):\n";
    foreach ($dataFromDb as $data) {
        echo "      - Periode {$data->periode}: {$data->tanggal_awal} s/d {$data->tanggal_akhir}\n";
        echo "        DPP: Rp " . number_format($data->dpp ?? 0) . "\n";
        echo "        Adjustment: Rp " . number_format($data->adjustment ?? 0) . "\n";
        echo "        Grand Total: Rp " . number_format($data->grand_total ?? 0) . "\n";
        echo "        Created: {$data->created_at}\n\n";
    }
} else {
    echo "   ❌ Data TIDAK DITEMUKAN di database\n";
    echo "   Kemungkinan penyebab:\n";
    echo "   1. Checkbox 'Hanya validasi' tercentang saat import\n";
    echo "   2. Data di-skip karena 'Skip duplicates' aktif\n";
    echo "   3. Error validasi saat import\n\n";
}

// 3. Check data terbaru yang diimport (10 terakhir)
echo "3. Data Terakhir yang Diimport (10 records):\n";
$latestData = DaftarTagihanKontainerSewa::orderBy('created_at', 'DESC')->limit(10)->get();

if ($latestData->count() > 0) {
    foreach ($latestData as $index => $data) {
        echo "   " . ($index + 1) . ". {$data->nomor_kontainer} - Periode {$data->periode}\n";
        echo "      Vendor: {$data->vendor}\n";
        echo "      Tanggal: {$data->tanggal_awal} s/d {$data->tanggal_akhir}\n";
        echo "      Grand Total: Rp " . number_format($data->grand_total ?? 0) . "\n";
        echo "      Created: {$data->created_at}\n\n";
    }
} else {
    echo "   ❌ Tidak ada data di database\n\n";
}

// 4. Check duplikat - data yang mungkin di-skip
echo "4. Check Potensi Duplikat (Sample: CBHU3952697, Periode 1):\n";
$duplicateCheck = DaftarTagihanKontainerSewa::where('nomor_kontainer', $sampleContainer)
    ->where('periode', 1)
    ->get();

if ($duplicateCheck->count() > 0) {
    echo "   ⚠️  Data sudah ada di database:\n";
    foreach ($duplicateCheck as $data) {
        echo "      - Tanggal: {$data->tanggal_awal} s/d {$data->tanggal_akhir}\n";
        echo "      - Status: {$data->status}\n";
        echo "      - Created: {$data->created_at}\n";
    }
    echo "\n   Jika import dengan 'Skip duplicates' aktif, data ini akan di-skip\n\n";
} else {
    echo "   ✅ Tidak ada duplikat - data siap untuk diimport\n\n";
}

// 5. Simulasi Logic Import
echo "5. Simulasi Import Logic:\n\n";

// Scenario 1: validate_only = true
echo "   Scenario 1: validate_only = TRUE\n";
echo "   if (!validate_only) { create(); }\n";
echo "   Result: Data TIDAK TERSIMPAN ❌\n\n";

// Scenario 2: validate_only = false
echo "   Scenario 2: validate_only = FALSE\n";
echo "   if (!validate_only) { create(); }\n";
echo "   Result: Data TERSIMPAN ✅\n\n";

// Scenario 3: skip_duplicates = true + data exists
echo "   Scenario 3: skip_duplicates = TRUE + data exists\n";
echo "   if (existing && skip_duplicates) { continue; }\n";
echo "   Result: Data DI-SKIP (tidak diimport) ⚠️\n\n";

// Scenario 4: update_existing = true + data exists
echo "   Scenario 4: update_existing = TRUE + data exists\n";
echo "   if (existing && update_existing) { update(); }\n";
echo "   Result: Data DI-UPDATE ✅\n\n";

// 6. Recommendations
echo "=== REKOMENDASI ===\n\n";

$hasData = DaftarTagihanKontainerSewa::where('nomor_kontainer', $sampleContainer)->exists();

if (!$hasData) {
    echo "✅ Data dari CSV belum ada di database\n";
    echo "   Action: Import dengan setting berikut:\n";
    echo "   [ ] Hanya validasi - UNCHECK INI!\n";
    echo "   [✓] Skip data yang sudah ada - Optional\n";
    echo "   [ ] Update data yang sudah ada - Optional\n\n";
} else {
    echo "⚠️  Data dari CSV sudah ada di database\n";
    echo "   Jika ingin import ulang, ada 2 opsi:\n\n";
    echo "   Opsi 1: Update data yang sudah ada\n";
    echo "   [ ] Hanya validasi\n";
    echo "   [ ] Skip data yang sudah ada - UNCHECK\n";
    echo "   [✓] Update data yang sudah ada - CHECK INI\n\n";

    echo "   Opsi 2: Hapus data lama dulu, baru import\n";
    echo "   1. DELETE FROM daftar_tagihan_kontainer_sewa WHERE nomor_kontainer = '{$sampleContainer}';\n";
    echo "   2. Import dengan 'Skip duplicates' aktif\n\n";
}

echo "=== CATATAN PENTING ===\n";
echo "Checkbox 'Hanya validasi (tidak menyimpan data)' HARUS UNCHECK!\n";
echo "Jika tercentang, data hanya divalidasi tapi TIDAK DISIMPAN ke database.\n\n";

// 7. Test dengan data dari CSV
echo "=== TEST: Cek Semua Kontainer dari CSV ===\n\n";
$csvContainers = [
    'CBHU3952697', 'CBHU4077764', 'CBHU5876322', 'CBHU5911444', 'CBHU5914130',
    'CCLU3806500', 'CCLU3836629', 'CSLU1004045', 'CSLU1247770', 'CXDU1108080',
    'DPEU4869769', 'RXTU4540180', 'STXU2015218'
];

$foundCount = 0;
$notFoundCount = 0;

foreach ($csvContainers as $container) {
    $exists = DaftarTagihanKontainerSewa::where('nomor_kontainer', $container)->exists();
    if ($exists) {
        $count = DaftarTagihanKontainerSewa::where('nomor_kontainer', $container)->count();
        echo "✅ {$container}: {$count} records\n";
        $foundCount++;
    } else {
        echo "❌ {$container}: NOT FOUND\n";
        $notFoundCount++;
    }
}

echo "\n";
echo "Summary:\n";
echo "- Found: {$foundCount} containers\n";
echo "- Not Found: {$notFoundCount} containers\n\n";

if ($notFoundCount > 0) {
    echo "⚠️  {$notFoundCount} kontainer dari CSV tidak ditemukan di database\n";
    echo "   Ini menunjukkan data TIDAK TERSIMPAN saat import\n";
    echo "   Penyebab: Kemungkinan checkbox 'Hanya validasi' tercentang\n\n";
} else {
    echo "✅ Semua kontainer dari CSV ada di database\n";
    echo "   Import berhasil!\n\n";
}

?>
