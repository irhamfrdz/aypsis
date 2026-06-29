<?php
$lines = file('app/Http/Controllers/ReportOngkosTrukController.php');
foreach ($lines as $num => $line) {
    if (strpos($line, 'InvoiceAktivitasLain::') !== false || strpos($line, '$adjInvoices') !== false) {
        echo ($num + 1) . ": " . trim($line) . "\n";
    }
}
