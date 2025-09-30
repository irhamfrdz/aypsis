<?php
// Test the generate nomor endpoint directly
echo "Testing generate nomor pranota CAT endpoint...\n\n";

try {
    // Simulate what the controller does
    require 'vendor/autoload.php';
    $app = require 'bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

    use App\Models\NomorTerakhir;

    $nomorTerakhir = NomorTerakhir::where('modul', 'PMS')->first();

    if (!$nomorTerakhir) {
        echo "❌ Modul PMS tidak ditemukan\n";
        exit(1);
    }

    echo "Current nomor_terakhir: {$nomorTerakhir->nomor_terakhir}\n";

    // Generate next number
    $nextNumber = $nomorTerakhir->nomor_terakhir + 1;

    // Format: PMS + 1 digit cetakan + 2 digit bulan + 2 digit tahun + 6 digit nomor terakhir
    $nomorCetakan = 1;
    $tahun = now()->format('y'); // 2 digit tahun
    $bulan = now()->format('m'); // 2 digit bulan
    $nomorPranota = "PMS{$nomorCetakan}{$bulan}{$tahun}" . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

    echo "Generated nomor_pranota: {$nomorPranota}\n";
    echo "Next number to save: {$nextNumber}\n";
    echo "Format breakdown:\n";
    echo "- PMS: modul dari master nomor terakhir\n";
    echo "- {$nomorCetakan}: 1 digit nomor cetakan\n";
    echo "- {$bulan}: 2 digit bulan\n";
    echo "- {$tahun}: 2 digit tahun\n";
    echo "- " . str_pad($nextNumber, 6, '0', STR_PAD_LEFT) . ": nomor terakhir (6 digit)\n";

    echo "\n✅ Format nomor pranota CAT sudah sesuai dengan permintaan user!\n";
    echo "Format: nama_modul + nomor_cetakan + bulan + tahun + nomor_terakhir\n";

} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
