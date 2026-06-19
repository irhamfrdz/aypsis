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
        $this->syncAllTagihans();

        $customers  = SewaCustomer::orderBy('nama_customer')->get();
        $tipes      = SewaTipe::orderBy('nama_tipe')->get();
        $ukurans    = SewaUkuran::orderBy('deskripsi_ukuran')->get();
        $kontainers = SewaKontainer::with(['customer', 'tipe', 'ukuran'])->get();
        $tarifs     = SewaTarif::with(['customer', 'tipe', 'ukuran'])->orderBy('tanggal_mulai_berlaku', 'desc')->get();
        $sewas      = SewaTransaksi::with(['customer', 'kontainer.tipe', 'kontainer.ukuran', 'tagihans'])->orderBy('tanggal_sewa', 'desc')->get();
        $invoices   = SewaInvoice::with(['customer', 'tagihans'])->orderBy('tanggal_invoice', 'desc')->get();
        $tagihans   = SewaTagihan::with(['transaksi.customer', 'transaksi.kontainer'])->orderBy('tanggal_awal', 'desc')->get();

        return view('sewa-kontainer.index', compact(
            'customers', 'tipes', 'ukurans', 'kontainers',
            'tarifs', 'sewas', 'invoices', 'tagihans'
        ));
    }

    // -----------------------------------------------------------------------
    // BILLING PERIOD GENERATION (PHP port of utils.ts)
    // -----------------------------------------------------------------------

    private function isLeapYear($year)
    {
        return ($year % 4 === 0 && $year % 100 !== 0) || ($year % 400 === 0);
    }

    private function getNextCycleStart(Carbon $curr)
    {
        $day  = $curr->day;
        $next = clone $curr;
        $next->addMonth(); // JS-like overflow: 30 Jan -> 2 Mar

        if ($next->day !== $day) {
            $nm = $curr->month + 2;
            $ny = $curr->year;
            if ($nm > 12) { $nm -= 12; $ny++; }
            return Carbon::createFromDate($ny, $nm, 1)->startOfDay();
        }
        return $next->startOfDay();
    }

    private function dateToExcelSerial(Carbon $date)
    {
        $base = Carbon::create(1899, 12, 30, 0, 0, 0, 'UTC');
        $d    = Carbon::createFromDate($date->year, $date->month, $date->day, 'UTC');
        return (int) $base->diffInDays($d);
    }

    private function generatePeriodsForSewa(SewaTransaksi $sewa, Carbon $today)
    {
        $startLocal = Carbon::parse($sewa->tanggal_sewa)->startOfDay();
        $limitLocal = $sewa->tanggal_kembali
            ? Carbon::parse($sewa->tanggal_kembali)->startOfDay()
            : $today->copy()->startOfDay();

        $containerPart = preg_replace('/\s+/', '', trim($sewa->no_kontainer));
        $serialPart    = $this->dateToExcelSerial($startLocal);

        $currStart = $startLocal->copy();
        $index     = 1;

        while ($currStart <= $limitLocal || $index === 1) {
            $nextStart     = $this->getNextCycleStart($currStart);
            $normalEndDate = $nextStart->copy()->subDay();

            $monthSuffix = str_pad($index, 2, '0', STR_PAD_LEFT);
            $id_tagihan  = "{$containerPart}{$serialPart}{$monthSuffix}";

            if ($normalEndDate <= $limitLocal) {
                $days      = $currStart->diffInDays($normalEndDate) + 1;
                $amount    = $sewa->jenis_tarif === 'Bulanan' ? $sewa->tarif_bulanan : $days * $sewa->tarif_harian;
                $tipeTarif = $sewa->jenis_tarif === 'Bulanan' ? 'BULANAN' : 'HARIAN';
                $this->upsertTagihan($id_tagihan, $sewa->id_sewa, $index, $currStart, $normalEndDate, $days, $tipeTarif, $amount);
                $currStart = $nextStart->copy();
                $index++;
            } else {
                $endLocal  = $limitLocal < $currStart ? $currStart->copy() : $limitLocal->copy();
                $days      = $currStart->diffInDays($endLocal) + 1;
                $amount    = 0;
                $tipeTarif = 'PRORATE';

                if ($sewa->jenis_tarif === 'Harian') {
                    $amount    = $days * $sewa->tarif_harian;
                    $tipeTarif = 'HARIAN';
                } else {
                    $sMonth   = $currStart->month;
                    $baseDays = 30;
                    if ($sMonth === 2) {
                        $baseDays = $this->isLeapYear($currStart->year) ? 29 : 28;
                    }
                    if ($days === $baseDays) {
                        $amount    = $sewa->tarif_bulanan;
                        $tipeTarif = 'BULANAN';
                    } else {
                        $amount = round($days * ($sewa->tarif_bulanan / $baseDays));
                    }
                }
                $this->upsertTagihan($id_tagihan, $sewa->id_sewa, $index, $currStart, $endLocal, $days, $tipeTarif, $amount);
                break;
            }
        }
    }

    private function upsertTagihan($id, $idSewa, $bulanKe, $start, $end, $days, $tipeTarif, $amount)
    {
        $existing = SewaTagihan::find($id);
        if (!$existing) {
            SewaTagihan::create([
                'id_tagihan'    => $id,
                'id_sewa'       => $idSewa,
                'bulan_ke'      => $bulanKe,
                'tanggal_awal'  => $start->format('Y-m-d'),
                'tanggal_akhir' => $end->format('Y-m-d'),
                'jumlah_hari'   => $days,
                'tipe_tarif'    => $tipeTarif,
                'jumlah_tagihan'=> $amount,
                'status_bayar'  => 'Belum Ditagih',
            ]);
        } elseif ($existing->status_bayar === 'Belum Ditagih') {
            $existing->update([
                'tanggal_awal'  => $start->format('Y-m-d'),
                'tanggal_akhir' => $end->format('Y-m-d'),
                'jumlah_hari'   => $days,
                'tipe_tarif'    => $tipeTarif,
                'jumlah_tagihan'=> $amount,
            ]);
        }
    }

    public function syncAllTagihans()
    {
        $today = Carbon::now('Asia/Jakarta');
        SewaTransaksi::all()->each(fn($s) => $this->generatePeriodsForSewa($s, $today));
    }

    // -----------------------------------------------------------------------
    // MASTER: CUSTOMER
    // -----------------------------------------------------------------------

    public function storeCustomer(Request $request)
    {
        $request->validate(['nama_customer' => 'required|string|max:255']);
        SewaCustomer::create(['id_customer' => 'cust_' . Str::random(8), 'nama_customer' => $request->nama_customer]);
        return response()->json(['success' => true]);
    }

    public function updateCustomer(Request $request, $id)
    {
        $request->validate(['nama_customer' => 'required|string|max:255']);
        SewaCustomer::findOrFail($id)->update(['nama_customer' => $request->nama_customer]);
        return response()->json(['success' => true]);
    }

    public function deleteCustomer($id)
    {
        if (SewaTransaksi::where('id_customer', $id)->exists()) {
            return response()->json(['success' => false, 'message' => 'Customer memiliki transaksi sewa, tidak bisa dihapus.'], 422);
        }
        SewaCustomer::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }

    // -----------------------------------------------------------------------
    // MASTER: TIPE
    // -----------------------------------------------------------------------

    public function storeTipe(Request $request)
    {
        $request->validate(['nama_tipe' => 'required|string|max:255']);
        SewaTipe::create(['id_tipe' => 'tipe_' . Str::random(8), 'nama_tipe' => $request->nama_tipe]);
        return response()->json(['success' => true]);
    }

    public function deleteTipe($id)
    {
        if (SewaKontainer::where('id_tipe', $id)->exists()) {
            return response()->json(['success' => false, 'message' => 'Tipe sedang digunakan oleh data kontainer.'], 422);
        }
        SewaTipe::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }

    // -----------------------------------------------------------------------
    // MASTER: UKURAN
    // -----------------------------------------------------------------------

    public function storeUkuran(Request $request)
    {
        $request->validate(['deskripsi_ukuran' => 'required|string|max:255']);
        $raw = trim($request->deskripsi_ukuran);
        if (is_numeric($raw)) $raw = $raw . "'";
        if (SewaUkuran::where('deskripsi_ukuran', $raw)->exists()) {
            return response()->json(['success' => false, 'message' => "Ukuran \"{$raw}\" sudah ada."], 422);
        }
        SewaUkuran::create(['id_ukuran' => 'sz_' . Str::random(8), 'deskripsi_ukuran' => $raw]);
        return response()->json(['success' => true]);
    }

    public function deleteUkuran($id)
    {
        if (SewaKontainer::where('id_ukuran', $id)->exists()) {
            return response()->json(['success' => false, 'message' => 'Ukuran sedang digunakan oleh data kontainer.'], 422);
        }
        SewaUkuran::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }

    // -----------------------------------------------------------------------
    // MASTER: KONTAINER
    // -----------------------------------------------------------------------

    public function storeKontainer(Request $request)
    {
        $request->validate([
            'no_kontainer' => 'required|string|unique:sewa_kontainers,no_kontainer',
            'id_customer'  => 'required|string|exists:sewa_customers,id_customer',
            'id_tipe'      => 'required|string|exists:sewa_tipes,id_tipe',
            'id_ukuran'    => 'required|string|exists:sewa_ukurans,id_ukuran',
        ]);
        SewaKontainer::create([
            'no_kontainer' => strtoupper(preg_replace('/\s+/', '', trim($request->no_kontainer))),
            'id_customer'  => $request->id_customer,
            'id_tipe'      => $request->id_tipe,
            'id_ukuran'    => $request->id_ukuran,
            'status_aktif' => true,
        ]);
        return response()->json(['success' => true]);
    }

    public function deleteKontainer($id)
    {
        if (SewaTransaksi::where('no_kontainer', $id)->exists()) {
            return response()->json(['success' => false, 'message' => 'Kontainer sudah memiliki sejarah transaksi sewa.'], 422);
        }
        SewaKontainer::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }

    // -----------------------------------------------------------------------
    // MASTER: TARIF
    // -----------------------------------------------------------------------

    public function storeTarif(Request $request)
    {
        $request->validate([
            'id_customer'           => 'required|string|exists:sewa_customers,id_customer',
            'id_tipe'               => 'required|string|exists:sewa_tipes,id_tipe',
            'id_ukuran'             => 'required|string|exists:sewa_ukurans,id_ukuran',
            'tarif_bulanan'         => 'required|numeric|min:0',
            'tarif_harian'          => 'required|numeric|min:0',
            'tanggal_mulai_berlaku' => 'required|date',
        ]);

        // Auto-close previous active tarif
        $prev = SewaTarif::where('id_customer', $request->id_customer)
            ->where('id_tipe', $request->id_tipe)
            ->where('id_ukuran', $request->id_ukuran)
            ->whereNull('tanggal_akhir_berlaku')
            ->first();
        if ($prev) {
            $prev->update(['tanggal_akhir_berlaku' => Carbon::parse($request->tanggal_mulai_berlaku)->subDay()->format('Y-m-d')]);
        }

        SewaTarif::create([
            'id_tarif'              => 'trf_' . Str::random(8),
            'id_customer'           => $request->id_customer,
            'id_tipe'               => $request->id_tipe,
            'id_ukuran'             => $request->id_ukuran,
            'tarif_bulanan'         => $request->tarif_bulanan,
            'tarif_harian'          => $request->tarif_harian,
            'tanggal_mulai_berlaku' => $request->tanggal_mulai_berlaku,
            'tanggal_akhir_berlaku' => null,
        ]);
        return response()->json(['success' => true]);
    }

    public function deleteTarif($id)
    {
        SewaTarif::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }

    // -----------------------------------------------------------------------
    // TRANSAKSI SEWA
    // -----------------------------------------------------------------------

    public function storeSewa(Request $request)
    {
        $request->validate([
            'no_kontainer'  => 'required|string|exists:sewa_kontainers,no_kontainer',
            'id_customer'   => 'required|string|exists:sewa_customers,id_customer',
            'tanggal_sewa'  => 'required|date',
            'tarif_bulanan' => 'required|numeric|min:0',
            'tarif_harian'  => 'required|numeric|min:0',
            'jenis_tarif'   => 'required|in:Bulanan,Harian',
        ]);

        if (SewaTransaksi::where('no_kontainer', $request->no_kontainer)->where('status_sewa', 'Aktif')->exists()) {
            return response()->json(['success' => false, 'message' => 'Kontainer masih Aktif disewa. Kembalikan terlebih dahulu.'], 422);
        }

        $noKon   = preg_replace('/\s+/', '', trim($request->no_kontainer));
        $serial  = $this->dateToExcelSerial(Carbon::parse($request->tanggal_sewa));
        $cycle   = SewaTransaksi::where('no_kontainer', $request->no_kontainer)->count() + 1;
        $id_sewa = "{$noKon}{$serial}" . str_pad($cycle, 2, '0', STR_PAD_LEFT);

        $sewa = SewaTransaksi::create([
            'id_sewa'        => $id_sewa,
            'no_kontainer'   => $request->no_kontainer,
            'id_customer'    => $request->id_customer,
            'tanggal_sewa'   => $request->tanggal_sewa,
            'tanggal_kembali'=> null,
            'tarif_bulanan'  => $request->tarif_bulanan,
            'tarif_harian'   => $request->tarif_harian,
            'jenis_tarif'    => $request->jenis_tarif,
            'status_sewa'    => 'Aktif',
            'catatan'        => $request->catatan,
        ]);

        $this->generatePeriodsForSewa($sewa, Carbon::now('Asia/Jakarta'));
        return response()->json(['success' => true, 'id_sewa' => $id_sewa]);
    }

    public function updateSewa(Request $request, $id)
    {
        $request->validate([
            'tanggal_sewa'   => 'required|date',
            'tanggal_kembali'=> 'nullable|date|after_or_equal:tanggal_sewa',
            'tarif_bulanan'  => 'required|numeric|min:0',
            'tarif_harian'   => 'required|numeric|min:0',
            'jenis_tarif'    => 'required|in:Bulanan,Harian',
        ]);
        $sewa = SewaTransaksi::findOrFail($id);
        $sewa->update([
            'tanggal_sewa'   => $request->tanggal_sewa,
            'tanggal_kembali'=> $request->tanggal_kembali ?: null,
            'tarif_bulanan'  => $request->tarif_bulanan,
            'tarif_harian'   => $request->tarif_harian,
            'jenis_tarif'    => $request->jenis_tarif,
            'status_sewa'    => $request->tanggal_kembali ? 'Selesai' : 'Aktif',
            'catatan'        => $request->catatan,
        ]);
        $this->generatePeriodsForSewa($sewa->fresh(), Carbon::now('Asia/Jakarta'));
        return response()->json(['success' => true]);
    }

    public function terminateSewa(Request $request, $id)
    {
        $request->validate(['tanggal_kembali' => 'required|date']);
        $sewa = SewaTransaksi::findOrFail($id);
        $sewa->update(['tanggal_kembali' => $request->tanggal_kembali, 'status_sewa' => 'Selesai']);
        $this->generatePeriodsForSewa($sewa->fresh(), Carbon::now('Asia/Jakarta'));
        return response()->json(['success' => true]);
    }

    public function deleteSewa($id)
    {
        DB::transaction(function () use ($id) {
            SewaTagihan::where('id_sewa', $id)->delete();
            SewaTransaksi::findOrFail($id)->delete();
        });
        return response()->json(['success' => true]);
    }

    // -----------------------------------------------------------------------
    // TAGIHAN (inline update)
    // -----------------------------------------------------------------------

    public function updateTagihan(Request $request, $id)
    {
        $request->validate([
            'status_bayar'            => 'nullable|string|in:Belum Ditagih,Pranota,Belum Bayar,Lunas',
            'tanggal_tagihan'         => 'nullable|date',
            'tanggal_bayar'           => 'nullable|date',
            'nomor_invoice_grup'      => 'nullable|string',
            'jumlah_tagihan_override' => 'nullable|numeric',
            'jumlah_bayar'            => 'nullable|numeric',
            'keterangan_selisih'      => 'nullable|string',
            'ppn'                     => 'nullable|numeric',
            'pph'                     => 'nullable|numeric',
            'nomor_bayar'             => 'nullable|string',
        ]);

        $tagihan = SewaTagihan::findOrFail($id);
        $data    = $request->only([
            'status_bayar', 'tanggal_tagihan', 'tanggal_bayar', 'nomor_invoice_grup',
            'jumlah_tagihan_override', 'jumlah_bayar', 'keterangan_selisih', 'ppn', 'pph', 'nomor_bayar',
        ]);

        // Auto-calc selisih & taxes when override amount set
        if (array_key_exists('jumlah_tagihan_override', $data) && $data['jumlah_tagihan_override'] !== null) {
            $ov           = floatval($data['jumlah_tagihan_override']);
            $data['ppn']  = round($ov * 0.11);
            $data['pph']  = round($ov * 0.02);
            $data['selisih_pembayaran'] = $ov - $tagihan->jumlah_tagihan;
        }

        // Auto-timestamps
        if (!empty($data['status_bayar'])) {
            if ($data['status_bayar'] === 'Lunas' && empty($data['tanggal_bayar'])) {
                $data['tanggal_bayar'] = now()->format('Y-m-d');
            }
            if (in_array($data['status_bayar'], ['Belum Bayar', 'Pranota']) && empty($data['tanggal_tagihan'])) {
                $data['tanggal_tagihan'] = now()->format('Y-m-d');
            }
        }

        $tagihan->update($data);
        return response()->json(['success' => true, 'tagihan' => $tagihan->fresh()]);
    }

    // -----------------------------------------------------------------------
    // INVOICE GRUP
    // -----------------------------------------------------------------------

    public function storeInvoice(Request $request)
    {
        $request->validate([
            'nomor_invoice'         => 'required|string|unique:sewa_invoices,nomor_invoice',
            'id_customer'           => 'required|string|exists:sewa_customers,id_customer',
            'tanggal_invoice'       => 'required|date',
            'status_pembayaran'     => 'required|string|in:Belum Bayar,Lunas',
            'list_id_tagihan'       => 'required|array|min:1',
            'adjustment_biaya'      => 'nullable|numeric',
            'adjustment_keterangan' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request) {
            SewaInvoice::create([
                'nomor_invoice'         => $request->nomor_invoice,
                'id_customer'           => $request->id_customer,
                'tanggal_invoice'       => $request->tanggal_invoice,
                'status_pembayaran'     => $request->status_pembayaran,
                'deskripsi'             => $request->deskripsi,
                'adjustment_biaya'      => $request->adjustment_biaya ?? 0,
                'adjustment_keterangan' => $request->adjustment_keterangan,
            ]);

            $newStatus = $request->status_pembayaran === 'Lunas' ? 'Lunas' : 'Belum Bayar';
            SewaTagihan::whereIn('id_tagihan', $request->list_id_tagihan)->update([
                'nomor_invoice_grup' => $request->nomor_invoice,
                'status_bayar'       => $newStatus,
                'tanggal_tagihan'    => now()->format('Y-m-d'),
            ]);
        });
        return response()->json(['success' => true]);
    }

    public function updateInvoice(Request $request, $nomor)
    {
        $request->validate([
            'status_pembayaran'     => 'required|string|in:Belum Bayar,Lunas',
            'adjustment_biaya'      => 'nullable|numeric',
            'adjustment_keterangan' => 'nullable|string',
            'tanggal_bayar'         => 'nullable|date',
            'nomor_bayar'           => 'nullable|string',
        ]);
        DB::transaction(function () use ($request, $nomor) {
            SewaInvoice::findOrFail($nomor)->update([
                'status_pembayaran'     => $request->status_pembayaran,
                'adjustment_biaya'      => $request->adjustment_biaya ?? 0,
                'adjustment_keterangan' => $request->adjustment_keterangan,
            ]);
            $upd = ['status_bayar' => $request->status_pembayaran === 'Lunas' ? 'Lunas' : 'Belum Bayar'];
            if ($request->status_pembayaran === 'Lunas') {
                $upd['tanggal_bayar'] = $request->tanggal_bayar ?: now()->format('Y-m-d');
                if ($request->nomor_bayar) $upd['nomor_bayar'] = $request->nomor_bayar;
            }
            SewaTagihan::where('nomor_invoice_grup', $nomor)->update($upd);
        });
        return response()->json(['success' => true]);
    }

    public function deleteInvoice($nomor)
    {
        DB::transaction(function () use ($nomor) {
            SewaTagihan::where('nomor_invoice_grup', $nomor)->update([
                'nomor_invoice_grup' => null,
                'status_bayar'       => 'Belum Ditagih',
                'tanggal_tagihan'    => null,
            ]);
            SewaInvoice::findOrFail($nomor)->delete();
        });
        return response()->json(['success' => true]);
    }

    // -----------------------------------------------------------------------
    // BULK PAYMENT IMPORT
    // -----------------------------------------------------------------------

    public function importPayment(Request $request)
    {
        $request->validate(['payload' => 'required|string']);
        $lines   = explode("\n", $request->payload);
        $results = [];
        $applied = 0;

        foreach ($lines as $i => $line) {
            $trimmed = trim($line);
            if (!$trimmed || str_starts_with($trimmed, '#')) continue;

            $sep = ';';
            if (str_contains($trimmed, "\t")) $sep = "\t";
            elseif (!str_contains($trimmed, ';') && str_contains($trimmed, ',')) $sep = ',';

            $parts = explode($sep, $trimmed);
            if (count($parts) < 3) {
                $results[] = ['line' => $i + 1, 'raw' => $trimmed, 'status' => 'error', 'msg' => 'Format tidak lengkap (min 3 kolom)'];
                continue;
            }

            $nomorBayar    = trim($parts[0]);
            $tanggalBayarR = trim($parts[1]);
            $nomorNota     = trim($parts[2]);

            if (!$nomorBayar || !$nomorNota) {
                $results[] = ['line' => $i + 1, 'raw' => $trimmed, 'status' => 'error', 'msg' => 'Nomor bayar/nota kosong'];
                continue;
            }

            $invoice = SewaInvoice::where('nomor_invoice', $nomorNota)->first();
            if (!$invoice) {
                $results[] = ['line' => $i + 1, 'raw' => $trimmed, 'status' => 'error', 'msg' => "Nota \"{$nomorNota}\" tidak ditemukan"];
                continue;
            }

            $tglBayar = now()->format('Y-m-d');
            if ($tanggalBayarR) {
                $parsed = $this->parseFlexDate($tanggalBayarR);
                if ($parsed) $tglBayar = $parsed;
            }

            DB::transaction(function () use ($invoice, $nomorBayar, $tglBayar) {
                $invoice->update(['status_pembayaran' => 'Lunas']);
                SewaTagihan::where('nomor_invoice_grup', $invoice->nomor_invoice)->update([
                    'status_bayar'  => 'Lunas',
                    'tanggal_bayar' => $tglBayar,
                    'nomor_bayar'   => $nomorBayar,
                ]);
            });
            $applied++;
            $results[] = ['line' => $i + 1, 'raw' => $trimmed, 'status' => 'ok', 'msg' => "Nota {$nomorNota} -> Lunas"];
        }

        return response()->json(['success' => true, 'applied' => $applied, 'results' => $results]);
    }

    private function parseFlexDate($str)
    {
        $str = trim($str);
        if (preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/', $str, $m)) {
            return sprintf('%04d-%02d-%02d', $m[3], $m[2], $m[1]);
        }
        if (preg_match('/^(\d{4})[\/\-](\d{1,2})[\/\-](\d{1,2})$/', $str, $m)) {
            return sprintf('%04d-%02d-%02d', $m[1], $m[2], $m[3]);
        }
        return null;
    }

    // -----------------------------------------------------------------------
    // BACKUP / RESTORE JSON
    // -----------------------------------------------------------------------

    public function exportJson()
    {
        $data = [
            'exported_at' => now()->toISOString(),
            'customers'   => SewaCustomer::all()->toArray(),
            'tipes'       => SewaTipe::all()->toArray(),
            'ukurans'     => SewaUkuran::all()->toArray(),
            'kontainers'  => SewaKontainer::all()->toArray(),
            'tarifs'      => SewaTarif::all()->toArray(),
            'sewas'       => SewaTransaksi::all()->toArray(),
            'invoices'    => SewaInvoice::all()->toArray(),
            'tagihans'    => SewaTagihan::all()->toArray(),
        ];
        return response()->json($data)->withHeaders([
            'Content-Disposition' => 'attachment; filename="backup_sewa_kontainer_' . now()->format('Y-m-d') . '.json"',
        ]);
    }

    public function importJson(Request $request)
    {
        $request->validate(['backup_file' => 'required|file']);
        $content = json_decode(file_get_contents($request->file('backup_file')->getRealPath()), true);
        if (!$content || !is_array($content)) {
            return response()->json(['success' => false, 'message' => 'Format JSON tidak valid.'], 422);
        }
        DB::transaction(function () use ($content) {
            if (!empty($content['customers'])) { SewaCustomer::truncate(); foreach ($content['customers'] as $c) SewaCustomer::create($c); }
            if (!empty($content['tipes']))     { SewaTipe::truncate();     foreach ($content['tipes'] as $t)     SewaTipe::create($t); }
            if (!empty($content['ukurans']))   { SewaUkuran::truncate();   foreach ($content['ukurans'] as $u)   SewaUkuran::create($u); }
            if (!empty($content['kontainers'])){ SewaKontainer::truncate();foreach ($content['kontainers'] as $k) SewaKontainer::create($k); }
            if (!empty($content['tarifs']))    { SewaTarif::truncate();    foreach ($content['tarifs'] as $tr)   SewaTarif::create($tr); }
            if (!empty($content['sewas']))     {
                SewaTagihan::truncate(); SewaInvoice::truncate(); SewaTransaksi::truncate();
                foreach ($content['sewas'] as $s) SewaTransaksi::create($s);
            }
            if (!empty($content['invoices']))  { SewaInvoice::truncate(); foreach ($content['invoices'] as $inv) { unset($inv['list_id_tagihan']); SewaInvoice::create($inv); } }
            if (!empty($content['tagihans']))  { SewaTagihan::truncate(); foreach ($content['tagihans'] as $t) SewaTagihan::create($t); }
        });
        return response()->json(['success' => true, 'message' => 'Data berhasil dipulihkan dari backup JSON.']);
    }

    // -----------------------------------------------------------------------
    // AJAX HELPERS
    // -----------------------------------------------------------------------

    public function getKontainerInfo($noKontainer)
    {
        $k = SewaKontainer::with(['customer', 'tipe', 'ukuran'])->find($noKontainer);
        if (!$k) return response()->json(['kontainer' => null]);
        $activeTarif = SewaTarif::where('id_customer', $k->id_customer)
            ->where('id_tipe', $k->id_tipe)
            ->where('id_ukuran', $k->id_ukuran)
            ->whereNull('tanggal_akhir_berlaku')
            ->first();
        return response()->json(['kontainer' => $k, 'activeTarif' => $activeTarif]);
    }
}
