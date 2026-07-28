<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

if (!\Illuminate\Support\Facades\Schema::hasColumn('stock_amprahan_usages', 'adjustment_nilai_keluar')) {
    \Illuminate\Support\Facades\Schema::table('stock_amprahan_usages', function ($table) {
        $table->decimal('adjustment_nilai_keluar', 20, 2)->default(0);
    });
}

\App\Models\StockAmprahanUsage::where('id', 3541)->update(['adjustment_nilai_keluar' => 67305000]);

echo "Usage Adjustment Updated!";
