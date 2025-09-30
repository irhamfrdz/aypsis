<?php
require "vendor/autoload.php";
$app = require "bootstrap/app.php";
$app->make("Illuminate\Contracts\Console\Kernel")->bootstrap();

try {
    // Get first unpaid pranota
    $pranota = \App\Models\PranotaTagihanKontainerSewa::where('status', '!=', 'paid')
        ->where('status', '!=', 'cancelled')
        ->first();

    if (!$pranota) {
        echo "No unpaid pranota found\n";
        exit;
    }

    echo "Testing payment for pranota: {$pranota->no_invoice}\n";

    // Simulate complete form data
    $paymentData = [
        'nomor_pembayaran' => 'BPK-1-25-09-000001',
        'bank' => 'Bank Test',
        'jenis_transaksi' => 'Debit',
        'tanggal_kas' => now()->format('d/m/Y'), // Format dari view
        'pranota_ids' => [$pranota->id],
        'total_tagihan_penyesuaian' => 0,
        'alasan_penyesuaian' => 'Test payment',
        'keterangan' => 'Test payment description'
    ];

    // Create controller instance and call store method
    $controller = new \App\Http\Controllers\PembayaranPranotaKontainerController();
    $request = new \Illuminate\Http\Request();
    $request->merge($paymentData);

    // Mock authentication
    \Illuminate\Support\Facades\Auth::shouldReceive('id')->andReturn(1);

    echo "Attempting to save payment...\n";

    // Call store method
    $response = $controller->store($request);

    echo "Payment saved successfully!\n";

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}
