<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;
use App\Models\Coa;
use App\Models\CoaTransaction;
use Carbon\Carbon;

function generateMasaString($start, $end)
{
    $months = [
        1 => 'januari', 2 => 'februari', 3 => 'maret', 4 => 'april',
        5 => 'mei', 6 => 'juni', 7 => 'juli', 8 => 'agustus',
        9 => 'september', 10 => 'oktober', 11 => 'november', 12 => 'desember'
    ];

    $startStr = $start->format('j') . ' ' . $months[(int)$start->format('n')] . ' ' . $start->format('Y');
    $endStr = $end->format('j') . ' ' . $months[(int)$end->format('n')] . ' ' . $end->format('Y');

    return $startStr . ' - ' . $endStr;
}

function updateCoaSaldo($coaId, $transaksiId)
{
    $transaksi = CoaTransaction::findOrFail($transaksiId);

    $previousSaldo = CoaTransaction::where('coa_id', $coaId)
        ->where(function($query) use ($transaksi) {
            $query->where('tanggal_transaksi', '<', $transaksi->tanggal_transaksi)
                  ->orWhere(function($q) use ($transaksi) {
                      $q->where('tanggal_transaksi', '=', $transaksi->tanggal_transaksi)
                        ->where('id', '<', $transaksi->id);
                  });
        })
        ->orderBy('tanggal_transaksi', 'desc')
        ->orderBy('id', 'desc')
        ->value('saldo') ?? 0;

    $newSaldo = $previousSaldo + $transaksi->debit - $transaksi->kredit;
    $transaksi->update(['saldo' => $newSaldo]);

    // Update subsequent
    $subsequentTransactions = CoaTransaction::where('coa_id', $coaId)
        ->where(function($query) use ($transaksi) {
            $query->where('tanggal_transaksi', '>', $transaksi->tanggal_transaksi)
                  ->orWhere(function($q) use ($transaksi) {
                      $q->where('tanggal_transaksi', '=', $transaksi->tanggal_transaksi)
                        ->where('id', '>', $transaksi->id);
                  });
        })
        ->orderBy('tanggal_transaksi', 'asc')
        ->orderBy('id', 'asc')
        ->get();

    $runningSaldo = $newSaldo;
    foreach ($subsequentTransactions as $st) {
        $runningSaldo = $runningSaldo + $st->debit - $st->kredit;
        $st->update(['saldo' => $runningSaldo]);
    }
}

function recordCoaTransaction($tagihan, $periode)
{
    $coa = Coa::where('nomor_akun', 'COA007')->first();
    if (!$coa) return;

    $debitAmount = (float) ($tagihan->grand_total ?? 0);
    if ($debitAmount <= 0) return;

    $transaksi = CoaTransaction::create([
        'coa_id' => $coa->id,
        'tanggal_transaksi' => Carbon::now()->format('Y-m-d'),
        'keterangan' => "Tagihan periode {$periode} - Kontainer {$tagihan->nomor_kontainer} ({$tagihan->vendor})",
        'debit' => $debitAmount,
        'kredit' => 0,
        'saldo' => 0,
        'jenis_transaksi' => 'tagihan_kontainer_sewa',
        'nomor_referensi' => "TKS-{$tagihan->nomor_kontainer}-P{$periode}",
        'created_by' => 1,
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now()
    ]);

    updateCoaSaldo($coa->id, $transaksi->id);
}

$nomorKontainer = 'EMCU6063235';
$baseRecord = DaftarTagihanKontainerSewa::where('nomor_kontainer', $nomorKontainer)
    ->where('periode', 1)
    ->first();

if (!$baseRecord) {
    die("Base record (Periode 1) not found for {$nomorKontainer}\n");
}

$startDate = Carbon::parse($baseRecord->tanggal_awal);
$currentDate = Carbon::now();
$totalMonthsToNow = intval($startDate->diffInMonths($currentDate));
$maxPeriode = $totalMonthsToNow + 1;

$existingMaxPeriode = DaftarTagihanKontainerSewa::where('nomor_kontainer', $nomorKontainer)
    ->max('periode') ?? 0;

echo "Target Container: {$nomorKontainer}\n";
echo "Start Date: {$startDate->toDateString()}\n";
echo "Current Max Periode: {$existingMaxPeriode}\n";
echo "Target Max Periode: {$maxPeriode}\n";

if ($existingMaxPeriode >= $maxPeriode) {
    echo "No new periods needed.\n";
    exit;
}

for ($periode = $existingMaxPeriode + 1; $periode <= $maxPeriode; $periode++) {
    $periodStart = $startDate->copy()->addMonthsNoOverflow($periode - 1);
    $periodEnd = $periodStart->copy()->addMonthsNoOverflow(1)->subDay();

    // Check if period already exists
    $exists = DaftarTagihanKontainerSewa::where('nomor_kontainer', $nomorKontainer)
        ->where('periode', $periode)
        ->exists();
    
    if ($exists) {
        echo "Periode {$periode} already exists, skipping.\n";
        continue;
    }

    $attributes = [
        'vendor' => $baseRecord->vendor,
        'nomor_kontainer' => $nomorKontainer,
        'tanggal_awal' => $baseRecord->tanggal_awal,
        'periode' => $periode,
    ];

    $monthlyPrice = (float)$baseRecord->dpp; // Fallback to base record DPP
    
    // Try pricelist
    $pr = \App\Models\MasterPricelistSewaKontainer::where('ukuran_kontainer', $baseRecord->size)
        ->where('vendor', $baseRecord->vendor)
        ->where(function($q) use ($periodStart){
            $q->where('tanggal_harga_awal', '<=', $periodStart->toDateString())
              ->where(function($q2) use ($periodStart){ 
                  $q2->whereNull('tanggal_harga_akhir')
                     ->orWhere('tanggal_harga_akhir','>=',$periodStart->toDateString()); 
              });
        })->orderBy('tanggal_harga_awal','desc')->first();
    
    if ($pr) {
        $monthlyPrice = (float)$pr->harga;
    }

    $values = [
        'size' => $baseRecord->size,
        'group' => $baseRecord->group,
        'tanggal_akhir' => $periodEnd->format('Y-m-d'), 
        'masa' => generateMasaString($periodStart, $periodEnd),
        'tarif' => 'Bulanan',
        'dpp' => $monthlyPrice,
        'status' => 'Belum Lunas', // Assuming default status
    ];

    $row = DaftarTagihanKontainerSewa::create(array_merge($attributes, $values));
    
    if ($row) {
        echo "Created Periode {$periode}: {$values['masa']} - DPP: {$values['dpp']}\n";
        recordCoaTransaction($row, $periode);
    }
}

echo "Done.\n";
