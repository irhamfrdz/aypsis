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

        $customers = SewaCustomer::orderBy('nama_customer')->get();
        $tipes = SewaTipe::orderBy('nama_tipe')->get();
        $ukurans = SewaUkuran::orderBy('deskripsi_ukuran')->get();
        $kontainers = SewaKontainer::with(['customer', 'tipe', 'ukuran'])->get();
        $tarifs = SewaTarif::with(['customer', 'tipe', 'ukuran'])->orderBy('tanggal_mulai_berlaku', 'desc')->get();
        $sewas = SewaTransaksi::with(['customer', 'kontainer.tipe', 'kontainer.ukuran', 'tagihans'])->orderBy('tanggal_sewa', 'desc')->get();
        $invoices = SewaInvoice::with(['customer', 'tagihans'])->orderBy('tanggal_invoice', 'desc')->get();
        $tagihans = SewaTagihan::with(['transaksi.customer', 'transaksi.kontainer'])->orderBy('tanggal_awal', 'desc')->get();

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
        $day = $curr->day;
        $next = clone $curr;
        $next->addMonth(); // JS-like overflow: 30 Jan -> 2 Mar

        if ($next->day !== $day) {
            $nm = $curr->month + 2;
            $ny = $curr->year;
            if ($nm > 12) {
                $nm -= 12;
                $ny++;
            }

            return Carbon::createFromDate($ny, $nm, 1)->startOfDay();
        }

        return $next->startOfDay();
    }

    private function dateToExcelSerial(Carbon $date)
    {
        $base = Carbon::create(1899, 12, 30, 0, 0, 0, 'UTC');
        $d = Carbon::createFromDate($date->year, $date->month, $date->day, 'UTC');

        return (int) $base->diffInDays($d);
    }

    private function generatePeriodsForSewa(SewaTransaksi $sewa, Carbon $today)
    {
        $startLocal = Carbon::parse($sewa->tanggal_sewa)->startOfDay();
        $limitLocal = $sewa->tanggal_kembali
            ? Carbon::parse($sewa->tanggal_kembali)->startOfDay()
            : $today->copy()->startOfDay();

        $containerPart = preg_replace('/\s+/', '', trim($sewa->no_kontainer));
        $serialPart = $this->dateToExcelSerial($startLocal);

        $currStart = $startLocal->copy();
        $index = 1;

        while ($currStart <= $limitLocal || $index === 1) {
            $nextStart = $this->getNextCycleStart($currStart);
            $normalEndDate = $nextStart->copy()->subDay();

            $monthSuffix = str_pad($index, 2, '0', STR_PAD_LEFT);
            $id_tagihan = "{$containerPart}{$serialPart}{$monthSuffix}";

            if ($normalEndDate <= $limitLocal) {
                $days = $currStart->diffInDays($normalEndDate) + 1;
                $amount = $sewa->jenis_tarif === 'Bulanan' ? $sewa->tarif_bulanan : $days * $sewa->tarif_harian;
                $tipeTarif = $sewa->jenis_tarif === 'Bulanan' ? 'BULANAN' : 'HARIAN';
                $this->upsertTagihan($id_tagihan, $sewa->id_sewa, $index, $currStart, $normalEndDate, $days, $tipeTarif, $amount);
                $currStart = $nextStart->copy();
                $index++;
            } else {
                $endLocal = $limitLocal < $currStart ? $currStart->copy() : $limitLocal->copy();
                $days = $currStart->diffInDays($endLocal) + 1;
                $amount = 0;
                $tipeTarif = 'PRORATE';

                if ($sewa->jenis_tarif === 'Harian') {
                    $amount = $days * $sewa->tarif_harian;
                    $tipeTarif = 'HARIAN';
                } else {
                    $sMonth = $currStart->month;
                    $baseDays = 30;
                    if ($sMonth === 2) {
                        $baseDays = $this->isLeapYear($currStart->year) ? 29 : 28;
                    }
                    if ($days === $baseDays) {
                        $amount = $sewa->tarif_bulanan;
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
        if (! $existing) {
            SewaTagihan::create([
                'id_tagihan' => $id,
                'id_sewa' => $idSewa,
                'bulan_ke' => $bulanKe,
                'tanggal_awal' => $start->format('Y-m-d'),
                'tanggal_akhir' => $end->format('Y-m-d'),
                'jumlah_hari' => $days,
                'tipe_tarif' => $tipeTarif,
                'jumlah_tagihan' => $amount,
                'status_bayar' => 'Belum Ditagih',
            ]);
        } elseif ($existing->status_bayar === 'Belum Ditagih') {
            $existing->update([
                'tanggal_awal' => $start->format('Y-m-d'),
                'tanggal_akhir' => $end->format('Y-m-d'),
                'jumlah_hari' => $days,
                'tipe_tarif' => $tipeTarif,
                'jumlah_tagihan' => $amount,
            ]);
        }
    }

    public function syncAllTagihans()
    {
        $today = Carbon::now('Asia/Jakarta');
        SewaTransaksi::all()->each(fn ($s) => $this->generatePeriodsForSewa($s, $today));
    }

    // -----------------------------------------------------------------------
    // MASTER: CUSTOMER
    // -----------------------------------------------------------------------

    public function storeCustomer(Request $request)
    {
        $request->validate(['nama_customer' => 'required|string|max:255']);
        SewaCustomer::create(['id_customer' => 'cust_'.Str::random(8), 'nama_customer' => $request->nama_customer]);

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
        SewaTipe::create(['id_tipe' => 'tipe_'.Str::random(8), 'nama_tipe' => $request->nama_tipe]);

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
        if (is_numeric($raw)) {
            $raw = $raw."'";
        }
        if (SewaUkuran::where('deskripsi_ukuran', $raw)->exists()) {
            return response()->json(['success' => false, 'message' => "Ukuran \"{$raw}\" sudah ada."], 422);
        }
        SewaUkuran::create(['id_ukuran' => 'sz_'.Str::random(8), 'deskripsi_ukuran' => $raw]);

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
            'id_customer' => 'required|string|exists:sewa_customers,id_customer',
            'id_tipe' => 'required|string|exists:sewa_tipes,id_tipe',
            'id_ukuran' => 'required|string|exists:sewa_ukurans,id_ukuran',
        ]);
        SewaKontainer::create([
            'no_kontainer' => strtoupper(preg_replace('/\s+/', '', trim($request->no_kontainer))),
            'id_customer' => $request->id_customer,
            'id_tipe' => $request->id_tipe,
            'id_ukuran' => $request->id_ukuran,
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
            'id_customer' => 'required|string|exists:sewa_customers,id_customer',
            'id_tipe' => 'required|string|exists:sewa_tipes,id_tipe',
            'id_ukuran' => 'required|string|exists:sewa_ukurans,id_ukuran',
            'tarif_bulanan' => 'required|numeric|min:0',
            'tarif_harian' => 'required|numeric|min:0',
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
            'id_tarif' => 'trf_'.Str::random(8),
            'id_customer' => $request->id_customer,
            'id_tipe' => $request->id_tipe,
            'id_ukuran' => $request->id_ukuran,
            'tarif_bulanan' => $request->tarif_bulanan,
            'tarif_harian' => $request->tarif_harian,
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
            'no_kontainer' => 'required|string|exists:sewa_kontainers,no_kontainer',
            'id_customer' => 'required|string|exists:sewa_customers,id_customer',
            'tanggal_sewa' => 'required|date',
            'tarif_bulanan' => 'required|numeric|min:0',
            'tarif_harian' => 'required|numeric|min:0',
            'jenis_tarif' => 'required|in:Bulanan,Harian',
        ]);

        if (SewaTransaksi::where('no_kontainer', $request->no_kontainer)->where('status_sewa', 'Aktif')->exists()) {
            return response()->json(['success' => false, 'message' => 'Kontainer masih Aktif disewa. Kembalikan terlebih dahulu.'], 422);
        }

        $noKon = preg_replace('/\s+/', '', trim($request->no_kontainer));
        $serial = $this->dateToExcelSerial(Carbon::parse($request->tanggal_sewa));
        $cycle = SewaTransaksi::where('no_kontainer', $request->no_kontainer)->count() + 1;
        $id_sewa = "{$noKon}{$serial}".str_pad($cycle, 2, '0', STR_PAD_LEFT);

        $sewa = SewaTransaksi::create([
            'id_sewa' => $id_sewa,
            'no_kontainer' => $request->no_kontainer,
            'id_customer' => $request->id_customer,
            'tanggal_sewa' => $request->tanggal_sewa,
            'tanggal_kembali' => null,
            'tarif_bulanan' => $request->tarif_bulanan,
            'tarif_harian' => $request->tarif_harian,
            'jenis_tarif' => $request->jenis_tarif,
            'status_sewa' => 'Aktif',
            'catatan' => $request->catatan,
        ]);

        $this->generatePeriodsForSewa($sewa, Carbon::now('Asia/Jakarta'));

        return response()->json(['success' => true, 'id_sewa' => $id_sewa]);
    }

    public function updateSewa(Request $request, $id)
    {
        $request->validate([
            'tanggal_sewa' => 'required|date',
            'tanggal_kembali' => 'nullable|date|after_or_equal:tanggal_sewa',
            'tarif_bulanan' => 'required|numeric|min:0',
            'tarif_harian' => 'required|numeric|min:0',
            'jenis_tarif' => 'required|in:Bulanan,Harian',
        ]);
        $sewa = SewaTransaksi::findOrFail($id);
        $sewa->update([
            'tanggal_sewa' => $request->tanggal_sewa,
            'tanggal_kembali' => $request->tanggal_kembali ?: null,
            'tarif_bulanan' => $request->tarif_bulanan,
            'tarif_harian' => $request->tarif_harian,
            'jenis_tarif' => $request->jenis_tarif,
            'status_sewa' => $request->tanggal_kembali ? 'Selesai' : 'Aktif',
            'catatan' => $request->catatan,
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
            'status_bayar' => 'nullable|string|in:Belum Ditagih,Pranota,Belum Bayar,Lunas',
            'tanggal_tagihan' => 'nullable|date',
            'tanggal_bayar' => 'nullable|date',
            'nomor_invoice_grup' => 'nullable|string',
            'jumlah_tagihan_override' => 'nullable|numeric',
            'jumlah_bayar' => 'nullable|numeric',
            'keterangan_selisih' => 'nullable|string',
            'ppn' => 'nullable|numeric',
            'pph' => 'nullable|numeric',
            'nomor_bayar' => 'nullable|string',
        ]);

        $tagihan = SewaTagihan::findOrFail($id);
        $data = $request->only([
            'status_bayar', 'tanggal_tagihan', 'tanggal_bayar', 'nomor_invoice_grup',
            'jumlah_tagihan_override', 'jumlah_bayar', 'keterangan_selisih', 'ppn', 'pph', 'nomor_bayar',
        ]);

        // Auto-calc selisih & taxes when override amount set
        if (array_key_exists('jumlah_tagihan_override', $data) && $data['jumlah_tagihan_override'] !== null) {
            $ov = floatval($data['jumlah_tagihan_override']);
            $data['ppn'] = round($ov * 0.11);
            $data['pph'] = round($ov * 0.02);
            $data['selisih_pembayaran'] = $ov - $tagihan->jumlah_tagihan;
        }

        // Auto-timestamps
        if (! empty($data['status_bayar'])) {
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
            'nomor_invoice' => 'required|string|unique:sewa_invoices,nomor_invoice',
            'id_customer' => 'required|string|exists:sewa_customers,id_customer',
            'tanggal_invoice' => 'required|date',
            'status_pembayaran' => 'required|string|in:Belum Bayar,Lunas',
            'list_id_tagihan' => 'required|array|min:1',
            'adjustment_biaya' => 'nullable|numeric',
            'adjustment_keterangan' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request) {
            SewaInvoice::create([
                'nomor_invoice' => $request->nomor_invoice,
                'id_customer' => $request->id_customer,
                'tanggal_invoice' => $request->tanggal_invoice,
                'status_pembayaran' => $request->status_pembayaran,
                'deskripsi' => $request->deskripsi,
                'adjustment_biaya' => $request->adjustment_biaya ?? 0,
                'adjustment_keterangan' => $request->adjustment_keterangan,
            ]);

            $newStatus = $request->status_pembayaran === 'Lunas' ? 'Lunas' : 'Belum Bayar';
            SewaTagihan::whereIn('id_tagihan', $request->list_id_tagihan)->update([
                'nomor_invoice_grup' => $request->nomor_invoice,
                'status_bayar' => $newStatus,
                'tanggal_tagihan' => now()->format('Y-m-d'),
            ]);
        });

        return response()->json(['success' => true]);
    }

    public function updateInvoice(Request $request, $nomor)
    {
        $request->validate([
            'status_pembayaran' => 'required|string|in:Belum Bayar,Lunas',
            'adjustment_biaya' => 'nullable|numeric',
            'adjustment_keterangan' => 'nullable|string',
            'tanggal_bayar' => 'nullable|date',
            'nomor_bayar' => 'nullable|string',
        ]);
        DB::transaction(function () use ($request, $nomor) {
            SewaInvoice::findOrFail($nomor)->update([
                'status_pembayaran' => $request->status_pembayaran,
                'adjustment_biaya' => $request->adjustment_biaya ?? 0,
                'adjustment_keterangan' => $request->adjustment_keterangan,
            ]);
            $upd = ['status_bayar' => $request->status_pembayaran === 'Lunas' ? 'Lunas' : 'Belum Bayar'];
            if ($request->status_pembayaran === 'Lunas') {
                $upd['tanggal_bayar'] = $request->tanggal_bayar ?: now()->format('Y-m-d');
                if ($request->nomor_bayar) {
                    $upd['nomor_bayar'] = $request->nomor_bayar;
                }
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
                'status_bayar' => 'Belum Ditagih',
                'tanggal_tagihan' => null,
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
        $lines = explode("\n", $request->payload);
        $results = [];
        $applied = 0;

        foreach ($lines as $i => $line) {
            $trimmed = trim($line);
            if (! $trimmed || str_starts_with($trimmed, '#')) {
                continue;
            }

            $sep = ';';
            if (str_contains($trimmed, "\t")) {
                $sep = "\t";
            } elseif (! str_contains($trimmed, ';') && str_contains($trimmed, ',')) {
                $sep = ',';
            }

            $parts = explode($sep, $trimmed);
            if (count($parts) < 3) {
                $results[] = ['line' => $i + 1, 'raw' => $trimmed, 'status' => 'error', 'msg' => 'Format tidak lengkap (min 3 kolom)'];

                continue;
            }

            $nomorBayar = trim($parts[0]);
            $tanggalBayarR = trim($parts[1]);
            $nomorNota = trim($parts[2]);

            if (! $nomorBayar || ! $nomorNota) {
                $results[] = ['line' => $i + 1, 'raw' => $trimmed, 'status' => 'error', 'msg' => 'Nomor bayar/nota kosong'];

                continue;
            }

            $invoice = SewaInvoice::where('nomor_invoice', $nomorNota)->first();
            if (! $invoice) {
                $results[] = ['line' => $i + 1, 'raw' => $trimmed, 'status' => 'error', 'msg' => "Nota \"{$nomorNota}\" tidak ditemukan"];

                continue;
            }

            $tglBayar = now()->format('Y-m-d');
            if ($tanggalBayarR) {
                $parsed = $this->parseFlexDate($tanggalBayarR);
                if ($parsed) {
                    $tglBayar = $parsed;
                }
            }

            DB::transaction(function () use ($invoice, $nomorBayar, $tglBayar) {
                $invoice->update(['status_pembayaran' => 'Lunas']);
                SewaTagihan::where('nomor_invoice_grup', $invoice->nomor_invoice)->update([
                    'status_bayar' => 'Lunas',
                    'tanggal_bayar' => $tglBayar,
                    'nomor_bayar' => $nomorBayar,
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
        if (! $str) {
            return null;
        }

        // dd/mm/yyyy or dd-mm-yyyy
        if (preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/', $str, $m)) {
            return sprintf('%04d-%02d-%02d', $m[3], $m[2], $m[1]);
        }
        // yyyy/mm/dd or yyyy-mm-dd
        if (preg_match('/^(\d{4})[\/\-](\d{1,2})[\/\-](\d{1,2})$/', $str, $m)) {
            return sprintf('%04d-%02d-%02d', $m[1], $m[2], $m[3]);
        }
        // Excel serial number (pure integer > 1000)
        if (preg_match('/^\d+(\.\d+)?$/', $str) && floatval($str) > 1000) {
            $serial = floatval($str);
            $base = \Carbon\Carbon::create(1899, 12, 30, 0, 0, 0, 'UTC');
            $d = $base->copy()->addDays((int) $serial);

            return $d->format('Y-m-d');
        }
        // Indonesian text: "21 Mei 25" or "21 Mei 2025" or "21 Apr 2024"
        $indoMonths = [
            'jan' => 1, 'januari' => 1, 'january' => 1,
            'feb' => 2, 'februari' => 2, 'february' => 2,
            'mar' => 3, 'maret' => 3, 'march' => 3,
            'apr' => 4, 'april' => 4,
            'mei' => 5, 'may' => 5,
            'jun' => 6, 'juni' => 6, 'june' => 6,
            'jul' => 7, 'juli' => 7, 'july' => 7,
            'agt' => 8, 'agustus' => 8, 'aug' => 8, 'august' => 8,
            'sep' => 9, 'september' => 9,
            'okt' => 10, 'oktober' => 10, 'oct' => 10, 'october' => 10,
            'nov' => 11, 'november' => 11,
            'des' => 12, 'desember' => 12, 'dec' => 12, 'december' => 12,
        ];
        if (preg_match('/^(\d{1,2})\s+([a-zA-Z]+)\s+(\d{2,4})$/', $str, $m)) {
            $day = (int) $m[1];
            $month = strtolower($m[2]);
            $year = (int) $m[3];
            if ($year < 100) {
                $year += 2000;
            }
            $mIdx = $indoMonths[substr($month, 0, 3)] ?? ($indoMonths[$month] ?? null);
            if ($mIdx && $day >= 1 && $day <= 31) {
                return sprintf('%04d-%02d-%02d', $year, $mIdx, $day);
            }
        }

        return null;
    }

    // -----------------------------------------------------------------------
    // BULK IMPORT (8 tipe)
    // -----------------------------------------------------------------------

    public function bulkImport(Request $request)
    {
        $request->validate([
            'type' => 'required|in:customer,tipe,ukuran,kontainer,tarif,sewa,pembayaran,pelunasan',
            'payload' => 'required|string',
        ]);

        $type = $request->type;
        $lines = explode("\n", $request->payload);
        $logs = [];
        $successCount = 0;
        $failedLines = [];

        // ---- Strict lookup helpers ----
        $getCustomer = function ($name) {
            $c = SewaCustomer::whereRaw('LOWER(nama_customer) = ?', [strtolower(trim($name))])->first();
            if (! $c) {
                throw new \Exception('Customer/Vendor "'.trim($name).'" tidak ditemukan di Master.');
            }

            return $c;
        };
        $getTipe = function ($name) {
            $t = SewaTipe::whereRaw('LOWER(nama_tipe) = ?', [strtolower(trim($name))])->first();
            if (! $t) {
                throw new \Exception('Tipe "'.trim($name).'" tidak ditemukan di Master Tipe.');
            }

            return $t;
        };
        $getUkuran = function ($desc) {
            $clean = trim($desc);
            if (is_numeric($clean)) {
                $clean = $clean."'";
            }
            $u = SewaUkuran::where('deskripsi_ukuran', $clean)->first();
            if (! $u) {
                throw new \Exception("Ukuran \"$clean\" tidak ditemukan di Master Ukuran.");
            }

            return $u;
        };

        foreach ($lines as $idx => $line) {
            $lineNum = $idx + 1;
            $trimmed = trim($line);
            if (! $trimmed || str_starts_with($trimmed, '#')) {
                continue;
            }

            try {
                switch ($type) {
                    // 1. Customer
                    case 'customer':
                        $name = $trimmed;
                        if (SewaCustomer::whereRaw('LOWER(nama_customer) = ?', [strtolower($name)])->exists()) {
                            throw new \Exception("Customer \"$name\" sudah terdaftar.");
                        }
                        SewaCustomer::create(['id_customer' => 'cust_'.Str::random(8), 'nama_customer' => $name]);
                        $successCount++;
                        break;

                        // 2. Tipe
                    case 'tipe':
                        if (SewaTipe::whereRaw('LOWER(nama_tipe) = ?', [strtolower($trimmed)])->exists()) {
                            throw new \Exception("Tipe \"$trimmed\" sudah terdaftar.");
                        }
                        SewaTipe::create(['id_tipe' => 'tipe_'.Str::random(8), 'nama_tipe' => $trimmed]);
                        $successCount++;
                        break;

                        // 3. Ukuran
                    case 'ukuran':
                        $raw = $trimmed;
                        if (is_numeric($raw)) {
                            $raw = $raw."'";
                        }
                        if (SewaUkuran::where('deskripsi_ukuran', $raw)->exists()) {
                            throw new \Exception("Ukuran \"$raw\" sudah terdaftar.");
                        }
                        SewaUkuran::create(['id_ukuran' => 'sz_'.Str::random(8), 'deskripsi_ukuran' => $raw]);
                        $successCount++;
                        break;

                        // 4. Kontainer
                    case 'kontainer':
                        $parts = array_map('trim', explode(';', $trimmed));
                        if (count($parts) < 4) {
                            throw new \Exception('Format salah. Wajib: NO_KONTAINER ; CUSTOMER ; TIPE ; UKURAN');
                        }
                        $kontNo = strtoupper(preg_replace('/\s+/', '', $parts[0]));
                        if (! $kontNo) {
                            throw new \Exception('No Kontainer kosong');
                        }
                        if (SewaKontainer::where('no_kontainer', $kontNo)->exists()) {
                            throw new \Exception("Kontainer \"$kontNo\" sudah terdaftar.");
                        }
                        $cust = $getCustomer($parts[1]);
                        $tipe = $getTipe($parts[2]);
                        $ukuran = $getUkuran($parts[3]);
                        SewaKontainer::create([
                            'no_kontainer' => $kontNo,
                            'id_customer' => $cust->id_customer,
                            'id_tipe' => $tipe->id_tipe,
                            'id_ukuran' => $ukuran->id_ukuran,
                            'status_aktif' => true,
                        ]);
                        $successCount++;
                        break;

                        // 5. Tarif
                    case 'tarif':
                        $parts = array_map('trim', explode(';', $trimmed));
                        if (count($parts) < 5) {
                            throw new \Exception('Format: CUSTOMER ; TIPE ; UKURAN ; TARIF_BULANAN ; TARIF_HARIAN ; [TGL_MULAI]');
                        }
                        $cust = $getCustomer($parts[0]);
                        $tipe = $getTipe($parts[1]);
                        $ukuran = $getUkuran($parts[2]);
                        $tarifBulan = floatval(preg_replace('/[^0-9.]/', '', $parts[3]));
                        $tarifHari = floatval(preg_replace('/[^0-9.]/', '', $parts[4]));
                        if ($tarifBulan <= 0 && $tarifHari <= 0) {
                            throw new \Exception('Tarif Bulanan atau Harian harus > 0');
                        }
                        $tglMulai = isset($parts[5]) && $parts[5]
                            ? $this->parseFlexDate($parts[5])
                            : now()->format('Y-m-d');
                        if (! $tglMulai) {
                            throw new \Exception("Format tanggal tidak valid: {$parts[5]}");
                        }
                        // Close previous active tarif
                        $prev = SewaTarif::where('id_customer', $cust->id_customer)
                            ->where('id_tipe', $tipe->id_tipe)
                            ->where('id_ukuran', $ukuran->id_ukuran)
                            ->whereNull('tanggal_akhir_berlaku')
                            ->first();
                        if ($prev) {
                            $prev->update(['tanggal_akhir_berlaku' => Carbon::parse($tglMulai)->subDay()->format('Y-m-d')]);
                        }
                        SewaTarif::create([
                            'id_tarif' => 'trf_'.Str::random(8),
                            'id_customer' => $cust->id_customer,
                            'id_tipe' => $tipe->id_tipe,
                            'id_ukuran' => $ukuran->id_ukuran,
                            'tarif_bulanan' => $tarifBulan,
                            'tarif_harian' => $tarifHari,
                            'tanggal_mulai_berlaku' => $tglMulai,
                            'tanggal_akhir_berlaku' => null,
                        ]);
                        $successCount++;
                        break;

                        // 6. Sewa
                    case 'sewa':
                        $parts = array_map('trim', explode(';', $trimmed));
                        $kontNo = strtoupper(preg_replace('/\s+/', '', $parts[0] ?? ''));
                        $custNameRaw = $parts[1] ?? '';
                        $tglSewaRaw = $parts[2] ?? '';
                        $tglKembaliRaw = $parts[3] ?? '';
                        $jenisTarif = strtolower($parts[4] ?? 'bulanan') === 'harian' ? 'Harian' : 'Bulanan';

                        if (! $kontNo) {
                            throw new \Exception('No Kontainer kosong');
                        }
                        $kontObj = SewaKontainer::find($kontNo);
                        if (! $kontObj) {
                            throw new \Exception("Kontainer \"$kontNo\" tidak terdaftar di Master.");
                        }

                        // Blank tanggal_sewa = update tanggal_kembali sewa aktif
                        if (! $tglSewaRaw) {
                            if (! $tglKembaliRaw) {
                                throw new \Exception('Kedua tanggal kosong');
                            }
                            $activeSewa = SewaTransaksi::where('no_kontainer', $kontNo)->where('status_sewa', 'Aktif')->first();
                            if (! $activeSewa) {
                                throw new \Exception("Kontainer \"$kontNo\" tidak punya sewa aktif untuk diperbarui.");
                            }
                            $endIso = $this->parseFlexDate($tglKembaliRaw);
                            if (! $endIso) {
                                throw new \Exception("Format tanggal kembali tidak valid: $tglKembaliRaw");
                            }
                            if ($endIso < $activeSewa->tanggal_sewa) {
                                throw new \Exception('Tanggal kembali tidak boleh sebelum tanggal sewa');
                            }
                            $activeSewa->update(['tanggal_kembali' => $endIso, 'status_sewa' => 'Selesai']);
                            $this->generatePeriodsForSewa($activeSewa->fresh(), Carbon::now('Asia/Jakarta'));
                            $successCount++;
                            break;
                        }

                        $cust = $getCustomer($custNameRaw);
                        $startIso = $this->parseFlexDate($tglSewaRaw);
                        if (! $startIso) {
                            throw new \Exception("Format tanggal sewa tidak valid: $tglSewaRaw");
                        }

                        // Check duplicate
                        if (SewaTransaksi::where('no_kontainer', $kontNo)->where('tanggal_sewa', $startIso)->exists()) {
                            throw new \Exception("Duplikat: Sewa \"$kontNo\" dengan tanggal \"$tglSewaRaw\" sudah ada.");
                        }
                        if (SewaTransaksi::where('no_kontainer', $kontNo)->where('status_sewa', 'Aktif')->exists()) {
                            throw new \Exception("Kontainer \"$kontNo\" masih aktif disewa. Kembalikan dahulu.");
                        }

                        $endIso = null;
                        if ($tglKembaliRaw) {
                            $endIso = $this->parseFlexDate($tglKembaliRaw);
                            if (! $endIso) {
                                throw new \Exception("Format tanggal kembali tidak valid: $tglKembaliRaw");
                            }
                            if ($endIso < $startIso) {
                                throw new \Exception('Tanggal kembali tidak boleh sebelum tanggal sewa');
                            }
                        }

                        // Find matching tarif
                        $tarif = SewaTarif::where('id_customer', $cust->id_customer)
                            ->where('id_tipe', $kontObj->id_tipe)
                            ->where('id_ukuran', $kontObj->id_ukuran)
                            ->whereNull('tanggal_akhir_berlaku')
                            ->first();

                        $serial = $this->dateToExcelSerial(Carbon::parse($startIso));
                        $cycle = SewaTransaksi::where('no_kontainer', $kontNo)->count() + 1;
                        $idSewa = "$kontNo$serial".str_pad($cycle, 2, '0', STR_PAD_LEFT);

                        $sewa = SewaTransaksi::create([
                            'id_sewa' => $idSewa,
                            'no_kontainer' => $kontNo,
                            'id_customer' => $cust->id_customer,
                            'tanggal_sewa' => $startIso,
                            'tanggal_kembali' => $endIso,
                            'tarif_bulanan' => $tarif ? $tarif->tarif_bulanan : 0,
                            'tarif_harian' => $tarif ? $tarif->tarif_harian : 0,
                            'jenis_tarif' => $jenisTarif,
                            'status_sewa' => $endIso ? 'Selesai' : 'Aktif',
                        ]);
                        $this->generatePeriodsForSewa($sewa, Carbon::now('Asia/Jakarta'));
                        $successCount++;
                        break;

                        // 7. Pembayaran / Pranota (Import tagihan dari Excel vendor)
                    case 'pembayaran':
                        $parts = array_map('trim', explode(';', $trimmed));
                        if (count($parts) < 3) {
                            throw new \Exception('Format: KONTAINER ; PERIODE ; TAGIHAN ; [No.Tagihan] ; [Tgl.Tagihan] ; [Siklus_Ke]');
                        }

                        $kontNo = strtoupper(preg_replace('/\s+/', '', $parts[0]));
                        $periodeNum = (int) ($parts[1] ?? 0);
                        $tagihanRaw = preg_replace('/[Rp$\s\.]/i', '', $parts[2] ?? '0');
                        $tagihanRaw = str_replace(',', '', $tagihanRaw);
                        $tagihanAmt = floatval($tagihanRaw);
                        $noTagihan = $parts[3] ?? '';
                        $tglTagihanRaw = $parts[4] ?? '';
                        $siklusKeRaw = $parts[5] ?? '';

                        if (! $kontNo) {
                            throw new \Exception('No Kontainer kosong');
                        }
                        if ($periodeNum < 1) {
                            throw new \Exception('Periode harus angka >= 1');
                        }

                        $tglTagihan = $tglTagihanRaw ? ($this->parseFlexDate($tglTagihanRaw) ?? now()->format('Y-m-d')) : now()->format('Y-m-d');

                        // Find matching sewa
                        $sewaList = SewaTransaksi::where('no_kontainer', $kontNo)->orderBy('tanggal_sewa')->get();
                        if ($sewaList->isEmpty()) {
                            throw new \Exception("Kontainer \"$kontNo\" tidak punya transaksi sewa.");
                        }

                        $selectedSewa = null;
                        if ($siklusKeRaw !== '') {
                            $cycleIdx = (int) $siklusKeRaw - 1;
                            if ($cycleIdx < 0 || $cycleIdx >= $sewaList->count()) {
                                throw new \Exception("Siklus ke-{$siklusKeRaw} tidak valid. Kontainer punya {$sewaList->count()} siklus.");
                            }
                            $selectedSewa = $sewaList[$cycleIdx];
                        } else {
                            // Auto-match: sewa yang punya periode ke-N
                            $candidates = $sewaList->filter(function ($sw) use ($periodeNum) {
                                $tagihan = SewaTagihan::where('id_sewa', $sw->id_sewa)->where('bulan_ke', $periodeNum)->first();

                                return $tagihan !== null;
                            });
                            if ($candidates->count() === 1) {
                                $selectedSewa = $candidates->first();
                            } elseif ($candidates->count() > 1) {
                                $info = $candidates->map(fn ($s, $i) => '• Siklus '.($sewaList->search(fn ($x) => $x->id_sewa === $s->id_sewa) + 1).': '.$s->tanggal_sewa)->join(', ');
                                throw new \Exception("KONFLIK SIKLUS: {$info}. Tambahkan \"; [no_siklus]\" di akhir baris.");
                            } else {
                                $selectedSewa = $sewaList->last();
                            }
                        }

                        $tagihan = SewaTagihan::where('id_sewa', $selectedSewa->id_sewa)->where('bulan_ke', $periodeNum)->first();
                        if (! $tagihan) {
                            throw new \Exception("Periode ke-$periodeNum tidak ditemukan pada sewa terpilih (mulai {$selectedSewa->tanggal_sewa}).");
                        }

                        // Duplicate check
                        if ($noTagihan && $tagihan->nomor_invoice_grup && strtolower($tagihan->nomor_invoice_grup) === strtolower($noTagihan)) {
                            throw new \Exception("Duplikat: Tagihan periode $periodeNum sudah ada no. tagihan \"$noTagihan\".");
                        }
                        if ($tagihan->status_bayar !== 'Belum Ditagih') {
                            throw new \Exception("Tagihan periode $periodeNum sudah tercatat (status: {$tagihan->status_bayar}). Gunakan edit manual.");
                        }

                        $ppn = round($tagihanAmt * 0.11);
                        $pph = round($tagihanAmt * 0.02);
                        $tagihan->update([
                            'status_bayar' => 'Pranota',
                            'jumlah_tagihan_override' => $tagihanAmt,
                            'selisih_pembayaran' => $tagihanAmt - $tagihan->jumlah_tagihan,
                            'ppn' => $ppn,
                            'pph' => $pph,
                            'nomor_invoice_grup' => $noTagihan ?: null,
                            'tanggal_tagihan' => $tglTagihan,
                            'keterangan_selisih' => ($tagihanAmt - $tagihan->jumlah_tagihan) != 0 ? 'Selisih dari import' : null,
                        ]);
                        $successCount++;
                        break;

                        // 8. Pelunasan (set draft bayar, status Belum Bayar)
                    case 'pelunasan':
                        $parts = array_map('trim', preg_split('/[;\t]+/', $trimmed));

                        // Detect header row silently
                        $isHeader = preg_match('/^(no|nomor|bukti|tanggal|tgl|nota|invoice|status)/i', $parts[0] ?? '');
                        if ($isHeader) {
                            break;
                        }

                        // Handle optional row number prefix
                        $nomorBayar = '';
                        $tglBayarRaw = '';
                        $nomorNota = '';

                        if (count($parts) >= 4) {
                            // Check if col[2] is a date (then col[0] is row number)
                            $d2 = $this->parseFlexDate($parts[2]);
                            $d1 = $this->parseFlexDate($parts[1]);
                            if ($d2) {
                                $nomorBayar = $parts[1];
                                $tglBayarRaw = $parts[2];
                                $nomorNota = $parts[3];
                            } elseif ($d1) {
                                $nomorBayar = $parts[0];
                                $tglBayarRaw = $parts[1];
                                $nomorNota = $parts[2];
                            } else {
                                $nomorBayar = $parts[1];
                                $tglBayarRaw = $parts[2];
                                $nomorNota = $parts[3];
                            }
                        } elseif (count($parts) >= 3) {
                            $nomorBayar = $parts[0];
                            $tglBayarRaw = $parts[1];
                            $nomorNota = $parts[2];
                        } else {
                            throw new \Exception('Format: No Bukti Bayar ; Tanggal Bayar ; Nomor Nota');
                        }

                        if (! $nomorBayar) {
                            throw new \Exception('Nomor Bukti Bayar kosong');
                        }
                        if (! $nomorNota) {
                            throw new \Exception('Nomor Nota kosong');
                        }

                        $tglBayar = $tglBayarRaw ? ($this->parseFlexDate($tglBayarRaw) ?? now()->format('Y-m-d')) : now()->format('Y-m-d');

                        // Find invoice or tagihans by nomor nota
                        $invoice = SewaInvoice::where('nomor_invoice', $nomorNota)->first();
                        $tagihansQ = SewaTagihan::where('nomor_invoice_grup', $nomorNota);
                        if ($invoice) {
                            $tagihansQ->orWhereIn('id_tagihan', $invoice->tagihans->pluck('id_tagihan'));
                        }
                        $tagihans = $tagihansQ->get();

                        if ($tagihans->isEmpty() && ! $invoice) {
                            throw new \Exception("Nomor Nota \"$nomorNota\" tidak ditemukan.");
                        }

                        $tagihans->each(fn ($t) => $t->update([
                            'status_bayar' => 'Belum Bayar',
                            'tanggal_bayar' => $tglBayar,
                            'nomor_bayar' => $nomorBayar,
                        ]));

                        if ($invoice) {
                            $invoice->update(['status_pembayaran' => 'Belum Bayar']);
                        }

                        $successCount++;
                        break;

                }
                $logs[] = ['line' => $lineNum, 'raw' => $trimmed, 'status' => 'ok', 'error' => ''];
            } catch (\Exception $e) {
                $logs[] = ['line' => $lineNum, 'raw' => $trimmed, 'status' => 'error', 'error' => $e->getMessage()];
                $failedLines[] = $line;
            }
        }

        return response()->json([
            'success' => true,
            'success_count' => $successCount,
            'logs' => $logs,
            'failed_lines' => implode("\n", $failedLines),
        ]);
    }

    public function previewImport(Request $request)
    {
        $request->validate(['type' => 'required|string', 'payload' => 'required|string']);
        // For pelunasan preview: parse without saving
        $lines = explode("\n", $request->payload);
        $preview = [];

        foreach ($lines as $idx => $line) {
            $trimmed = trim($line);
            if (! $trimmed || str_starts_with($trimmed, '#')) {
                continue;
            }

            $parts = array_map('trim', preg_split('/[;\t]+/', $trimmed));
            $isHeader = preg_match('/^(no|nomor|bukti|tanggal|tgl|nota|invoice|status)/i', $parts[0] ?? '');
            if ($isHeader) {
                continue;
            }

            $nomorBayar = '';
            $tglBayarRaw = '';
            $nomorNota = '';
            if (count($parts) >= 4) {
                $d2 = $this->parseFlexDate($parts[2]);
                $d1 = $this->parseFlexDate($parts[1]);
                if ($d2) {
                    $nomorBayar = $parts[1];
                    $tglBayarRaw = $parts[2];
                    $nomorNota = $parts[3];
                } elseif ($d1) {
                    $nomorBayar = $parts[0];
                    $tglBayarRaw = $parts[1];
                    $nomorNota = $parts[2];
                } else {
                    $nomorBayar = $parts[1];
                    $tglBayarRaw = $parts[2];
                    $nomorNota = $parts[3];
                }
            } elseif (count($parts) >= 3) {
                $nomorBayar = $parts[0];
                $tglBayarRaw = $parts[1];
                $nomorNota = $parts[2];
            } else {
                $preview[] = ['line' => $idx + 1, 'raw' => $trimmed, 'is_valid' => false, 'error' => 'Format kolom tidak lengkap', 'nomor_nota' => '', 'customer' => '', 'grand_total' => 0, 'tanggal_bayar' => ''];

                continue;
            }

            $tglBayar = $tglBayarRaw ? ($this->parseFlexDate($tglBayarRaw) ?? now()->format('Y-m-d')) : now()->format('Y-m-d');
            $invoice = SewaInvoice::with('tagihans')->where('nomor_invoice', $nomorNota)->first();
            $tagihans = SewaTagihan::where('nomor_invoice_grup', $nomorNota)->get();
            $allTagihans = $invoice ? $tagihans->merge($invoice->tagihans)->unique('id_tagihan') : $tagihans;

            if ($allTagihans->isEmpty() && ! $invoice) {
                $preview[] = ['line' => $idx + 1, 'raw' => $trimmed, 'is_valid' => false, 'error' => "Nota \"$nomorNota\" tidak ditemukan", 'nomor_nota' => $nomorNota, 'customer' => '', 'grand_total' => 0, 'tanggal_bayar' => $tglBayar];

                continue;
            }

            $customerId = $invoice ? $invoice->id_customer : (SewaTransaksi::find($allTagihans->first()->id_sewa)?->id_customer ?? '');
            $customerName = SewaCustomer::find($customerId)?->nama_customer ?? '-';
            $grandTotal = $allTagihans->sum(fn ($t) => $t->jumlah_tagihan_override ?? $t->jumlah_tagihan);

            $preview[] = [
                'line' => $idx + 1,
                'raw' => $trimmed,
                'is_valid' => true,
                'error' => '',
                'nomor_nota' => $nomorNota,
                'nomor_bayar' => $nomorBayar,
                'customer' => $customerName,
                'grand_total' => $grandTotal,
                'tanggal_bayar' => $tglBayar,
                'jml_tagihan' => $allTagihans->count(),
            ];
        }

        return response()->json(['success' => true, 'preview' => $preview]);
    }

    // -----------------------------------------------------------------------
    // BACKUP / RESTORE JSON
    // -----------------------------------------------------------------------

    public function exportJson()
    {
        $data = [
            'exported_at' => now()->toISOString(),
            'customers' => SewaCustomer::all()->toArray(),
            'tipes' => SewaTipe::all()->toArray(),
            'ukurans' => SewaUkuran::all()->toArray(),
            'kontainers' => SewaKontainer::all()->toArray(),
            'tarifs' => SewaTarif::all()->toArray(),
            'sewas' => SewaTransaksi::all()->toArray(),
            'invoices' => SewaInvoice::all()->toArray(),
            'tagihans' => SewaTagihan::all()->toArray(),
        ];

        return response()->json($data)->withHeaders([
            'Content-Disposition' => 'attachment; filename="backup_sewa_kontainer_'.now()->format('Y-m-d').'.json"',
        ]);
    }

    public function importJson(Request $request)
    {
        $request->validate(['backup_file' => 'required|file']);
        $content = json_decode(file_get_contents($request->file('backup_file')->getRealPath()), true);
        if (! $content || ! is_array($content)) {
            return response()->json(['success' => false, 'message' => 'Format JSON tidak valid.'], 422);
        }
        DB::transaction(function () use ($content) {
            if (! empty($content['customers'])) {
                SewaCustomer::truncate();
                foreach ($content['customers'] as $c) {
                    SewaCustomer::create($c);
                }
            }
            if (! empty($content['tipes'])) {
                SewaTipe::truncate();
                foreach ($content['tipes'] as $t) {
                    SewaTipe::create($t);
                }
            }
            if (! empty($content['ukurans'])) {
                SewaUkuran::truncate();
                foreach ($content['ukurans'] as $u) {
                    SewaUkuran::create($u);
                }
            }
            if (! empty($content['kontainers'])) {
                SewaKontainer::truncate();
                foreach ($content['kontainers'] as $k) {
                    SewaKontainer::create($k);
                }
            }
            if (! empty($content['tarifs'])) {
                SewaTarif::truncate();
                foreach ($content['tarifs'] as $tr) {
                    SewaTarif::create($tr);
                }
            }
            if (! empty($content['sewas'])) {
                SewaTagihan::truncate();
                SewaInvoice::truncate();
                SewaTransaksi::truncate();
                foreach ($content['sewas'] as $s) {
                    SewaTransaksi::create($s);
                }
            }
            if (! empty($content['invoices'])) {
                SewaInvoice::truncate();
                foreach ($content['invoices'] as $inv) {
                    unset($inv['list_id_tagihan']);
                    SewaInvoice::create($inv);
                }
            }
            if (! empty($content['tagihans'])) {
                SewaTagihan::truncate();
                foreach ($content['tagihans'] as $t) {
                    SewaTagihan::create($t);
                }
            }
        });

        return response()->json(['success' => true, 'message' => 'Data berhasil dipulihkan dari backup JSON.']);
    }

    // -----------------------------------------------------------------------
    // AJAX HELPERS
    // -----------------------------------------------------------------------

    public function getKontainerInfo($noKontainer)
    {
        $k = SewaKontainer::with(['customer', 'tipe', 'ukuran'])->find($noKontainer);
        if (! $k) {
            return response()->json(['kontainer' => null]);
        }
        $activeTarif = SewaTarif::where('id_customer', $k->id_customer)
            ->where('id_tipe', $k->id_tipe)
            ->where('id_ukuran', $k->id_ukuran)
            ->whereNull('tanggal_akhir_berlaku')
            ->first();

        return response()->json(['kontainer' => $k, 'activeTarif' => $activeTarif]);
    }

    public function wipeData()
    {
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        SewaTagihan::truncate();
        SewaInvoice::truncate();
        SewaTransaksi::truncate();
        SewaTarif::truncate();
        SewaKontainer::truncate();
        SewaUkuran::truncate();
        SewaTipe::truncate();
        SewaCustomer::truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        return response()->json(['success' => true, 'message' => 'Semua data sewa kontainer berhasil dibersihkan!']);
    }
}
