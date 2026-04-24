<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TandaTerimaBongkaranBatam;
use App\Models\SuratJalanBongkaranBatam;
use App\Models\Gudang;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TandaTerimaBongkaranBatamController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:tanda-terima-bongkaran-batam-view', ['only' => ['index', 'show']]);
        $this->middleware('permission:tanda-terima-bongkaran-batam-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:tanda-terima-bongkaran-batam-update', ['only' => ['edit', 'update']]);
        $this->middleware('permission:tanda-terima-bongkaran-batam-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $tipe = $request->get('tipe', 'surat_jalan'); // default: surat_jalan
        
        if ($tipe === 'tanda_terima') {
            $query = TandaTerimaBongkaranBatam::with(['suratJalanBongkaran', 'gudang', 'creator']);

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('nomor_tanda_terima', 'LIKE', "%{$search}%")
                      ->orWhere('no_kontainer', 'LIKE', "%{$search}%")
                      ->orWhereHas('suratJalanBongkaran', function($q) use ($search) {
                          $q->where('nomor_surat_jalan', 'LIKE', "%{$search}%");
                      });
                });
            }

            $tandaTerimas = $query->orderBy('created_at', 'desc')->paginate(20);
            
            return view('tanda-terima-bongkaran-batam.index', compact('tandaTerimas'));
        } else {
            // Query for Surat Jalan Bongkaran with lokasi = 'batam'
            $query = SuratJalanBongkaranBatam::with(['bl', 'tandaTerima'])
                ->where('lokasi', 'batam');

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('nomor_surat_jalan', 'LIKE', "%{$search}%")
                      ->orWhere('no_kontainer', 'LIKE', "%{$search}%")
                      ->orWhere('no_bl', 'LIKE', "%{$search}%");
                });
            }

            // Status filter
            if ($request->filled('status')) {
                if ($request->status === 'sudah') {
                    $query->whereHas('tandaTerima');
                } elseif ($request->status === 'belum') {
                    $query->whereDoesntHave('tandaTerima');
                }
            }

            $suratJalans = $query->orderBy('created_at', 'desc')->paginate(20);

            $gudangs = Gudang::where('status', 'aktif')->orderBy('nama_gudang')->get();
            
            return view('tanda-terima-bongkaran-batam.index', compact('suratJalans', 'gudangs'));
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal_tanda_terima' => 'required|date',
            'surat_jalan_bongkaran_id' => 'required|exists:surat_jalan_bongkarans,id',
            'gudang_id' => 'required|exists:gudangs,id',
            'keterangan' => 'nullable|string',
            'lembur' => 'nullable|boolean',
            'nginap' => 'nullable|boolean',
            'tidak_lembur_nginap' => 'nullable|boolean',
        ]);

        try {
            DB::beginTransaction();

            $suratJalan = SuratJalanBongkaranBatam::findOrFail($validated['surat_jalan_bongkaran_id']);
            
            $validated['nomor_tanda_terima'] = TandaTerimaBongkaranBatam::generateNoTandaTerima();
            $validated['no_kontainer'] = $suratJalan->no_kontainer;
            $validated['no_seal'] = $suratJalan->no_seal;
            $validated['kegiatan'] = $suratJalan->kegiatan;
            $validated['status'] = 'active';
            $validated['created_by'] = Auth::id();
            
            $validated['lembur'] = $request->boolean('lembur');
            $validated['nginap'] = $request->boolean('nginap');
            $validated['tidak_lembur_nginap'] = $request->boolean('tidak_lembur_nginap');

            $tandaTerima = TandaTerimaBongkaranBatam::create($validated);

            // Update status surat jalan
            $suratJalan->update([
                'status' => 'completed'
            ]);

            DB::commit();

            return redirect()
                ->route('tanda-terima-bongkaran-batam.index', ['tipe' => 'tanda_terima'])
                ->with('success', 'Tanda terima bongkaran batam berhasil dibuat!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $tandaTerima = TandaTerimaBongkaranBatam::findOrFail($id);
            $tandaTerima->delete();

            return redirect()
                ->route('tanda-terima-bongkaran-batam.index', ['tipe' => 'tanda_terima'])
                ->with('success', 'Tanda terima berhasil dihapus!');

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
