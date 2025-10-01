<?php

// Script untuk verify bank data yang sudah diperbaiki
echo "=== VERIFY BANK DATA AFTER SEEDING ===\n\n";

// Simple connection untuk menghindari Laravel issues
$host = '127.0.0.1';
$dbname = 'aypsis';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // 1. Check bank count
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM banks");
    $bankCount = $stmt->fetch(PDO::FETCH_OBJ)->count;
    echo "1. Jumlah bank di database: $bankCount\n\n";
    
    if ($bankCount > 0) {
        // 2. List first 10 banks
        echo "2. Sample bank names:\n";
        $stmt = $pdo->query("SELECT name FROM banks ORDER BY name LIMIT 10");
        $banks = $stmt->fetchAll(PDO::FETCH_OBJ);
        foreach ($banks as $bank) {
            echo "   - {$bank->name}\n";
        }
        
        // 3. Check Ahmad's bank data
        echo "\n3. Data bank Ahmad Fauzi Rahman:\n";
        $stmt = $pdo->prepare("SELECT nama_lengkap, nama_bank, bank_cabang, akun_bank, atas_nama FROM karyawan WHERE nama_lengkap = ?");
        $stmt->execute(['Ahmad Fauzi Rahman']);
        $karyawan = $stmt->fetch(PDO::FETCH_OBJ);
        
        if ($karyawan) {
            echo "   Nama: {$karyawan->nama_lengkap}\n";
            echo "   Bank: '{$karyawan->nama_bank}'\n";
            echo "   Cabang: '{$karyawan->bank_cabang}'\n";
            echo "   No Rekening: '{$karyawan->akun_bank}'\n";
            echo "   Atas Nama: '{$karyawan->atas_nama}'\n";
            
            // 4. Check if bank exists in banks table
            echo "\n4. Validasi bank di tabel banks:\n";
            $stmt = $pdo->prepare("SELECT name FROM banks WHERE name = ?");
            $stmt->execute([$karyawan->nama_bank]);
            $bankExists = $stmt->fetch(PDO::FETCH_OBJ);
            
            if ($bankExists) {
                echo "   ✅ Bank '{$karyawan->nama_bank}' ditemukan di tabel banks\n";
                echo "   ✅ Dropdown seharusnya menampilkan bank yang selected\n";
            } else {
                echo "   ❌ Bank '{$karyawan->nama_bank}' TIDAK ditemukan di tabel banks\n";
            }
            
        } else {
            echo "   ❌ Karyawan 'Ahmad Fauzi Rahman' tidak ditemukan!\n";
        }
        
    } else {
        echo "❌ Tidak ada bank di database! BankSeeder mungkin gagal.\n";
    }
    
} catch (PDOException $e) {
    echo "❌ Error koneksi database: " . $e->getMessage() . "\n";
    
    // Check apakah tabel ada
    if (strpos($e->getMessage(), 'Table') !== false && strpos($e->getMessage(), "doesn't exist") !== false) {
        echo "\n💡 Kemungkinan tabel belum ada. Jalankan:\n";
        echo "   php artisan migrate\n";
        echo "   php artisan db:seed\n";
    }
}

?>