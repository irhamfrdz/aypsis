<?php

/**
 * Script untuk membuat akun user untuk semua karyawan
 * Username: nama_panggilan
 * Password: password
 * Status: approved
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Karyawan;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

echo "===========================================\n";
echo "Script Pembuatan Akun Karyawan\n";
echo "===========================================\n\n";

// Get all karyawan yang belum punya user account
$karyawans = Karyawan::whereDoesntHave('user')
    ->whereNotNull('nama_panggilan')
    ->where('nama_panggilan', '!=', '')
    ->get();

if ($karyawans->isEmpty()) {
    echo "Tidak ada karyawan yang perlu dibuatkan akun.\n";
    echo "Semua karyawan sudah memiliki akun atau tidak memiliki nama panggilan.\n\n";
    exit(0);
}

echo "Ditemukan " . $karyawans->count() . " karyawan yang belum memiliki akun.\n\n";
echo "Data yang akan dibuat:\n";
echo str_repeat("-", 80) . "\n";
printf("%-5s | %-15s | %-30s | %-20s\n", "No", "NIK", "Nama Lengkap", "Username");
echo str_repeat("-", 80) . "\n";

$index = 1;
foreach ($karyawans as $karyawan) {
    printf(
        "%-5d | %-15s | %-30s | %-20s\n",
        $index++,
        $karyawan->nik ?? '-',
        substr($karyawan->nama_lengkap ?? '-', 0, 30),
        $karyawan->nama_panggilan
    );
}
echo str_repeat("-", 80) . "\n\n";

// Konfirmasi
echo "Apakah Anda ingin melanjutkan pembuatan akun? (yes/no): ";
$handle = fopen("php://stdin", "r");
$line = trim(fgets($handle));
fclose($handle);

if (strtolower($line) !== 'yes' && strtolower($line) !== 'y') {
    echo "\nProses dibatalkan.\n\n";
    exit(0);
}

echo "\nMemulai pembuatan akun...\n\n";

$successCount = 0;
$errorCount = 0;
$errors = [];

DB::beginTransaction();

try {
    foreach ($karyawans as $karyawan) {
        try {
            // Generate username dari nama_panggilan
            $username = strtolower(trim($karyawan->nama_panggilan));
            
            // Cek apakah username sudah ada
            $existingUser = User::where('username', $username)->first();
            
            if ($existingUser) {
                // Jika username sudah ada, tambahkan nomor di belakangnya
                $counter = 1;
                $newUsername = $username . $counter;
                
                while (User::where('username', $newUsername)->exists()) {
                    $counter++;
                    $newUsername = $username . $counter;
                }
                
                $username = $newUsername;
                echo "⚠ Username '{$karyawan->nama_panggilan}' sudah ada, menggunakan '{$username}'\n";
            }
            
            // Buat user baru
            $user = User::create([
                'name' => $karyawan->nama_lengkap ?? $karyawan->nama_panggilan,
                'username' => $username,
                'password' => Hash::make('password'),
                'karyawan_id' => $karyawan->id,
                'status' => 'approved',
                'registration_reason' => 'Auto-generated account by system',
                'approved_by' => 1, // Admin user ID (sesuaikan dengan ID admin Anda)
                'approved_at' => now(),
            ]);
            
            echo "✓ Berhasil membuat akun untuk: {$karyawan->nama_lengkap} (Username: {$username})\n";
            $successCount++;
            
        } catch (\Exception $e) {
            $errorMessage = "✗ Gagal membuat akun untuk {$karyawan->nama_lengkap}: " . $e->getMessage();
            echo $errorMessage . "\n";
            $errors[] = $errorMessage;
            $errorCount++;
        }
    }
    
    DB::commit();
    
    echo "\n" . str_repeat("=", 80) . "\n";
    echo "Proses selesai!\n";
    echo str_repeat("=", 80) . "\n";
    echo "Total karyawan diproses: " . $karyawans->count() . "\n";
    echo "Berhasil dibuat: " . $successCount . "\n";
    echo "Gagal: " . $errorCount . "\n";
    
    if (!empty($errors)) {
        echo "\nDetail Error:\n";
        foreach ($errors as $error) {
            echo "  - " . $error . "\n";
        }
    }
    
    echo "\n";
    echo "CATATAN PENTING:\n";
    echo "- Username: dari nama_panggilan (huruf kecil)\n";
    echo "- Password default: password\n";
    echo "- Status: approved\n";
    echo "- Semua karyawan sudah bisa login dengan username dan password tersebut\n";
    echo "- Disarankan agar setiap karyawan mengganti password setelah login pertama kali\n";
    echo "\n";
    
} catch (\Exception $e) {
    DB::rollback();
    echo "\n✗ ERROR: Terjadi kesalahan fatal: " . $e->getMessage() . "\n";
    echo "Semua perubahan dibatalkan (rollback).\n\n";
    exit(1);
}
