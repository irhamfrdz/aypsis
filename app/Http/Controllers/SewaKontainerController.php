<?php

namespace App\Http\Controllers;

use App\Models\SewaCustomer;
use App\Models\SewaInvoice;
use App\Models\SewaKontainer;
use App\Models\SewaTagihan;
use App\Models\SewaTarif;
use App\Models\SewaTipe;
use App\Models\SewaTransaksi;
use App\Models\SewaUkuran;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SewaKontainerController extends Controller
{
    public function index(Request $request)
    {
        // Sync tags/periods first to ensure everything is up to date
        $this->syncAllTagihans();

        $customers = SewaCustomer::orderBy('nama_customer')->get();
        $tipes = SewaTipe::orderBy('nama_tipe')->get();
        $ukurans = SewaUkuran::orderBy('deskripsi_ukuran')->get();
        $kontainers = SewaKontainer::with(['customer', 'tipe', 'ukuran'])->get();
        $tarifs = SewaTarif::with(['customer', 'tipe', 'ukuran'])->get();
        $sewas = SewaTransaksi::with(['customer', 'kontainer'])->get();
        $invoices = SewaInvoice::with(['customer', 'tagihans'])->get();
        $tagihans = SewaTagihan::with(['transaksi.customer', 'transaksi.kontainer', 'invoice'])->get();

        return view('sewa-kontainer.index', compact(
            'customers',
            'tipes',
            'ukurans',
            'kontainers',
            'tarifs',
            'sewas',
            'invoices',
            'tagihans'
        ));
    }

    // Dynamic period generator helper matching utils.ts
    private function isLeapYear($year)
    {
        return ($year % 4 === 0 && $year % 100 !== 0) || ($year % 400 === 0);
    }

    private function getNextCycleStart(Carbon $curr)
    {
        $year = $curr->year;
        $month = $curr->month; // 1-12
        $day = $curr->day;

        // Try setting next month with same day
        $nextMonthSameDay = clone $curr;
        $nextMonthSameDay->addMonthNoOverflow();

        // If the day doesn't match, or it overflowed (e.g. 30 Jan + 1 month -> 28/29 Feb)
        // Carbon's addMonthNoOverflow stops at end of month (e.g. 28 Feb).
        // Let's match JS Date overflow checking exactly:
        $nextMonthReal = clone $curr;
        $nextMonthReal->addMonth(); // 30 Jan + 1 month -> 2 Mar / 3 Mar

        if ($nextMonthReal->day !== $day) {
            // next cycle starts on 1st of the next-next month (e.g. 1st March)
            $nextNextMonth = $month + 2;
            $nextNextYear = $year;
            if ($nextNextMonth > 12) {
                $nextNextMonth -= 12;
                $nextNextYear += 1;
            }

            return Carbon::createFromDate($nextNextYear, $nextNextMonth, 1)->startOfDay();
        }

        return $nextMonthReal->startOfDay();
    }

    public function syncAllTagihans()
    {
        $sewas = SewaTransaksi::all();
        $today = Carbon::now('Asia/Jakarta');

        foreach ($sewas as $sewa) {
            $this->generatePeriodsForSewa($sewa, $today);
        }
    }

    private function generatePeriodsForSewa(SewaTransaksi $sewa, Carbon $today)
    {
        $startLocal = Carbon::parse($sewa->tanggal_sewa)->startOfDay();
        $limitLocal = $sewa->tanggal_kembali ? Carbon::parse($sewa->tanggal_kembali)->startOfDay() : $today->copy()->startOfDay();

        $containerPart = preg_replace('/\s+/', '', trim($sewa->no_kontainer));

        // Convert tanggal_sewa to excel serial equivalent
        $serialPart = $this->dateToExcelSerial($startLocal);

        $currStart = $startLocal->copy();
        $index = 1;

        while ($currStart <= $limitLocal || $index === 1) {
            $nextStart = $this->getNextCycleStart($currStart);
            $normalEndDate = $nextStart->copy()->subDay();

            $monthSuffix = str_pad($index, 2, '0', STR_PAD_LEFT);
            $id_tagihan = "{$containerPart}{$serialPart}{$monthSuffix}";

            if ($normalEndDate <= $limitLocal) {
                // Completed full billing cycle
                $days = $currStart->diffInDays($normalEndDate) + 1;
                $amount = $sewa->jenis_tarif === 'Bulanan' ? $sewa->tarif_bulanan : $days * $sewa->tarif_harian;
                $tipe_tarif = $sewa->jenis_tarif === 'Bulanan' ? 'BULANAN' : 'HARIAN';

                $this->createOrKeepTagihan($id_tagihan, $sewa->id_sewa, $index, $currStart, $normalEndDate, $days, $tipe_tarif, $amount);

                $currStart = $nextStart->copy();
                $index++;
            } else {
                // Prorate or ongoing period
                $endLocal = $limitLocal < $currStart ? $currStart->copy() : $limitLocal->copy();
                $days = $currStart->diffInDays($endLocal) + 1;

                $amount = 0;
                $tipe_tarif = 'PRORATE';

                if ($sewa->jenis_tarif === 'Harian') {
                    $amount = $days * $sewa->tarif_harian;
                    $tipe_tarif = 'HARIAN';
                } else {
                    $sMonth = $currStart->month;
                    $baseDays = 30;
                    if ($sMonth === 2) {
                        $baseDays = $this->isLeapYear($currStart->year) ? 29 : 28;
                    }

                    if ($days === $baseDays) {
                        $amount = $sewa->tarif_bulanan;
                        $tipe_tarif = 'BULANAN';
                    } else {
                        $dailyRate = $sewa->tarif_bulanan / $baseDays;
                        $amount = round($days * $dailyRate);
                        $tipe_tarif = 'PRORATE';
                    }
                }

                $this->createOrKeepTagihan($id_tagihan, $sewa->id_sewa, $index, $currStart, $endLocal, $days, $tipe_tarif, $amount);
                break;
            }
        }
    }

    private function createOrKeepTagihan($id_tagihan, $id_sewa, $bulan_ke, $start, $end, $days, $tipe_tarif, $amount)
    {
        $existing = SewaTagihan::find($id_tagihan);
        if (! $existing) {
            SewaTagihan::create([
                'id_tagihan' => $id_tagihan,
                'id_sewa' => $id_sewa,
                'bulan_ke' => $bulan_ke,
                'tanggal_awal' => $start->format('Y-m-d'),
                'tanggal_akhir' => $end->format('Y-m-d'),
                'jumlah_hari' => $days,
                'tipe_tarif' => $tipe_tarif,
                'jumlah_tagihan' => $amount,
                'status_bayar' => 'Belum Ditagih',
            ]);
        } else {
            // Update estimated variables if not overridden
            if ($existing->status_bayar === 'Belum Ditagih') {
                $existing->update([
                    'tanggal_awal' => $start->format('Y-m-d'),
                    'tanggal_akhir' => $end->format('Y-m-d'),
                    'jumlah_hari' => $days,
                    'tipe_tarif' => $tipe_tarif,
                    'jumlah_tagihan' => $amount,
                ]);
            }
        }
    }

    private function dateToExcelSerial(Carbon $date)
    {
        $baseDate = Carbon::create(1899, 12, 30, 0, 0, 0, 'Asia/Jakarta');

        return (int) $baseDate->diffInDays($date);
    }

    // Customer CRUD
    public function storeCustomer(Request $request)
    {
        $request->validate(['nama_customer' => 'required|string|max:255']);
        $id = 'cust_'.Str::random(8);
        SewaCustomer::create([
            'id_customer' => $id,
            'nama_customer' => $request->nama_customer,
        ]);

        return response()->json(['success' => true]);
    }

    public function updateCustomer(Request $request, $id)
    {
        $request->validate(['nama_customer' => 'required|string|max:255']);
        $cust = SewaCustomer::findOrFail($id);
        $cust->update(['nama_customer' => $request->nama_customer]);

        return response()->json(['success' => true]);
    }

    public function deleteCustomer($id)
    {
        SewaCustomer::findOrFail($id)->delete();

        return response()->json(['success' => true]);
    }

    // Tipe CRUD
    public function storeTipe(Request $request)
    {
        $request->validate(['nama_tipe' => 'required|string|max:255']);
        $id = 'tipe_'.Str::random(8);
        SewaTipe::create([
            'id_tipe' => $id,
            'nama_tipe' => $request->nama_tipe,
        ]);

        return response()->json(['success' => true]);
    }

    public function updateTipe(Request $request, $id)
    {
        $request->validate(['nama_tipe' => 'required|string|max:255']);
        $tipe = SewaTipe::findOrFail($id);
        $tipe->update(['nama_tipe' => $request->nama_tipe]);

        return response()->json(['success' => true]);
    }

    public function deleteTipe($id)
    {
        SewaTipe::findOrFail($id)->delete();

        return response()->json(['success' => true]);
    }

    // Ukuran CRUD
    public function storeUkuran(Request $request)
    {
        $request->validate(['deskripsi_ukuran' => 'required|string|max:255']);
        $id = 'sz_'.Str::random(8);
        SewaUkuran::create([
            'id_ukuran' => $id,
            'deskripsi_ukuran' => $request->deskripsi_ukuran,
        ]);

        return response()->json(['success' => true]);
    }

    public function updateUkuran(Request $request, $id)
    {
        $request->validate(['deskripsi_ukuran' => 'required|string|max:255']);
        $ukuran = SewaUkuran::findOrFail($id);
        $ukuran->update(['deskripsi_ukuran' => $request->deskripsi_ukuran]);

        return response()->json(['success' => true]);
    }

    public function deleteUkuran($id)
    {
        SewaUkuran::findOrFail($id)->delete();

        return response()->json(['success' => true]);
    }

    // Kontainer CRUD
    public function storeKontainer(Request $request)
    {
        $request->validate([
            'no_kontainer' => 'required|string|unique:sewa_kontainers,no_kontainer',
            'id_customer' => 'required|string',
            'id_tipe' => 'required|string',
            'id_ukuran' => 'required|string',
        ]);
        SewaKontainer::create([
            'no_kontainer' => strtoupper(trim($request->no_kontainer)),
            'id_customer' => $request->id_customer,
            'id_tipe' => $request->id_tipe,
            'id_ukuran' => $request->id_ukuran,
            'status_aktif' => true,
        ]);

        return response()->json(['success' => true]);
    }

    public function updateKontainer(Request $request, $id)
    {
        $request->validate([
            'id_customer' => 'required|string',
            'id_tipe' => 'required|string',
            'id_ukuran' => 'required|string',
            'status_aktif' => 'required|boolean',
        ]);
        $kon = SewaKontainer::findOrFail($id);
        $kon->update($request->all());

        return response()->json(['success' => true]);
    }

    public function deleteKontainer($id)
    {
        SewaKontainer::findOrFail($id)->delete();

        return response()->json(['success' => true]);
    }

    // Tarif CRUD
    public function storeTarif(Request $request)
    {
        $request->validate([
            'id_customer' => 'required|string',
            'id_tipe' => 'required|string',
            'id_ukuran' => 'required|string',
            'tarif_bulanan' => 'required|numeric',
            'tarif_harian' => 'required|numeric',
            'tanggal_mulai_berlaku' => 'required|date',
        ]);
        $id = 'trf_'.Str::random(8);
        SewaTarif::create(array_merge($request->all(), [
            'id_tarif' => $id,
            'tanggal_akhir_berlaku' => $request->tanggal_akhir_berlaku ?: null,
        ]));

        return response()->json(['success' => true]);
    }

    public function updateTarif(Request $request, $id)
    {
        $request->validate([
            'id_customer' => 'required|string',
            'id_tipe' => 'required|string',
            'id_ukuran' => 'required|string',
            'tarif_bulanan' => 'required|numeric',
            'tarif_harian' => 'required|numeric',
            'tanggal_mulai_berlaku' => 'required|date',
        ]);
        $trf = SewaTarif::findOrFail($id);
        $trf->update(array_merge($request->all(), [
            'tanggal_akhir_berlaku' => $request->tanggal_akhir_berlaku ?: null,
        ]));

        return response()->json(['success' => true]);
    }

    public function deleteTarif($id)
    {
        SewaTarif::findOrFail($id)->delete();

        return response()->json(['success' => true]);
    }

    // Transaksi Sewa (Contracts) CRUD
    public function storeSewa(Request $request)
    {
        $request->validate([
            'no_kontainer' => 'required|string',
            'id_customer' => 'required|string',
            'tanggal_sewa' => 'required|date',
            'tarif_bulanan' => 'required|numeric',
            'tarif_harian' => 'required|numeric',
            'jenis_tarif' => 'required|string|in:Bulanan,Harian',
            'status_sewa' => 'required|string|in:Aktif,Selesai',
        ]);

        $id = 'sewa_'.Str::random(8);
        $sewa = SewaTransaksi::create(array_merge($request->all(), [
            'id_sewa' => $id,
            'tanggal_kembali' => $request->tanggal_kembali ?: null,
        ]));

        $this->generatePeriodsForSewa($sewa, Carbon::now('Asia/Jakarta'));

        return response()->json(['success' => true]);
    }

    public function updateSewa(Request $request, $id)
    {
        $request->validate([
            'no_kontainer' => 'required|string',
            'id_customer' => 'required|string',
            'tanggal_sewa' => 'required|date',
            'tarif_bulanan' => 'required|numeric',
            'tarif_harian' => 'required|numeric',
            'jenis_tarif' => 'required|string|in:Bulanan,Harian',
            'status_sewa' => 'required|string|in:Aktif,Selesai',
        ]);

        $sewa = SewaTransaksi::findOrFail($id);
        $sewa->update(array_merge($request->all(), [
            'tanggal_kembali' => $request->tanggal_kembali ?: null,
        ]));

        $this->generatePeriodsForSewa($sewa, Carbon::now('Asia/Jakarta'));

        return response()->json(['success' => true]);
    }

    public function terminateSewa(Request $request, $id)
    {
        $request->validate(['tanggal_kembali' => 'required|date']);
        $sewa = SewaTransaksi::findOrFail($id);
        $sewa->update([
            'tanggal_kembali' => $request->tanggal_kembali,
            'status_sewa' => 'Selesai',
        ]);

        $this->generatePeriodsForSewa($sewa, Carbon::now('Asia/Jakarta'));

        return response()->json(['success' => true]);
    }

    public function deleteSewa($id)
    {
        SewaTransaksi::findOrFail($id)->delete();

        return response()->json(['success' => true]);
    }

    // Payment Override / Tagihan Period updates
    public function payTagihanOverride(Request $request, $id)
    {
        $request->validate([
            'status_bayar' => 'required|string',
            'tanggal_tagihan' => 'nullable|date',
            'tanggal_bayar' => 'nullable|date',
            'nomor_invoice_grup' => 'nullable|string',
            'jumlah_tagihan_override' => 'nullable|numeric',
            'jumlah_bayar' => 'nullable|numeric',
            'selisih_pembayaran' => 'nullable|numeric',
            'keterangan_selisih' => 'nullable|string',
            'ppn' => 'nullable|numeric',
            'pph' => 'nullable|numeric',
            'nomor_bayar' => 'nullable|string',
        ]);

        $tagihan = SewaTagihan::findOrFail($id);
        $tagihan->update($request->all());

        return response()->json(['success' => true]);
    }

    // Invoice Manager - Groups
    public function storeInvoice(Request $request)
    {
        $request->validate([
            'nomor_invoice' => 'required|string|unique:sewa_invoices,nomor_invoice',
            'id_customer' => 'required|string',
            'tanggal_invoice' => 'required|date',
            'status_pembayaran' => 'required|string',
            'list_id_tagihan' => 'required|array',
        ]);

        DB::transaction(function () use ($request) {
            SewaInvoice::create([
                'nomor_invoice' => $request->nomor_invoice,
                'id_customer' => $request->id_customer,
                'tanggal_invoice' => $request->tanggal_invoice,
                'status_pembayaran' => $request->status_pembayaran,
                'deskripsi' => $request->deskripsi,
                'adjustment_biaya' => $request->adjustment_biaya ?: 0,
                'adjustment_keterangan' => $request->adjustment_keterangan,
            ]);

            SewaTagihan::whereIn('id_tagihan', $request->list_id_tagihan)->update([
                'nomor_invoice_grup' => $request->nomor_invoice,
                'status_bayar' => $request->status_pembayaran === 'Lunas' ? 'Lunas' : 'Belum Bayar',
            ]);
        });

        return response()->json(['success' => true]);
    }

    public function deleteInvoice($id)
    {
        DB::transaction(function () use ($id) {
            SewaTagihan::where('nomor_invoice_grup', $id)->update([
                'nomor_invoice_grup' => null,
                'status_bayar' => 'Belum Ditagih',
            ]);
            SewaInvoice::findOrFail($id)->delete();
        });

        return response()->json(['success' => true]);
    }

    // Bulk JSON/Excel Import matching BulkImportPanel.tsx logic
    public function importBackup(Request $request)
    {
        $request->validate(['backup_file' => 'required|file']);
        $file = $request->file('backup_file');
        $content = json_decode(file_get_contents($file->getRealPath()), true);

        if (! $content) {
            return back()->with('error', 'Format backup JSON tidak valid.');
        }

        DB::transaction(function () use ($content) {
            // Drop & Import logic for local testing/overwrite
            if (isset($content['customers'])) {
                SewaCustomer::truncate();
                foreach ($content['customers'] as $c) {
                    SewaCustomer::create($c);
                }
            }

            if (isset($content['tipes'])) {
                SewaTipe::truncate();
                foreach ($content['tipes'] as $t) {
                    SewaTipe::create($t);
                }
            }

            if (isset($content['ukurans'])) {
                SewaUkuran::truncate();
                foreach ($content['ukurans'] as $u) {
                    SewaUkuran::create($u);
                }
            }

            if (isset($content['kontainers'])) {
                SewaKontainer::truncate();
                foreach ($content['kontainers'] as $k) {
                    SewaKontainer::create($k);
                }
            }

            if (isset($content['tarifs'])) {
                SewaTarif::truncate();
                foreach ($content['tarifs'] as $tr) {
                    SewaTarif::create($tr);
                }
            }

            if (isset($content['sewas'])) {
                SewaTransaksi::truncate();
                foreach ($content['sewas'] as $s) {
                    SewaTransaksi::create($s);
                }
            }

            if (isset($content['invoices'])) {
                SewaInvoice::truncate();
                foreach ($content['invoices'] as $inv) {
                    // Remove temporary list_id_tagihan property if present
                    $list = $inv['list_id_tagihan'] ?? [];
                    unset($inv['list_id_tagihan']);
                    SewaInvoice::create($inv);
                }
            }

            // Sync payment overrides
            if (isset($content['paymentOverrides'])) {
                foreach ($content['paymentOverrides'] as $id_tagihan => $ov) {
                    SewaTagihan::where('id_tagihan', $id_tagihan)->update([
                        'status_bayar' => $ov['status_bayar'] ?? 'Belum Ditagih',
                        'tanggal_bayar' => $ov['tanggal_bayar'] ?? null,
                        'tanggal_tagihan' => $ov['tanggal_tagihan'] ?? null,
                        'nomor_invoice_grup' => $ov['nomor_invoice_grup'] ?? null,
                        'jumlah_tagihan_override' => $ov['jumlah_tagihan_override'] ?? null,
                        'jumlah_bayar' => $ov['jumlah_bayar'] ?? null,
                        'selisih_pembayaran' => $ov['selisih_pembayaran'] ?? null,
                        'keterangan_selisih' => $ov['keterangan_selisih'] ?? null,
                        'ppn' => $ov['ppn'] ?? null,
                        'pph' => $ov['pph'] ?? null,
                        'nomor_bayar' => $ov['nomor_bayar'] ?? null,
                    ]);
                }
            }
        });

        return back()->with('success', 'Sistem berhasil memulihkan semua data dari file backup JSON!');
    }
}
