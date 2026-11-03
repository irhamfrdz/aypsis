<?php

namespace App\Http\Controllers;

use App\Models\Coa;
use App\Models\PembayaranPranotaObAntarGudang;
use App\Models\PranotaObAntarGudang;
use App\Services\CoaTransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PembayaranPranotaObAntarGudangController extends Controller
{
    protected $coaTransactionService;

    public function __construct(CoaTransactionService $coaTransactionService)
    {
        $this->coaTransactionService = $coaTransactionService;
    }

    public function index()
    {
        $pembayaranList = PembayaranPranotaObAntarGudang::with(['creator'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('pembayaran-pranota-ob-antar-gudang.index', compact('pembayaranList'));
    }

    public function create()
    {
        // Get all unpaid Pranota OB Antar Gudang
        $pranotaList = PranotaObAntarGudang::where('status_pembayaran', 'Belum Lunas')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get all COA accounts for Akun Biaya
        $akunBiaya = Coa::orderBy('kode_nomor')->get();

        // Get Bank/Kas accounts only
        $akunBank = Coa::where(function ($query) {
            $query->where('tipe_akun', 'Kas/Bank')
                ->orWhere('tipe_akun', 'Bank/Kas')
                ->orWhere('tipe_akun', 'LIKE', '%Kas%')
                ->orWhere('tipe_akun', 'LIKE', '%Bank%');
        })
            ->orderByRaw('CAST(nomor_akun AS UNSIGNED) ASC')
            ->get();

        $nomorPembayaran = PembayaranPranotaObAntarGudang::generateNomorPembayaran();

        return view('pembayaran-pranota-ob-antar-gudang.create', compact('pranotaList', 'akunBank', 'akunBiaya', 'nomorPembayaran'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nomor_pembayaran' => 'required|string|unique:pembayaran_pranota_ob_antar_gudangs,nomor_pembayaran',
            'nomor_accurate' => 'nullable|string|max:255',
            'debit_kredit' => 'required|in:debit,credit',
            'akun_items' => 'required|array|min:1',
            'akun_items.*.akun_coa_id' => 'required|distinct|exists:akun_coa,id',
            'akun_items.*.nominal' => 'required|numeric|gt:0',
            'akun_items.*.memo' => 'nullable|string|max:2000',
            'akun_bank_id' => 'required|exists:akun_coa,id',
            'tanggal_kas' => 'required|date',
            'pranota_ids' => 'required|array|min:1',
            'pranota_ids.*' => 'exists:pranota_ob_antar_gudangs,id',
            'penyesuaian' => 'nullable|numeric',
            'alasan_penyesuaian' => 'nullable|string',
            'keterangan' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $pranotaIds = $request->input('pranota_ids');
            $penyesuaian = floatval($request->input('penyesuaian', 0));

            // Validate all selected pranotas are unpaid
            $pranotas = PranotaObAntarGudang::whereIn('id', $pranotaIds)->get();
            $totalTagihan = 0;

            foreach ($pranotas as $pranota) {
                if ($pranota->status_pembayaran === 'Lunas') {
                    throw new \Exception("Pranota {$pranota->nomor_pranota} sudah lunas.");
                }
                $totalTagihan += floatval($pranota->grand_total);
            }

            $totalSetelahPenyesuaian = $totalTagihan + $penyesuaian;
            $akunItems = collect($request->input('akun_items'));
            $coaById = Coa::whereIn('id', $akunItems->pluck('akun_coa_id'))->get()->keyBy('id');
            $piutang = $akunItems->sum(fn ($item) => $coaById[$item['akun_coa_id']]->isPiutangLainLain() ? (float) $item['nominal'] : 0);
            $totalBiaya = $akunItems->sum(fn ($item) => $coaById[$item['akun_coa_id']]->isPiutangLainLain() ? 0 : (float) $item['nominal']);
            if ($totalSetelahPenyesuaian <= 0 || abs($totalBiaya - $totalSetelahPenyesuaian) > 0.01 || $piutang >= $totalSetelahPenyesuaian) {
                throw new \Exception('Jumlah akun biaya harus sama dengan tagihan setelah penyesuaian, dan potongan piutang harus lebih kecil dari tagihan.');
            }

            $akunBank = Coa::findOrFail($request->akun_bank_id);

            // Create payment record
            $pembayaran = PembayaranPranotaObAntarGudang::create([
                'nomor_pembayaran' => $request->nomor_pembayaran,
                'nomor_accurate' => $request->nomor_accurate,
                'nomor_cetakan' => 1,
                'bank' => $akunBank->nama_akun,
                'jenis_transaksi' => $request->debit_kredit,
                'tanggal_kas' => $request->tanggal_kas,
                'total_pembayaran' => $totalTagihan,
                'penyesuaian' => $penyesuaian,
                'total_setelah_penyesuaian' => $totalSetelahPenyesuaian - $piutang,
                'alasan_penyesuaian' => $request->alasan_penyesuaian,
                'keterangan' => $request->keterangan,
                'status' => 'approved',
                'pranota_ob_antar_gudang_ids' => $pranotaIds,
                'akun_coa_id' => $akunItems->first()['akun_coa_id'],
                'akun_bank_id' => $request->akun_bank_id,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);
            $pembayaran->akunItems()->createMany($akunItems->all());

            // Update status of all associated pranotas to Lunas
            PranotaObAntarGudang::whereIn('id', $pranotaIds)->update([
                'status_pembayaran' => 'Lunas',
            ]);

            // Double Entry Journaling
            $journalKeterangan = 'Pembayaran Pranota OB Antar Gudang - '.$request->nomor_pembayaran;
            if ($request->keterangan) {
                $journalKeterangan .= ' | '.$request->keterangan;
            }
            if ($request->alasan_penyesuaian) {
                $journalKeterangan .= ' | Penyesuaian: '.$request->alasan_penyesuaian;
            }

            $this->recordPaymentJournal($pembayaran->load('akunItems.akunCoa'), $journalKeterangan);

            DB::commit();

            return redirect()->route('pembayaran-pranota-ob-antar-gudang.index')
                ->with('success', 'Pembayaran Pranota OB Antar Gudang berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error storing PembayaranPranotaObAntarGudang: '.$e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan pembayaran: '.$e->getMessage());
        }
    }

    public function show($id)
    {
        $pembayaran = PembayaranPranotaObAntarGudang::with('akunItems.akunCoa')->findOrFail($id);

        return view('pembayaran-pranota-ob-antar-gudang.show', compact('pembayaran'));
    }

    public function updateTanggalPranota(Request $request, $id, $pranotaId)
    {
        $validated = $request->validate([
            'tanggal_pranota' => ['required', 'date_format:Y-m-d'],
        ]);

        $pembayaran = PembayaranPranotaObAntarGudang::findOrFail($id);
        $pranotaIds = collect($pembayaran->pranota_ob_antar_gudang_ids)
            ->map(fn ($pranotaId) => (int) $pranotaId);

        abort_unless($pranotaIds->contains((int) $pranotaId), 404);

        $pranota = PranotaObAntarGudang::findOrFail($pranotaId);
        $pranota->update(['tanggal_pranota' => $validated['tanggal_pranota']]);

        return redirect()
            ->route('pembayaran-pranota-ob-antar-gudang.show', $pembayaran->id)
            ->with('success', 'Tanggal pranota '.$pranota->nomor_pranota.' berhasil diperbarui.');
    }

    public function edit($id)
    {
        $pembayaran = PembayaranPranotaObAntarGudang::with('akunItems.akunCoa')->findOrFail($id);

        // Get all COA accounts for Akun Biaya
        $akunBiaya = Coa::orderBy('kode_nomor')->get();

        // Get Bank/Kas accounts only
        $akunBank = Coa::where(function ($query) {
            $query->where('tipe_akun', 'Kas/Bank')
                ->orWhere('tipe_akun', 'Bank/Kas')
                ->orWhere('tipe_akun', 'LIKE', '%Kas%')
                ->orWhere('tipe_akun', 'LIKE', '%Bank%');
        })
            ->orderByRaw('CAST(nomor_akun AS UNSIGNED) ASC')
            ->get();

        return view('pembayaran-pranota-ob-antar-gudang.edit', compact('pembayaran', 'akunBank', 'akunBiaya'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nomor_accurate' => 'nullable|string|max:255',
            'debit_kredit' => 'required|in:debit,credit',
            'akun_items' => 'required|array|min:1',
            'akun_items.*.akun_coa_id' => 'required|distinct|exists:akun_coa,id',
            'akun_items.*.nominal' => 'required|numeric|gt:0',
            'akun_items.*.memo' => 'nullable|string|max:2000',
            'akun_bank_id' => 'required|exists:akun_coa,id',
            'tanggal_kas' => 'required|date',
            'penyesuaian' => 'nullable|numeric',
            'alasan_penyesuaian' => 'nullable|string',
            'keterangan' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $pembayaran = PembayaranPranotaObAntarGudang::findOrFail($id);
            $penyesuaian = floatval($request->input('penyesuaian', 0));
            $totalSetelahPenyesuaian = floatval($pembayaran->total_pembayaran) + $penyesuaian;
            $akunItems = collect($request->input('akun_items'));
            $coaById = Coa::whereIn('id', $akunItems->pluck('akun_coa_id'))->get()->keyBy('id');
            $piutang = $akunItems->sum(fn ($item) => $coaById[$item['akun_coa_id']]->isPiutangLainLain() ? (float) $item['nominal'] : 0);
            $totalBiaya = $akunItems->sum(fn ($item) => $coaById[$item['akun_coa_id']]->isPiutangLainLain() ? 0 : (float) $item['nominal']);
            if ($totalSetelahPenyesuaian <= 0 || abs($totalBiaya - $totalSetelahPenyesuaian) > 0.01 || $piutang >= $totalSetelahPenyesuaian) {
                throw new \Exception('Jumlah akun biaya harus sama dengan tagihan setelah penyesuaian, dan potongan piutang harus lebih kecil dari tagihan.');
            }

            $akunBank = Coa::findOrFail($request->akun_bank_id);

            $pembayaran->update([
                'nomor_accurate' => $request->nomor_accurate,
                'bank' => $akunBank->nama_akun,
                'jenis_transaksi' => $request->debit_kredit,
                'tanggal_kas' => $request->tanggal_kas,
                'penyesuaian' => $penyesuaian,
                'total_setelah_penyesuaian' => $totalSetelahPenyesuaian - $piutang,
                'alasan_penyesuaian' => $request->alasan_penyesuaian,
                'keterangan' => $request->keterangan,
                'akun_coa_id' => $akunItems->first()['akun_coa_id'],
                'akun_bank_id' => $request->akun_bank_id,
                'updated_by' => Auth::id(),
            ]);
            $pembayaran->akunItems()->delete();
            $pembayaran->akunItems()->createMany($akunItems->all());

            // Sync COA double entry: delete first, then recreate
            if (! $this->coaTransactionService->deleteTransactionByReference($pembayaran->nomor_pembayaran)) {
                throw new \RuntimeException('Gagal menghapus jurnal lama.');
            }

            $journalKeterangan = 'Pembayaran Pranota OB Antar Gudang - '.$pembayaran->nomor_pembayaran.' (Updated)';
            if ($request->keterangan) {
                $journalKeterangan .= ' | '.$request->keterangan;
            }
            if ($request->alasan_penyesuaian) {
                $journalKeterangan .= ' | Penyesuaian: '.$request->alasan_penyesuaian;
            }

            $this->recordPaymentJournal($pembayaran->fresh('akunItems.akunCoa'), $journalKeterangan);

            DB::commit();

            return redirect()->route('pembayaran-pranota-ob-antar-gudang.index')
                ->with('success', 'Pembayaran Pranota OB Antar Gudang berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating PembayaranPranotaObAntarGudang: '.$e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui pembayaran: '.$e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $pembayaran = PembayaranPranotaObAntarGudang::findOrFail($id);
            $pranotaIds = $pembayaran->pranota_ob_antar_gudang_ids ?? [];

            // Restore status of all associated pranotas back to Belum Lunas
            if (! empty($pranotaIds)) {
                PranotaObAntarGudang::whereIn('id', $pranotaIds)->update([
                    'status_pembayaran' => 'Belum Lunas',
                ]);
            }

            // Delete associated COA transactions
            $this->coaTransactionService->deleteTransactionByReference($pembayaran->nomor_pembayaran);

            // Delete payment record
            $pembayaran->delete();

            DB::commit();

            return redirect()->route('pembayaran-pranota-ob-antar-gudang.index')
                ->with('success', 'Pembayaran Pranota OB Antar Gudang berhasil dihapus dan status pranota dikembalikan ke Belum Lunas.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting PembayaranPranotaObAntarGudang: '.$e->getMessage());

            return redirect()->route('pembayaran-pranota-ob-antar-gudang.index')
                ->with('error', 'Gagal menghapus pembayaran: '.$e->getMessage());
        }
    }

    public function print($id)
    {
        $pembayaran = PembayaranPranotaObAntarGudang::with('akunItems.akunCoa')->findOrFail($id);

        return view('pembayaran-pranota-ob-antar-gudang.print', compact('pembayaran'));
    }

    private function recordPaymentJournal(PembayaranPranotaObAntarGudang $pembayaran, string $keterangan): void
    {
        $items = $pembayaran->akunItems;
        $total = (float) $pembayaran->total_setelah_penyesuaian;
        $piutang = $items->sum(fn ($item) => $item->akunCoa?->isPiutangLainLain() ? (float) $item->nominal : 0);
        if ($total <= 0 || abs($items->sum(fn ($item) => (float) $item->nominal) - 2 * $piutang - $total) > 0.01) {
            throw new \RuntimeException('Rincian akun tidak seimbang dengan total pembayaran.');
        }

        $tanggal = $pembayaran->tanggal_kas->format('Y-m-d');
        $debitBiaya = $pembayaran->jenis_transaksi === 'credit';
        foreach ($items as $item) {
            $akun = $item->akunCoa;
            if (! $akun) {
                throw new \RuntimeException('Akun biaya tidak ditemukan.');
            }
            $memo = trim((string) $item->memo);
            $this->recordJournalLine(
                $akun,
                (float) $item->nominal,
                $akun->isPiutangLainLain() ? ! $debitBiaya : $debitBiaya,
                $pembayaran,
                $tanggal,
                $memo === '' ? $keterangan : $keterangan.' | Memo akun: '.$memo
            );
        }

        $this->recordJournalLine(Coa::findOrFail($pembayaran->akun_bank_id), $total, ! $debitBiaya, $pembayaran, $tanggal, $keterangan);
    }

    private function recordJournalLine(Coa $akun, float $jumlah, bool $debit, PembayaranPranotaObAntarGudang $pembayaran, string $tanggal, string $keterangan): void
    {
        $recorded = $this->coaTransactionService->recordTransaction(
            $akun->nama_akun,
            $debit ? $jumlah : 0,
            $debit ? 0 : $jumlah,
            $tanggal,
            $pembayaran->nomor_pembayaran,
            'Pembayaran Pranota OB Antar Gudang',
            $keterangan,
            $akun->id
        );
        if (! $recorded) {
            throw new \RuntimeException('Gagal mencatat jurnal akun '.$akun->nama_akun.'.');
        }
    }
}
