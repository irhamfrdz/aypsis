<?php

require_once 'vendor/autoload.php';

use App\Models\Karyawan;

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== CHECKING KARYAWAN DATABASE DATA ===\n";

try {
    // Check data for Rohman
    $rohmanData = Karyawan::where('nama_lengkap', 'LIKE', '%rohman%')->get();
    
    if ($rohmanData->count() > 0) {
        echo "\n=== ROHMAN DATA FROM DATABASE ===\n";
        foreach($rohmanData as $karyawan) {
            echo "ID: " . $karyawan->id . "\n";
            echo "Nama: " . ($karyawan->nama_lengkap ?? 'NULL') . "\n";
            echo "Tanggal Berhenti: " . ($karyawan->tanggal_berhenti ?? 'NULL') . "\n";
            echo "Divisi: " . ($karyawan->divisi ?? 'NULL') . "\n";
            echo "Pekerjaan: " . ($karyawan->pekerjaan ?? 'NULL') . "\n";
            echo "Alamat: " . ($karyawan->alamat ?? 'NULL') . "\n";
            echo "Telepon: " . ($karyawan->telepon ?? 'NULL') . "\n";
            echo "Status: " . ($karyawan->status ?? 'NULL') . "\n";
            echo "---\n";
        }
    } else {
        echo "No data found for 'rohman'\n";
        
        // Check sample data
        $sampleData = Karyawan::take(5)->get();
        
        echo "\n=== SAMPLE DATA FROM DATABASE ===\n";
        foreach($sampleData as $karyawan) {
            echo "ID: " . $karyawan->id . "\n";
            echo "Nama: " . ($karyawan->nama_lengkap ?? 'NULL') . "\n";
            echo "Tanggal Berhenti: " . ($karyawan->tanggal_berhenti ?? 'NULL') . "\n";
            echo "Divisi: " . ($karyawan->divisi ?? 'NULL') . "\n";
            echo "Pekerjaan: " . ($karyawan->pekerjaan ?? 'NULL') . "\n";
            echo "---\n";
        }
    }
    
    // Test fresh data
    echo "\n=== TESTING FRESH DATA RETRIEVAL ===\n";
    $fresh = Karyawan::fresh()->take(3)->get();
    foreach($fresh as $karyawan) {
        echo "Fresh ID: " . $karyawan->id . "\n";
        echo "Fresh Nama: " . ($karyawan->nama_lengkap ?? 'NULL') . "\n";
        echo "Fresh Tanggal Berhenti: " . ($karyawan->tanggal_berhenti ?? 'NULL') . "\n";
        echo "Fresh Divisi: " . ($karyawan->divisi ?? 'NULL') . "\n";
        echo "---\n";
    }
    
} catch(Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

?>