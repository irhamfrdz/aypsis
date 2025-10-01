<?php

// Script untuk fix case sensitivity bank name di karyawan
echo "=== FIX BANK NAME CASE SENSITIVITY ===\n\n";

$host = '127.0.0.1';
$dbname = 'aypsis';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // 1. Ambil data karyawan yang nama banknya dalam huruf besar
    echo "1. Mencari karyawan dengan nama bank case sensitivity issue:\n";
    $stmt = $pdo->prepare("SELECT id, nama_lengkap, nama_bank FROM karyawans WHERE nama_lengkap = ?");
    $stmt->execute(['AHMAD FAUZI RAHMAN']); // Nama sudah dalam huruf besar
    $karyawan = $stmt->fetch(PDO::FETCH_OBJ);
    
    if ($karyawan) {
        echo "   Found: {$karyawan->nama_lengkap}\n";
        echo "   Bank saat ini: '{$karyawan->nama_bank}'\n";
        
        // 2. Cari bank yang sesuai di tabel banks (case insensitive)
        echo "\n2. Mencari bank yang sesuai di tabel banks:\n";
        $stmt = $pdo->prepare("SELECT id, name FROM banks WHERE UPPER(name) = UPPER(?)");
        $stmt->execute([$karyawan->nama_bank]);
        $correctBank = $stmt->fetch(PDO::FETCH_OBJ);
        
        if ($correctBank) {
            echo "   Bank yang benar: '{$correctBank->name}'\n";
            
            // 3. Update nama bank karyawan agar sesuai dengan format di tabel banks
            echo "\n3. Updating nama bank karyawan...\n";
            $stmt = $pdo->prepare("UPDATE karyawans SET nama_bank = ? WHERE id = ?");
            $result = $stmt->execute([$correctBank->name, $karyawan->id]);
            
            if ($result) {
                echo "   ✅ BERHASIL! Bank name updated dari '{$karyawan->nama_bank}' ke '{$correctBank->name}'\n";
                
                // 4. Verification
                echo "\n4. Verification setelah update:\n";
                $stmt = $pdo->prepare("SELECT nama_bank FROM karyawans WHERE id = ?");
                $stmt->execute([$karyawan->id]);
                $updatedKaryawan = $stmt->fetch(PDO::FETCH_OBJ);
                
                echo "   Bank setelah update: '{$updatedKaryawan->nama_bank}'\n";
                echo "   Bank di tabel banks: '{$correctBank->name}'\n";
                echo "   Match: " . ($updatedKaryawan->nama_bank === $correctBank->name ? "✅ YES" : "❌ NO") . "\n";
                
                if ($updatedKaryawan->nama_bank === $correctBank->name) {
                    echo "\n🎉 MASALAH TERATASI!\n";
                    echo "Sekarang dropdown bank akan menampilkan pilihan yang benar di form edit.\n";
                }
                
            } else {
                echo "   ❌ Gagal mengupdate data\n";
            }
            
        } else {
            echo "   ❌ Tidak ditemukan bank yang sesuai di tabel banks\n";
        }
        
    } else {
        echo "   Karyawan tidak ditemukan\n";
    }
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

?>