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
    echo "🔍 DEBUGGING DIVISI & PEKERJAAN FORM EDIT\n";
    echo "=========================================\n\n";

    // 1. Cek data karyawan sample
    $karyawan = Capsule::table('karyawans')
                ->where('nik', '3201234567890123')
                ->first();

    if ($karyawan) {
        echo "📋 DATA KARYAWAN SAMPLE:\n";
        echo "Nama: {$karyawan->nama_lengkap}\n";
        echo "Divisi: '{$karyawan->divisi}'\n";
        echo "Pekerjaan: '{$karyawan->pekerjaan}'\n\n";
    } else {
        echo "❌ Karyawan sample tidak ditemukan!\n";
        exit;
    }

    // 2. Cek data divisi yang tersedia
    $divisis = Capsule::table('divisis')->get();
    echo "📊 DAFTAR DIVISI YANG TERSEDIA:\n";
    echo "Total divisi: " . $divisis->count() . "\n";
    foreach ($divisis as $divisi) {
        $isMatch = $divisi->nama_divisi === $karyawan->divisi ? " ✅ (MATCH)" : "";
        echo "- '{$divisi->nama_divisi}'{$isMatch}\n";
    }
    echo "\n";

    // 3. Cek data pekerjaan yang tersedia
    $pekerjaans = Capsule::table('pekerjaans')->get();
    echo "💼 DAFTAR PEKERJAAN YANG TERSEDIA:\n";
    echo "Total pekerjaan: " . $pekerjaans->count() . "\n";

    $pekerjaanByDivisi = [];
    foreach ($pekerjaans as $pekerjaan) {
        if (!isset($pekerjaanByDivisi[$pekerjaan->divisi])) {
            $pekerjaanByDivisi[$pekerjaan->divisi] = [];
        }
        $pekerjaanByDivisi[$pekerjaan->divisi][] = $pekerjaan->nama_pekerjaan;
    }

    foreach ($pekerjaanByDivisi as $divisiName => $pekerjaanList) {
        echo "\n📁 Divisi: '{$divisiName}'\n";
        foreach ($pekerjaanList as $pekerjaan) {
            $isMatch = ($divisiName === $karyawan->divisi && $pekerjaan === $karyawan->pekerjaan) ? " ✅ (MATCH)" : "";
            echo "  - '{$pekerjaan}'{$isMatch}\n";
        }
    }

    // 4. Analisis masalah
    echo "\n🔍 ANALISIS MASALAH:\n";
    echo "====================\n";

    // Cek apakah divisi karyawan ada dalam tabel divisis
    $divisiExists = false;
    foreach ($divisis as $divisi) {
        if ($divisi->nama_divisi === $karyawan->divisi) {
            $divisiExists = true;
            break;
        }
    }

    if ($divisiExists) {
        echo "✅ Divisi karyawan ('{$karyawan->divisi}') ada dalam tabel divisis\n";
    } else {
        echo "❌ Divisi karyawan ('{$karyawan->divisi}') TIDAK ada dalam tabel divisis\n";
        echo "💡 Solusi: Tambahkan divisi '{$karyawan->divisi}' ke tabel divisis\n";
    }

    // Cek apakah pekerjaan karyawan ada dalam tabel pekerjaans untuk divisi yang sesuai
    $pekerjaanExists = false;
    if (isset($pekerjaanByDivisi[$karyawan->divisi])) {
        foreach ($pekerjaanByDivisi[$karyawan->divisi] as $pekerjaan) {
            if ($pekerjaan === $karyawan->pekerjaan) {
                $pekerjaanExists = true;
                break;
            }
        }
    }

    if ($pekerjaanExists) {
        echo "✅ Pekerjaan karyawan ('{$karyawan->pekerjaan}') ada dalam tabel pekerjaans untuk divisi '{$karyawan->divisi}'\n";
    } else {
        echo "❌ Pekerjaan karyawan ('{$karyawan->pekerjaan}') TIDAK ada dalam tabel pekerjaans untuk divisi '{$karyawan->divisi}'\n";
        echo "💡 Solusi: Tambahkan pekerjaan '{$karyawan->pekerjaan}' ke tabel pekerjaans untuk divisi '{$karyawan->divisi}'\n";
    }

    // 5. Generate JSON untuk debugging JavaScript
    echo "\n🔧 JSON UNTUK DEBUGGING:\n";
    echo "========================\n";
    echo "pekerjaanByDivisi JSON:\n";
    echo json_encode($pekerjaanByDivisi, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    // 6. Recommendations
    echo "\n💡 REKOMENDASI PERBAIKAN:\n";
    echo "==========================\n";

    if (!$divisiExists || !$pekerjaanExists) {
        echo "1. Jalankan seeder untuk menambah divisi dan pekerjaan yang kurang\n";
        echo "2. Atau update data karyawan untuk menggunakan divisi/pekerjaan yang sudah ada\n";
        echo "3. Cek controller edit apakah sudah passing data \$divisis, \$pekerjaanByDivisi, dll\n";
    } else {
        echo "✅ Data divisi dan pekerjaan sudah sesuai\n";
        echo "❗ Kemungkinan masalah ada di:\n";
        echo "   1. JavaScript timing (sudah diperbaiki dengan setTimeout)\n";
        echo "   2. Controller tidak passing data yang diperlukan\n";
        echo "   3. Blade template tidak menerima variabel dengan benar\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
