<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Analyze Supir/Kenek vs Karyawan ===\n\n";

// Check if supir and kenek values are IDs or names
$suratJalans = \App\Models\SuratJalan::whereNotNull('supir')
    ->orWhereNotNull('kenek')
    ->limit(5)
    ->get(['id', 'no_surat_jalan', 'supir', 'kenek']);

echo "Sample SuratJalan supir/kenek data:\n";
foreach ($suratJalans as $sj) {
    echo "- {$sj->no_surat_jalan}:\n";
    echo "  Supir: " . ($sj->supir ?: 'NULL') . " (type: " . gettype($sj->supir) . ")\n";
    echo "  Kenek: " . ($sj->kenek ?: 'NULL') . " (type: " . gettype($sj->kenek) . ")\n";
    
    // Check if they are IDs (numeric)
    if (is_numeric($sj->supir)) {
        $karyawan = \App\Models\Karyawan::find($sj->supir);
        if ($karyawan) {
            echo "  → Supir Karyawan: {$karyawan->nik} - {$karyawan->nama_lengkap}\n";
        } else {
            echo "  → No Karyawan found for supir ID: {$sj->supir}\n";
        }
    } else if ($sj->supir) {
        // Try to find by name
        $karyawan = \App\Models\Karyawan::where('nama_lengkap', 'like', "%{$sj->supir}%")
            ->orWhere('nama_panggilan', 'like', "%{$sj->supir}%")
            ->first();
        if ($karyawan) {
            echo "  → Supir found by name: {$karyawan->nik} - {$karyawan->nama_lengkap}\n";
        } else {
            echo "  → No Karyawan found for supir name: {$sj->supir}\n";
        }
    }
    
    if (is_numeric($sj->kenek)) {
        $karyawan = \App\Models\Karyawan::find($sj->kenek);
        if ($karyawan) {
            echo "  → Kenek Karyawan: {$karyawan->nik} - {$karyawan->nama_lengkap}\n";
        }
    } else if ($sj->kenek) {
        $karyawan = \App\Models\Karyawan::where('nama_lengkap', 'like', "%{$sj->kenek}%")
            ->orWhere('nama_panggilan', 'like', "%{$sj->kenek}%")
            ->first();
        if ($karyawan) {
            echo "  → Kenek found by name: {$karyawan->nik} - {$karyawan->nama_lengkap}\n";
        } else {
            echo "  → No Karyawan found for kenek name: {$sj->kenek}\n";
        }
    }
    echo "\n";
}

echo "=== Karyawan sample data ===\n";
$karyawans = \App\Models\Karyawan::limit(5)->get(['id', 'nik', 'nama_panggilan', 'nama_lengkap']);
foreach ($karyawans as $k) {
    echo "- ID: {$k->id}, NIK: {$k->nik}, Nama: {$k->nama_lengkap} ({$k->nama_panggilan})\n";
}

echo "\n=== Conclusion ===\n";
echo "It seems supir/kenek fields contain NAMES, not IDs.\n";
echo "We need to either:\n";
echo "1. Create relationships based on name matching\n";
echo "2. Show supir/kenek names instead of NIK\n";
echo "3. Add NIK field directly to surat_jalans table\n";