<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== CHECKING USER KARYAWAN_ID ===" . PHP_EOL;

$user = \App\Models\User::where('username', 'admin')->first();
if ($user) {
    echo 'User: ' . $user->username . ' (ID: ' . $user->id . ')' . PHP_EOL;
    echo 'karyawan_id: ' . ($user->karyawan_id ?: 'NULL/EMPTY') . PHP_EOL;

    if (empty($user->karyawan_id)) {
        echo PHP_EOL . '❌ PROBLEM FOUND: User tidak memiliki karyawan_id!' . PHP_EOL;
        echo 'Middleware EnsureKaryawanPresent akan memblokir akses ke route approval/surat-jalan.' . PHP_EOL;
        echo PHP_EOL . 'SOLUSI: Set karyawan_id untuk user admin.' . PHP_EOL;

        // Cek apakah ada data karyawan
        $karyawan = \App\Models\Karyawan::first();
        if ($karyawan) {
            echo 'Ada karyawan tersedia (ID: ' . $karyawan->id . ', Nama: ' . $karyawan->nama . ')' . PHP_EOL;
            echo 'Mau assign otomatis? (Y/N)';
        } else {
            echo 'Tidak ada data karyawan. Perlu buat karyawan dulu.' . PHP_EOL;
        }
    } else {
        echo '✅ karyawan_id OK: ' . $user->karyawan_id . PHP_EOL;
    }
} else {
    echo '❌ User admin tidak ditemukan' . PHP_EOL;
}

echo PHP_EOL . "=== END CHECK ===" . PHP_EOL;
