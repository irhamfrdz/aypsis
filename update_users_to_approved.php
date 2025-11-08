<?php
/**
 * Script untuk mengubah status semua user menjadi 'approved'
 * Dibuat: 2025-11-07
 * 
 * Script ini akan:
 * 1. Menampilkan user dengan status tidak approved
 * 2. Mengubah semua user status menjadi 'approved'
 * 3. Menampilkan hasil verifikasi
 */

// Load Laravel
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== SCRIPT MENGUBAH STATUS USER MENJADI APPROVED ===\n";
echo "Tanggal: " . date('Y-m-d H:i:s') . "\n\n";

try {
    // 1. Cek struktur tabel users untuk memastikan kolom status
    echo "1. Memeriksa struktur tabel users...\n";
    $columns = DB::select("SHOW COLUMNS FROM users LIKE 'status'");
    
    if (empty($columns)) {
        echo "❌ Kolom 'status' tidak ditemukan di tabel users.\n";
        echo "Mencari kolom alternatif...\n";
        
        $allColumns = DB::select("SHOW COLUMNS FROM users");
        echo "Kolom yang tersedia:\n";
        foreach ($allColumns as $column) {
            echo "- " . $column->Field . " (" . $column->Type . ")\n";
        }
        exit(1);
    }
    
    echo "✅ Kolom 'status' ditemukan.\n\n";
    
    // 2. Tampilkan user yang belum approved
    echo "2. Mencari user yang belum approved...\n";
    $unapprovedUsers = DB::table('users')
        ->where(function($query) {
            $query->where('status', '!=', 'approved')
                  ->orWhere('is_approved', '!=', 1)
                  ->orWhereNull('status');
        })
        ->select('id', 'username', 'status', 'is_approved', 'approved_at', 'created_at')
        ->get();
    
    if ($unapprovedUsers->isEmpty()) {
        echo "✅ Semua user sudah berstatus 'approved'.\n";
        
        // Tampilkan semua user untuk konfirmasi
        $allUsers = DB::table('users')
            ->select('id', 'username', 'status', 'is_approved', 'approved_at', 'created_at')
            ->get();
        
        echo "\nDaftar semua user:\n";
        foreach ($allUsers as $user) {
            echo sprintf(
                "- ID: %d | Username: %s | Status: %s | Is Approved: %s | Approved At: %s | Created: %s\n",
                $user->id,
                $user->username,
                $user->status,
                $user->is_approved ? 'Yes' : 'No',
                $user->approved_at ?? 'NULL',
                $user->created_at ?? 'NULL'
            );
        }
        exit(0);
    }
    
    echo "Ditemukan " . $unapprovedUsers->count() . " user yang belum approved:\n";
    foreach ($unapprovedUsers as $user) {
        echo sprintf(
            "- ID: %d | Username: %s | Status: %s | Is Approved: %s | Approved At: %s | Created: %s\n",
            $user->id,
            $user->username,
            $user->status ?? 'NULL',
            $user->is_approved ? 'Yes' : 'No',
            $user->approved_at ?? 'NULL',
            $user->created_at ?? 'NULL'
        );
    }
    
    // 3. Konfirmasi sebelum update
    echo "\n3. Konfirmasi update status ke 'approved'? (y/n): ";
    $handle = fopen("php://stdin", "r");
    $confirmation = trim(fgets($handle));
    fclose($handle);
    
    if (strtolower($confirmation) !== 'y') {
        echo "❌ Operasi dibatalkan.\n";
        exit(0);
    }
    
    // 4. Update semua user menjadi approved
    echo "\n4. Mengupdate status user menjadi 'approved'...\n";
    
    $updatedCount = DB::table('users')
        ->where(function($query) {
            $query->where('status', '!=', 'approved')
                  ->orWhere('is_approved', '!=', 1)
                  ->orWhereNull('status');
        })
        ->update([
            'status' => 'approved',
            'is_approved' => 1,
            'approved_at' => now(),
            'approved_by' => 1, // Admin user ID
            'updated_at' => now()
        ]);
    
    echo "✅ Berhasil mengupdate {$updatedCount} user.\n\n";
    
    // 5. Verifikasi hasil
    echo "5. Verifikasi hasil update...\n";
    
    $stillUnapproved = DB::table('users')
        ->where(function($query) {
            $query->where('status', '!=', 'approved')
                  ->orWhere('is_approved', '!=', 1);
        })
        ->count();
    
    if ($stillUnapproved > 0) {
        echo "⚠️  Masih ada {$stillUnapproved} user yang belum approved.\n";
        
        $remaining = DB::table('users')
            ->where(function($query) {
                $query->where('status', '!=', 'approved')
                      ->orWhere('is_approved', '!=', 1);
            })
            ->select('id', 'username', 'status', 'is_approved')
            ->get();
        
        echo "User yang masih belum approved:\n";
        foreach ($remaining as $user) {
            echo "- ID: {$user->id} | Username: {$user->username} | Status: {$user->status} | Is Approved: " . ($user->is_approved ? 'Yes' : 'No') . "\n";
        }
    } else {
        echo "✅ Semua user sekarang berstatus 'approved'!\n";
    }
    
    // 6. Tampilkan ringkasan final
    echo "\n6. Ringkasan final:\n";
    
    $totalUsers = DB::table('users')->count();
    $approvedUsers = DB::table('users')->where('status', 'approved')->where('is_approved', 1)->count();
    $pendingUsers = DB::table('users')->where('status', 'pending')->count();
    $rejectedUsers = DB::table('users')->where('status', 'rejected')->count();
    $nullStatusUsers = DB::table('users')->whereNull('status')->count();
    $isApprovedFalse = DB::table('users')->where('is_approved', 0)->count();
    
    echo "Total user: {$totalUsers}\n";
    echo "Status approved (lengkap): {$approvedUsers}\n";
    echo "Status pending: {$pendingUsers}\n";
    echo "Status rejected: {$rejectedUsers}\n";
    echo "Status NULL: {$nullStatusUsers}\n";
    echo "Is_approved = 0: {$isApprovedFalse}\n";
    
    // 7. Tampilkan beberapa user terbaru untuk verifikasi
    echo "\n7. Beberapa user terbaru (untuk verifikasi):\n";
    
    $recentUsers = DB::table('users')
        ->select('id', 'username', 'status', 'is_approved', 'approved_at', 'updated_at')
        ->orderBy('updated_at', 'desc')
        ->limit(10)
        ->get();
    
    foreach ($recentUsers as $user) {
        echo sprintf(
            "✓ ID: %d | Username: %s | Status: %s | Is Approved: %s | Approved At: %s | Updated: %s\n",
            $user->id,
            $user->username,
            $user->status,
            $user->is_approved ? 'Yes' : 'No',
            $user->approved_at ?? 'NULL',
            $user->updated_at ?? 'NULL'
        );
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

echo "\n=== SCRIPT SELESAI ===\n";
echo "Semua user sekarang berstatus 'approved' dan dapat login ke sistem.\n";
echo "Waktu selesai: " . date('Y-m-d H:i:s') . "\n";