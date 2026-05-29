<?php

namespace App\Http\Controllers;

use App\Models\BtmSewaTransaction;
use App\Models\Karyawan;
use App\Models\Kontainer;
use App\Models\SuratJalanKontainerSewa;
use App\Models\TandaTerimaSuratJalanKontainerSewa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TandaTerimaSuratJalanKontainerSewaController extends Controller
{
    /**
     * Get next number for tanda terima kontainer sewa
     */
    public function getNextNumber()
    {
        try {
            $now = now();
            $bulan = $now->format('m');
            $tahun = $now->format('y');

            // Get last tanda terima for current month/year
            $lastTandaTerima = TandaTerimaSuratJalanKontainerSewa::whereYear('created_at', $now->year)
                ->whereMonth('created_at', $now->month)
                ->orderBy('id', 'desc')
                ->first();

            $nextNumber = 1;

            if ($lastTandaTerima && $lastTandaTerima->nomor_tanda_terima) {
                // Extract running number from format TTKSMMYYXXXXXX (14 characters)
                $nomorTerima = $lastTandaTerima->nomor_tanda_terima;
                if (strlen($nomorTerima) >= 14) {
                    $lastRunningNumber = (int) substr($nomorTerima, -6);
                    $nextNumber = $lastRunningNumber + 1;
                }
            }

            return response()->json([
                'success' => true,
                'next_number' => $nextNumber,
                'bulan' => $bulan,
                'tahun' => $tahun,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'next_number' => 1,
            ], 500);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $tipe = $request->get('tipe', 'surat_jalan'); // default: surat_jalan

        $kontainers = Kontainer::whereNotNull('nomor_seri_gabungan')
            ->where('nomor_seri_gabungan', '!=', '')
            ->orderBy('nomor_seri_gabungan')
            ->get();

        if ($tipe === 'tanda_terima') {
            $query = TandaTerimaSuratJalanKontainerSewa::with(['suratJalanKontainerSewa']);

            // Search filter
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('nomor_tanda_terima', 'LIKE', "%{$search}%")
                        ->orWhere('nomor_kontainer', 'LIKE', "%{$search}%")
                        ->orWhere('nomor_surat_jalan', 'LIKE', "%{$search}%")
                        ->orWhere('supir', 'LIKE', "%{$search}%");
                });
            }

            // Filter Lembur/Nginap
            if ($request->boolean('f_lembur')) {
                $query->where('lembur', true);
            }
            if ($request->boolean('f_nginap')) {
                $query->where('nginap', true);
            }
            if ($request->boolean('f_tidak_lembur_nginap')) {
                $query->where('tidak_lembur_nginap', true);
            }

            $tandaTerimas = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

            // Get supirs for dropdown modal
            $supirs = Karyawan::select('id', 'nama_panggilan', 'plat')
                ->where('divisi', 'supir')
                ->orderBy('nama_panggilan')
                ->get();

            return view('tanda-terima-surat-jalan-kontainer-sewa.index', compact('tandaTerimas', 'supirs', 'kontainers'));
        } else {
            // Pending Surat Jalan
            $query = SuratJalanKontainerSewa::query();

            // Search filter
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('nomor_surat_jalan', 'LIKE', "%{$search}%")
                        ->orWhere('nomor_kontainer', 'LIKE', "%{$search}%")
                        ->orWhere('supir', 'LIKE', "%{$search}%")
                        ->orWhere('vendor', 'LIKE', "%{$search}%");
                });
            }

            // Status filter
            if ($request->filled('status')) {
                if ($request->status === 'sudah') {
                    $query->whereHas('items'); // wait, SuratJalanKontainerSewa has one-to-one or one-to-many? Let's check relation
                }
            }

            // For pending tab, show those status = 'aktif'
            $query->where('status', 'aktif');

            $suratJalans = $query->orderBy('tanggal', 'desc')->orderBy('id', 'desc')->paginate(20)->withQueryString();

            // Get supirs for dropdown modal
            $supirs = Karyawan::select('id', 'nama_panggilan', 'plat')
                ->where('divisi', 'supir')
                ->orderBy('nama_panggilan')
                ->get();

            return view('tanda-terima-surat-jalan-kontainer-sewa.index', compact('suratJalans', 'supirs', 'kontainers'));
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $surat_jalan_id = $request->get('surat_jalan_id');
        $selectedSj = null;

        if ($surat_jalan_id) {
            $selectedSj = SuratJalanKontainerSewa::findOrFail($surat_jalan_id);
        }

        // Get all active Surat Jalan Kontainer Sewa that don't have a Tanda Terima yet
        $suratJalans = SuratJalanKontainerSewa::where('status', 'aktif')
            ->orderBy('nomor_surat_jalan', 'desc')
            ->get();

        // Get supir list
        $supirs = Karyawan::where('divisi', 'supir')
            ->orderBy('nama_lengkap')
            ->get(['id', 'nama_lengkap', 'plat']);

        return view('tanda-terima-surat-jalan-kontainer-sewa.create', compact('suratJalans', 'selectedSj', 'supirs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomor_tanda_terima' => 'required|string|max:255|unique:tanda_terima_surat_jalan_kontainer_sewas,nomor_tanda_terima',
            'tanggal_tanda_terima' => 'required|date',
            'tanggal_mulai_sewa' => 'required|date',
            'surat_jalan_kontainer_sewa_id' => 'required|exists:surat_jalan_kontainer_sewas,id',
            'nomor_kontainer' => 'required|string|size:11',
            'supir' => 'nullable|string|max:255',
            'no_plat' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
            'lembur' => 'nullable|boolean',
            'nginap' => 'nullable|boolean',
            'tidak_lembur_nginap' => 'nullable|boolean',
        ]);

        // Ensure at least one checkbox is selected
        if (! $request->boolean('lembur') && ! $request->boolean('nginap') && ! $request->boolean('tidak_lembur_nginap')) {
            return redirect()->back()->withInput()->withErrors([
                'lembur' => 'Harap pilih minimal satu opsi (Lembur, Nginap, atau Tidak Lembur & Nginap).',
            ]);
        }

        DB::beginTransaction();
        try {
            $suratJalan = SuratJalanKontainerSewa::findOrFail($validated['surat_jalan_kontainer_sewa_id']);

            $validated['nomor_surat_jalan'] = $suratJalan->nomor_surat_jalan;
            $validated['tipe_kontainer'] = $suratJalan->tipe_kontainer;
            $validated['ukuran'] = $suratJalan->ukuran;
            $validated['kegiatan'] = $suratJalan->tipe; // 'pengambilan' or 'pengembalian'
            $validated['status'] = 'completed';

            $validated['lembur'] = $request->boolean('lembur');
            $validated['nginap'] = $request->boolean('nginap');
            $validated['tidak_lembur_nginap'] = $request->boolean('tidak_lembur_nginap');
            $validated['created_by'] = Auth::id();
            $validated['updated_by'] = Auth::id();

            if (empty($validated['supir'])) {
                $validated['supir'] = $suratJalan->supir;
            }
            if (empty($validated['no_plat'])) {
                $validated['no_plat'] = $suratJalan->no_plat;
            }

            $tandaTerima = TandaTerimaSuratJalanKontainerSewa::create($validated);

            // Sync/create kontainer and transaction record
            $this->syncKontainer(
                $validated['nomor_kontainer'],
                $validated['tanggal_mulai_sewa'],
                $suratJalan->ukuran,
                $suratJalan->tipe_kontainer,
                $suratJalan->vendor,
                $suratJalan->tipe
            );

            // Update status of Surat Jalan Kontainer Sewa to selesai
            $suratJalan->update([
                'status' => 'selesai',
                'supir' => $validated['supir'],
                'no_plat' => $validated['no_plat'],
                'nomor_kontainer' => $validated['nomor_kontainer'],
                'updated_by' => Auth::id(),
            ]);

            DB::commit();

            return redirect()
                ->route('tanda-terima-surat-jalan-kontainer-sewa.index', ['tipe' => 'tanda_terima'])
                ->with('success', "Tanda terima kontainer sewa {$tandaTerima->nomor_tanda_terima} berhasil dibuat!");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error storing Tanda Terima Surat Jalan Kontainer Sewa: '.$e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $tandaTerima = TandaTerimaSuratJalanKontainerSewa::with(['suratJalanKontainerSewa'])->findOrFail($id);

        return view('tanda-terima-surat-jalan-kontainer-sewa.show', compact('tandaTerima'));
    }

    public function edit($id)
    {
        $tandaTerima = TandaTerimaSuratJalanKontainerSewa::findOrFail($id);

        $suratJalans = SuratJalanKontainerSewa::orderBy('nomor_surat_jalan', 'desc')->get();

        $supirs = Karyawan::where('divisi', 'supir')
            ->orderBy('nama_lengkap')
            ->get(['id', 'nama_lengkap', 'plat']);

        $kontainers = Kontainer::whereNotNull('nomor_seri_gabungan')
            ->where('nomor_seri_gabungan', '!=', '')
            ->orderBy('nomor_seri_gabungan')
            ->get();

        return view('tanda-terima-surat-jalan-kontainer-sewa.edit', compact('tandaTerima', 'suratJalans', 'supirs', 'kontainers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nomor_tanda_terima' => 'required|string|max:255|unique:tanda_terima_surat_jalan_kontainer_sewas,nomor_tanda_terima,'.$id,
            'tanggal_tanda_terima' => 'required|date',
            'tanggal_mulai_sewa' => 'required|date',
            'nomor_kontainer' => 'required|string|size:11',
            'supir' => 'nullable|string|max:255',
            'no_plat' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
            'lembur' => 'nullable|boolean',
            'nginap' => 'nullable|boolean',
            'tidak_lembur_nginap' => 'nullable|boolean',
        ]);

        // Ensure at least one checkbox is selected
        if (! $request->boolean('lembur') && ! $request->boolean('nginap') && ! $request->boolean('tidak_lembur_nginap')) {
            return redirect()->back()->withInput()->withErrors([
                'lembur' => 'Harap pilih minimal satu opsi (Lembur, Nginap, atau Tidak Lembur & Nginap).',
            ]);
        }

        DB::beginTransaction();
        try {
            $tandaTerima = TandaTerimaSuratJalanKontainerSewa::findOrFail($id);

            $validated['lembur'] = $request->boolean('lembur');
            $validated['nginap'] = $request->boolean('nginap');
            $validated['tidak_lembur_nginap'] = $request->boolean('tidak_lembur_nginap');
            $validated['updated_by'] = Auth::id();

            $tandaTerima->update($validated);

            // Sync/create kontainer and transaction record
            $this->syncKontainer(
                $validated['nomor_kontainer'],
                $validated['tanggal_mulai_sewa'],
                $tandaTerima->ukuran,
                $tandaTerima->tipe_kontainer,
                $tandaTerima->suratJalanKontainerSewa->vendor ?? null,
                $tandaTerima->kegiatan
            );

            // Sync with related Surat Jalan Kontainer Sewa
            if ($tandaTerima->surat_jalan_kontainer_sewa_id) {
                $suratJalan = SuratJalanKontainerSewa::find($tandaTerima->surat_jalan_kontainer_sewa_id);
                if ($suratJalan) {
                    $suratJalan->update([
                        'supir' => $validated['supir'] ?: $suratJalan->supir,
                        'no_plat' => $validated['no_plat'] ?: $suratJalan->no_plat,
                        'nomor_kontainer' => $validated['nomor_kontainer'] ?: $suratJalan->nomor_kontainer,
                        'updated_by' => Auth::id(),
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->route('tanda-terima-surat-jalan-kontainer-sewa.index', ['tipe' => 'tanda_terima'])
                ->with('success', 'Tanda terima kontainer sewa berhasil diperbarui!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating Tanda Terima Surat Jalan Kontainer Sewa: '.$e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $tandaTerima = TandaTerimaSuratJalanKontainerSewa::findOrFail($id);

            // Revert related Surat Jalan Kontainer Sewa status to 'aktif'
            if ($tandaTerima->surat_jalan_kontainer_sewa_id) {
                $suratJalan = SuratJalanKontainerSewa::find($tandaTerima->surat_jalan_kontainer_sewa_id);
                if ($suratJalan) {
                    $suratJalan->update([
                        'status' => 'aktif',
                        'updated_by' => Auth::id(),
                    ]);
                }
            }

            $tandaTerima->delete();

            DB::commit();

            return redirect()
                ->route('tanda-terima-surat-jalan-kontainer-sewa.index', ['tipe' => 'tanda_terima'])
                ->with('success', 'Tanda terima kontainer sewa berhasil dihapus dan status surat jalan dikembalikan ke Aktif!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error destroying Tanda Terima Surat Jalan Kontainer Sewa: '.$e->getMessage());

            return back()
                ->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    /**
     * Print tanda terima
     */
    public function print($id)
    {
        $tandaTerima = TandaTerimaSuratJalanKontainerSewa::with(['suratJalanKontainerSewa'])->findOrFail($id);

        return view('tanda-terima-surat-jalan-kontainer-sewa.print', compact('tandaTerima'));
    }

    /**
     * Sync or create container in kontainers and btm_sewa_transactions table
     */
    private function syncKontainer($nomorKontainer, $tanggalMulaiSewa, $ukuran = null, $tipeKontainer = null, $vendor = null, $kegiatan = 'pengambilan')
    {
        if (empty($nomorKontainer)) {
            return;
        }

        $nomor = strtoupper(trim($nomorKontainer));
        $kontainer = Kontainer::where('nomor_seri_gabungan', $nomor)->first();

        // 1. Sync Kontainer Table
        if ($kontainer) {
            $kontainer->update([
                'tanggal_mulai_sewa' => $tanggalMulaiSewa,
            ]);
        } else {
            $awalan = strlen($nomor) >= 4 ? substr($nomor, 0, 4) : $nomor;
            $seri = strlen($nomor) >= 10 ? substr($nomor, 4, 6) : '';
            $akhiran = strlen($nomor) >= 11 ? substr($nomor, 10, 1) : '';

            Kontainer::create([
                'awalan_kontainer' => $awalan,
                'nomor_seri_kontainer' => $seri,
                'akhiran_kontainer' => $akhiran,
                'nomor_seri_gabungan' => $nomor,
                'ukuran' => $ukuran ?? '40',
                'tipe_kontainer' => $tipeKontainer ?? 'Dry',
                'tanggal_mulai_sewa' => $tanggalMulaiSewa,
                'vendor' => $vendor,
                'status' => 'Tersedia',
            ]);
        }

        // 2. Sync BtmSewaTransaction Table
        if ($kegiatan === 'pengambilan') {
            $activeTrx = BtmSewaTransaction::where('unit_number', $nomor)
                ->whereNull('date_out')
                ->first();

            if ($activeTrx) {
                $activeTrx->update([
                    'date_in' => $tanggalMulaiSewa,
                ]);
            } else {
                BtmSewaTransaction::create([
                    'unit_number' => $nomor,
                    'date_in' => $tanggalMulaiSewa,
                    'date_out' => null,
                    'billing_mode' => 'B',
                ]);
            }
        } elseif ($kegiatan === 'pengembalian') {
            $activeTrx = BtmSewaTransaction::where('unit_number', $nomor)
                ->whereNull('date_out')
                ->first();

            if ($activeTrx) {
                $activeTrx->update([
                    'date_out' => $tanggalMulaiSewa,
                ]);
            } else {
                // If there's no active transaction (maybe manually deleted or missing), create a closed one
                BtmSewaTransaction::create([
                    'unit_number' => $nomor,
                    'date_in' => $tanggalMulaiSewa,
                    'date_out' => $tanggalMulaiSewa,
                    'billing_mode' => 'B',
                ]);
            }
        }
    }
}
