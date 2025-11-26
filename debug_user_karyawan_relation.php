<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DEBUGGING HUBUNGAN USER-KARYAWAN ===\n\n";

// 1. Cari user anggi
$user = App\Models\User::where('username', 'anggi')->first();

if (!$user) {
    echo "❌ User 'anggi' tidak ditemukan!\n";
    exit;
}

echo "✅ User ditemukan!\n";
echo "   ID: {$user->id}\n";
echo "   Username: {$user->username}\n";
echo "   Name: {$user->name}\n";
echo "   Email: {$user->email}\n\n";

// 2. Cek semua karyawan yang berhubungan dengan user ini
echo "=== PENCARIAN KARYAWAN BERDASARKAN USER_ID ===\n";
$karyawanByUserId = App\Models\Karyawan::where('user_id', $user->id)->get();

echo "Jumlah karyawan dengan user_id {$user->id}: " . $karyawanByUserId->count() . "\n";
foreach ($karyawanByUserId as $k) {
    echo "  - ID: {$k->id}, Nama: {$k->nama}, NIK: {$k->nik}\n";
}

// 3. Cek semua karyawan dengan nama yang mirip "ANGGI"
echo "\n=== PENCARIAN KARYAWAN BERDASARKAN NAMA ===\n";
$karyawanByName = App\Models\Karyawan::where('nama', 'like', '%ANGGI%')->get();

echo "Jumlah karyawan dengan nama mengandung 'ANGGI': " . $karyawanByName->count() . "\n";
foreach ($karyawanByName as $k) {
    echo "  - ID: {$k->id}, User_ID: {$k->user_id}, Nama: {$k->nama}, NIK: {$k->nik}\n";
}

// 4. Cek apakah ada karyawan dengan NIK 0203
echo "\n=== PENCARIAN KARYAWAN BERDASARKAN NIK ===\n";
$karyawanByNik = App\Models\Karyawan::where('nik', 'like', '%0203%')->get();

echo "Jumlah karyawan dengan NIK mengandung '0203': " . $karyawanByNik->count() . "\n";
foreach ($karyawanByNik as $k) {
    echo "  - ID: {$k->id}, User_ID: {$k->user_id}, Nama: {$k->nama}, NIK: {$k->nik}\n";
}

// 5. Cek semua karyawan yang tidak memiliki user_id
echo "\n=== KARYAWAN TANPA USER_ID ===\n";
$karyawanTanpaUser = App\Models\Karyawan::whereNull('user_id')->get();

echo "Jumlah karyawan tanpa user_id: " . $karyawanTanpaUser->count() . "\n";
foreach ($karyawanTanpaUser as $k) {
    echo "  - ID: {$k->id}, Nama: {$k->nama}, NIK: {$k->nik}\n";
}

// 6. Cek struktur table karyawan
echo "\n=== STRUKTUR TABLE KARYAWANS ===\n";
try {
    $columns = DB::select("SHOW COLUMNS FROM karyawans");
    foreach ($columns as $column) {
        echo "  - {$column->Field}: {$column->Type} (Null: {$column->Null}, Default: {$column->Default})\n";
    }
} catch (Exception $e) {
    echo "Error getting table structure: " . $e->getMessage() . "\n";
}

// 7. Jika ada karyawan ANGGI tanpa user_id, hubungkan dengan user anggi
$anggiKaryawan = App\Models\Karyawan::where('nama', 'like', '%ANGGI%')->whereNull('user_id')->first();

if ($anggiKaryawan) {
    echo "\n=== MENGHUBUNGKAN KARYAWAN DENGAN USER ===\n";
    echo "Ditemukan karyawan ANGGI tanpa user_id:\n";
    echo "  - ID: {$anggiKaryawan->id}, Nama: {$anggiKaryawan->nama}, NIK: {$anggiKaryawan->nik}\n";
    
    try {
        $anggiKaryawan->user_id = $user->id;
        $anggiKaryawan->save();
        
        echo "✅ Berhasil menghubungkan karyawan {$anggiKaryawan->nama} dengan user {$user->username}\n";
        
        // Verifikasi
        $user->refresh();
        $karyawan = $user->karyawan;
        if ($karyawan) {
            echo "✅ Verifikasi: User {$user->username} sekarang terhubung dengan karyawan {$karyawan->nama}\n";
        }
    } catch (Exception $e) {
        echo "❌ Error menghubungkan: " . $e->getMessage() . "\n";
    }
} else {
    echo "\n❌ Tidak ditemukan karyawan ANGGI yang bisa dihubungkan\n";
}

echo "\nScript completed.\n";