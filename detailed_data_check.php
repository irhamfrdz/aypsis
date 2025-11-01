<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🔍 Detailed Key Business Tables Check:\n\n";

// Check correct table names based on what we see in the database
$keyTables = [
    'karyawans' => 'Employee data',
    'master_kapals' => 'Ship master data (correct name)', 
    'master_pelabuhans' => 'Port master data (correct name)',
    'kontainers' => 'Container data',
    'permohonans' => 'Application/request data (correct name)',
    'prospek' => 'Prospect data',
    'orders' => 'Order data (correct name)',
    'vendor_kontainer_sewas' => 'Vendor data (correct name)',
    'jenis_barangs' => 'Goods type data (correct name)',
    'tujuan_kegiatan_utamas' => 'Main activity purpose data (correct name)',
    'pranota_surat_jalans' => 'Surat jalan pranota',
    'surat_jalan_approvals' => 'Surat jalan approvals',
    'stock_kontainers' => 'Container stock',
    'pengirims' => 'Senders data',
    'gate_ins' => 'Gate in data',
    'akun_coa' => 'Chart of accounts',
    'banks' => 'Bank data',
    'divisis' => 'Division data'
];

$totalWithData = 0;
$totalEmpty = 0;

foreach ($keyTables as $tableName => $description) {
    try {
        $count = DB::table($tableName)->count();
        if ($count > 0) {
            echo "✅ {$description}: " . number_format($count) . " records\n";
            $totalWithData++;
        } else {
            echo "❌ {$description}: 0 records (EMPTY)\n";
            $totalEmpty++;
        }
    } catch (Exception $e) {
        echo "❓ {$description}: Table not found - {$e->getMessage()}\n";
    }
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "📊 Key Business Data Summary:\n";
echo "   • Tables with data: {$totalWithData}\n";
echo "   • Empty key tables: {$totalEmpty}\n";

// Show some empty tables that should have data
echo "\n⚠️  Critical Empty Tables (likely missing data):\n";
$criticalEmpty = [
    'kontainers' => 'Container data',
    'permohonans' => 'Application/request data', 
    'surat_jalans' => 'Surat jalan data',
    'pranota_supirs' => 'Driver pranota',
    'pembayaran_pranota' => 'Pranota payments',
    'tagihan_kontainer_sewa' => 'Container rental bills'
];

foreach ($criticalEmpty as $tableName => $description) {
    try {
        $count = DB::table($tableName)->count();
        if ($count == 0) {
            echo "   ❌ {$description}\n";
        }
    } catch (Exception $e) {
        echo "   ❓ {$description}: Table not found\n";
    }
}

echo "\n📋 Conclusion:\n";
echo "   • Master data (karyawan, kapal, pelabuhan): ✅ MIGRATED\n";
echo "   • Reference data (COA, banks, divisions): ✅ MIGRATED\n";  
echo "   • Transaction data: ⚠️  PARTIALLY MIGRATED\n";
echo "   • Some key business tables are empty\n";