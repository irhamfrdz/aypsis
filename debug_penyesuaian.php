<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== SEMUA SURAT JALAN AKAN DITAMPILKAN ===\n";
echo "Total surat jalan: " . App\Models\SuratJalan::count() . "\n";
echo "With order_id (yang akan ditampilkan): " . App\Models\SuratJalan::whereNotNull('order_id')->count() . "\n";

echo "\n=== Surat Jalan Bongkaran ===\n";
echo "Total surat jalan bongkaran (semua akan ditampilkan): " . App\Models\SuratJalanBongkaran::count() . "\n";

echo "\n=== Total Yang Akan Ditampilkan ===\n";
$totalRegular = App\Models\SuratJalan::whereNotNull('order_id')->count();
$totalBongkaran = App\Models\SuratJalanBongkaran::count();
echo "Total surat jalan regular: " . $totalRegular . "\n";
echo "Total surat jalan bongkaran: " . $totalBongkaran . "\n";
echo "Total keseluruhan: " . ($totalRegular + $totalBongkaran) . "\n";

echo "\n=== Status Pembayaran Uang Jalan (untuk info) ===\n";
$statuses = App\Models\SuratJalan::select('status_pembayaran_uang_jalan')->distinct()->get()->pluck('status_pembayaran_uang_jalan');
foreach ($statuses as $status) {
    $count = App\Models\SuratJalan::where('status_pembayaran_uang_jalan', $status)->count();
    echo "Status '$status': $count records\n";
}