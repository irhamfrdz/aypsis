<?php

// Script untuk verify bank data karyawan (dengan nama tabel yang benar)
echo "=== VERIFY KARYAWAN BANK DATA ===\n\n";

$host = '127.0.0.1';
$dbname = 'aypsis';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. Check Ahmad's bank data
    echo "1. Data bank Ahmad Fauzi Rahman:\n";
    $stmt = $pdo->prepare("SELECT nama_lengkap, nama_bank, bank_cabang, akun_bank, atas_nama FROM karyawans WHERE nama_lengkap = ?");
    $stmt->execute(['Ahmad Fauzi Rahman']);
    $karyawan = $stmt->fetch(PDO::FETCH_OBJ);

    if ($karyawan) {
        echo "   Nama: {$karyawan->nama_lengkap}\n";
        echo "   Bank: '{$karyawan->nama_bank}'\n";
        echo "   Cabang: '{$karyawan->bank_cabang}'\n";
        echo "   No Rekening: '{$karyawan->akun_bank}'\n";
        echo "   Atas Nama: '{$karyawan->atas_nama}'\n\n";

        // 2. Check if bank exists in banks table
        echo "2. Validasi bank di tabel banks:\n";
        $stmt = $pdo->prepare("SELECT id, name FROM banks WHERE name = ?");
        $stmt->execute([$karyawan->nama_bank]);
        $bankExists = $stmt->fetch(PDO::FETCH_OBJ);

        if ($bankExists) {
            echo "   ✅ Bank '{$karyawan->nama_bank}' ditemukan di tabel banks (ID: {$bankExists->id})\n";
            echo "   ✅ Dropdown seharusnya menampilkan bank yang selected\n\n";

            // 3. Simulate the dropdown logic dari edit.blade.php
            echo "3. Simulasi logic dropdown di edit.blade.php:\n";
            echo "   Kondisi: old('nama_bank', \$karyawan->nama_bank) == \$bank->name\n";
            echo "   Value karyawan: '{$karyawan->nama_bank}'\n";
            echo "   Value bank: '{$bankExists->name}'\n";
            echo "   Comparison: '{$karyawan->nama_bank}' == '{$bankExists->name}' = " . ($karyawan->nama_bank === $bankExists->name ? 'TRUE ✅' : 'FALSE ❌') . "\n\n";

            if ($karyawan->nama_bank === $bankExists->name) {
                echo "4. 🎉 MASALAH SUDAH TERATASI!\n";
                echo "   Bank akan ter-select dengan benar di dropdown.\n";
                echo "   Jika masih tidak muncul, kemungkinan:\n";
                echo "   - Cache view perlu di-clear: php artisan view:clear\n";
                echo "   - Browser cache perlu di-refresh (Ctrl+F5)\n";
            }

        } else {
            echo "   ❌ Bank '{$karyawan->nama_bank}' TIDAK ditemukan di tabel banks\n";

            // Show similar banks
            echo "\n   Bank yang mirip:\n";
            $stmt = $pdo->query("SELECT name FROM banks ORDER BY name");
            $allBanks = $stmt->fetchAll(PDO::FETCH_OBJ);
            foreach ($allBanks as $bank) {
                $similarity = similar_text(strtolower($karyawan->nama_bank), strtolower($bank->name), $percent);
                if ($percent > 70) {
                    echo "   - '{$bank->name}' (similarity: {$percent}%)\n";
                }
            }
        }

    } else {
        echo "   ❌ Karyawan 'Ahmad Fauzi Rahman' tidak ditemukan di tabel karyawans!\n";
    }

} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

?>
