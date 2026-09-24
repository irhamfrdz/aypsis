<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PranotaPumlController extends Controller
{
    public function index()
    {
        $pranotas = \App\Models\PranotaPuml::orderBy('created_at', 'desc')->get();
        return view('pranota-puml.index', compact('pranotas'));
    }

    public function create()
    {
        // Get all draft Uang Makan
        $draftUangMakan = \App\Models\PranotaUangMakan::where('status', 'draft')
                            ->whereNull('pranota_puml_id')
                            ->orderBy('tanggal_pranota', 'desc')
                            ->get();
                            
        // Get all draft Lembur
        $draftLembur = \App\Models\PranotaLemburKaryawanHeader::where('status', 'draft')
                            ->whereNull('pranota_puml_id')
                            ->orderBy('tanggal_pranota', 'desc')
                            ->get();

        // Riwayat: submitted/completed Uang Makan (already in a PUML)
        $riwayatUangMakan = \App\Models\PranotaUangMakan::where('status', '!=', 'draft')
                            ->with('details')
                            ->orderBy('tanggal_pranota', 'desc')
                            ->take(20)
                            ->get();

        // Riwayat: submitted/completed Lembur (already in a PUML)
        $riwayatLembur = \App\Models\PranotaLemburKaryawanHeader::where('status', '!=', 'draft')
                            ->orderBy('tanggal_pranota', 'desc')
                            ->take(20)
                            ->get();

        return view('pranota-puml.create', compact('draftUangMakan', 'draftLembur', 'riwayatUangMakan', 'riwayatLembur'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal_pranota' => 'required|date',
            'uang_makan_ids' => 'nullable|array',
            'lembur_ids' => 'nullable|array',
        ]);

        if (empty($request->uang_makan_ids) && empty($request->lembur_ids)) {
            return back()->with('error', 'Minimal pilih satu Pranota Uang Makan atau Lembur untuk digabung.');
        }

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            // Generate nomor PUML
            $nomorTerakhir = \App\Models\NomorTerakhir::where('modul', 'PUML')->first();
            if (! $nomorTerakhir) {
                $nomorTerakhir = \App\Models\NomorTerakhir::create(['modul' => 'PUML', 'nomor_terakhir' => 0]);
            }
            $nextNumber = $nomorTerakhir->nomor_terakhir + 1;
            $tahun = now()->format('y');
            $bulan = now()->format('m');
            $nomorPranota = "PUML1{$bulan}{$tahun}" . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

            $totalUangMakan = 0;
            $totalLembur = 0;
            $periodeStart = null;
            $periodeEnd = null; // Bisa dihitung dari data tanggal_pranota anak jika diperlukan

            // Create header PUML
            $puml = \App\Models\PranotaPuml::create([
                'nomor_pranota' => $nomorPranota,
                'tanggal_pranota' => $request->tanggal_pranota,
                'status' => 'submitted',
                'created_by' => auth()->id(),
            ]);

            // Assign uang_makan_ids
            if (!empty($request->uang_makan_ids)) {
                $umRecords = \App\Models\PranotaUangMakan::whereIn('id', $request->uang_makan_ids)->get();
                foreach ($umRecords as $um) {
                    $totalUangMakan += $um->total_nominal;
                    $um->update(['pranota_puml_id' => $puml->id, 'status' => 'submitted']);
                }
            }

            // Assign lembur_ids
            if (!empty($request->lembur_ids)) {
                $lemburRecords = \App\Models\PranotaLemburKaryawanHeader::whereIn('id', $request->lembur_ids)->get();
                foreach ($lemburRecords as $lm) {
                    $totalLembur += $lm->total_setelah_adjustment;
                    $lm->update(['pranota_puml_id' => $puml->id, 'status' => 'submitted']);
                }
            }

            $puml->update([
                'total_uang_makan' => $totalUangMakan,
                'total_lembur' => $totalLembur,
                'grand_total' => $totalUangMakan + $totalLembur
            ]);

            $nomorTerakhir->update(['nomor_terakhir' => $nextNumber]);

            \Illuminate\Support\Facades\DB::commit();

            return redirect()->route('pranota-puml.index')->with('success', 'Pranota Gabungan (PUML) berhasil dibuat!');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->with('error', 'Gagal membuat PUML: ' . $e->getMessage());
        }
    }
    
    public function exportExcel($id)
    {
        $puml = \App\Models\PranotaPuml::findOrFail($id);
        $filename = 'PUML_' . $puml->nomor_pranota . '_' . now()->format('Ymd_His') . '.xlsx';
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\PranotaPumlExport($puml), $filename);
    }

    public function show($id)
    {
        $puml = \App\Models\PranotaPuml::with(['uangMakans.details.karyawan', 'lemburs.karyawans.karyawan', 'potongans'])->findOrFail($id);
        
        $potonganMap = [];
        foreach ($puml->potongans as $pot) {
            $key = class_basename($pot->tipe_karyawan) . '_' . $pot->karyawan_id;
            $potonganMap[$key] = $pot;
        }
        
        // Kita butuh merekap data berdasarkan karyawan_id dan tipe_karyawan
        $karyawanRekap = [];
        
        foreach ($puml->uangMakans as $um) {
            foreach ($um->details as $d) {
                $kid = class_basename($d->tipe_karyawan) . '_' . $d->karyawan_id;
                if (!isset($karyawanRekap[$kid])) {
                    $pot = $potonganMap[$kid] ?? null;
                    $karyawanRekap[$kid] = [
                        'karyawan' => $d->karyawan,
                        'total_uang_makan' => 0,
                        'total_lembur' => 0,
                        'pot_utang' => $pot ? $pot->pot_utang : 0,
                        'pot_bpjs' => $pot ? $pot->pot_bpjs : 0,
                        'pot_pph' => $pot ? $pot->pot_pph : 0,
                        'pot_terlambat' => $pot ? $pot->pot_terlambat : 0,
                    ];
                }
                $karyawanRekap[$kid]['total_uang_makan'] += $d->total_akhir;
            }
        }
        
        foreach ($puml->lemburs as $lm) {
            foreach ($lm->karyawans as $d) {
                $tipe_karyawan = $d->tipe_karyawan ?? 'App\\Models\\Karyawan';
                $kid = class_basename($tipe_karyawan) . '_' . $d->karyawan_id;
                if (!isset($karyawanRekap[$kid])) {
                    $pot = $potonganMap[$kid] ?? null;
                    $karyawanRekap[$kid] = [
                        'karyawan' => $d->karyawan,
                        'total_uang_makan' => 0,
                        'total_lembur' => 0,
                        'pot_utang' => $pot ? $pot->pot_utang : 0,
                        'pot_bpjs' => $pot ? $pot->pot_bpjs : 0,
                        'pot_pph' => $pot ? $pot->pot_pph : 0,
                        'pot_terlambat' => $pot ? $pot->pot_terlambat : 0,
                    ];
                }
                $karyawanRekap[$kid]['total_lembur'] += $d->total_akhir;
            }
        }
        
        // Urutkan berdasarkan nama karyawan
        $karyawanRekap = collect($karyawanRekap)->sortBy(function ($item) {
            return strtolower($item['karyawan']->nama_lengkap ?? 'z');
        });
        
        return view('pranota-puml.show', compact('puml', 'karyawanRekap'));
    }

    public function storePotongan(Request $request, $id)
    {
        $puml = \App\Models\PranotaPuml::findOrFail($id);
        $potonganData = $request->input('potongan', []);
        
        $totalPotonganSeluruhnya = 0;
        
        foreach ($potonganData as $karyawanKey => $data) {
            // Parse key like "Karyawan_257"
            $parts = explode('_', $karyawanKey);
            $tipeKaryawan = count($parts) > 1 ? 'App\\Models\\' . $parts[0] : 'App\\Models\\Karyawan';
            $karyawanId = count($parts) > 1 ? $parts[1] : $karyawanKey;

            $pot_utang = (float)(preg_replace('/[^0-9-]/', '', $data['pot_utang'] ?? '0') ?: 0);
            $pot_bpjs = (float)(preg_replace('/[^0-9-]/', '', $data['pot_bpjs'] ?? '0') ?: 0);
            $pot_pph = (float)(preg_replace('/[^0-9-]/', '', $data['pot_pph'] ?? '0') ?: 0);
            $pot_terlambat = (float)(preg_replace('/[^0-9-]/', '', $data['pot_terlambat'] ?? '0') ?: 0);

            \App\Models\PranotaPumlPotongan::updateOrCreate(
                [
                    'pranota_puml_id' => $puml->id,
                    'tipe_karyawan' => $tipeKaryawan,
                    'karyawan_id' => $karyawanId
                ],
                [
                    'pot_utang' => $pot_utang,
                    'pot_bpjs' => $pot_bpjs,
                    'pot_pph' => $pot_pph,
                    'pot_terlambat' => $pot_terlambat,
                ]
            );
            
            $totalPotonganSeluruhnya += ($pot_utang + $pot_bpjs + $pot_pph + $pot_terlambat);
        }
        
        $puml->update([
            'grand_total' => $puml->total_uang_makan + $puml->total_lembur - $totalPotonganSeluruhnya
        ]);
        
        return back()->with('success', 'Data potongan berhasil disimpan!');
    }

    public function detachUangMakan($id)
    {
        $um = \App\Models\PranotaUangMakan::findOrFail($id);

        // Hanya boleh di-detach jika PUML induknya belum approved/selesai
        if ($um->pranota_puml_id) {
            $puml = \App\Models\PranotaPuml::find($um->pranota_puml_id);
            if ($puml && in_array($puml->status, ['approved', 'paid'])) {
                return back()->with('error', 'Pranota tidak dapat dilepaskan karena PUML induk sudah ' . $puml->status . '.');
            }

            // Recalculate PUML grand total jika ada
            if ($puml) {
                $puml->update([
                    'total_uang_makan' => max(0, $puml->total_uang_makan - $um->total_nominal),
                    'grand_total'      => max(0, $puml->grand_total - $um->total_nominal),
                ]);
            }
        }

        $um->update([
            'pranota_puml_id' => null,
            'status'          => 'draft',
        ]);

        return back()->with('success', "Pranota Uang Makan {$um->nomor_pranota} berhasil dilepaskan dan dikembalikan ke status draft.");
    }

    public function detachLembur($id)
    {
        $lm = \App\Models\PranotaLemburKaryawanHeader::findOrFail($id);

        if ($lm->pranota_puml_id) {
            $puml = \App\Models\PranotaPuml::find($lm->pranota_puml_id);
            if ($puml && in_array($puml->status, ['approved', 'paid'])) {
                return back()->with('error', 'Pranota tidak dapat dilepaskan karena PUML induk sudah ' . $puml->status . '.');
            }

            if ($puml) {
                $puml->update([
                    'total_lembur' => max(0, $puml->total_lembur - $lm->total_setelah_adjustment),
                    'grand_total'  => max(0, $puml->grand_total - $lm->total_setelah_adjustment),
                ]);
            }
        }

        $lm->update([
            'pranota_puml_id' => null,
            'status'          => 'draft',
        ]);

        return back()->with('success', "Pranota Lembur {$lm->nomor_pranota} berhasil dilepaskan dan dikembalikan ke status draft.");
    }

    public function destroy($id)
    {
        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            $puml = \App\Models\PranotaPuml::findOrFail($id);
            
            // Revert children (uang_makan and lembur) status back to 'draft' and remove relation
            \App\Models\PranotaUangMakan::where('pranota_puml_id', $puml->id)->update([
                'pranota_puml_id' => null,
                'status' => 'draft'
            ]);

            \App\Models\PranotaLemburKaryawanHeader::where('pranota_puml_id', $puml->id)->update([
                'pranota_puml_id' => null,
                'status' => 'draft'
            ]);

            // Delete the parent
            $puml->delete();

            \Illuminate\Support\Facades\DB::commit();

            return redirect()->route('pranota-puml.index')->with('success', 'Data Pranota PUML berhasil dihapus dan status pranota anak dikembalikan ke draft.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->with('error', 'Gagal menghapus data PUML: ' . $e->getMessage());
        }
    }
}
