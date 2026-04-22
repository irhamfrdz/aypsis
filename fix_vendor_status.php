<?php

use App\Models\PranotaInvoiceVendorSupir;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Memulai sinkronisasi status (cascade) untuk Pranota Invoice Vendor Supir...\n";
DB::beginTransaction();

try {
    $pranotas = PranotaInvoiceVendorSupir::with('invoiceTagihanVendors.tagihanSupirVendors')->get();
    $count = 0;

    foreach ($pranotas as $pranota) {
        $status = $pranota->status_pembayaran;
        
        if ($pranota->invoiceTagihanVendors) {
            foreach ($pranota->invoiceTagihanVendors as $invoice) {
                if ($invoice->status_pembayaran !== $status) {
                    $invoice->status_pembayaran = $status;
                    $invoice->save();
                    $count++;
                }

                if ($invoice->tagihanSupirVendors) {
                    foreach ($invoice->tagihanSupirVendors as $tagihan) {
                        if ($tagihan->status_pembayaran !== $status) {
                            $tagihan->status_pembayaran = $status;
                            $tagihan->save();
                            $count++;
                        }
                    }
                }
            }
        }
    }

    DB::commit();
    echo "Selesai! Berhasil memperbaiki status pada $count record Tagihan/Invoice.\n";
} catch (\Exception $e) {
    DB::rollBack();
    echo "Terjadi kesalahan: " . $e->getMessage() . "\n";
}
