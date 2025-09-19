<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

echo "🔍 Memeriksa User Admin dan Relasi Karyawan\n";
echo "===========================================\n\n";

$user = User::where('username', 'admin')->with('karyawan')->first();

if ($user) {
    echo "✅ User admin ditemukan:\n";
    echo "   - ID: {$user->id}\n";
    echo "   - Username: {$user->username}\n";
    echo "   - Karyawan ID: " . ($user->karyawan_id ?: 'NULL') . "\n\n";

    if ($user->karyawan) {
        echo "✅ User memiliki relasi karyawan:\n";
        echo "   - Karyawan ID: {$user->karyawan->id}\n";
        echo "   - Nama: {$user->karyawan->nama_lengkap}\n";
        echo "   - NIK: {$user->karyawan->nik}\n";
        echo "   - Divisi: {$user->karyawan->divisi}\n\n";

        echo "🎯 Sidebar Check:\n";
        echo "   - Has Karyawan: ✅ YES (sidebar akan ditampilkan)\n";
    } else {
        echo "❌ User TIDAK memiliki relasi karyawan yang valid\n";
        echo "   - Karyawan ID: {$user->karyawan_id}\n\n";

        echo "🎯 Sidebar Check:\n";
        echo "   - Has Karyawan: ❌ NO (sidebar TIDAK akan ditampilkan)\n\n";

        echo "🔍 Mencari karyawan dengan ID {$user->karyawan_id}:\n";
        $karyawan = \App\Models\Karyawan::find($user->karyawan_id);
        if ($karyawan) {
            echo "   ✅ Karyawan ditemukan di database\n";
            echo "   - Nama: {$karyawan->nama_lengkap}\n";
            echo "   💡 Masalah: Relasi tidak dimuat dengan benar\n";
        } else {
            echo "   ❌ Karyawan TIDAK ditemukan di database\n";
            echo "   💡 Masalah: karyawan_id tidak valid\n";
        }
    }

} else {
    echo "❌ User admin tidak ditemukan\n";
}

echo "\n🔧 Solusi:\n";
echo "=========\n";
echo "1. Pastikan user admin memiliki karyawan_id yang valid\n";
echo "2. Pastikan karyawan dengan ID tersebut ada di tabel karyawans\n";
echo "3. Atau modifikasi kondisi sidebar untuk admin\n";
