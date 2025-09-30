<?php

// Test script to simulate form submission for pembayaran pranota CAT
echo "Testing form submission simulation..." . PHP_EOL;

try {
    // Include Laravel bootstrap
    require_once 'vendor/autoload.php';
    $app = require_once 'bootstrap/app.php';
    $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

    // Get unpaid pranota
    $pranota = \App\Models\PranotaTagihanCat::where('status', 'unpaid')->first();

    if (!$pranota) {
        echo "No unpaid pranota found!" . PHP_EOL;
        exit;
    }

    echo "Found pranota: {$pranota->no_invoice} (ID: {$pranota->id})" . PHP_EOL;

    // Simulate form request data
    $requestData = [
        'nomor_pembayaran' => 'FORM-TEST-' . time(),
        'bank' => 'Bank BCA',
        'jenis_transaksi' => 'debit',
        'tanggal_kas' => now()->toDateString(), // Y-m-d format
        'pranota_ids' => [$pranota->id],
        'total_tagihan_penyesuaian' => 0,
        'alasan_penyesuaian' => null,
        'keterangan' => 'Test pembayaran via form'
    ];

    echo "Request data: " . json_encode($requestData, JSON_PRETTY_PRINT) . PHP_EOL;

    // Create Request object
    $request = new \Illuminate\Http\Request();
    $request->merge($requestData);

    // Test validation
    $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
        'nomor_pembayaran' => 'required|string|unique:pembayaran_pranota_cat',
        'bank' => 'required|string|max:255',
        'jenis_transaksi' => 'required|in:debit,credit',
        'tanggal_kas' => 'required|date',
        'pranota_ids' => 'required|array|min:1',
        'pranota_ids.*' => 'exists:pranota_tagihan_cat,id',
        'total_tagihan_penyesuaian' => 'nullable|numeric',
        'alasan_penyesuaian' => 'nullable|string',
        'keterangan' => 'nullable|string'
    ]);

    if ($validator->fails()) {
        echo "VALIDATION FAILED: " . json_encode($validator->errors()->all()) . PHP_EOL;
        exit;
    }

    echo "Validation passed!" . PHP_EOL;

    // Simulate controller logic
    \Illuminate\Support\Facades\DB::beginTransaction();

    $pranotaIds = $request->input('pranota_ids');
    $penyesuaian = floatval($request->input('total_tagihan_penyesuaian', 0));

    $pranotas = \App\Models\PranotaTagihanCat::whereIn('id', $pranotaIds)->get();

    foreach ($pranotas as $p) {
        if ($p->status !== 'unpaid') {
            throw new \Exception("Pranota {$p->no_invoice} sudah dibayar");
        }
    }

    $totalPembayaran = $pranotas->sum('total_amount');

    // Create pembayaran record
    $pembayaran = \App\Models\PembayaranPranotaCat::create([
        'nomor_pembayaran' => $request->nomor_pembayaran,
        'bank' => $request->bank,
        'jenis_transaksi' => $request->jenis_transaksi,
        'tanggal_kas' => $request->tanggal_kas,
        'total_pembayaran' => $totalPembayaran,
        'penyesuaian' => $penyesuaian,
        'total_setelah_penyesuaian' => $totalPembayaran + $penyesuaian,
        'alasan_penyesuaian' => $request->alasan_penyesuaian,
        'keterangan' => $request->keterangan,
        'status' => 'approved'
    ]);

    echo "Pembayaran created with ID: {$pembayaran->id}" . PHP_EOL;

    // Create payment items
    foreach ($pranotas as $p) {
        \App\Models\PembayaranPranotaCatItem::create([
            'pembayaran_pranota_cat_id' => $pembayaran->id,
            'pranota_tagihan_cat_id' => $p->id,
            'amount' => $p->total_amount
        ]);

        $p->update(['status' => 'paid']);
    }

    \Illuminate\Support\Facades\DB::commit();
    echo "SUCCESS: Form simulation completed!" . PHP_EOL;

} catch (Exception $e) {
    \Illuminate\Support\Facades\DB::rollback();
    echo "ERROR: " . $e->getMessage() . PHP_EOL;
    echo "File: " . $e->getFile() . ":" . $e->getLine() . PHP_EOL;
}
