<?php

require_once 'vendor/autoload.php';

use App\Models\Karyawan;

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== TESTING EXPORT LOGIC ===\n";

try {
    // Simulate the export logic
    $karyawans = Karyawan::orderBy('nama_lengkap', 'asc')->get();
    
    echo "Total karyawan: " . $karyawans->count() . "\n";
    
    // Find Rohman specifically
    $rohman = $karyawans->filter(function($karyawan) {
        return stripos($karyawan->nama_lengkap, 'rohman') !== false;
    })->first();
    
    if ($rohman) {
        echo "\n=== ROHMAN EXPORT DATA ===\n";
        echo "Original tanggal_berhenti: " . ($rohman->tanggal_berhenti ?? 'NULL') . "\n";
        echo "Tanggal_berhenti type: " . gettype($rohman->tanggal_berhenti) . "\n";
        
        // Test date formatting
        $tanggal_berhenti_formatted = '';
        if ($rohman->tanggal_berhenti) {
            if ($rohman->tanggal_berhenti instanceof \Carbon\Carbon) {
                $tanggal_berhenti_formatted = $rohman->tanggal_berhenti->format('d/M/Y');
            } else {
                $tanggal_berhenti_formatted = date('d/M/Y', strtotime($rohman->tanggal_berhenti));
            }
        }
        
        echo "Formatted tanggal_berhenti: " . $tanggal_berhenti_formatted . "\n";
        
        // Test CSV row generation
        $csvRow = [
            $rohman->nama_lengkap,
            $rohman->divisi ?? '',
            $rohman->pekerjaan ?? '',
            $rohman->alamat ?? '',
            $rohman->telepon ?? '',
            $tanggal_berhenti_formatted,
            $rohman->status ?? ''
        ];
        
        echo "\n=== CSV ROW DATA ===\n";
        echo "CSV Row: " . implode(', ', $csvRow) . "\n";
    }
    
    // Test fresh() on instance
    $testKaryawan = Karyawan::first();
    if ($testKaryawan) {
        echo "\n=== TESTING FRESH ON INSTANCE ===\n";
        $freshKaryawan = $testKaryawan->fresh();
        echo "Fresh test successful\n";
    }
    
} catch(Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

?>