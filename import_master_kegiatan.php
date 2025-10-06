<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Memulai import data master_kegiatans...\n";

try {
    // Baca file SQL backup
    $sqlFile = 'c:\folder_kerjaan\backup\aypsis_backup.sql';
    
    if (!file_exists($sqlFile)) {
        die("File backup tidak ditemukan: $sqlFile\n");
    }
    
    echo "Membaca file backup...\n";
    $content = file_get_contents($sqlFile);
    
    // Cari INSERT statement untuk master_kegiatans
    $pattern = '/INSERT INTO `master_kegiatans` VALUES (.+?);/s';
    
    if (preg_match($pattern, $content, $matches)) {
        $insertData = $matches[1];
        
        echo "Data ditemukan, menghapus data lama...\n";
        
        // Hapus data lama
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('master_kegiatans')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        echo "Mengimpor data baru...\n";
        
        // Execute insert statement
        $sql = "INSERT INTO `master_kegiatans` VALUES " . $insertData;
        DB::statement($sql);
        
        $count = DB::table('master_kegiatans')->count();
        echo "Berhasil! Total $count data master_kegiatans telah diimpor.\n";
        
    } else {
        echo "Data INSERT untuk master_kegiatans tidak ditemukan dalam file backup.\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
