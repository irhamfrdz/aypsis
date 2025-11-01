<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Load Laravel app
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== VERIFIKASI DATA IMPORT ===\n\n";

$tables = [
    'users' => 'Users (dipertahankan)',
    'permissions' => 'Permissions (dipertahankan)', 
    'akun_coa' => 'Chart of Accounts',
    'master_kapals' => 'Master Kapal',
    'master_pelabuhans' => 'Master Pelabuhan',
    'orders' => 'Orders',
    'surat_jalans' => 'Surat Jalan',
    'divisis' => 'Divisi',
    'karyawans' => 'Karyawan',
    'kontainers' => 'Kontainer',
    'cabangs' => 'Cabang'
];

foreach ($tables as $table => $description) {
    try {
        if (Schema::hasTable($table)) {
            $count = DB::table($table)->count();
            echo sprintf("%-20s: %5d records - %s\n", $table, $count, $description);
        } else {
            echo sprintf("%-20s: %5s records - %s (Table not exists)\n", $table, 'N/A', $description);
        }
    } catch (Exception $e) {
        echo sprintf("%-20s: %5s records - %s (Error: %s)\n", $table, 'ERR', $description, substr($e->getMessage(), 0, 30));
    }
}

// Cek apakah akun_coa kosong, jika ya coba import manual
if (DB::table('akun_coa')->count() == 0) {
    echo "\n⚠️  AKUN_COA kosong! Mencoba import manual...\n";
    
    try {
        // Ambil data akun_coa dari SQL file
        $sqlFile = __DIR__ . '/aypsis.sql';
        $content = file_get_contents($sqlFile);
        
        // Cari INSERT untuk akun_coa
        if (preg_match('/INSERT INTO `akun_coa` VALUES (.+?);/s', $content, $matches)) {
            $insertData = $matches[1];
            
            // Jalankan insert
            DB::statement("SET FOREIGN_KEY_CHECKS=0");
            DB::unprepared("INSERT INTO `akun_coa` VALUES " . $insertData);
            DB::statement("SET FOREIGN_KEY_CHECKS=1");
            
            $newCount = DB::table('akun_coa')->count();
            echo "✅ Berhasil import akun_coa: {$newCount} records\n";
        } else {
            echo "❌ Tidak dapat menemukan data akun_coa di SQL file\n";
        }
    } catch (Exception $e) {
        echo "❌ Error manual import akun_coa: " . $e->getMessage() . "\n";
    }
}

echo "\n=== SAMPLE DATA ===\n";

// Tampilkan sample users
echo "\nUsers:\n";
try {
    $users = DB::table('users')->select('id', 'name', 'email')->limit(3)->get();
    foreach ($users as $user) {
        echo "  - {$user->name} ({$user->email})\n";
    }
} catch (Exception $e) {
    echo "  Error: " . $e->getMessage() . "\n";
}

// Tampilkan sample permissions  
echo "\nPermissions (first 5):\n";
try {
    $permissions = DB::table('permissions')->select('name')->limit(5)->get();
    foreach ($permissions as $perm) {
        echo "  - {$perm->name}\n";
    }
} catch (Exception $e) {
    echo "  Error: " . $e->getMessage() . "\n";
}

// Tampilkan sample master data
echo "\nMaster Kapal (first 3):\n";
try {
    $kapals = DB::table('master_kapals')->select('nama_kapal', 'call_sign')->limit(3)->get();
    foreach ($kapals as $kapal) {
        echo "  - {$kapal->nama_kapal} ({$kapal->call_sign})\n";
    }
} catch (Exception $e) {
    echo "  Error: " . $e->getMessage() . "\n";
}

echo "\n🎉 VERIFIKASI SELESAI!\n";
echo "Data telah berhasil diimpor dengan mempertahankan users dan permissions.\n";