<?php

namespace App\Http\Controllers;

use App\Models\MasterKapal;
use App\Models\ProspekBatam;
use App\Models\SuratJalanBatam;
use App\Models\TandaTerimaBatam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TandaTerimaBatamController extends Controller
{
    /**
     * Map an input tipe_kontainer string to the database enum values for tanda_terimas
     */
    private function mapTipeKontainerValue(?string $value): ?string
    {
        if (! $value) {
            return null;
        }
        $v = strtolower(trim($value));

        $map = [
            'fcl' => 'fcl',
            'lcl' => 'lcl',
            'cargo' => 'cargo',
            'dry' => 'fcl',
            'high cube' => 'fcl',
            'hc' => 'fcl',
            'reefer' => 'fcl',
            'free use' => 'fcl',
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
        $tipe = $request->input('tipe', 'standard');

        $statusOptions = [
            'semua' => 'Semua Status',
            'belum_ada_tanda_terima' => 'Belum Ada Tanda Terima',
            'sudah_ada_tanda_terima' => 'Sudah Ada Tanda Terima',
        ];

        if ($tipe === 'bongkaran') {
            $query = \App\Models\SuratJalanBongkaranBatam::with(['bl', 'tandaTerima'])->where('lokasi', 'batam');

            if ($status === 'belum_ada_tanda_terima') {
                $query->whereDoesntHave('tandaTerima');
            } elseif ($status === 'sudah_ada_tanda_terima') {
                $query->whereHas('tandaTerima');
            }

            if (! empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('nomor_surat_jalan', 'like', "%{$search}%")
                        ->orWhere('supir', 'like', "%{$search}%")
                        ->orWhere('no_plat', 'like', "%{$search}%")
                        ->orWhere('no_kontainer', 'like', "%{$search}%")
                        ->orWhere('no_bl', 'like', "%{$search}%");
                });
            }
            $suratJalans = $query->orderBy('created_at', 'desc')->paginate(50);
        } elseif ($tipe === 'tarik_kosong') {
            $query = \App\Models\SuratJalanTarikKosongBatam::with(['tandaTerima']);

            if ($status === 'belum_ada_tanda_terima') {
                $query->whereDoesntHave('tandaTerima');
            } elseif ($status === 'sudah_ada_tanda_terima') {
                $query->whereHas('tandaTerima');
            }

            if (! empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('no_surat_jalan', 'like', "%{$search}%")
                        ->orWhere('supir', 'like', "%{$search}%")
                        ->orWhere('no_plat', 'like', "%{$search}%")
                        ->orWhere('no_kontainer', 'like', "%{$search}%");
                });
            }
            $suratJalans = $query->orderBy('created_at', 'desc')->paginate(50);
        } elseif ($tipe === 'langsir') {
            $query = \App\Models\LangsirBatam::query();

            if ($status === 'belum_ada_tanda_terima') {
                $query->whereNotExists(function ($q) {
                    $q->select(DB::raw(1))
                        ->from('tanda_terima_batams')
                        ->whereColumn('tanda_terima_batams.no_surat_jalan', 'langsir_batams.no_transaksi');
                });
            } elseif ($status === 'sudah_ada_tanda_terima') {
                $query->whereExists(function ($q) {
                    $q->select(DB::raw(1))
                        ->from('tanda_terima_batams')
                        ->whereColumn('tanda_terima_batams.no_surat_jalan', 'langsir_batams.no_transaksi');
                });
            }

            if (! empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('no_transaksi', 'like', "%{$search}%")
                        ->orWhere('supir', 'like', "%{$search}%")
                        ->orWhere('no_plat', 'like', "%{$search}%")
                        ->orWhere('no_kontainer', 'like', "%{$search}%");
                });
            }
            $suratJalans = $query->orderBy('created_at', 'desc')->paginate(50);
        } else {
            // standard
            $query = SuratJalanBatam::with(['orderBatam.pengirim']);

            // Filter status checkpoint (must be checked point to create Tanda Terima)
            $query->where('status', 'sudah_checkpoint');

            if ($status === 'belum_ada_tanda_terima') {
                $query->whereDoesntHave('tandaTerima');
            } elseif ($status === 'sudah_ada_tanda_terima') {
                $query->whereHas('tandaTerima');
            }

            if (! empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('no_surat_jalan', 'like', "%{$search}%")
                        ->orWhere('supir', 'like', "%{$search}%")
                        ->orWhere('no_plat', 'like', "%{$search}%")
                        ->orWhere('no_kontainer', 'like', "%{$search}%")
                        ->orWhereHas('orderBatam', function ($orderQuery) use ($search) {
                            $orderQuery->whereHas('pengirim', function ($pengirimQuery) use ($search) {
                                $pengirimQuery->where('nama_pengirim', 'like', "%{$search}%");
                            });
                        });
                });
            }
            $suratJalans = $query->orderBy('created_at', 'desc')->paginate(50);
        }

        return view('tanda-terima-batam.select_surat_jalan', compact('suratJalans', 'search', 'status', 'statusOptions', 'tipe'));
    }

    /**
     * Display a listing of tanda terima
     */
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $status = $request->input('status', '');
        $tab = $request->input('tab', 'standard');

        $lastUpdateStr = now()->format('H:i');

        if ($tab === 'bongkaran') {
            $query = \App\Models\TandaTerimaBongkaranBatam::with(['suratJalanBongkaran', 'gudang', 'creator']);
            if (! empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('nomor_tanda_terima', 'LIKE', "%{$search}%")
                        ->orWhere('no_kontainer', 'LIKE', "%{$search}%")
                        ->orWhereHas('suratJalanBongkaran', function ($q) use ($search) {
                            $q->where('nomor_surat_jalan', 'LIKE', "%{$search}%");
                        });
                });
            }
            if (! empty($status)) {
                $query->where('status', $status);
            }
            $tandaTerimas = $query->orderBy('created_at', 'desc')->paginate(25);
        } elseif ($tab === 'tarik_kosong') {
            $query = \App\Models\TandaTerimaSuratJalanTarikKosongBatam::with(['suratJalan', 'creator']);
            if (! empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('no_tanda_terima', 'like', "%{$search}%")
                        ->orWhere('no_surat_jalan', 'like', "%{$search}%")
                        ->orWhere('supir', 'like', "%{$search}%")
                        ->orWhere('no_plat', 'like', "%{$search}%")
                        ->orWhere('no_kontainer', 'like', "%{$search}%");
                });
            }
            $tandaTerimas = $query->orderBy('created_at', 'desc')->paginate(25);
        } else {
            // standard
            $query = TandaTerimaBatam::with(['suratJalan.orderBatam.pengirim']);
            if (! empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('no_surat_jalan', 'like', "%{$search}%")
                        ->orWhere('no_kontainer', 'like', "%{$search}%")
                        ->orWhere('supir', 'like', "%{$search}%")
                        ->orWhere('no_plat', 'like', "%{$search}%")
                        ->orWhere('penerima', 'like', "%{$search}%");
                });
            }
            if (! empty($status)) {
                $query->where('status', $status);
            }
            $tandaTerimas = $query->orderBy('created_at', 'desc')->paginate(25);
        }

        return view('tanda-terima-batam.index', compact('tandaTerimas', 'search', 'status', 'tab', 'lastUpdateStr'));
    }

    /**
     * Show the form for creating a new tanda terima
     */
    public function create(Request $request)
    {
        $suratJalanId = $request->input('surat_jalan_id');
        $tipe = $request->input('tipe', 'standard');

        if (! $suratJalanId) {
            return redirect()->route('tanda-terima-batam.select-surat-jalan')
                ->with('error', 'Silakan pilih surat jalan terlebih dahulu');
        }

        if ($tipe === 'langsir') {
            $langsir = \App\Models\LangsirBatam::findOrFail($suratJalanId);

            // Map LangsirBatam fields to standard SuratJalanBatam structure:
            $mappedSj = new \App\Models\SuratJalanBatam;
            $mappedSj->id = $langsir->id;
            $mappedSj->no_surat_jalan = $langsir->no_transaksi;
            $mappedSj->tanggal_surat_jalan = $langsir->tanggal;
            $mappedSj->supir = $langsir->supir;
            $mappedSj->no_plat = $langsir->no_plat;
            $mappedSj->no_kontainer = $langsir->no_kontainer;
            $mappedSj->size = $langsir->size;
            $mappedSj->no_seal = $langsir->no_seal;
            $mappedSj->kegiatan = 'Langsir: '.$langsir->keterangan;
            $mappedSj->tipe_kontainer = 'fcl';
            $mappedSj->jumlah_kontainer = 1;

            $suratJalan = $mappedSj;
        } else {
            $suratJalan = SuratJalanBatam::with(['orderBatam.pengirim'])->findOrFail($suratJalanId);

            if ($suratJalan->tandaTerima) {
                return redirect()->route('tanda-terima-batam.edit', $suratJalan->tandaTerima->id)
                    ->with('info', 'Tanda terima untuk surat jalan ini sudah ada.');
            }
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

        // Additional data needed by create.blade.php
        $masterKegiatans = \App\Models\MasterKegiatan::where('status', 'aktif')
            ->where('type', 'kegiatan surat jalan')
            ->orderBy('nama_kegiatan')->get();
        $karyawans = \App\Models\Karyawan::orderBy('nama_lengkap')->get();
        $masterTujuanKirims = \App\Models\MasterTujuanKirim::where('status', 'active')->orderBy('nama_tujuan')->get();

        return view('tanda-terima-batam.create', compact(
            'suratJalan',
            'masterKapals',
            'pengirims',
            'terms',
            'jenisBarangs',
            'karyawanSupirs',
            'karyawans',
            'kranisKenek',
            'stockKontainers',
            'masterPenerimaList',
            'gudangs',
            'masterKegiatans',
            'masterTujuanKirims'
        ));
    }

    /**
     * Store a newly created tanda terima in storage
     */
    public function store(Request $request)
    {
        $tipe = $request->input('tipe', 'standard');

        $validationRules = [
            'tanggal' => 'required|date',
        ];

        if ($tipe === 'langsir') {
            $validationRules['surat_jalan_id'] = 'required|exists:langsir_batams,id';
        } else {
            $validationRules['surat_jalan_id'] = 'required|exists:surat_jalan_batams,id';
        }

        $request->validate($validationRules);

        DB::beginTransaction();
        try {
            if ($tipe === 'langsir') {
                $langsir = \App\Models\LangsirBatam::findOrFail($request->surat_jalan_id);
                $suratJalan = new \App\Models\SuratJalanBatam;
                $suratJalan->id = $langsir->id;
                $suratJalan->no_surat_jalan = $langsir->no_transaksi;
                $suratJalan->tanggal_surat_jalan = $langsir->tanggal;
                $suratJalan->supir = $langsir->supir;
                $suratJalan->no_plat = $langsir->no_plat;
                $suratJalan->no_kontainer = $langsir->no_kontainer;
                $suratJalan->size = $langsir->size;
                $suratJalan->no_seal = $langsir->no_seal;
                $suratJalan->kegiatan = 'Langsir: '.$langsir->keterangan;
                $suratJalan->tipe_kontainer = 'fcl';
                $suratJalan->jumlah_kontainer = 1;
            } else {
                $suratJalan = SuratJalanBatam::with(['orderBatam.pengirim'])->findOrFail($request->surat_jalan_id);
            }

            $tandaTerima = new TandaTerimaBatam;
            if ($tipe !== 'langsir') {
                $tandaTerima->surat_jalan_batam_id = $suratJalan->id;
            }
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
            if ($tipe !== 'langsir') {
                $tandaTerima->autoLinkProspek();
            }

            DB::commit();

            return redirect()->route('tanda-terima-batam.index')->with('success', 'Tanda Terima Batam berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error store TandaTerimaBatam: '.$e->getMessage());

            return redirect()->back()->withInput()->with('error', 'Gagal membuat Tanda Terima: '.$e->getMessage());
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
            $dataToUpdate = $request->except([
                'jumlah', 'satuan', 'panjang', 'lebar', 'tinggi',
                'meter_kubik', 'tonase', 'nama_barang', 'nomor_kontainer', 'no_seal',
                'gambar_checkpoint', 'lembur', 'nginap', 'tidak_lembur_nginap',
            ]);

            $dataToUpdate['lembur'] = $request->boolean('lembur');
            $dataToUpdate['nginap'] = $request->boolean('nginap');
            $dataToUpdate['tidak_lembur_nginap'] = $request->boolean('tidak_lembur_nginap');

            $dimensiDetails = [];
            if ($request->has('jumlah') && is_array($request->jumlah)) {
                $namaBarangArray = $request->nama_barang ?? [];
                $jumlahArray = $request->jumlah;
                $satuanArray = $request->satuan ?? [];
                $panjangArray = $request->panjang ?? [];
                $lebarArray = $request->lebar ?? [];
                $tinggiArray = $request->tinggi ?? [];
                $meterKubikArray = $request->meter_kubik ?? [];
                $tonaseArray = $request->tonase ?? [];

                foreach ($jumlahArray as $index => $jumlah) {
                    if (empty($namaBarangArray[$index]) && empty($jumlah) && empty($satuanArray[$index]) &&
                        empty($panjangArray[$index]) && empty($lebarArray[$index]) &&
                        empty($tinggiArray[$index]) && empty($tonaseArray[$index])) {
                        continue;
                    }

                    $dimensiDetails[] = [
                        'nama_barang' => $namaBarangArray[$index] ?? null,
                        'jumlah' => $jumlah ? (int) $jumlah : null,
                        'satuan' => $satuanArray[$index] ?? null,
                        'panjang' => isset($panjangArray[$index]) && $panjangArray[$index] !== '' ? round((float) $panjangArray[$index], 3) : null,
                        'lebar' => isset($lebarArray[$index]) && $lebarArray[$index] !== '' ? round((float) $lebarArray[$index], 3) : null,
                        'tinggi' => isset($tinggiArray[$index]) && $tinggiArray[$index] !== '' ? round((float) $tinggiArray[$index], 3) : null,
                        'meter_kubik' => isset($meterKubikArray[$index]) && $meterKubikArray[$index] !== '' ? round((float) $meterKubikArray[$index], 3) : null,
                        'tonase' => isset($tonaseArray[$index]) && $tonaseArray[$index] !== '' ? round((float) $tonaseArray[$index], 3) : null,
                    ];
                }
            }

            if (! empty($dimensiDetails)) {
                $dataToUpdate['dimensi_details'] = $dimensiDetails;
                $dataToUpdate['dimensi_items'] = $dimensiDetails;
                $first = $dimensiDetails[0];
                $dataToUpdate['jumlah'] = $first['jumlah'];
                $dataToUpdate['satuan'] = $first['satuan'];
                $dataToUpdate['panjang'] = $first['panjang'];
                $dataToUpdate['lebar'] = $first['lebar'];
                $dataToUpdate['tinggi'] = $first['tinggi'];
                $dataToUpdate['meter_kubik'] = $first['meter_kubik'];
                $dataToUpdate['tonase'] = $first['tonase'];
            }

            if ($request->has('nomor_kontainer') && is_array($request->nomor_kontainer)) {
                $nomorKontainers = array_filter($request->nomor_kontainer, function ($value) {
                    return ! empty(trim($value));
                });
                if (! empty($nomorKontainers)) {
                    $dataToUpdate['no_kontainer'] = implode(',', $nomorKontainers);
                }
            }
            if ($request->has('no_seal') && is_array($request->no_seal)) {
                $noSeals = array_filter($request->no_seal, function ($value) {
                    return ! empty(trim($value));
                });
                if (! empty($noSeals)) {
                    $dataToUpdate['no_seal'] = implode(',', $noSeals);
                }
            }

            $tandaTerima = TandaTerimaBatam::findOrFail($id);
            $tandaTerima->update($dataToUpdate);

            // Re-link or update Prospek if needed
            $tandaTerima->autoLinkProspek();

            DB::commit();

            return redirect()->route('tanda-terima-batam.index')->with('success', 'Tanda Terima Batam berhasil diupdate');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error update TandaTerimaBatam: '.$e->getMessage());

            return redirect()->back()->withInput()->with('error', 'Gagal update Tanda Terima: '.$e->getMessage());
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
            Log::error('Error destroy TandaTerimaBatam: '.$e->getMessage());

            return redirect()->back()->with('error', 'Gagal menghapus Tanda Terima');
        }
    }
}
