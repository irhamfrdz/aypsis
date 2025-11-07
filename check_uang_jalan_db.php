<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== PEMERIKSAAN DATABASE PEMBAYARAN PRANOTA UANG JALAN ===\n\n";

// 1. Cek tabel pembayaran_pranota_uang_jalans
echo "1. 📋 STRUKTUR TABEL PEMBAYARAN_PRANOTA_UANG_JALANS:\n";
if (Schema::hasTable('pembayaran_pranota_uang_jalans')) {
    echo "✅ Tabel pembayaran_pranota_uang_jalans ADA\n";
    
    $columns = DB::select("SHOW COLUMNS FROM pembayaran_pranota_uang_jalans");
    foreach ($columns as $column) {
        echo "   - {$column->Field} ({$column->Type}) " . 
             ($column->Null == 'YES' ? 'NULL' : 'NOT NULL') . 
             ($column->Key ? " [{$column->Key}]" : '') . "\n";
    }
} else {
    echo "❌ Tabel pembayaran_pranota_uang_jalans TIDAK ADA!\n";
}

echo "\n";

// 2. Cek tabel pranota_uang_jalans
echo "2. 📋 STRUKTUR TABEL PRANOTA_UANG_JALANS:\n";
if (Schema::hasTable('pranota_uang_jalans')) {
    echo "✅ Tabel pranota_uang_jalans ADA\n";
    
    $columns = DB::select("SHOW COLUMNS FROM pranota_uang_jalans");
    foreach ($columns as $column) {
        echo "   - {$column->Field} ({$column->Type}) " . 
             ($column->Null == 'YES' ? 'NULL' : 'NOT NULL') . 
             ($column->Key ? " [{$column->Key}]" : '') . "\n";
    }
} else {
    echo "❌ Tabel pranota_uang_jalans TIDAK ADA!\n";
}

echo "\n";

// 3. Cek tabel uang_jalans (detail items)
echo "3. 📋 STRUKTUR TABEL UANG_JALANS:\n";
if (Schema::hasTable('uang_jalans')) {
    echo "✅ Tabel uang_jalans ADA\n";
    
    $columns = DB::select("SHOW COLUMNS FROM uang_jalans");
    foreach ($columns as $column) {
        echo "   - {$column->Field} ({$column->Type}) " . 
             ($column->Null == 'YES' ? 'NULL' : 'NOT NULL') . 
             ($column->Key ? " [{$column->Key}]" : '') . "\n";
    }
} else {
    echo "❌ Tabel uang_jalans TIDAK ADA!\n";
}

echo "\n";

// 4. Cek tabel coa_transactions (untuk double book accounting)
echo "4. 📋 STRUKTUR TABEL COA_TRANSACTIONS:\n";
if (Schema::hasTable('coa_transactions')) {
    echo "✅ Tabel coa_transactions ADA\n";
    
    $columns = DB::select("SHOW COLUMNS FROM coa_transactions");
    foreach ($columns as $column) {
        echo "   - {$column->Field} ({$column->Type}) " . 
             ($column->Null == 'YES' ? 'NULL' : 'NOT NULL') . 
             ($column->Key ? " [{$column->Key}]" : '') . "\n";
    }
} else {
    echo "❌ Tabel coa_transactions TIDAK ADA!\n";
}

echo "\n";

// 5. Cek akun COA yang diperlukan
echo "5. 🏦 CEK AKUN COA YANG DIPERLUKAN:\n";
try {
    // Akun Biaya Uang Jalan Muat
    $biayaUangJalan = DB::table('akun_coa')->where('nama_akun', 'Biaya Uang Jalan Muat')->first();
    if ($biayaUangJalan) {
        echo "✅ Biaya Uang Jalan Muat: {$biayaUangJalan->nama_akun} (Saldo: Rp " . number_format($biayaUangJalan->saldo, 0, ',', '.') . ")\n";
        echo "   Tipe: {$biayaUangJalan->tipe_akun}, Kode: {$biayaUangJalan->kode_nomor}\n";
    } else {
        echo "❌ Akun 'Biaya Uang Jalan Muat' TIDAK DITEMUKAN!\n";
    }
    
    // Akun Bank/Kas
    $bankAccounts = DB::table('akun_coa')
        ->where(function($query) {
            $query->where('nama_akun', 'LIKE', '%kas%')
                  ->orWhere('nama_akun', 'LIKE', '%bank%')
                  ->orWhere('nama_akun', 'LIKE', '%BCA%')
                  ->orWhere('nama_akun', 'LIKE', '%BRI%')
                  ->orWhere('nama_akun', 'LIKE', '%Mandiri%');
        })
        ->get();
    
    echo "\n📱 Akun Bank/Kas yang tersedia (" . $bankAccounts->count() . " akun):\n";
    foreach ($bankAccounts->take(5) as $bank) {
        echo "   ✅ {$bank->nama_akun} (Saldo: Rp " . number_format($bank->saldo, 0, ',', '.') . ")\n";
    }
    if ($bankAccounts->count() > 5) {
        echo "   ... dan " . ($bankAccounts->count() - 5) . " akun lainnya\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Error saat cek COA: " . $e->getMessage() . "\n";
}

echo "\n";

// 6. Cek sample data pranota uang jalan
echo "6. 📊 SAMPLE DATA PRANOTA UANG JALAN:\n";
try {
    $samplePranota = DB::table('pranota_uang_jalans')
        ->select('id', 'nomor_pranota', 'tanggal_pranota', 'status_pembayaran')
        ->orderBy('id', 'desc')
        ->limit(3)
        ->get();
    
    if ($samplePranota->count() > 0) {
        echo "✅ Found " . $samplePranota->count() . " sample pranota uang jalan:\n";
        foreach ($samplePranota as $pranota) {
            echo "   - ID: {$pranota->id}, Nomor: {$pranota->nomor_pranota}, Status: " . ($pranota->status_pembayaran ?? 'Belum Bayar') . "\n";
        }
    } else {
        echo "⚠️  Belum ada data pranota uang jalan\n";
    }
    
    // Count total pranota
    $totalPranota = DB::table('pranota_uang_jalans')->count();
    echo "📈 Total pranota uang jalan: {$totalPranota} records\n";
    
} catch (\Exception $e) {
    echo "❌ Error saat cek pranota: " . $e->getMessage() . "\n";
}

echo "\n";

// 7. Cek sample data uang jalan (detail items)
echo "7. 📋 SAMPLE DATA UANG JALAN (DETAIL):\n";
try {
    $sampleUangJalan = DB::table('uang_jalans')
        ->select('id', 'pranota_uang_jalan_id', 'supir', 'dari', 'tujuan', 'nominal')
        ->orderBy('id', 'desc')
        ->limit(3)
        ->get();
    
    if ($sampleUangJalan->count() > 0) {
        echo "✅ Found " . $sampleUangJalan->count() . " sample uang jalan items:\n";
        foreach ($sampleUangJalan as $item) {
            $nominal = $item->nominal ?? 0;
            echo "   - ID: {$item->id}, Supir: {$item->supir}, Rute: {$item->dari}-{$item->tujuan}, Nominal: Rp " . number_format($nominal, 0, ',', '.') . "\n";
        }
    } else {
        echo "⚠️  Belum ada data uang jalan detail\n";
    }
    
    // Count total uang jalan items
    $totalUangJalan = DB::table('uang_jalans')->count();
    echo "📈 Total uang jalan items: {$totalUangJalan} records\n";
    
} catch (\Exception $e) {
    echo "❌ Error saat cek uang jalan: " . $e->getMessage() . "\n";
}

echo "\n";

// 8. Cek pembayaran yang sudah ada
echo "8. 💰 SAMPLE DATA PEMBAYARAN UANG JALAN:\n";
try {
    $samplePembayaran = DB::table('pembayaran_pranota_uang_jalans')
        ->select('id', 'nomor_pembayaran', 'tanggal_pembayaran', 'total_pembayaran', 'bank', 'jenis_transaksi')
        ->orderBy('id', 'desc')
        ->limit(3)
        ->get();
    
    if ($samplePembayaran->count() > 0) {
        echo "✅ Found " . $samplePembayaran->count() . " sample pembayaran:\n";
        foreach ($samplePembayaran as $pembayaran) {
            $total = $pembayaran->total_pembayaran ?? 0;
            echo "   - Nomor: {$pembayaran->nomor_pembayaran}, Total: Rp " . number_format($total, 0, ',', '.') . ", Bank: {$pembayaran->bank}\n";
        }
    } else {
        echo "⚠️  Belum ada data pembayaran uang jalan\n";
    }
    
    // Count total pembayaran
    $totalPembayaran = DB::table('pembayaran_pranota_uang_jalans')->count();
    echo "📈 Total pembayaran uang jalan: {$totalPembayaran} records\n";
    
} catch (\Exception $e) {
    echo "❌ Error saat cek pembayaran: " . $e->getMessage() . "\n";
}

echo "\n";

// 9. Cek COA Transactions (journal entries)
echo "9. 📊 COA TRANSACTIONS (DOUBLE BOOK ENTRIES):\n";
try {
    $coaTransactions = DB::table('coa_transactions')
        ->where('keterangan', 'LIKE', '%uang jalan%')
        ->orWhere('referensi', 'LIKE', '%PPT%')
        ->orderBy('id', 'desc')
        ->limit(5)
        ->get();
    
    if ($coaTransactions->count() > 0) {
        echo "✅ Found " . $coaTransactions->count() . " COA transactions terkait uang jalan:\n";
        foreach ($coaTransactions as $trans) {
            $jumlah = $trans->jumlah ?? 0;
            echo "   - Ref: {$trans->referensi}, Akun: {$trans->nama_akun}, Jumlah: Rp " . number_format($jumlah, 0, ',', '.') . " ({$trans->jenis_transaksi})\n";
        }
    } else {
        echo "⚠️  Belum ada COA transactions untuk uang jalan\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Error saat cek COA transactions: " . $e->getMessage() . "\n";
}

echo "\n=== RINGKASAN STATUS DATABASE ===\n";

$status = [];

// Check tabel utama
$status['tabel_pembayaran'] = Schema::hasTable('pembayaran_pranota_uang_jalans') ? '✅' : '❌';
$status['tabel_pranota'] = Schema::hasTable('pranota_uang_jalans') ? '✅' : '❌';
$status['tabel_uang_jalan'] = Schema::hasTable('uang_jalans') ? '✅' : '❌';
$status['tabel_coa_trans'] = Schema::hasTable('coa_transactions') ? '✅' : '❌';

// Check akun COA
try {
    $biayaExists = DB::table('akun_coa')->where('nama_akun', 'Biaya Uang Jalan Muat')->exists();
    $status['akun_biaya'] = $biayaExists ? '✅' : '❌';
    
    $bankExists = DB::table('akun_coa')->where('nama_akun', 'LIKE', '%kas%')->exists();
    $status['akun_bank'] = $bankExists ? '✅' : '❌';
} catch (\Exception $e) {
    $status['akun_biaya'] = '❌';
    $status['akun_bank'] = '❌';
}

echo "📋 Tabel Pembayaran Pranota Uang Jalan: {$status['tabel_pembayaran']}\n";
echo "📋 Tabel Pranota Uang Jalan: {$status['tabel_pranota']}\n";
echo "📋 Tabel Uang Jalan (Detail): {$status['tabel_uang_jalan']}\n";
echo "📋 Tabel COA Transactions: {$status['tabel_coa_trans']}\n";
echo "🏦 Akun Biaya Uang Jalan Muat: {$status['akun_biaya']}\n";
echo "🏦 Akun Bank/Kas: {$status['akun_bank']}\n";

$allGood = !in_array('❌', $status);
echo "\n" . ($allGood ? "🎉 DATABASE SIAP UNTUK DOUBLE BOOK ACCOUNTING!" : "⚠️  PERLU PERBAIKAN DATABASE") . "\n";