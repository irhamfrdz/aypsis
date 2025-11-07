<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Karyawan;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

echo "=== SCRIPT PEMBUATAN AKUN USER UNTUK SUPIR ===\n\n";

try {
    DB::beginTransaction();

    // Get all karyawan with divisi supir
    $supirKaryawans = Karyawan::where('divisi', 'LIKE', '%supir%')
        ->orWhere('divisi', 'LIKE', '%SUPIR%')
        ->orWhere('divisi', 'LIKE', '%Supir%')
        ->get();

    echo "📊 Ditemukan {$supirKaryawans->count()} karyawan dengan divisi supir\n\n";

    if ($supirKaryawans->count() === 0) {
        echo "❌ Tidak ada karyawan dengan divisi supir yang ditemukan.\n";
        exit(1);
    }

    $successCount = 0;
    $skippedCount = 0;
    $errorCount = 0;
    $reports = [];

    foreach ($supirKaryawans as $karyawan) {
        $namaPanggilan = trim($karyawan->nama_panggilan);
        $namaLengkap = trim($karyawan->nama_lengkap);
        $nik = trim($karyawan->nik);

        // Skip if no nama panggilan
        if (empty($namaPanggilan)) {
            echo "⚠️  SKIP: {$namaLengkap} ({$nik}) - Nama panggilan kosong\n";
            $skippedCount++;
            $reports[] = [
                'status' => 'SKIP',
                'nik' => $nik,
                'nama' => $namaLengkap,
                'username' => '-',
                'reason' => 'Nama panggilan kosong'
            ];
            continue;
        }

        // Generate username from nama panggilan
        $baseUsername = strtolower(str_replace([' ', '.', ',', '-'], '', $namaPanggilan));
        $baseUsername = preg_replace('/[^a-z0-9]/', '', $baseUsername); // Remove non-alphanumeric
        
        if (empty($baseUsername)) {
            echo "⚠️  SKIP: {$namaLengkap} ({$nik}) - Username tidak valid setelah diproses\n";
            $skippedCount++;
            $reports[] = [
                'status' => 'SKIP',
                'nik' => $nik,
                'nama' => $namaLengkap,
                'username' => '-',
                'reason' => 'Username tidak valid'
            ];
            continue;
        }

        // Check if username already exists and make it unique
        $username = $baseUsername;
        $counter = 1;
        while (User::where('username', $username)->exists()) {
            $username = $baseUsername . $counter;
            $counter++;
        }

        // Check if user already exists for this karyawan
        $existingUser = User::where('karyawan_id', $karyawan->id)
            ->orWhere('username', $username)
            ->first();

        if ($existingUser) {
            echo "⚠️  SKIP: {$namaLengkap} ({$nik}) - User sudah ada (Username: {$existingUser->username})\n";
            $skippedCount++;
            $reports[] = [
                'status' => 'SKIP',
                'nik' => $nik,
                'nama' => $namaLengkap,
                'username' => $existingUser->username,
                'reason' => 'User sudah ada'
            ];
            continue;
        }

        try {
            // Create user account
            $user = User::create([
                'username' => $username,
                'password' => Hash::make('password'),
                'role' => 'supir', // Set role as supir
                'is_approved' => true, // Auto approve
                'status' => 'active', // Set active status
                'karyawan_id' => $karyawan->id, // Link to karyawan
                'registration_reason' => 'Auto created for supir - ' . date('Y-m-d H:i:s'),
                'approved_by' => 1, // Assume admin user ID is 1
                'approved_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            echo "✅ SUCCESS: {$namaLengkap} ({$nik}) - Username: {$username}\n";
            $successCount++;
            $reports[] = [
                'status' => 'SUCCESS',
                'nik' => $nik,
                'nama' => $namaLengkap,
                'username' => $username,
                'reason' => 'Berhasil dibuat'
            ];

        } catch (\Exception $e) {
            echo "❌ ERROR: {$namaLengkap} ({$nik}) - {$e->getMessage()}\n";
            $errorCount++;
            $reports[] = [
                'status' => 'ERROR',
                'nik' => $nik,
                'nama' => $namaLengkap,
                'username' => $username,
                'reason' => $e->getMessage()
            ];
        }
    }

    DB::commit();

    // Summary Report
    echo "\n" . str_repeat("=", 60) . "\n";
    echo "📊 RINGKASAN PEMBUATAN AKUN USER SUPIR\n";
    echo str_repeat("=", 60) . "\n";
    echo "✅ Berhasil dibuat: {$successCount} akun\n";
    echo "⚠️  Dilewati: {$skippedCount} karyawan\n";
    echo "❌ Gagal: {$errorCount} akun\n";
    echo "📋 Total diproses: " . ($successCount + $skippedCount + $errorCount) . " karyawan\n\n";

    // Detailed Report
    if (!empty($reports)) {
        echo "📋 LAPORAN DETAIL:\n";
        echo str_repeat("-", 100) . "\n";
        printf("%-8s | %-15s | %-25s | %-15s | %-30s\n", 'STATUS', 'NIK', 'NAMA', 'USERNAME', 'KETERANGAN');
        echo str_repeat("-", 100) . "\n";

        foreach ($reports as $report) {
            $statusIcon = match($report['status']) {
                'SUCCESS' => '✅',
                'SKIP' => '⚠️ ',
                'ERROR' => '❌',
                default => '❓'
            };

            printf("%-8s | %-15s | %-25s | %-15s | %-30s\n", 
                $statusIcon . ' ' . $report['status'],
                substr($report['nik'], 0, 15),
                substr($report['nama'], 0, 25),
                substr($report['username'], 0, 15),
                substr($report['reason'], 0, 30)
            );
        }
        echo str_repeat("-", 100) . "\n";
    }

    // Default credentials info
    if ($successCount > 0) {
        echo "\n🔐 INFORMASI LOGIN:\n";
        echo "   Username: [nama_panggilan_karyawan]\n";
        echo "   Password: password\n";
        echo "   Role: supir\n";
        echo "   Status: active & approved\n\n";

        echo "📋 CONTOH LOGIN:\n";
        $successfulUser = collect($reports)->where('status', 'SUCCESS')->first();
        if ($successfulUser) {
            echo "   Username: {$successfulUser['username']}\n";
            echo "   Password: password\n";
        }

        echo "\n⚠️  CATATAN PENTING:\n";
        echo "   - Password default adalah 'password' untuk semua akun\n";
        echo "   - Role otomatis diset sebagai 'supir'\n";
        echo "   - Status otomatis active dan approved\n";
        echo "   - Akun terhubung dengan data karyawan melalui karyawan_id\n";
        echo "   - Username dibuat dari nama panggilan (lowercase, tanpa spasi)\n";
        echo "   - Jika username duplikat, akan ditambah angka di belakang\n";
        echo "   - Anjurkan supir untuk mengganti password setelah login pertama\n\n";
    }

    echo "🎉 Script selesai dijalankan!\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "💥 ERROR FATAL: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
    echo "🔄 Database rollback dilakukan.\n";
    exit(1);
}