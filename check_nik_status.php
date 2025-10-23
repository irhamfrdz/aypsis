<?php

// Load Laravel environment
require_once __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Karyawan;

try {
    echo "Checking all NIKs in range 1500-1510...\n\n";

    // Check for NIKs in extended range
    $existingNiks = Karyawan::whereRaw('nik REGEXP \'^[0-9]+$\'')
                            ->whereRaw('CAST(nik AS UNSIGNED) >= 1500 AND CAST(nik AS UNSIGNED) <= 1510')
                            ->orderByRaw('CAST(nik AS UNSIGNED) ASC')
                            ->get(['nik', 'nama_lengkap']);

    if ($existingNiks->count() > 0) {
        echo "Found existing NIKs in range 1500-1510:\n";
        foreach ($existingNiks as $karyawan) {
            echo "- NIK: {$karyawan->nik}, Nama: {$karyawan->nama_lengkap}\n";
        }
    } else {
        echo "No existing NIKs found in range 1500-1510\n";
    }

    echo "\n";

    // Test with specific NIK check
    $nik1502Exists = Karyawan::where('nik', '1502')->exists();
    echo "NIK 1502 exists: " . ($nik1502Exists ? 'YES' : 'NO') . "\n";

    $nik1503Exists = Karyawan::where('nik', '1503')->exists();
    echo "NIK 1503 exists: " . ($nik1503Exists ? 'YES' : 'NO') . "\n";

    echo "\nNext NIK should be: ";

    if ($nik1502Exists && !$nik1503Exists) {
        echo "1503 (because 1502 already exists)\n";
    } elseif (!$nik1502Exists) {
        echo "1502 (because it's the starting number)\n";
    } else {
        // Find the next available
        $nextAvailable = 1502;
        while (Karyawan::where('nik', (string)$nextAvailable)->exists()) {
            $nextAvailable++;
        }
        echo "$nextAvailable (next available after checking conflicts)\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
