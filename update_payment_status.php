<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Updating Payment Status ===\n";

// Find the payment
$payment = DB::table('pembayaran_pranota_kontainer')
    ->where('nomor_pembayaran', 'BTK12509000001')
    ->first();

if ($payment) {
    echo "Payment found: {$payment->nomor_pembayaran}\n";
    echo "Current status: {$payment->status}\n";
    echo "Total pembayaran: {$payment->total_pembayaran}\n";
    echo "Total setelah penyesuaian: {$payment->total_tagihan_setelah_penyesuaian}\n";

    if ($payment->status === 'pending') {
        // Update status to approved
        DB::table('pembayaran_pranota_kontainer')
            ->where('id', $payment->id)
            ->update([
                'status' => 'approved',
                'disetujui_oleh' => 1, // assuming admin user ID 1
                'tanggal_persetujuan' => now()
            ]);

        // Update related pranota status to paid
        $items = DB::table('pembayaran_pranota_kontainer_items')
            ->where('pembayaran_pranota_kontainer_id', $payment->id)
            ->get();

        foreach ($items as $item) {
            DB::table('pranotalist')
                ->where('id', $item->pranota_id)
                ->update(['status' => 'paid']);
        }

        echo "\n✅ Payment status updated to 'approved'\n";
        echo "✅ Related pranota status updated to 'paid'\n";
    } else {
        echo "\n⚠️ Payment status is already: {$payment->status}\n";
    }
} else {
    echo "❌ Payment not found!\n";
}
