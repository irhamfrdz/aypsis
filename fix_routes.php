<?php
$path = __DIR__ . '/routes/web.php';
$content = file_get_contents($path);

$old = "Route::put('stock-ban/{stock_ban}/return-to-shop', [\\App\\Http\\Controllers\\StockBanController::class, 'returnToShop'])";

$pos = strpos($content, $old);

if ($pos === false) {
    echo "Could not find return-to-shop route line.\n";

    // Try to find alternate form
    $pos2 = strpos($content, 'return-to-shop');
    if ($pos2 !== false) {
        echo "Found 'return-to-shop' at position $pos2\n";
        echo "Context: " . substr($content, $pos2 - 50, 200) . "\n";
    }
    exit(1);
}

echo "Found at position $pos\n";

// Check if restore-to-stock already exists
if (strpos($content, 'restore-to-stock') !== false) {
    echo "restore-to-stock route already exists!\n";
    exit(0);
}

// Build the new block
$newRoute = "\n           Route::put('stock-ban/{stock_ban}/restore-to-stock', [\\App\\Http\\Controllers\\StockBanController::class, 'restoreToStock'])\n                ->name('stock-ban.restore-to-stock')\n                ->middleware('can:stock-ban-update');";

// Find the end of return-to-shop block (after its middleware line)
$afterOld = strpos($content, "->middleware('can:stock-ban-update');", $pos);
if ($afterOld === false) {
    echo "Could not find end of return-to-shop block.\n";
    exit(1);
}
$insertAt = $afterOld + strlen("->middleware('can:stock-ban-update');");

$content = substr($content, 0, $insertAt) . $newRoute . substr($content, $insertAt);

file_put_contents($path, $content);
echo "Done! restore-to-stock route added.\n";
