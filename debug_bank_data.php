<?php

require_once 'bootstrap/app.php';

use Illuminate\Support\Facades\DB;

echo "=== DEBUG BANK DATA SELECTION ===\n\n";

// 1. Cek data karyawan Ahmad Fauzi Rahman
echo "1. Data Karyawan:\n";
$karyawan = DB::table('karyawan')
    ->where('nama_lengkap', 'Ahmad Fauzi Rahman')
    ->first();

if ($karyawan) {
    echo "   NIK: {$karyawan->nik}\n";
    echo "   Nama: {$karyawan->nama_lengkap}\n";
    echo "   Bank yang tersimpan: '{$karyawan->nama_bank}'\n";
    echo "   Cabang Bank: '{$karyawan->bank_cabang}'\n";
    echo "   No Rekening: '{$karyawan->akun_bank}'\n";
    echo "   Atas Nama: '{$karyawan->atas_nama}'\n\n";
} else {
    echo "   Karyawan tidak ditemukan!\n\n";
}

// 2. Cek semua data bank yang tersedia
echo "2. Daftar Bank yang tersedia:\n";
$banks = DB::table('banks')->get();

if ($banks->count() > 0) {
    foreach ($banks as $index => $bank) {
        $isMatch = ($karyawan && $karyawan->nama_bank == $bank->name) ? " ← MATCH!" : "";
        echo "   " . ($index + 1) . ". ID: {$bank->id}, Name: '{$bank->name}'{$isMatch}\n";
    }
} else {
    echo "   Tidak ada data bank di tabel 'banks'!\n";
}

echo "\n";

// 3. Cek apakah ada perbedaan nama bank
if ($karyawan && $karyawan->nama_bank) {
    echo "3. Analisis Matching:\n";
    $exactMatch = $banks->where('name', $karyawan->nama_bank)->first();

    if ($exactMatch) {
        echo "   ✅ EXACT MATCH ditemukan: '{$karyawan->nama_bank}' = '{$exactMatch->name}'\n";
    } else {
        echo "   ❌ TIDAK ADA EXACT MATCH untuk '{$karyawan->nama_bank}'\n";
        echo "   \n   Kemungkinan penyebab:\n";
        echo "   - Nama bank di data karyawan berbeda dengan nama di tabel banks\n";
        echo "   - Ada spasi ekstra atau karakter khusus\n";
        echo "   - Case sensitivity (huruf besar/kecil)\n\n";

        echo "   Mencari bank dengan nama mirip:\n";
        foreach ($banks as $bank) {
            $similarity = similar_text(strtolower($karyawan->nama_bank), strtolower($bank->name), $percent);
            if ($percent > 50) {
                echo "   - '{$bank->name}' (similarity: {$percent}%)\n";
            }
        }
    }
}

// 4. Cek jika ada data lain di tabel karyawan dengan nama_bank
echo "\n4. Karyawan lain dengan data bank:\n";
$karyawanWithBank = DB::table('karyawan')
    ->whereNotNull('nama_bank')
    ->where('nama_bank', '!=', '')
    ->select('nama_lengkap', 'nama_bank')
    ->get();

if ($karyawanWithBank->count() > 0) {
    foreach ($karyawanWithBank as $k) {
        echo "   - {$k->nama_lengkap}: '{$k->nama_bank}'\n";
    }
} else {
    echo "   Tidak ada karyawan dengan data bank\n";
}

?>
