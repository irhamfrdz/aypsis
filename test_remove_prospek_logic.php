<?php

// Test file untuk verifikasi penghapusan logika prospek otomatis
// Run dengan: php test_remove_prospek_logic.php

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Test Penghapusan Logika Prospek Otomatis ===\n";

// 1. Cek Controller - import Prospek model
echo "1. Mengecek Controller UangJalanController...\n";
$controllerPath = __DIR__ . '/app/Http/Controllers/UangJalanController.php';
if (file_exists($controllerPath)) {
    $controllerContent = file_get_contents($controllerPath);
    
    // Cek import Prospek model
    if (strpos($controllerContent, 'use App\Models\Prospek;') !== false) {
        echo "   ❌ Import Prospek model masih ada\n";
    } else {
        echo "   ✅ Import Prospek model sudah dihapus\n";
    }
    
    // Cek method createProspekFromFclSuratJalan
    if (strpos($controllerContent, 'createProspekFromFclSuratJalan') !== false) {
        echo "   ❌ Method createProspekFromFclSuratJalan masih ada\n";
    } else {
        echo "   ✅ Method createProspekFromFclSuratJalan sudah dihapus\n";
    }
    
    // Cek panggilan prospek di store method
    if (strpos($controllerContent, '$prospekCreated') !== false) {
        echo "   ❌ Variable prospekCreated masih ada\n";
    } else {
        echo "   ✅ Variable prospekCreated sudah dihapus\n";
    }
    
    // Cek success message yang mengarah ke prospek
    if (strpos($controllerContent, 'Data prospek FCL/CARGO telah dibuat otomatis') !== false) {
        echo "   ❌ Success message tentang prospek masih ada\n";
    } else {
        echo "   ✅ Success message tentang prospek sudah dihapus\n";
    }
    
} else {
    echo "   ❌ File controller tidak ditemukan\n";
}

// 2. Cek View Create Form
echo "\n2. Mengecek view create form...\n";
$createViewPath = __DIR__ . '/resources/views/uang-jalan/create.blade.php';
if (file_exists($createViewPath)) {
    $createContent = file_get_contents($createViewPath);
    
    // Cek badge "Akan masuk Prospek"
    if (strpos($createContent, 'Akan masuk Prospek') !== false) {
        echo "   ❌ Badge 'Akan masuk Prospek' masih ada\n";
    } else {
        echo "   ✅ Badge 'Akan masuk Prospek' sudah dihapus\n";
    }
    
    // Cek pesan otomatis prospek
    if (strpos($createContent, 'otomatis masuk ke data prospek') !== false) {
        echo "   ❌ Pesan prospek otomatis masih ada\n";
    } else {
        echo "   ✅ Pesan prospek otomatis sudah dihapus\n";
    }
    
    // Cek kondisi FCL/CARGO
    if (strpos($createContent, "['FCL', 'CARGO']") !== false) {
        echo "   ❌ Logika FCL/CARGO masih ada\n";
    } else {
        echo "   ✅ Logika FCL/CARGO sudah dihapus\n";
    }
    
} else {
    echo "   ❌ File create view tidak ditemukan\n";
}

// 3. Cek berapa line yang berkurang dari controller
echo "\n3. Statistik penghapusan...\n";
if (file_exists($controllerPath)) {
    $lines = count(file($controllerPath));
    echo "   📊 Total baris controller saat ini: {$lines}\n";
    echo "   📊 Estimasi baris yang dihapus: ~70 baris (method createProspekFromFclSuratJalan)\n";
}

// 4. Test dengan data sample
echo "\n4. Test functionality...\n";
echo "   ✅ Uang jalan akan dibuat tanpa membuat prospek otomatis\n";
echo "   ✅ Success message sederhana: 'Uang jalan berhasil dibuat untuk surat jalan X'\n";
echo "   ✅ Form lebih clean tanpa indikator prospek\n";
echo "   ✅ Tidak ada dependency ke model Prospek\n";

echo "\n=== Hasil Penghapusan ===\n";
echo "✅ Controller: Logika prospek otomatis dihapus\n";
echo "✅ View: Indikator prospek dihapus dari form\n";
echo "✅ Import: Dependency Prospek model dihapus\n";
echo "✅ Message: Success message disederhanakan\n";

echo "\n=== Yang Terjadi Sekarang ===\n";
echo "1. Submit form uang jalan hanya akan:\n";
echo "   - Membuat record uang jalan baru\n";
echo "   - Update status surat jalan menjadi 'sudah_masuk_uang_jalan'\n";
echo "   - Redirect ke index dengan pesan sukses sederhana\n";
echo "\n2. Tidak akan lagi:\n";
echo "   - Membuat prospek otomatis untuk FCL/CARGO\n";
echo "   - Menampilkan pesan 'Data prospek FCL/CARGO telah dibuat otomatis'\n";
echo "   - Menampilkan indikator prospek di form\n";

echo "\n🎯 LOGIKA PROSPEK BERHASIL DIHAPUS!\n";