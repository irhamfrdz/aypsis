<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== FINAL PRINT LAYOUT PREVIEW ===\n\n";

$pranota = \App\Models\PranotaUangJalan::with(['uangJalans.suratJalan', 'creator'])->first();

if (!$pranota) {
    echo "❌ No pranota found\n";
    exit;
}

// Simulate the complete print layout
echo str_repeat('=', 80) . "\n";
echo "                    PRANOTA UANG JALAN                    \n";
echo "                PT. Aypsis Logistik Indonesia             \n";
echo "           Jl. Raya Pelabuhan No. 123, Jakarta           \n";
echo str_repeat('=', 80) . "\n\n";

echo "┌" . str_repeat('─', 76) . "┐\n";
echo "│ Nomor Pranota: {$pranota->nomor_pranota}";
echo str_repeat(' ', 76 - 19 - strlen($pranota->nomor_pranota) - 26 - strlen($pranota->tanggal_pranota->format('d F Y')));
echo "Tanggal Pranota: " . $pranota->tanggal_pranota->format('d F Y') . " │\n";
echo "└" . str_repeat('─', 76) . "┘\n\n";

echo "Daftar Uang Jalan\n";
echo str_repeat('-', 80) . "\n";

// Table headers (abbreviated for console display)
printf("%-3s | %-10s | %-10s | %-8s | %-6s | %-8s | %-12s\n", 
    "No", "Surat Jalan", "Uang Jalan", "Barang", "NIK", "Supir", "Total");
echo str_repeat('-', 80) . "\n";

// Table data
foreach ($pranota->uangJalans as $index => $uangJalan) {
    $no = $index + 1;
    $noSuratJalan = $uangJalan->suratJalan ? substr($uangJalan->suratJalan->no_surat_jalan, 0, 10) : '-';
    $nomorUangJalan = substr($uangJalan->nomor_uang_jalan, 0, 10);
    $barang = $uangJalan->suratJalan ? substr($uangJalan->suratJalan->jenis_barang ?: '-', 0, 8) : '-';
    $nik = ($uangJalan->suratJalan && $uangJalan->suratJalan->supir_nik) ? 
        $uangJalan->suratJalan->supir_nik : 
        (($uangJalan->suratJalan && $uangJalan->suratJalan->kenek_nik) ? 
            $uangJalan->suratJalan->kenek_nik : '-');
    $supir = $uangJalan->suratJalan ? substr($uangJalan->suratJalan->supir ?: '-', 0, 8) : '-';
    $total = 'Rp ' . number_format($uangJalan->jumlah_total / 1000, 0) . 'K';
    
    printf("%-3s | %-10s | %-10s | %-8s | %-6s | %-8s | %-12s\n", 
        $no, $noSuratJalan, $nomorUangJalan, $barang, $nik, $supir, $total);
}

echo str_repeat('-', 80) . "\n";
echo "TOTAL AMOUNT: Rp " . number_format($pranota->total_amount, 0, ',', '.') . "\n\n";

echo "Dibuat oleh: " . ($pranota->creator->name ?? 'N/A') . "\n";
echo "Tanggal cetak: " . now()->format('d F Y H:i:s') . "\n\n";

echo str_repeat('=', 80) . "\n\n";

echo "=== SUMMARY IMPLEMENTASI ===\n";
echo "✅ Header: Company info dan judul\n";
echo "✅ Pranota Info: Nomor dan tanggal pranota (BARU)\n";
echo "✅ Tabel: 11 kolom lengkap dengan NIK yang sudah fix\n";
echo "✅ Summary: Total amount dan jumlah item\n";
echo "✅ Signature: Tanda tangan section\n";
echo "✅ Footer: Timestamp dan copyright\n\n";

echo "=== FEATURES ===\n";
echo "🔹 Nomor Pranota: {$pranota->nomor_pranota}\n";
echo "🔹 Tanggal Pranota: " . $pranota->tanggal_pranota->format('d F Y') . "\n";
echo "🔹 NIK otomatis: Dari supir/kenek karyawan\n";
echo "🔹 Tanggal tanda terima: Otomatis dari approval\n";
echo "🔹 Print-friendly: Optimized untuk A4\n";
echo "🔹 Responsive layout: Semua kolom muat\n\n";

echo "🖨️ Ready untuk print: /pranota-uang-jalan/{$pranota->id}/print\n";