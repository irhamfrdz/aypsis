<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$stockId = 3788;
$usages = App\Models\StockAmprahanUsage::where('stock_amprahan_id', $stockId)->orderBy('created_at', 'desc')->get();

$total = $usages->sum('jumlah');

if ($total > 58) {
    $diff = $total - 58;
    // Kurangi dari usage terakhir (terbaru)
    $lastUsage = $usages->first();
    if ($lastUsage->jumlah >= $diff) {
        $lastUsage->jumlah -= $diff;
        $lastUsage->save();
        echo "Fixed usages sum to 58. Deducted $diff from usage ID {$lastUsage->id}.\n";
    } else {
        echo "Cannot deduct $diff from a single usage.\n";
    }
} else if ($total < 58) {
    echo "Total is less than 58 ($total). Needs manual checking.\n";
} else {
    echo "Total usages is already 58.\n";
}
