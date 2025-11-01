<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\PranotaUangKenek;
use App\Models\SuratJalan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PranotaUangKenekController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PranotaUangKenek::with(['suratJalan', 'createdBy', 'approvedBy']);

        // Filter by search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('no_pranota', 'like', "%{$search}%")
                  ->orWhere('no_surat_jalan', 'like', "%{$search}%")
                  ->orWhere('kenek_nama', 'like', "%{$search}%")
                  ->orWhere('no_plat', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tanggal', [$request->start_date, $request->end_date]);
        }

        // Filter by kenek
        if ($request->filled('kenek')) {
            $query->where('kenek_nama', 'like', "%{$request->kenek}%");
        }

        $pranotaUangKeneks = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('pranota-uang-kenek.index', compact('pranotaUangKeneks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get available surat jalans that have kenek and are approved
        $suratJalans = SuratJalan::where('status', 'approved')
            ->whereNotNull('kenek')
            ->where('kenek', '!=', '')
            ->where('status_pembayaran_uang_rit_kenek', SuratJalan::STATUS_UANG_RIT_KENEK_BELUM_DIBAYAR)
            ->orderBy('tanggal_surat_jalan', 'desc')
            ->get();

        return view('pranota-uang-kenek.create', compact('suratJalans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string',
            'surat_jalan_data' => 'required|array|min:1',
            'surat_jalan_data.*.selected' => 'required',
            'surat_jalan_data.*.no_surat_jalan' => 'required|string|max:255',
            'surat_jalan_data.*.kenek_nama' => 'required|string|max:255',
            'surat_jalan_data.*.no_plat' => 'required|string|max:255',
            'surat_jalan_data.*.uang_rit_kenek' => 'required|numeric|min:0',
        ], [
            'surat_jalan_data.required' => 'Silakan pilih minimal satu surat jalan.',
            'surat_jalan_data.min' => 'Silakan pilih minimal satu surat jalan.',
        ]);

        DB::beginTransaction();
        try {
            // Filter hanya data yang dipilih
            $selectedData = collect($request->surat_jalan_data)
                ->filter(function ($item) {
                    return isset($item['selected']) && $item['selected'];
                });

            if ($selectedData->isEmpty()) {
                return back()->withErrors(['surat_jalan_data' => 'Silakan pilih minimal satu surat jalan.'])->withInput();
            }

            $createdPranota = [];
            foreach ($selectedData as $suratJalanId => $data) {
                // Generate nomor pranota
                $nomorPranota = $this->generateNomorPranota();
                
                // Hitung total rit (hanya kenek)
                $totalRit = $data['uang_rit_kenek'];
                $totalUang = $totalRit; // Hanya uang kenek saja
                
                $pranotaUangKenek = PranotaUangKenek::create([
                    'no_pranota' => $nomorPranota,
                    'tanggal' => $request->tanggal,
                    'surat_jalan_id' => $suratJalanId,
                    'no_surat_jalan' => $data['no_surat_jalan'],
                    'supir_nama' => $data['supir_nama'] ?? '-',
                    'kenek_nama' => $data['kenek_nama'],
                    'no_plat' => $data['no_plat'],
                    'uang_rit_kenek' => $data['uang_rit_kenek'],
                    'total_rit' => $totalRit,
                    'total_uang' => $totalUang,
                    'keterangan' => $request->keterangan,
                    'status' => PranotaUangKenek::STATUS_DRAFT,
                    'created_by' => Auth::id(),
                ]);

                $createdPranota[] = $pranotaUangKenek;

                // Update status surat jalan
                $suratJalan = SuratJalan::find($suratJalanId);
                if ($suratJalan) {
                    $suratJalan->update([
                        'status_pembayaran_uang_rit_kenek' => SuratJalan::STATUS_UANG_RIT_KENEK_SUDAH_MASUK_PRANOTA
                    ]);
                }

                Log::info('Pranota Uang Kenek created', [
                    'pranota_id' => $pranotaUangKenek->id,
                    'no_pranota' => $pranotaUangKenek->no_pranota,
                    'surat_jalan_id' => $suratJalanId,
                    'created_by' => Auth::user()->name,
                ]);
            }

            DB::commit();

            $totalPranota = count($createdPranota);
            $totalUangKeseluruhan = collect($createdPranota)->sum('total_uang');

            return redirect()->route('pranota-uang-kenek.index')
                           ->with('success', "Berhasil membuat {$totalPranota} pranota uang kenek dengan total Rp " . number_format($totalUangKeseluruhan, 0, ',', '.'));

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error creating Pranota Uang Kenek: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Terjadi kesalahan saat membuat pranota uang kenek.'])->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(PranotaUangKenek $pranotaUangKenek)
    {
        $pranotaUangKenek->load(['suratJalan', 'createdBy', 'updatedBy', 'approvedBy']);
        
        // Parse combined data 
        $kenekDetails = [];
        if ($pranotaUangKenek->no_surat_jalan) {
            $suratJalanArray = explode(',', $pranotaUangKenek->no_surat_jalan);
            $kenekNamaArray = explode(',', $pranotaUangKenek->kenek_nama);
            $uangRitArray = explode(',', $pranotaUangKenek->uang_rit_kenek);
            
            foreach ($suratJalanArray as $index => $noSuratJalan) {
                $kenekDetails[] = [
                    'no_surat_jalan' => trim($noSuratJalan),
                    'kenek_nama' => trim($kenekNamaArray[$index] ?? ''),
                    'uang_rit' => floatval($uangRitArray[$index] ?? 0)
                ];
            }
        }
        
        return view('pranota-uang-kenek.show', compact('pranotaUangKenek', 'kenekDetails'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PranotaUangKenek $pranotaUangKenek)
    {
        // Only allow editing if status is draft
        if (!$pranotaUangKenek->isDraft()) {
            return redirect()->route('pranota-uang-kenek.index')
                           ->with('error', 'Pranota uang kenek hanya dapat diedit jika status masih draft.');
        }

        return view('pranota-uang-kenek.edit', compact('pranotaUangKenek'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PranotaUangKenek $pranotaUangKenek)
    {
        // Only allow editing if status is draft
        if (!$pranotaUangKenek->isDraft()) {
            return redirect()->route('pranota-uang-kenek.index')
                           ->with('error', 'Pranota uang kenek hanya dapat diedit jika status masih draft.');
        }

        $request->validate([
            'tanggal' => 'required|date',
            'kenek_nama' => 'required|string|max:255',
            'no_plat' => 'required|string|max:255',
            'uang_rit_kenek' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $pranotaUangKenek->update([
                'tanggal' => $request->tanggal,
                'kenek_nama' => $request->kenek_nama,
                'no_plat' => $request->no_plat,
                'uang_rit_kenek' => $request->uang_rit_kenek,
                'keterangan' => $request->keterangan,
                'updated_by' => Auth::id(),
            ]);

            Log::info('Pranota Uang Kenek updated', [
                'pranota_id' => $pranotaUangKenek->id,
                'no_pranota' => $pranotaUangKenek->no_pranota,
                'updated_by' => Auth::user()->name,
            ]);

            DB::commit();
            return redirect()->route('pranota-uang-kenek.index')
                           ->with('success', 'Pranota uang kenek berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error updating Pranota Uang Kenek: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Terjadi kesalahan saat memperbarui pranota uang kenek.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PranotaUangKenek $pranotaUangKenek)
    {
        // Only allow deletion if status is draft
        if (!$pranotaUangKenek->isDraft()) {
            return redirect()->route('pranota-uang-kenek.index')
                           ->with('error', 'Pranota uang kenek hanya dapat dihapus jika status masih draft.');
        }

        DB::beginTransaction();
        try {
            // Reset surat jalan status
            if ($pranotaUangKenek->suratJalan) {
                $pranotaUangKenek->suratJalan->update([
                    'status_pembayaran_uang_rit_kenek' => SuratJalan::STATUS_UANG_RIT_KENEK_BELUM_DIBAYAR
                ]);
            }

            $pranotaUangKenek->delete();

            Log::info('Pranota Uang Kenek deleted', [
                'pranota_id' => $pranotaUangKenek->id,
                'no_pranota' => $pranotaUangKenek->no_pranota,
                'deleted_by' => Auth::user()->name,
            ]);

            DB::commit();
            return redirect()->route('pranota-uang-kenek.index')
                           ->with('success', 'Pranota uang kenek berhasil dihapus.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error deleting Pranota Uang Kenek: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Terjadi kesalahan saat menghapus pranota uang kenek.']);
        }
    }

    /**
     * Submit pranota for approval
     */
    public function submit(Request $request, PranotaUangKenek $pranotaUangKenek)
    {
        if (!$pranotaUangKenek->isDraft()) {
            return redirect()->route('pranota-uang-kenek.index')
                           ->with('error', 'Hanya pranota dengan status draft yang dapat disubmit.');
        }

        DB::beginTransaction();
        try {
            $pranotaUangKenek->submit();

            // Update surat jalan status
            if ($pranotaUangKenek->suratJalan) {
                $pranotaUangKenek->suratJalan->update([
                    'status_pembayaran_uang_rit_kenek' => SuratJalan::STATUS_UANG_RIT_KENEK_PRANOTA_SUBMITTED
                ]);
            }

            Log::info('Pranota Uang Kenek submitted', [
                'pranota_id' => $pranotaUangKenek->id,
                'no_pranota' => $pranotaUangKenek->no_pranota,
                'submitted_by' => Auth::user()->name,
            ]);

            DB::commit();
            return redirect()->route('pranota-uang-kenek.index')
                           ->with('success', 'Pranota uang kenek berhasil disubmit untuk approval.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error submitting Pranota Uang Kenek: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Terjadi kesalahan saat submit pranota uang kenek.']);
        }
    }

    /**
     * Approve pranota
     */
    public function approve(Request $request, PranotaUangKenek $pranotaUangKenek)
    {
        if (!$pranotaUangKenek->isSubmitted()) {
            return redirect()->route('pranota-uang-kenek.index')
                           ->with('error', 'Hanya pranota dengan status submitted yang dapat diapprove.');
        }

        DB::beginTransaction();
        try {
            $pranotaUangKenek->approve();

            // Update surat jalan status
            if ($pranotaUangKenek->suratJalan) {
                $pranotaUangKenek->suratJalan->update([
                    'status_pembayaran_uang_rit_kenek' => SuratJalan::STATUS_UANG_RIT_KENEK_PRANOTA_APPROVED
                ]);
            }

            Log::info('Pranota Uang Kenek approved', [
                'pranota_id' => $pranotaUangKenek->id,
                'no_pranota' => $pranotaUangKenek->no_pranota,
                'approved_by' => Auth::user()->name,
            ]);

            DB::commit();
            return redirect()->route('pranota-uang-kenek.index')
                           ->with('success', 'Pranota uang kenek berhasil diapprove.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error approving Pranota Uang Kenek: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Terjadi kesalahan saat approve pranota uang kenek.']);
        }
    }

    /**
     * Mark pranota as paid
     */
    public function markAsPaid(Request $request, PranotaUangKenek $pranotaUangKenek)
    {
        if (!$pranotaUangKenek->isApproved()) {
            return redirect()->route('pranota-uang-kenek.index')
                           ->with('error', 'Hanya pranota dengan status approved yang dapat ditandai sebagai dibayar.');
        }

        $request->validate([
            'tanggal_bayar' => 'required|date',
        ]);

        DB::beginTransaction();
        try {
            $pranotaUangKenek->markAsPaid($request->tanggal_bayar);

            // Update surat jalan status
            if ($pranotaUangKenek->suratJalan) {
                $pranotaUangKenek->suratJalan->update([
                    'status_pembayaran_uang_rit_kenek' => SuratJalan::STATUS_UANG_RIT_KENEK_DIBAYAR
                ]);
            }

            Log::info('Pranota Uang Kenek marked as paid', [
                'pranota_id' => $pranotaUangKenek->id,
                'no_pranota' => $pranotaUangKenek->no_pranota,
                'tanggal_bayar' => $request->tanggal_bayar,
                'marked_by' => Auth::user()->name,
            ]);

            DB::commit();
            return redirect()->route('pranota-uang-kenek.index')
                           ->with('success', 'Pranota uang kenek berhasil ditandai sebagai dibayar.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error marking Pranota Uang Kenek as paid: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Terjadi kesalahan saat menandai pranota sebagai dibayar.']);
        }
    }

    /**
     * Print pranota uang kenek
     */
    public function print(PranotaUangKenek $pranotaUangKenek)
    {
        $pranotaUangKenek->load(['suratJalan', 'createdBy']);
        
        // Parse combined data like pranota rit
        $kenekDetails = [];
        if ($pranotaUangKenek->no_surat_jalan) {
            $suratJalanArray = explode(',', $pranotaUangKenek->no_surat_jalan);
            $kenekNamaArray = explode(',', $pranotaUangKenek->kenek_nama);
            $uangRitArray = explode(',', $pranotaUangKenek->uang_rit_kenek);
            
            foreach ($suratJalanArray as $index => $noSuratJalan) {
                $kenekDetails[] = [
                    'no_surat_jalan' => trim($noSuratJalan),
                    'kenek_nama' => trim($kenekNamaArray[$index] ?? ''),
                    'uang_rit' => floatval($uangRitArray[$index] ?? 0)
                ];
            }
        }
        
        return view('pranota-uang-kenek.print', compact('pranotaUangKenek', 'kenekDetails'));
    }

    /**
     * Generate unique pranota number
     */
    private function generateNomorPranota()
    {
        $prefix = 'PUK'; // Pranota Uang Kenek
        $year = date('Y');
        $month = date('m');
        
        // Get last number for this month
        $lastPranota = PranotaUangKenek::where('no_pranota', 'like', "{$prefix}-{$year}{$month}%")
                          ->orderBy('no_pranota', 'desc')
                          ->first();
        
        if ($lastPranota) {
            $lastNumber = intval(substr($lastPranota->no_pranota, -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . '-' . $year . $month . '-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
}
