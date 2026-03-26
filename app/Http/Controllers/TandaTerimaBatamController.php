<?php

namespace App\Http\Controllers;

use App\Models\TandaTerimaBatam;
use App\Models\ProspekBatam;
use App\Models\MasterKapal;
use App\Models\SuratJalanBatam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TandaTerimaBatamController extends Controller
{
    /**
     * Map an input tipe_kontainer string to the database enum values for tanda_terimas
     */
    private function mapTipeKontainerValue(?string $value): ?string
    {
        if (!$value) return null;
        $v = strtolower(trim($value));

        $map = [
            'fcl' => 'fcl',
            'lcl' => 'lcl',
            'cargo' => 'cargo',
            'dry' => 'fcl',
            'high cube' => 'fcl',
            'hc' => 'fcl',
            'reefer' => 'fcl',
        ];

        if (isset($map[$v])) {
            return $map[$v];
        }

        foreach ($map as $k => $m) {
            if (strpos($v, $k) !== false) {
                return $m;
            }
        }

        return null;
    }

    /**
     * Show surat jalan selection page
     */
    public function selectSuratJalan(Request $request)
    {
        $search = $request->input('search', '');
        $status = $request->input('status', 'belum_ada_tanda_terima');

        $statusOptions = [
            'semua' => 'Semua Status',
            'belum_ada_tanda_terima' => 'Belum Ada Tanda Terima',
            'sudah_ada_tanda_terima' => 'Sudah Ada Tanda Terima'
        ];

        $query = SuratJalanBatam::with(['orderBatam.pengirim']);

        // Filter status checkpoint (must be checked point to create Tanda Terima)
        $query->where('status', 'sudah_checkpoint');

        if ($status === 'belum_ada_tanda_terima') {
            $query->whereDoesntHave('tandaTerima');
        } elseif ($status === 'sudah_ada_tanda_terima') {
            $query->whereHas('tandaTerima');
        }

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('no_surat_jalan', 'like', "%{$search}%")
                  ->orWhere('supir', 'like', "%{$search}%")
                  ->orWhere('no_plat', 'like', "%{$search}%")
                  ->orWhere('no_kontainer', 'like', "%{$search}%")
                  ->orWhereHas('orderBatam', function($orderQuery) use ($search) {
                      $orderQuery->whereHas('pengirim', function($pengirimQuery) use ($search) {
                          $pengirimQuery->where('nama_pengirim', 'like', "%{$search}%");
                      });
                  });
            });
        }

        $suratJalans = $query->orderBy('created_at', 'desc')->paginate(50);

        return view('tanda-terima-batam.select_surat_jalan', compact('suratJalans', 'search', 'status', 'statusOptions'));
    }

    /**
     * Display a listing of tanda terima
     */
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $status = $request->input('status', '');
        
        $lastUpdateStr = now()->format('H:i');

        $query = TandaTerimaBatam::with(['suratJalan.orderBatam.pengirim']);

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('no_surat_jalan', 'like', "%{$search}%")
                  ->orWhere('no_kontainer', 'like', "%{$search}%")
                  ->orWhere('supir', 'like', "%{$search}%")
                  ->orWhere('no_plat', 'like', "%{$search}%")
                  ->orWhere('penerima', 'like', "%{$search}%");
            });
        }

        if (!empty($status)) {
            $query->where('status', $status);
        }

        $tandaTerimas = $query->orderBy('created_at', 'desc')->paginate(25);

        return view('tanda-terima-batam.index', compact('tandaTerimas', 'search', 'status', 'lastUpdateStr'));
    }

    /**
     * Show the form for creating a new tanda terima
     */
    public function create(Request $request)
    {
        $suratJalanId = $request->input('surat_jalan_id');
        
        if (!$suratJalanId) {
            return redirect()->route('tanda-terima-batam.select-surat-jalan')
                ->with('error', 'Silakan pilih surat jalan terlebih dahulu');
        }

        $suratJalan = SuratJalanBatam::with(['orderBatam.pengirim'])->findOrFail($suratJalanId);
        
        if ($suratJalan->tandaTerima) {
            return redirect()->route('tanda-terima-batam.edit', $suratJalan->tandaTerima->id)
                ->with('info', 'Tanda terima untuk surat jalan ini sudah ada.');
        }

        $masterKapals = MasterKapal::where('status', 'aktif')->orderBy('nama_kapal')->get();
        $pengirims = \App\Models\Pengirim::orderBy('nama_pengirim')->get();
        $terms = \App\Models\Term::where('status', 'active')->orderBy('nama_status')->get();
        $jenisBarangs = \App\Models\JenisBarang::where('status', 'active')->orderBy('nama_barang')->get();
        $karyawanSupirs = \App\Models\Karyawan::where('divisi', 'supir')->orderBy('nama_lengkap')->get();
        $kranisKenek = \App\Models\Karyawan::where('divisi', 'krani')->orderBy('nama_lengkap')->get();
        
        // Mock stock kontainers for now as it's complex in original controller
        $stockKontainers = \App\Models\StockKontainer::select('nomor_seri_gabungan', 'ukuran', 'tipe_kontainer')
            ->orderBy('nomor_seri_gabungan')
            ->get();

        $masterPenerimaList = \App\Models\Penerima::where('status', 'active')->orderBy('nama_penerima')->get();
        $gudangs = \App\Models\Gudang::where('status', 'aktif')->orderBy('nama_gudang')->get();

        return view('tanda-terima-batam.create', compact('suratJalan', 'masterKapals', 'pengirims', 'terms', 'jenisBarangs', 'karyawanSupirs', 'kranisKenek', 'stockKontainers', 'masterPenerimaList', 'gudangs'));
    }

    /**
     * Store a newly created tanda terima in storage
     */
    public function store(Request $request)
    {
        $request->validate([
            'surat_jalan_id' => 'required|exists:surat_jalan_batams,id',
            'tanggal' => 'required|date',
            // ... add other validations as needed
        ]);

        DB::beginTransaction();
        try {
            $suratJalan = SuratJalanBatam::with(['orderBatam.pengirim'])->findOrFail($request->surat_jalan_id);

            $tandaTerima = new TandaTerimaBatam();
            $tandaTerima->surat_jalan_batam_id = $suratJalan->id;
            $tandaTerima->no_surat_jalan = $suratJalan->no_surat_jalan;
            $tandaTerima->tanggal_surat_jalan = $suratJalan->tanggal_surat_jalan;
            $tandaTerima->supir = $suratJalan->supir;
            $tandaTerima->kegiatan = $suratJalan->kegiatan;
            $tandaTerima->jenis_barang = $suratJalan->jenis_barang;
            
            // Map tipe_kontainer
            $tandaTerima->tipe_kontainer = $this->mapTipeKontainerValue($suratJalan->tipe_kontainer) ?: 'fcl';
            
            $tandaTerima->size = $suratJalan->size;
            $tandaTerima->jumlah_kontainer = $suratJalan->jumlah_kontainer;
            $tandaTerima->no_kontainer = $suratJalan->no_kontainer;
            $tandaTerima->no_seal = $suratJalan->no_seal;
            
            $tandaTerima->estimasi_nama_kapal = $request->estimasi_nama_kapal;
            $tandaTerima->nomor_ro = $request->nomor_ro;
            $tandaTerima->tanggal = $request->tanggal;
            $tandaTerima->no_plat = $request->no_plat ?: $suratJalan->no_plat;
            $tandaTerima->penerima = $request->penerima;
            $tandaTerima->alamat_penerima = $request->alamat_penerima ?: $suratJalan->alamat_penerima;
            
            $tandaTerima->input_by = Auth::id();
            $tandaTerima->status = 'pending';
            
            $tandaTerima->save();

            // Link to ProspekBatam
            $tandaTerima->autoLinkProspek();

            DB::commit();
            return redirect()->route('tanda-terima-batam.index')->with('success', 'Tanda Terima Batam berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error store TandaTerimaBatam: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Gagal membuat Tanda Terima: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $tandaTerima = TandaTerimaBatam::with(['suratJalan.orderBatam.pengirim'])->findOrFail($id);
        return view('tanda-terima-batam.show', compact('tandaTerima'));
    }

    public function edit($id)
    {
        $tandaTerima = TandaTerimaBatam::with(['suratJalan.orderBatam.pengirim'])->findOrFail($id);
        $masterKapals = MasterKapal::where('status', 'aktif')->orderBy('nama_kapal')->get();
        $masterPenerimaList = \App\Models\Penerima::where('status', 'active')->orderBy('nama_penerima')->get();
        
        return view('tanda-terima-batam.edit', compact('tandaTerima', 'masterKapals', 'masterPenerimaList'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal' => 'required|date',
        ]);

        DB::beginTransaction();
        try {
            $tandaTerima = TandaTerimaBatam::findOrFail($id);
            $tandaTerima->update($request->all());
            
            // Re-link or update Prospek if needed
            $tandaTerima->autoLinkProspek();

            DB::commit();
            return redirect()->route('tanda-terima-batam.index')->with('success', 'Tanda Terima Batam berhasil diupdate');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error update TandaTerimaBatam: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Gagal update Tanda Terima: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $tandaTerima = TandaTerimaBatam::findOrFail($id);
            
            // Find and unlink/delete ProspekBatam if needed
            $prospek = ProspekBatam::where('tanda_terima_batam_id', $tandaTerima->id)->first();
            if ($prospek) {
                $prospek->tanda_terima_batam_id = null;
                $prospek->save();
            }
            
            $tandaTerima->delete();
            
            DB::commit();
            return redirect()->route('tanda-terima-batam.index')->with('success', 'Tanda Terima Batam berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error destroy TandaTerimaBatam: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus Tanda Terima');
        }
    }
}
