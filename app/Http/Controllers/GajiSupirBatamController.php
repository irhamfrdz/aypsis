<?php

namespace App\Http\Controllers;

use App\Models\GajiSupirBatam;
use App\Models\Karyawan;
use Illuminate\Http\Request;

class GajiSupirBatamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $bulan = $request->get('bulan', '');
        $tahun = $request->get('tahun', '');
        $karyawanId = $request->get('karyawan_id', '');
        $statusPembayaran = $request->get('status_pembayaran', '');

        $query = GajiSupirBatam::with('karyawan');

        if ($search) {
            $query->whereHas('karyawan', function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%")
                    ->orWhere('plat', 'like', "%{$search}%");
            });
        }

        if ($bulan !== '') {
            $query->where('periode_bulan', $bulan);
        }

        if ($tahun !== '') {
            $query->where('periode_tahun', $tahun);
        }

        if ($karyawanId !== '') {
            $query->where('karyawan_id', $karyawanId);
        }

        if ($statusPembayaran !== '') {
            $query->where('status_pembayaran', $statusPembayaran);
        }

        $gajiList = $query->orderBy('periode_tahun', 'desc')
            ->orderBy('periode_bulan', 'desc')
            ->orderBy('id', 'desc')
            ->paginate($request->get('per_page', 15));

        // Get list of Batam supir for filter dropdown
        $supirList = Karyawan::where('cabang', 'BATAM')
            ->where(function ($q) {
                $q->where('pekerjaan', 'like', 'SUPIR%')
                    ->orWhere('pekerjaan', 'like', '%DRIVER%');
            })
            ->orderBy('nama_lengkap')
            ->get();

        return view('gaji-supir-batam.index', compact('gajiList', 'supirList', 'search', 'bulan', 'tahun', 'karyawanId', 'statusPembayaran'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $supirList = Karyawan::where('cabang', 'BATAM')
            ->where(function ($q) {
                $q->where('pekerjaan', 'like', 'SUPIR%')
                    ->orWhere('pekerjaan', 'like', '%DRIVER%');
            })
            ->orderBy('nama_lengkap')
            ->get();

        return view('gaji-supir-batam.create', compact('supirList'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'karyawan_id' => 'required|exists:karyawans,id',
            'periode_bulan' => 'required|integer|min:1|max:12',
            'periode_tahun' => 'required|integer|min:2000|max:2100',
            'gaji_pokok' => 'required|numeric|min:0',
            'tunjangan_kehadiran' => 'nullable|numeric|min:0',
            'tunjangan_makan' => 'nullable|numeric|min:0',
            'tunjangan_lainnya' => 'nullable|numeric|min:0',
            'potongan_bpjs' => 'nullable|numeric|min:0',
            'potongan_pinjaman' => 'nullable|numeric|min:0',
            'potongan_lainnya' => 'nullable|numeric|min:0',
            'status_pembayaran' => 'required|in:PENDING,PAID,CANCELLED',
            'tanggal_dibayar' => 'nullable|date',
            'keterangan' => 'nullable|string',
        ]);

        // Unique validation
        $exists = GajiSupirBatam::where('karyawan_id', $validated['karyawan_id'])
            ->where('periode_bulan', $validated['periode_bulan'])
            ->where('periode_tahun', $validated['periode_tahun'])
            ->exists();

        if ($exists) {
            return back()->withInput()->with('error', 'Gaji supir untuk periode tersebut sudah ada!');
        }

        $data = $validated;
        $data['tunjangan_kehadiran'] = $data['tunjangan_kehadiran'] ?? 0;
        $data['tunjangan_makan'] = $data['tunjangan_makan'] ?? 0;
        $data['tunjangan_lainnya'] = $data['tunjangan_lainnya'] ?? 0;
        $data['potongan_bpjs'] = $data['potongan_bpjs'] ?? 0;
        $data['potongan_pinjaman'] = $data['potongan_pinjaman'] ?? 0;
        $data['potongan_lainnya'] = $data['potongan_lainnya'] ?? 0;

        $data['total_gaji'] = $data['gaji_pokok'] +
                              $data['tunjangan_kehadiran'] +
                              $data['tunjangan_makan'] +
                              $data['tunjangan_lainnya'] -
                              $data['potongan_bpjs'] -
                              $data['potongan_pinjaman'] -
                              $data['potongan_lainnya'];

        if ($data['status_pembayaran'] === 'PAID' && empty($data['tanggal_dibayar'])) {
            $data['tanggal_dibayar'] = now()->format('Y-m-d');
        }

        GajiSupirBatam::create($data);

        return redirect()->route('gaji-supir-batam.index')
            ->with('success', 'Gaji supir berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $gaji = GajiSupirBatam::with('karyawan')->findOrFail($id);

        return view('gaji-supir-batam.show', compact('gaji'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $gaji = GajiSupirBatam::findOrFail($id);
        $supirList = Karyawan::where('cabang', 'BATAM')
            ->where(function ($q) {
                $q->where('pekerjaan', 'like', 'SUPIR%')
                    ->orWhere('pekerjaan', 'like', '%DRIVER%');
            })
            ->orderBy('nama_lengkap')
            ->get();

        return view('gaji-supir-batam.edit', compact('gaji', 'supirList'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $gaji = GajiSupirBatam::findOrFail($id);

        $validated = $request->validate([
            'karyawan_id' => 'required|exists:karyawans,id',
            'periode_bulan' => 'required|integer|min:1|max:12',
            'periode_tahun' => 'required|integer|min:2000|max:2100',
            'gaji_pokok' => 'required|numeric|min:0',
            'tunjangan_kehadiran' => 'nullable|numeric|min:0',
            'tunjangan_makan' => 'nullable|numeric|min:0',
            'tunjangan_lainnya' => 'nullable|numeric|min:0',
            'potongan_bpjs' => 'nullable|numeric|min:0',
            'potongan_pinjaman' => 'nullable|numeric|min:0',
            'potongan_lainnya' => 'nullable|numeric|min:0',
            'status_pembayaran' => 'required|in:PENDING,PAID,CANCELLED',
            'tanggal_dibayar' => 'nullable|date',
            'keterangan' => 'nullable|string',
        ]);

        // Unique validation excluding self
        $exists = GajiSupirBatam::where('karyawan_id', $validated['karyawan_id'])
            ->where('periode_bulan', $validated['periode_bulan'])
            ->where('periode_tahun', $validated['periode_tahun'])
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return back()->withInput()->with('error', 'Gaji supir untuk periode tersebut sudah ada!');
        }

        $data = $validated;
        $data['tunjangan_kehadiran'] = $data['tunjangan_kehadiran'] ?? 0;
        $data['tunjangan_makan'] = $data['tunjangan_makan'] ?? 0;
        $data['tunjangan_lainnya'] = $data['tunjangan_lainnya'] ?? 0;
        $data['potongan_bpjs'] = $data['potongan_bpjs'] ?? 0;
        $data['potongan_pinjaman'] = $data['potongan_pinjaman'] ?? 0;
        $data['potongan_lainnya'] = $data['potongan_lainnya'] ?? 0;

        $data['total_gaji'] = $data['gaji_pokok'] +
                              $data['tunjangan_kehadiran'] +
                              $data['tunjangan_makan'] +
                              $data['tunjangan_lainnya'] -
                              $data['potongan_bpjs'] -
                              $data['potongan_pinjaman'] -
                              $data['potongan_lainnya'];

        if ($data['status_pembayaran'] === 'PAID' && empty($data['tanggal_dibayar'])) {
            $data['tanggal_dibayar'] = now()->format('Y-m-d');
        }

        if ($data['status_pembayaran'] !== 'PAID') {
            $data['tanggal_dibayar'] = null;
        }

        $gaji->update($data);

        return redirect()->route('gaji-supir-batam.index')
            ->with('success', 'Gaji supir berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $gaji = GajiSupirBatam::findOrFail($id);
        $gaji->delete();

        return redirect()->route('gaji-supir-batam.index')
            ->with('success', 'Gaji supir berhasil dihapus!');
    }

    /**
     * Mark salary as paid.
     */
    public function bayar(Request $request, $id)
    {
        $gaji = GajiSupirBatam::findOrFail($id);
        $gaji->update([
            'status_pembayaran' => 'PAID',
            'tanggal_dibayar' => now()->format('Y-m-d'),
        ]);

        return redirect()->route('gaji-supir-batam.index')
            ->with('success', 'Gaji supir berhasil dibayar!');
    }
}
