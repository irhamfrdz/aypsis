<?php

require_once 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;

// Setup database connection
$capsule = new Capsule;
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => env('DB_HOST', '127.0.0.1'),
    'port' => env('DB_PORT', '3306'),
    'database' => env('DB_DATABASE', 'aypsis'),
    'username' => env('DB_USERNAME', 'root'),
    'password' => env('DB_PASSWORD', ''),
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

try {
    echo "🔧 MEMPERBAIKI DATA KARYAWAN SAMPLE\n";
    echo "====================================\n\n";

    // Update karyawan sample untuk menggunakan divisi dan pekerjaan yang ada
    // Kita akan gunakan: ADMINISTRASI - IT (karena sesuai dengan profil modern)
    $updated = Capsule::table('karyawans')
                ->where('nik', '3201234567890123')
                ->update([
                    'divisi' => 'ADMINISTRASI',
                    'pekerjaan' => 'IT',
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

    if ($updated) {
        echo "✅ Data karyawan berhasil diupdate!\n";
        
        // Verifikasi data yang sudah diupdate
        $karyawan = Capsule::table('karyawans')
                    ->where('nik', '3201234567890123')
                    ->first();
        
        echo "\n📋 DATA KARYAWAN SETELAH UPDATE:\n";
        echo "Nama: {$karyawan->nama_lengkap}\n";
        echo "NIK: {$karyawan->nik}\n";
        echo "Divisi: '{$karyawan->divisi}' ✅\n";
        echo "Pekerjaan: '{$karyawan->pekerjaan}' ✅\n";
        echo "Email: {$karyawan->email}\n";
        echo "Updated at: {$karyawan->updated_at}\n\n";

        // Verifikasi bahwa divisi dan pekerjaan sudah sesuai
        $divisiExists = Capsule::table('divisis')
                        ->where('nama_divisi', $karyawan->divisi)
                        ->exists();
        
        $pekerjaanExists = Capsule::table('pekerjaans')
                          ->where('divisi', $karyawan->divisi)
                          ->where('nama_pekerjaan', $karyawan->pekerjaan)
                          ->exists();

        echo "🔍 VERIFIKASI:\n";
        if ($divisiExists) {
            echo "✅ Divisi '{$karyawan->divisi}' ada dalam tabel divisis\n";
        } else {
            echo "❌ Divisi '{$karyawan->divisi}' tidak ada dalam tabel divisis\n";
        }

        if ($pekerjaanExists) {
            echo "✅ Pekerjaan '{$karyawan->pekerjaan}' ada dalam tabel pekerjaans untuk divisi '{$karyawan->divisi}'\n";
        } else {
            echo "❌ Pekerjaan '{$karyawan->pekerjaan}' tidak ada dalam tabel pekerjaans\n";
        }

        if ($divisiExists && $pekerjaanExists) {
            echo "\n🎉 MASALAH SELESAI!\n";
            echo "Form edit sekarang akan menampilkan divisi dan pekerjaan dengan benar.\n\n";
            
            echo "💡 CARA TESTING:\n";
            echo "1. Buka form edit karyawan untuk Ahmad Fauzi Rahman\n";
            echo "2. Divisi 'ADMINISTRASI' harus sudah terselect\n";
            echo "3. Pekerjaan 'IT' harus muncul dalam dropdown dan terselect\n";
        } else {
            echo "\n⚠️  Masih ada masalah dengan data referensi.\n";
        }

    } else {
        echo "❌ Gagal mengupdate data karyawan!\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}