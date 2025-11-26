<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== MEMBUAT RECORD KARYAWAN UNTUK USER ANGGI ===\n\n";

// Cari user anggi
$user = App\Models\User::where('username', 'anggi')->first();

if (!$user) {
    echo "❌ User 'anggi' tidak ditemukan!\n";
    exit;
}

echo "✅ User ditemukan: {$user->username} - {$user->name}\n";

// Cek apakah sudah ada karyawan
$existingKaryawan = App\Models\Karyawan::where('user_id', $user->id)->first();

if ($existingKaryawan) {
    echo "ℹ️  Karyawan sudah ada dengan ID: {$existingKaryawan->id}\n";
    echo "   Nama: {$existingKaryawan->nama}\n";
    echo "   NIK: {$existingKaryawan->nik}\n";
} else {
    echo "➕ Membuat record karyawan baru...\n";
    
    // Ambil divisi default (misal admin)
    $divisi = App\Models\Divisi::where('nama', 'Admin')->first();
    if (!$divisi) {
        // Jika tidak ada divisi admin, ambil divisi pertama
        $divisi = App\Models\Divisi::first();
    }
    
    // Ambil pekerjaan default
    $pekerjaan = App\Models\Pekerjaan::where('nama', 'Staff')->first();
    if (!$pekerjaan) {
        // Jika tidak ada pekerjaan staff, ambil pekerjaan pertama
        $pekerjaan = App\Models\Pekerjaan::first();
    }
    
    echo "   Using Divisi: " . ($divisi ? $divisi->nama : 'NULL') . "\n";
    echo "   Using Pekerjaan: " . ($pekerjaan ? $pekerjaan->nama : 'NULL') . "\n";
    
    try {
        $karyawan = App\Models\Karyawan::create([
            'user_id' => $user->id,
            'nama' => $user->name,
            'nik' => '12345' . $user->id, // Generate NIK sederhana
            'email' => $user->email ?: $user->username . '@company.com',
            'status' => 'active',
            'divisi_id' => $divisi ? $divisi->id : null,
            'pekerjaan_id' => $pekerjaan ? $pekerjaan->id : null,
            'tanggal_masuk' => now(),
        ]);
        
        echo "✅ Karyawan berhasil dibuat dengan ID: {$karyawan->id}\n";
        echo "   Nama: {$karyawan->nama}\n";
        echo "   NIK: {$karyawan->nik}\n";
        echo "   Email: {$karyawan->email}\n";
        echo "   Status: {$karyawan->status}\n";
        echo "   Divisi: " . ($karyawan->divisi->nama ?? 'NULL') . "\n";
        echo "   Pekerjaan: " . ($karyawan->pekerjaan->nama ?? 'NULL') . "\n";
        
    } catch (Exception $e) {
        echo "❌ Error membuat karyawan: " . $e->getMessage() . "\n";
        echo "   File: " . $e->getFile() . "\n";
        echo "   Line: " . $e->getLine() . "\n";
        
        // Tampilkan kolom yang required dari table karyawan
        try {
            $tableInfo = DB::select("DESCRIBE karyawans");
            echo "\n📋 Struktur table karyawans:\n";
            foreach ($tableInfo as $column) {
                $null = $column->Null == 'NO' ? 'REQUIRED' : 'optional';
                echo "   - {$column->Field}: {$column->Type} ({$null})\n";
            }
        } catch (Exception $e2) {
            echo "   Could not get table structure: " . $e2->getMessage() . "\n";
        }
    }
}

echo "\nScript completed.\n";