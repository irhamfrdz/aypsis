<?php

// Seeder check untuk bank dan karyawan
echo "=== BANK SEEDER CHECK ===\n\n";

// Check if SampleKaryawanSeeder was run
$seedersPath = 'database/seeders/SampleKaryawanSeeder.php';

if (file_exists($seedersPath)) {
    echo "1. Membaca SampleKaryawanSeeder.php:\n";
    $content = file_get_contents($seedersPath);

    // Extract bank name from seeder
    if (preg_match("/'nama_bank'\s*=>\s*'([^']+)'/", $content, $matches)) {
        $bankFromSeeder = $matches[1];
        echo "   Bank di seeder: '$bankFromSeeder'\n\n";

        // Check Bank model/seeder
        $bankSeederPath = 'database/seeders/BankSeeder.php';

        if (file_exists($bankSeederPath)) {
            echo "2. Membaca BankSeeder.php:\n";
            $bankContent = file_get_contents($bankSeederPath);
            echo "   Isi BankSeeder:\n";

            // Extract bank names from seeder
            if (preg_match_all("/'name'\s*=>\s*'([^']+)'/", $bankContent, $bankMatches)) {
                foreach ($bankMatches[1] as $index => $bankName) {
                    $isMatch = ($bankName === $bankFromSeeder) ? " ← MATCH!" : "";
                    echo "   - '$bankName'$isMatch\n";
                }
            } else {
                echo "   Tidak ditemukan bank names di BankSeeder\n";
            }

        } else {
            echo "2. BankSeeder.php tidak ditemukan!\n";
        }

    } else {
        echo "   Tidak ditemukan nama_bank di SampleKaryawanSeeder\n";
    }

    echo "\n3. Kemungkinan penyebab dropdown kosong:\n";
    echo "   a. BankSeeder belum dijalankan (php artisan db:seed --class=BankSeeder)\n";
    echo "   b. Nama bank di SampleKaryawanSeeder tidak sesuai dengan BankSeeder\n";
    echo "   c. Tabel 'banks' kosong atau tidak ada\n";
    echo "   d. Model Bank tidak ada atau ada error\n\n";

    echo "4. Cara debug:\n";
    echo "   - Cek: php artisan tinker\n";
    echo "   - Run: App\\Models\\Bank::count()\n";
    echo "   - Run: App\\Models\\Bank::pluck('name')\n";
    echo "   - Run: App\\Models\\Karyawan::where('nama_lengkap', 'Ahmad Fauzi Rahman')->value('nama_bank')\n";

} else {
    echo "SampleKaryawanSeeder.php tidak ditemukan di $seedersPath\n";
}

?>
