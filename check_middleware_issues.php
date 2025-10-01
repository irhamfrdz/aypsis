<?php

// Script untuk mengecek masalah middleware yang mungkin memblokir user admin

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

echo "=== CEK MASALAH MIDDLEWARE USER ADMIN ===\n\n";

$admin = User::where('username', 'admin')->with('karyawan')->first();

if (!$admin) {
    echo "❌ User admin tidak ditemukan!\n";
    exit;
}

echo "✅ User admin ditemukan: {$admin->username} (ID: {$admin->id})\n\n";

// 1. Cek EnsureKaryawanPresent
echo "=== 1. CEK MIDDLEWARE EnsureKaryawanPresent ===\n";
echo "karyawan_id: " . ($admin->karyawan_id ?? 'NULL') . "\n";

if (empty($admin->karyawan_id)) {
    echo "❌ MASALAH DITEMUKAN: User admin tidak memiliki karyawan_id!\n";
    echo "🔧 SOLUSI: Buat atau hubungkan karyawan dengan user admin\n";

    // Cek apakah ada karyawan yang bisa dihubungkan
    $karyawan = \App\Models\Karyawan::where('nama_lengkap', 'LIKE', '%admin%')
                                   ->orWhere('nama_lengkap', 'LIKE', '%administrator%')
                                   ->first();
    if ($karyawan) {
        echo "📋 Karyawan yang bisa dihubungkan: {$karyawan->nama_lengkap} (ID: {$karyawan->id})\n";
    } else {
        echo "📋 Tidak ada karyawan yang cocok. Perlu buat karyawan baru.\n";
    }
} else {
    echo "✅ User admin memiliki karyawan_id: {$admin->karyawan_id}\n";

    if ($admin->karyawan) {
        echo "✅ Karyawan terhubung: {$admin->karyawan->nama_lengkap}\n";
    } else {
        echo "❌ Karyawan dengan ID {$admin->karyawan_id} tidak ditemukan!\n";
    }
}
echo "\n";

// 2. Cek EnsureUserApproved
echo "=== 2. CEK MIDDLEWARE EnsureUserApproved ===\n";
echo "Status user: " . ($admin->status ?? 'NULL') . "\n";

if ($admin->status !== 'approved') {
    echo "❌ MASALAH DITEMUKAN: User admin status bukan 'approved'!\n";
    echo "🔧 SOLUSI: Update status user admin menjadi 'approved'\n";
} else {
    echo "✅ Status user admin sudah 'approved'\n";
}
echo "\n";

// 3. Cek EnsureCrewChecklistComplete
echo "=== 3. CEK MIDDLEWARE EnsureCrewChecklistComplete ===\n";
if ($admin->karyawan) {
    $isAbk = method_exists($admin->karyawan, 'isAbk') ? $admin->karyawan->isAbk() : false;
    echo "Apakah karyawan ABK: " . ($isAbk ? 'YA' : 'TIDAK') . "\n";

    if ($isAbk) {
        echo "ℹ️  Middleware ini akan membuat checklist default jika belum ada\n";
        echo "   Tidak memblokir akses, hanya memastikan checklist ada\n";
    } else {
        echo "✅ Karyawan bukan ABK, middleware ini tidak berpengaruh\n";
    }
} else {
    echo "⚠️  Tidak bisa cek karena karyawan tidak terhubung\n";
}
echo "\n";

// 4. Berikan solusi perbaikan
echo "=== SOLUSI PERBAIKAN ===\n";

$needsFix = false;

if (empty($admin->karyawan_id) || !$admin->karyawan) {
    echo "🔧 PERBAIKAN 1: Hubungkan karyawan dengan user admin\n";
    $needsFix = true;
}

if ($admin->status !== 'approved') {
    echo "🔧 PERBAIKAN 2: Update status user admin menjadi 'approved'\n";
    $needsFix = true;
}

if (!$needsFix) {
    echo "✅ Tidak ada masalah middleware yang ditemukan\n";
    echo "💡 Kemungkinan masalah lain:\n";
    echo "   - Cache browser perlu dibersihkan\n";
    echo "   - Session Laravel perlu di-refresh (logout/login)\n";
    echo "   - Ada error di log aplikasi\n";
    echo "   - JavaScript error di browser console\n";
} else {
    echo "\n📋 Script perbaikan akan dibuat...\n";
}

echo "\n=== INFORMASI DEBUG TAMBAHAN ===\n";
echo "User created_at: " . ($admin->created_at ?? 'NULL') . "\n";
echo "User updated_at: " . ($admin->updated_at ?? 'NULL') . "\n";
echo "User approved_at: " . ($admin->approved_at ?? 'NULL') . "\n";
echo "User approved_by: " . ($admin->approved_by ?? 'NULL') . "\n";
