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
        $startDate = $request->get('start_date', '');
        $endDate = $request->get('end_date', '');

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

        if ($startDate !== '') {
            $query->whereDate('tanggal_mulai', '>=', $startDate);
        }

        if ($endDate !== '') {
            $query->whereDate('tanggal_selesai', '<=', $endDate);
        }

        $gajiList = $query->orderBy('tanggal_mulai', 'desc')
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

        return view('gaji-supir-batam.index', compact('gajiList', 'supirList', 'search', 'bulan', 'tahun', 'karyawanId', 'statusPembayaran', 'startDate', 'endDate'));
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
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'gaji_pokok' => 'required|numeric|min:0',
            'biaya_bensin' => 'nullable|numeric|min:0',
            'status_pembayaran' => 'required|in:PENDING,PAID,CANCELLED',
            'tanggal_dibayar' => 'nullable|date',
            'keterangan' => 'nullable|string',
        ]);

        // Overlap validation
        $overlap = GajiSupirBatam::where('karyawan_id', $validated['karyawan_id'])
            ->where(function ($q) use ($validated) {
                $q->where(function ($q1) use ($validated) {
                    $q1->whereDate('tanggal_mulai', '<=', $validated['tanggal_selesai'])
                        ->whereDate('tanggal_selesai', '>=', $validated['tanggal_mulai']);
                });
            })
            ->exists();

        if ($overlap) {
            return back()->withInput()->with('error', 'Gaji supir untuk tanggal periode tersebut sudah tumpang tindih dengan data yang ada!');
        }

        $data = $validated;
        $data['biaya_bensin'] = $validated['biaya_bensin'] ?? 0;

        // Derive month and year from tanggal_mulai for fallback fields
        $startDateObj = \Carbon\Carbon::parse($validated['tanggal_mulai']);
        $data['periode_bulan'] = (int) $startDateObj->format('n');
        $data['periode_tahun'] = (int) $startDateObj->format('Y');
        $data['periode_minggu'] = 1;
        $data['total_gaji'] = $data['gaji_pokok'] - $data['biaya_bensin'];

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

        $karyawan = $gaji->karyawan;
        $namaPanggilan = $karyawan->nama_panggilan;

        $startDate = $gaji->tanggal_mulai;
        $endDate = $gaji->tanggal_selesai;

        if (! $startDate || ! $endDate) {
            $bulan = (int) $gaji->periode_bulan;
            $tahun = (int) $gaji->periode_tahun;
            $periodeMinggu = (int) ($gaji->periode_minggu ?? 1);

            if ($periodeMinggu == 1) {
                $startDate = \Carbon\Carbon::create($tahun, $bulan, 1)->startOfDay();
                $endDate = \Carbon\Carbon::create($tahun, $bulan, 15)->endOfDay();
            } else {
                $startDate = \Carbon\Carbon::create($tahun, $bulan, 16)->startOfDay();
                $endDate = \Carbon\Carbon::create($tahun, $bulan, 1)->endOfMonth()->endOfDay();
            }
        } else {
            $startDate = \Carbon\Carbon::parse($startDate)->startOfDay();
            $endDate = \Carbon\Carbon::parse($endDate)->endOfDay();
        }

        $regularSJs = \App\Models\SuratJalanBatam::where('supir', $namaPanggilan)
            ->whereBetween('tanggal_surat_jalan', [$startDate, $endDate])
            ->get();

        $bongkaranSJs = \App\Models\SuratJalanBongkaranBatam::where('supir', $namaPanggilan)
            ->whereBetween('tanggal_surat_jalan', [$startDate, $endDate])
            ->get();

        $tarikKosongSJs = \App\Models\SuratJalanTarikKosongBatam::where('supir', $namaPanggilan)
            ->whereBetween('tanggal_surat_jalan', [$startDate, $endDate])
            ->get();

        $waybills = [];
        foreach ($regularSJs as $sj) {
            $waybills[] = [
                'type' => 'Regular',
                'no_surat_jalan' => $sj->no_surat_jalan,
                'tanggal' => $sj->tanggal_surat_jalan->format('d/m/Y'),
                'rit' => is_numeric($sj->uang_jalan) ? (float) $sj->uang_jalan : 0,
            ];
        }
        foreach ($bongkaranSJs as $sj) {
            $waybills[] = [
                'type' => 'Bongkaran',
                'no_surat_jalan' => $sj->nomor_surat_jalan,
                'tanggal' => $sj->tanggal_surat_jalan->format('d/m/Y'),
                'rit' => is_numeric($sj->uang_jalan_nominal) ? (float) $sj->uang_jalan_nominal : 0,
            ];
        }
        foreach ($tarikKosongSJs as $sj) {
            $waybills[] = [
                'type' => 'Tarik Kosong',
                'no_surat_jalan' => $sj->no_surat_jalan,
                'tanggal' => $sj->tanggal_surat_jalan->format('d/m/Y'),
                'rit' => is_numeric($sj->uang_jalan) ? (float) $sj->uang_jalan : 0,
            ];
        }

        return view('gaji-supir-batam.show', compact('gaji', 'waybills'));
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
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'gaji_pokok' => 'required|numeric|min:0',
            'biaya_bensin' => 'nullable|numeric|min:0',
            'status_pembayaran' => 'required|in:PENDING,PAID,CANCELLED',
            'tanggal_dibayar' => 'nullable|date',
            'keterangan' => 'nullable|string',
        ]);

        // Overlap validation excluding self
        $overlap = GajiSupirBatam::where('karyawan_id', $validated['karyawan_id'])
            ->where('id', '!=', $id)
            ->where(function ($q) use ($validated) {
                $q->where(function ($q1) use ($validated) {
                    $q1->whereDate('tanggal_mulai', '<=', $validated['tanggal_selesai'])
                        ->whereDate('tanggal_selesai', '>=', $validated['tanggal_mulai']);
                });
            })
            ->exists();

        if ($overlap) {
            return back()->withInput()->with('error', 'Gaji supir untuk tanggal periode tersebut sudah tumpang tindih dengan data yang ada!');
        }

        $data = $validated;
        $data['biaya_bensin'] = $validated['biaya_bensin'] ?? 0;

        $startDateObj = \Carbon\Carbon::parse($validated['tanggal_mulai']);
        $data['periode_bulan'] = (int) $startDateObj->format('n');
        $data['periode_tahun'] = (int) $startDateObj->format('Y');
        $data['total_gaji'] = $data['gaji_pokok'] - $data['biaya_bensin'];

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

    /**
     * AJAX endpoint to calculate waybill earnings using custom date range.
     */
    public function calculate(Request $request)
    {
        $validated = $request->validate([
            'karyawan_id' => 'required|exists:karyawans,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
        ]);

        $karyawan = Karyawan::findOrFail($validated['karyawan_id']);
        $namaPanggilan = $karyawan->nama_panggilan;

        $startDate = \Carbon\Carbon::parse($validated['tanggal_mulai'])->startOfDay();
        $endDate = \Carbon\Carbon::parse($validated['tanggal_selesai'])->endOfDay();

        // Search in SuratJalanBatam, SuratJalanBongkaranBatam, and SuratJalanTarikKosongBatam
        $regularSJs = \App\Models\SuratJalanBatam::where('supir', $namaPanggilan)
            ->whereBetween('tanggal_surat_jalan', [$startDate, $endDate])
            ->get();

        $bongkaranSJs = \App\Models\SuratJalanBongkaranBatam::where('supir', $namaPanggilan)
            ->whereBetween('tanggal_surat_jalan', [$startDate, $endDate])
            ->get();

        $tarikKosongSJs = \App\Models\SuratJalanTarikKosongBatam::where('supir', $namaPanggilan)
            ->whereBetween('tanggal_surat_jalan', [$startDate, $endDate])
            ->get();

        $totalRit = 0;
        $waybills = [];

        foreach ($regularSJs as $sj) {
            $ritVal = is_numeric($sj->uang_jalan) ? (float) $sj->uang_jalan : 0;
            $totalRit += $ritVal;
            $waybills[] = [
                'id' => $sj->id,
                'type' => 'Regular',
                'no_surat_jalan' => $sj->no_surat_jalan,
                'tanggal' => $sj->tanggal_surat_jalan->format('d/m/Y'),
                'rit' => $ritVal,
            ];
        }

        foreach ($bongkaranSJs as $sj) {
            $ritVal = is_numeric($sj->uang_jalan_nominal) ? (float) $sj->uang_jalan_nominal : 0;
            $totalRit += $ritVal;
            $waybills[] = [
                'id' => $sj->id,
                'type' => 'Bongkaran',
                'no_surat_jalan' => $sj->nomor_surat_jalan,
                'tanggal' => $sj->tanggal_surat_jalan->format('d/m/Y'),
                'rit' => $ritVal,
            ];
        }

        foreach ($tarikKosongSJs as $sj) {
            $ritVal = is_numeric($sj->uang_jalan) ? (float) $sj->uang_jalan : 0;
            $totalRit += $ritVal;
            $waybills[] = [
                'id' => $sj->id,
                'type' => 'Tarik Kosong',
                'no_surat_jalan' => $sj->no_surat_jalan,
                'tanggal' => $sj->tanggal_surat_jalan->format('d/m/Y'),
                'rit' => $ritVal,
            ];
        }

        // Query biaya bensin for this driver in the period
        $bensinList = \App\Models\BiayaBensin::where('karyawan_id', $karyawan->id)
            ->whereBetween('tanggal', [$startDate->toDateString(), $endDate->toDateString()])
            ->orderBy('tanggal')
            ->get();

        $totalBiayaBensin = 0;
        $bensinItems = [];
        foreach ($bensinList as $b) {
            $biaya = is_numeric($b->biaya) ? (float) $b->biaya : 0;
            $totalBiayaBensin += $biaya;
            $bensinItems[] = [
                'id' => $b->id,
                'tanggal' => \Carbon\Carbon::parse($b->tanggal)->format('d/m/Y'),
                'liter' => (float) $b->liter,
                'biaya' => $biaya,
                'keterangan' => $b->keterangan ?? '-',
            ];
        }

        return response()->json([
            'gaji_pokok' => $totalRit,
            'waybills' => $waybills,
            'total_biaya_bensin' => $totalBiayaBensin,
            'bensin_items' => $bensinItems,
        ]);
    }
}
