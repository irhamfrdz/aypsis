<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== MENGHUBUNGKAN USER ANGGI DENGAN KARYAWAN ===\n\n";

// Cari user anggi
$user = App\Models\User::where('username', 'anggi')->first();

if (!$user) {
    echo "❌ User 'anggi' tidak ditemukan!\n";
    exit;
}

echo "✅ User ditemukan: ID {$user->id}, Username: {$user->username}, Name: {$user->name}\n";

// Cari karyawan ANGGI dengan NIK 0203
$karyawan = DB::table('karyawans')->where('nik', '0203')->where('nama_panggilan', 'ANGGI')->first();

if (!$karyawan) {
    echo "❌ Karyawan ANGGI dengan NIK 0203 tidak ditemukan!\n";
    exit;
}

echo "✅ Karyawan ditemukan:\n";
echo "   ID: {$karyawan->id}\n";
echo "   NIK: {$karyawan->nik}\n";
echo "   Nama Panggilan: {$karyawan->nama_panggilan}\n";
echo "   Nama Lengkap: {$karyawan->nama_lengkap}\n";
echo "   User ID saat ini: " . ($karyawan->user_id ?: 'NULL') . "\n\n";

if ($karyawan->user_id) {
    echo "ℹ️  Karyawan sudah terhubung dengan user ID {$karyawan->user_id}\n";
    
    // Cek apakah user_id yang terhubung valid
    $existingUser = App\Models\User::find($karyawan->user_id);
    if ($existingUser) {
        echo "   Terhubung dengan user: {$existingUser->username} ({$existingUser->name})\n";
    } else {
        echo "   ⚠️  User ID {$karyawan->user_id} tidak valid, akan diupdate\n";
        $updateNeeded = true;
    }
} else {
    echo "➡️  Karyawan belum terhubung dengan user manapun\n";
    $updateNeeded = true;
}

if (isset($updateNeeded)) {
    echo "\n=== UPDATING KARYAWAN ===\n";
    
    try {
        $updated = DB::table('karyawans')
            ->where('id', $karyawan->id)
            ->update(['user_id' => $user->id]);
        
        if ($updated) {
            echo "✅ Berhasil menghubungkan karyawan ID {$karyawan->id} dengan user ID {$user->id}\n";
            
            // Verifikasi
            echo "\n=== VERIFIKASI ===\n";
            $updatedKaryawan = DB::table('karyawans')->where('id', $karyawan->id)->first();
            echo "Karyawan {$updatedKaryawan->nama_panggilan} sekarang terhubung dengan user_id: {$updatedKaryawan->user_id}\n";
            
            // Test relasi
            $user->refresh();
            $testKaryawan = App\Models\Karyawan::where('user_id', $user->id)->first();
            if ($testKaryawan) {
                echo "✅ Relasi berhasil: User {$user->username} -> Karyawan {$testKaryawan->nama_panggilan}\n";
            } else {
                echo "❌ Relasi gagal ditemukan\n";
            }
        } else {
            echo "❌ Gagal update karyawan\n";
        }
    } catch (Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "\n";
    }
} else {
    echo "\n✅ Tidak perlu update, karyawan sudah terhubung dengan benar\n";
}

echo "\nScript completed.\n";