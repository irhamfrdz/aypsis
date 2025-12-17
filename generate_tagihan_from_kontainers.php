<?php

require "vendor/autoload.php";

$app = require "bootstrap/app.php";
$app->make("Illuminate\Contracts\Console\Kernel")->bootstrap();

use App\Models\Kontainer;
use App\Models\DaftarTagihanKontainerSewa;
use Carbon\Carbon;

echo "🔍 Mencari kontainer yang memiliki tanggal_mulai_sewa...\n";

// Ambil semua kontainer yang memiliki tanggal_mulai_sewa
$kontainers = Kontainer::whereNotNull('tanggal_mulai_sewa')
    ->where('status', '!=', 'Dikembalikan') // Asumsi status bukan dikembalikan
    ->get();

echo "📦 Ditemukan " . $kontainers->count() . " kontainer dengan tanggal_mulai_sewa\n\n";

$createdCount = 0;
$skippedCount = 0;

foreach ($kontainers as $kontainer) {
    $tanggalMulai = Carbon::parse($kontainer->tanggal_mulai_sewa);
    $tanggalSelesai = $kontainer->tanggal_selesai_sewa ? Carbon::parse($kontainer->tanggal_selesai_sewa) : null;

    // Jika ada tanggal_selesai_sewa, buat tagihan sampai tanggal itu
    // Jika tidak, buat tagihan sampai bulan sekarang
    $endDate = $tanggalSelesai ?: Carbon::now()->endOfMonth();

    $currentStart = $tanggalMulai->copy()->startOfMonth();
    $periode = 1;

    while ($currentStart->lte($endDate)) {
        $currentEnd = $currentStart->copy()->endOfMonth();

        // Jika ada tanggal_selesai_sewa dan currentEnd melewati, potong
        if ($tanggalSelesai && $currentEnd->gt($tanggalSelesai)) {
            $currentEnd = $tanggalSelesai;
        }

        // Cek apakah tagihan untuk periode ini sudah ada
        $existingTagihan = DaftarTagihanKontainerSewa::where('nomor_kontainer', $kontainer->nomor_seri_gabungan)
            ->where('periode', $periode)
            ->first();

        if ($existingTagihan) {
            echo "⏭️  Skip: {$kontainer->nomor_seri_gabungan} periode {$periode} - sudah ada tagihan\n";
            $skippedCount++;
        } else {
            // Hitung DPP (contoh sederhana, bisa disesuaikan)
            $days = $currentStart->diffInDays($currentEnd) + 1;
            $dpp = $days * 50000; // Contoh harga per hari

            // Hitung PPN dan PPH
            $ppn = $dpp * 0.11;
            $pph = $dpp * 0.02; // Contoh 2%
            $grandTotal = $dpp + $ppn - $pph;

            // Create tagihan
            $tagihan = DaftarTagihanKontainerSewa::create([
                'vendor' => 'ZONA', // Default vendor
                'nomor_kontainer' => $kontainer->nomor_seri_gabungan,
                'size' => $kontainer->ukuran,
                'tanggal_awal' => $currentStart->format('Y-m-d'),
                'tanggal_akhir' => $currentEnd->format('Y-m-d'),
                'periode' => $periode,
                'masa' => $currentStart->format('j M Y') . ' - ' . $currentEnd->format('j M Y'),
                'tarif' => 'Bulanan', // Atau hitung berdasarkan hari
                'status' => 'ongoing',
                'dpp' => $dpp,
                'ppn' => $ppn,
                'pph' => $pph,
                'grand_total' => $grandTotal,
            ]);

            echo "✅ Created tagihan ID {$tagihan->id} untuk {$kontainer->nomor_seri_gabungan} periode {$periode}\n";
            $createdCount++;
        }

        // Pindah ke bulan berikutnya
        $currentStart->addMonth();
        $periode++;
    }
}

echo "\n📊 Ringkasan:\n";
echo "✅ Tagihan dibuat: $createdCount\n";
echo "⏭️  Tagihan di-skip: $skippedCount\n";
echo "🎯 Total kontainer diproses: " . $kontainers->count() . "\n";

?>