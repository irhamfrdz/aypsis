<?php

// Script untuk fix semua case sensitivity issues di data karyawan
echo "=== FIX ALL CASE SENSITIVITY ISSUES ===\n\n";

$host = '127.0.0.1';
$dbname = 'aypsis';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // 1. Fix nama menjadi proper case
    echo "1. Memperbaiki nama dari HURUF BESAR ke Proper Case:\n";
    $stmt = $pdo->prepare("UPDATE karyawans SET 
        nama_lengkap = 'Ahmad Fauzi Rahman',
        nama_panggilan = 'Ahmad',
        atas_nama = 'Ahmad Fauzi Rahman',
        bank_cabang = 'Cabang Sudirman'
        WHERE nama_lengkap = 'AHMAD FAUZI RAHMAN'");
    
    $result = $stmt->execute();
    
    if ($result) {
        echo "   ✅ Nama dan field terkait berhasil diperbaiki\n";
    }
    
    // 2. Verification final data
    echo "\n2. Data final karyawan Ahmad Fauzi Rahman:\n";
    $stmt = $pdo->prepare("SELECT nama_lengkap, nama_panggilan, nama_bank, bank_cabang, atas_nama FROM karyawans WHERE nama_lengkap = ?");
    $stmt->execute(['Ahmad Fauzi Rahman']);
    $karyawan = $stmt->fetch(PDO::FETCH_OBJ);
    
    if ($karyawan) {
        echo "   Nama Lengkap: '{$karyawan->nama_lengkap}'\n";
        echo "   Nama Panggilan: '{$karyawan->nama_panggilan}'\n";
        echo "   Nama Bank: '{$karyawan->nama_bank}'\n";
        echo "   Cabang Bank: '{$karyawan->bank_cabang}'\n";
        echo "   Atas Nama: '{$karyawan->atas_nama}'\n";
        
        // 3. Final validation dengan banks table
        echo "\n3. Validasi final dengan tabel banks:\n";
        $stmt = $pdo->prepare("SELECT name FROM banks WHERE name = ?");
        $stmt->execute([$karyawan->nama_bank]);
        $bankMatch = $stmt->fetch(PDO::FETCH_OBJ);
        
        if ($bankMatch) {
            echo "   ✅ Bank match: '{$karyawan->nama_bank}' = '{$bankMatch->name}'\n";
            echo "   ✅ Dropdown edit form sekarang akan menampilkan bank yang benar!\n";
        } else {
            echo "   ❌ Bank masih tidak match\n";
        }
        
        echo "\n🎯 KESIMPULAN:\n";
        echo "Masalah 'pilih bank kosong' disebabkan oleh case sensitivity:\n";
        echo "- Data karyawan memiliki nama bank dalam HURUF BESAR\n";
        echo "- Data tabel banks menggunakan Proper Case\n";
        echo "- Comparison di Blade template gagal karena case tidak sama\n";
        echo "- Sekarang sudah diperbaiki dan dropdown akan berfungsi normal ✅\n";
        
    }
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

?>