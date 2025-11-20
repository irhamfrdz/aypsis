<?php
/**
 * Script untuk otomatis membuat pranota berdasarkan group dan periode
 * 
 * KONDISI:
 * 1. Kontainer harus memiliki invoice_vendor (tidak null)
 * 2. Kontainer yang memiliki group yang sama dan periode yang sama akan dimasukkan ke pranota yang sama
 * 3. Kontainer yang belum masuk pranota (status_pranota = null)
 * 
 * Cara menjalankan:
 * php auto_create_pranota_by_group.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\DaftarTagihanKontainerSewa;
use App\Models\PranotaTagihanKontainerSewa;
use Carbon\Carbon;

echo "\n";
echo "========================================\n";
echo "AUTO CREATE PRANOTA BY GROUP & PERIODE\n";
echo "========================================\n";
echo "\n";

try {
    // Ambil semua kontainer yang:
    // 1. Belum masuk pranota (status_pranota = null)
    // 2. Memiliki invoice_vendor
    // 3. Memiliki group (tidak null dan tidak kosong)
    $kontainers = DaftarTagihanKontainerSewa::whereNull('status_pranota')
        ->whereNotNull('invoice_vendor')
        ->where('invoice_vendor', '!=', '')
        ->whereNotNull('group')
        ->where('group', '!=', '')
        ->orderBy('group')
        ->orderBy('periode')
        ->orderBy('vendor')
        ->get();

    echo "📊 Jumlah kontainer yang memenuhi syarat: {$kontainers->count()}\n";
    
    if ($kontainers->count() === 0) {
        echo "ℹ️  Tidak ada kontainer yang perlu dibuatkan pranota.\n\n";
        exit(0);
    }

    echo "\n";
    echo "Mengelompokkan kontainer berdasarkan group dan periode...\n";
    echo "----------------------------------------\n";

    // Kelompokkan berdasarkan group dan periode
    $grouped = $kontainers->groupBy(function($item) {
        return $item->group . '|' . $item->periode;
    });

    echo "📦 Jumlah group-periode unik: {$grouped->count()}\n\n";

    // Konfirmasi
    echo "Detail pengelompokan:\n";
    echo "----------------------------------------\n";
    foreach ($grouped as $key => $items) {
        list($group, $periode) = explode('|', $key);
        $vendors = $items->pluck('vendor')->unique()->implode(', ');
        $totalAmount = $items->sum('grand_total');
        
        echo "Group: {$group} | Periode: {$periode}\n";
        echo "  - Jumlah kontainer: {$items->count()}\n";
        echo "  - Vendor: {$vendors}\n";
        echo "  - Total nilai: Rp " . number_format($totalAmount, 0, '.', ',') . "\n";
        echo "  - Kontainer: " . $items->pluck('nomor_kontainer')->implode(', ') . "\n";
        echo "\n";
    }

    echo "----------------------------------------\n";
    echo "Apakah Anda yakin ingin membuat pranota untuk semua group di atas? (ketik 'YES' untuk melanjutkan): ";

    $handle = fopen("php://stdin", "r");
    $confirmation = trim(fgets($handle));
    fclose($handle);

    if ($confirmation !== 'YES') {
        echo "\n❌ Pembuatan pranota dibatalkan.\n\n";
        exit(0);
    }

    echo "\n";
    echo "Memulai pembuatan pranota...\n";
    echo "----------------------------------------\n";

    $createdCount = 0;
    $processedContainers = 0;

    DB::beginTransaction();

    try {
        foreach ($grouped as $key => $items) {
            list($group, $periode) = explode('|', $key);
            
            // Ambil data dari kontainer pertama sebagai referensi
            $firstItem = $items->first();
            $vendor = $firstItem->vendor;
            
            // Hitung statistik kontainer
            $containerStats = [];
            foreach ($items as $item) {
                $size = $item->size . 'ft';
                if (!isset($containerStats[$size])) {
                    $containerStats[$size] = 0;
                }
                $containerStats[$size]++;
            }
            
            // Generate keterangan
            $statParts = [];
            foreach ($containerStats as $size => $count) {
                $statParts[] = "{$count} kontainer {$size}";
            }
            $keterangan = 'Pranota ' . implode(' dan ', $statParts) . " - Group {$group} - Periode {$periode}";
            
            // Generate nomor invoice
            $today = Carbon::today();
            $yearMonth = $today->format('ym'); // Format: 2511 untuk November 2025
            
            // Cari nomor terakhir untuk bulan ini
            $lastPranota = PranotaTagihanKontainerSewa::where('no_invoice', 'like', "TK1{$yearMonth}%")
                ->orderBy('no_invoice', 'desc')
                ->first();
            
            if ($lastPranota) {
                // Ambil 7 digit terakhir dan tambah 1
                $lastNumber = intval(substr($lastPranota->no_invoice, -7));
                $newNumber = $lastNumber + 1;
            } else {
                // Mulai dari 1
                $newNumber = 1;
            }
            
            $noInvoice = 'TK1' . $yearMonth . str_pad($newNumber, 7, '0', STR_PAD_LEFT);
            
            // Hitung total
            $totalGrandTotal = $items->sum('grand_total');
            
            // Ambil IDs dari tagihan
            $tagihanIds = $items->pluck('id')->toArray();
            
            // Buat pranota baru
            $pranota = PranotaTagihanKontainerSewa::create([
                'no_invoice' => $noInvoice,
                'tanggal_pranota' => $today,
                'keterangan' => $keterangan,
                'total_amount' => $totalGrandTotal,
                'jumlah_tagihan' => $items->count(),
                'tagihan_kontainer_sewa_ids' => json_encode($tagihanIds),
                'status' => 'unpaid',
            ]);
            
            // Update semua kontainer dalam group ini
            foreach ($items as $item) {
                $item->update([
                    'pranota_id' => $pranota->id,
                    'status_pranota' => 'included',
                ]);
                $processedContainers++;
            }
            
            $createdCount++;
            
            echo "✅ Pranota #{$noInvoice} dibuat untuk Group: {$group}, Periode: {$periode}\n";
            echo "   - Kontainer: {$items->count()}\n";
            echo "   - Total: Rp " . number_format($totalGrandTotal, 0, '.', ',') . "\n";
            echo "\n";
        }

        DB::commit();

        echo "========================================\n";
        echo "✅ PEMBUATAN PRANOTA BERHASIL!\n";
        echo "========================================\n";
        echo "Jumlah pranota dibuat: {$createdCount}\n";
        echo "Jumlah kontainer diproses: {$processedContainers}\n";
        echo "\n";

    } catch (\Exception $e) {
        DB::rollBack();
        throw $e;
    }

} catch (\Exception $e) {
    echo "\n";
    echo "========================================\n";
    echo "❌ ERROR SAAT MEMBUAT PRANOTA\n";
    echo "========================================\n";
    echo "Pesan error: " . $e->getMessage() . "\n";
    echo "\n";
    echo "Stack trace:\n";
    echo $e->getTraceAsString() . "\n";
    echo "\n";
    
    exit(1);
}

echo "Selesai.\n";
echo "\n";
