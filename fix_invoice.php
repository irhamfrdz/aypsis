<?php

use App\Models\InvoiceAktivitasLain;

echo "Memulai proses ubah status invoice...\n";

$invoices = [
    'IAL-09-26-000050',
    'IAL-09-26-000051',
    'IAL-09-26-000052',
];

$updated = InvoiceAktivitasLain::whereIn('nomor_invoice', $invoices)
    ->update(['status' => 'draft']);

echo "Berhasil mengubah status {$updated} invoice menjadi draft.\n";
