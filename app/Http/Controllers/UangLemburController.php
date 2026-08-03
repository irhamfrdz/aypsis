<?php

namespace App\Http\Controllers;

use App\Models\UangLembur;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Carbon\Carbon;

class UangLemburController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $query = UangLembur::with('karyawan')->latest();
        
        if ($search) {
            $query->whereHas('karyawan', function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }
        
        $lemburs = $query->paginate(15);
        
        return view('uang-lembur.index', compact('lemburs'));
    }

    public function create()
    {
        $karyawans = Karyawan::whereNull('tanggal_berhenti')->orderBy('nama_lengkap')->get();
        return view('uang-lembur.create', compact('karyawans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'karyawan_id' => 'required|exists:karyawans,id',
            'tanggal' => 'required|date',
            'tipe_hari' => 'required|in:Hari Kerja,Hari Libur',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
            'keterangan' => 'nullable|string',
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $karyawan = Karyawan::findOrFail($validated['karyawan_id']);
        
        $result = $this->calculateNominal($karyawan->penempatan, $validated['tipe_hari'], $validated['jam_mulai'], $validated['jam_selesai']);
        
        $validated['total_jam'] = $result['total_jam'];
        $validated['nominal_uang'] = $result['nominal_uang'];

        UangLembur::create($validated);

        return redirect()->route('uang-lembur.index')->with('success', 'Data uang lembur berhasil ditambahkan.');
    }

    public function edit(UangLembur $uangLembur)
    {
        $karyawans = Karyawan::whereNull('tanggal_berhenti')->orderBy('nama_lengkap')->get();
        return view('uang-lembur.edit', compact('uangLembur', 'karyawans'));
    }

    public function update(Request $request, UangLembur $uangLembur)
    {
        $validated = $request->validate([
            'karyawan_id' => 'required|exists:karyawans,id',
            'tanggal' => 'required|date',
            'tipe_hari' => 'required|in:Hari Kerja,Hari Libur',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
            'keterangan' => 'nullable|string',
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $karyawan = Karyawan::findOrFail($validated['karyawan_id']);
        
        $result = $this->calculateNominal($karyawan->penempatan, $validated['tipe_hari'], $validated['jam_mulai'], $validated['jam_selesai']);
        
        $validated['total_jam'] = $result['total_jam'];
        $validated['nominal_uang'] = $result['nominal_uang'];

        $uangLembur->update($validated);

        return redirect()->route('uang-lembur.index')->with('success', 'Data uang lembur berhasil diupdate.');
    }

    public function destroy(UangLembur $uangLembur)
    {
        $uangLembur->delete();
        return redirect()->route('uang-lembur.index')->with('success', 'Data uang lembur berhasil dihapus.');
    }

    private function calculateNominal($penempatan, $tipe_hari, $jam_mulai, $jam_selesai)
    {
        $mulai = Carbon::parse($jam_mulai);
        $selesai = Carbon::parse($jam_selesai);
        
        // Handle cross-midnight (jam_selesai is less than jam_mulai)
        if ($selesai < $mulai) {
            $selesai->addDay();
        }
        
        $total_jam = $selesai->diffInMinutes($mulai) / 60;
        $nominal = 0;
        
        $penempatan = strtoupper(trim($penempatan));
        
        $endHour = (int) $selesai->format('H');
        $endHourCheck = $endHour;
        if ($endHourCheck >= 0 && $endHourCheck <= 8 && $selesai > $mulai && $selesai->format('Y-m-d') > $mulai->format('Y-m-d')) {
            $endHourCheck += 24; 
        }

        if (in_array($penempatan, ['JAKARTA PELABUHAN', 'JAKARTA KRANI'])) {
            if ($tipe_hari == 'Hari Kerja') {
                if ($endHourCheck >= 24 || ($endHourCheck >= 0 && $endHourCheck <= 8)) {
                    $nominal = 50000;
                } else {
                    $nominal = 30000;
                }
            } else { 
                if ($endHourCheck >= 18 || $endHourCheck < 8) { 
                    $nominal = 100000;
                } else {
                    $nominal = 50000;
                }
            }
        } 
        elseif ($penempatan == 'JAKARTA PELABUHAN 1') {
            $nominal = 5000 * $total_jam;
        } 
        elseif (in_array($penempatan, ['JAKARTA GARASI', 'JAKARTA HARIAN', 'JAKARTA SUPIR'])) {
            if ($tipe_hari == 'Hari Kerja') {
                if ($endHourCheck >= 24 || ($endHourCheck >= 0 && $endHourCheck <= 8)) {
                    $nominal = 60000;
                } else {
                    $nominal = 40000;
                }
            } else { 
                if ($endHourCheck >= 18 || $endHourCheck < 8) {
                    $nominal = 110000;
                } else {
                    $nominal = 80000;
                }
            }
        }
        else {
            $nominal = 0;
        }

        return [
            'total_jam' => round($total_jam, 2),
            'nominal_uang' => $nominal
        ];
    }
}
