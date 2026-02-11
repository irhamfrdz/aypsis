<?php

// Test script untuk mengecek apakah ada order dengan data penerima
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Order;

try {
    echo "Checking orders with penerima data...\n";
    
    $orders = Order::where(function($q) {
        $q->whereNotNull('penerima')
          ->orWhereNotNull('penerima_id')
          ->orWhereNotNull('alamat_penerima');
    })
    ->with(['suratJalans.tandaTerima', 'penerima'])
    ->limit(5)
    ->get();
    
    echo "Found " . $orders->count() . " orders\n\n";
    
    foreach ($orders as $order) {
        echo "Order: " . $order->nomor_order . "\n";
        echo "  - Penerima ID: " . ($order->penerima_id ?? 'null') . "\n";
        echo "  - Penerima (attr): " . ($order->getAttributeValue('penerima') ?? 'null') . "\n";
        echo "  - Alamat: " . ($order->alamat_penerima ?? 'null') . "\n";
        echo "  - Surat Jalans: " . $order->suratJalans->count() . "\n";
        
        foreach ($order->suratJalans as $sj) {
            echo "    - SJ: " . $sj->no_surat_jalan;
            if ($sj->tandaTerima) {
                echo " => Tanda Terima ID: " . $sj->tandaTerima->id . "\n";
            } else {
                echo " => No Tanda Terima\n";
            }
        }
        echo "\n";
    }
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
