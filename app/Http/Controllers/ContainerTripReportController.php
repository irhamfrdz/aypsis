<?php

namespace App\Http\Controllers;

use App\Models\ContainerTrip;
use App\Models\Payment;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ContainerTripReportController extends Controller
{
    /**
     * Display the container trip report/dashboard with calculation logic.
     */
    public function dashboard(Request $request)
    {
        // 1. Ambil data dengan eager loading vendor
        $query = ContainerTrip::with('vendor');

        // Optional: Filter by specific month/year if needed (not in strict requirement but good practice)
        
        $trips = $query->get();
        $data_laporan = [];

        foreach ($trips as $trip) {
            $awal = Carbon::parse($trip->tgl_ambil);
            $akhir = $trip->tgl_kembali ? Carbon::parse($trip->tgl_kembali) : now();

            // Loop per bulan
            $current = $awal->copy()->startOfMonth();
            while ($current <= $akhir->copy()->startOfMonth()) {
                
                $periode_str = $current->format('Y-m'); // e.g., 2024-01

                // Cek apakah periode ini sudah lunas di history bayar
                // We assume one payment per container per period
                $sudah_bayar = Payment::where('container_trip_id', $trip->id)
                                ->where('periode_bulan', $periode_str)
                                ->first();

                // Hitung Tanggal Efektif
                $tgl_mulai_hitung = ($current->copy()->startOfMonth() > $awal) ? $current->copy()->startOfMonth() : $awal;
                $tgl_selesai_hitung = ($current->copy()->endOfMonth() < $akhir) ? $current->copy()->endOfMonth() : $akhir;
                
                // Tambah 1 hari agar inklusif (contoh 1-31 = 31 hari)
                $selisih_hari = $tgl_mulai_hitung->diffInDays($tgl_selesai_hitung) + 1;
                
                // Fallback: Jika tanggal berakhir sebelum tanggal mulai (edge case), set hari 0
                if ($tgl_mulai_hitung > $tgl_selesai_hitung) {
                    $selisih_hari = 0;
                }

                // Logika Hitung DPP (Prorata 30 Hari)
                $dpp = 0;
                $harga_sewa = $trip->harga_sewa; // from container_trips table
                
                if ($trip->vendor && $trip->vendor->tipe_hitung == 'bulanan') {
                    // Jika bulanan & sudah lewat 28 hari, dianggap 1 bulan penuh
                    if ($selisih_hari >= 28) {
                        $dpp = $harga_sewa;
                    } else {
                        $dpp = ($selisih_hari / 30) * $harga_sewa;
                    }
                } else {
                    // Harian
                    $dpp = ($selisih_hari / 30) * $harga_sewa;
                }

                // Hitung Pajak & Materai
                $ppn = $dpp * 0.11;
                $pph23 = $dpp * 0.02;
                $total_bruto = $dpp + $ppn;
                
                // Logika Materai (Jika > 5 Juta)
                $materai = ($total_bruto > 5000000) ? 10000 : 0;
                
                $netto = $total_bruto - $pph23 + $materai;

                $data_laporan[] = [
                    'id_trip' => $trip->id,
                    'vendor' => $trip->vendor ? $trip->vendor->nama_vendor : 'N/A',
                    'no_kontainer' => $trip->no_kontainer,
                    'periode' => $periode_str,
                    'hari' => $selisih_hari,
                    'dpp' => $dpp,
                    'ppn' => $ppn,
                    'pph23' => $pph23,
                    'materai' => $materai,
                    'total' => $netto,
                    'status' => $sudah_bayar ? 'LUNAS' : 'BELUM BAYAR',
                    'no_invoice' => $sudah_bayar ? $sudah_bayar->no_invoice : null,
                    'tgl_ambil' => $trip->tgl_ambil->format('Y-m-d'),
                ];

                $current->addMonth();
            }
        }

        // Sort by periode desc, then vendor
        usort($data_laporan, function ($a, $b) {
            return strcmp($b['periode'], $a['periode']);
        });

        return view('container-trip.report', compact('data_laporan'));
    }

    /**
     * Process payment for a specific container trip period.
     */
    public function bayar(Request $request)
    {
        $request->validate([
            'container_trip_id' => 'required|exists:container_trips,id',
            'periode_bulan' => 'required',
            'no_invoice' => 'required|string|max:255',
        ]);

        // Prevent double payment
        $exists = Payment::where('container_trip_id', $request->container_trip_id)
                        ->where('periode_bulan', $request->periode_bulan)
                        ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'Tagihan periode ini sudah dibayar sebelumnya!');
        }

        Payment::create([
            'container_trip_id' => $request->container_trip_id,
            'periode_bulan' => $request->periode_bulan,
            'no_invoice' => $request->no_invoice,
        ]);

        return redirect()->back()->with('success', 'Tagihan berhasil ditandai Lunas!');
    }

    /**
     * Display summary dashboard (aggregated data).
     */
    public function summary()
    {
        // Re-use logic or call private method to get data
        // For simplicity, we'll duplicate the logic here or refactor if needed.
        // Given constraint "don't change existing files", we keep logic here.
        
        $trips = ContainerTrip::with('vendor')->get();
        $summary = [];

        foreach ($trips as $trip) {
            $awal = Carbon::parse($trip->tgl_ambil);
            $akhir = $trip->tgl_kembali ? Carbon::parse($trip->tgl_kembali) : now();
            
            $current = $awal->copy()->startOfMonth();
            while ($current <= $akhir->copy()->startOfMonth()) {
                $periode_str = $current->format('Y-m');
                                
                $sudah_bayar = Payment::where('container_trip_id', $trip->id)
                                ->where('periode_bulan', $periode_str)
                                ->exists();

                if (!$sudah_bayar) {
                    $tgl_mulai = ($current->copy()->startOfMonth() > $awal) ? $current->copy()->startOfMonth() : $awal;
                    $tgl_selesai = ($current->copy()->endOfMonth() < $akhir) ? $current->copy()->endOfMonth() : $akhir;
                    $hari = $tgl_mulai->diffInDays($tgl_selesai) + 1;
                    if ($tgl_mulai > $tgl_selesai) $hari = 0;

                    $dpp = 0;
                    $harga = $trip->harga_sewa;

                    if ($trip->vendor && $trip->vendor->tipe_hitung == 'bulanan') {
                        if ($hari >= 28) {
                            $dpp = $harga;
                        } else {
                            $dpp = ($hari / 30) * $harga;
                        }
                    } else {
                        $dpp = ($hari / 30) * $harga;
                    }

                    $ppn = $dpp * 0.11;
                    $pph = $dpp * 0.02;
                    $bruto = $dpp + $ppn;
                    $materai = ($bruto > 5000000) ? 10000 : 0;
                    $netto = $bruto - $pph + $materai;

                    $vendor_name = $trip->vendor ? $trip->vendor->nama_vendor : 'Unknown';

                    if (!isset($summary[$vendor_name])) {
                        $summary[$vendor_name] = [
                            'nama' => $vendor_name,
                            'total_dpp' => 0,
                            'total_ppn' => 0,
                            'total_pph' => 0,
                            'total_netto' => 0,
                            'jumlah_unit' => 0
                        ];
                    }

                    $summary[$vendor_name]['total_dpp'] += $dpp;
                    $summary[$vendor_name]['total_ppn'] += $ppn;
                    $summary[$vendor_name]['total_pph'] += $pph;
                    $summary[$vendor_name]['total_netto'] += $netto;
                    $summary[$vendor_name]['jumlah_unit'] += 1;
                }
                
                $current->addMonth();
            }
        }

        return view('container-trip.summary', compact('summary'));
    }
    /**
     * Display simple input form.
     */
    public function create()
    {
        $vendors = \App\Models\Vendor::all();
        return view('container-trip.simple_create', compact('vendors'));
    }

    /**
     * Store simple container trip data and redirect to report.
     */
    public function store(Request $request)
    {
        $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'no_kontainer' => 'required|string|max:255',
            'ukuran' => 'required|in:20,40',
            'tgl_ambil' => 'required|date',
            'harga_sewa' => 'required|numeric|min:0',
        ]);

        ContainerTrip::create([
            'vendor_id' => $request->vendor_id,
            'no_kontainer' => $request->no_kontainer,
            'ukuran' => $request->ukuran,
            'tgl_ambil' => $request->tgl_ambil,
            'harga_sewa' => $request->harga_sewa,
        ]);

        return redirect()->route('container-trip.report.dashboard')->with('success', 'Data Kontainer Berhasil Ditambahkan!');
    }
}
