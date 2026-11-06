<?php

namespace App\Http\Controllers;

use App\Exports\BiayaBensinRangeExport;
use App\Models\AuditLog;
use App\Models\BiayaBensin;
use App\Models\Karyawan;
use App\Models\MasterKartuBensinBatam;
use App\Models\Mobil;
use App\Services\BiayaBensinCardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class BiayaBensinController extends Controller
{
    private function supirOptions()
    {
        // A HOANG (AHWANG) also records fuel expenses as a vehicle supervisor.
        return Karyawan::where('divisi', 'LIKE', '%supir%')
            ->orWhere('pekerjaan', 'LIKE', '%supir%')
            ->orWhereRaw("UPPER(REPLACE(nama_lengkap, ' ', '')) = ?", ['AHOANG'])
            ->orWhereRaw("UPPER(REPLACE(nama_panggilan, ' ', '')) = ?", ['AHOANG'])
            ->get()
            ->each(function (Karyawan $supir) {
                $namaLengkap = strtoupper(str_replace(' ', '', $supir->nama_lengkap ?? ''));
                $namaPanggilan = strtoupper(str_replace(' ', '', $supir->nama_panggilan ?? ''));
                if ($namaLengkap === 'AHOANG' || $namaPanggilan === 'AHOANG') {
                    $supir->setAttribute('label_supir', 'AHOANG'.($namaPanggilan && $namaPanggilan !== 'AHOANG' ? ' ('.$supir->nama_panggilan.')' : ''));
                }
            });
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = BiayaBensin::with(['mobil', 'supir', 'creator']);

        $search = trim((string) $request->query('search', ''));
        if ($search !== '') {
            $transactionId = preg_match('/^(?:BBS-?)?0*([1-9]\d*)$/i', $search, $matches)
                ? (int) $matches[1]
                : null;

            $query->where(function ($q) use ($search, $transactionId) {
                if ($transactionId !== null) {
                    $q->orWhere('id', $transactionId);
                }

                $q->orWhere('nomor_kartu', 'like', '%'.$search.'%')
                    ->orWhere('keterangan', 'like', '%'.$search.'%')
                    ->orWhereHas('mobil', function ($mobil) use ($search) {
                        $mobil->where('nomor_polisi', 'like', '%'.$search.'%')
                            ->orWhere('kode_no', 'like', '%'.$search.'%');
                    })
                    ->orWhereHas('alatBerat', function ($alatBerat) use ($search) {
                        $alatBerat->where('nama', 'like', '%'.$search.'%')
                            ->orWhere('kode_alat', 'like', '%'.$search.'%');
                    })
                    ->orWhereHas('supir', function ($supir) use ($search) {
                        $supir->where('nama_panggilan', 'like', '%'.$search.'%')
                            ->orWhere('nama_lengkap', 'like', '%'.$search.'%');
                    });
            });
        }

        if ($request->has('start_date') && $request->has('end_date') && $request->start_date && $request->end_date) {
            $query->whereBetween('tanggal', [$request->start_date, $request->end_date]);
        }

        if ($request->filled('nama_supir')) {
            $query->whereHas('supir', function ($q) use ($request) {
                $q->where('nama_panggilan', 'like', '%'.$request->nama_supir.'%')
                    ->orWhere('nama_lengkap', 'like', '%'.$request->nama_supir.'%');
            });
        }

        $items = $query->orderBy('tanggal', 'desc')->paginate(20);

        $supirs = $this->supirOptions();

        return view('biaya-bensin.index', compact('items', 'supirs'));
    }

    public function exportExcel(Request $request)
    {
        $validated = $request->validate([
            'export_start_date' => 'required|date_format:Y-m-d',
            'export_end_date' => 'required|date_format:Y-m-d|after_or_equal:export_start_date',
        ]);

        $startDate = $validated['export_start_date'];
        $endDate = $validated['export_end_date'];

        return Excel::download(
            new BiayaBensinRangeExport($startDate, $endDate),
            "biaya-bensin-{$startDate}-sampai-{$endDate}.xlsx"
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $mobils = Mobil::all()->map(function ($mobil) {
            $lastBensin = BiayaBensin::where('mobil_id', $mobil->id)
                ->whereNotNull('km_akhir')
                ->orderBy('tanggal', 'desc')
                ->orderBy('id', 'desc')
                ->first();
            $mobil->last_km_akhir = $lastBensin ? $lastBensin->km_akhir : 0;

            return $mobil;
        });
        $alatBerats = \App\Models\AlatBerat::all()->map(function ($alatBerat) {
            $lastBensin = BiayaBensin::where('alat_berat_id', $alatBerat->id)
                ->whereNotNull('km_akhir')
                ->orderBy('tanggal', 'desc')
                ->orderBy('id', 'desc')
                ->first();
            $alatBerat->last_km_akhir = $lastBensin ? $lastBensin->km_akhir : 0;

            return $alatBerat;
        });
        $supirs = $this->supirOptions();
        $kartus = MasterKartuBensinBatam::all();

        $lastEntry = BiayaBensin::where('created_by', Auth::id())
            ->where('liter', '>', 0)
            ->where('biaya', '>', 0)
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->first();

        $lastHargaPerLiter = $lastEntry && $lastEntry->liter > 0 ? round($lastEntry->biaya / $lastEntry->liter, 2) : null;

        $lastNomorKartu = BiayaBensin::where('created_by', Auth::id())
            ->whereNotNull('nomor_kartu')
            ->where('nomor_kartu', '!=', '')
            ->orderBy('id', 'desc')
            ->value('nomor_kartu');

        $nomorTransaksi = BiayaBensin::formatNomorTransaksi(((int) BiayaBensin::max('id')) + 1);

        return view('biaya-bensin.create', compact('mobils', 'alatBerats', 'supirs', 'lastHargaPerLiter', 'lastNomorKartu', 'kartus', 'nomorTransaksi'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'kendaraan_id' => 'required|string',
            'nomor_kartu' => 'nullable|string|max:50|exists:master_kartu_bensin_batams,nomor_kartu',
            'karyawan_id' => 'required|exists:karyawans,id',
            'km_awal' => 'nullable|integer',
            'km_akhir' => 'nullable|integer',
            'liter' => 'required|numeric',
            'biaya' => 'required|numeric',
            'harga_per_liter' => 'nullable|numeric',
            'keterangan' => 'nullable|string',
            'nama_bank' => 'nullable|string|max:50',
            'nomor_rekening' => 'nullable|string|max:100',
            'penerima_rekening' => 'nullable|string|max:150',
            'bukti_beli' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
        ]);

        $kendaraan_id = $validated['kendaraan_id'];
        unset($validated['kendaraan_id']);

        if (strpos($kendaraan_id, 'mobil_') === 0) {
            $validated['mobil_id'] = str_replace('mobil_', '', $kendaraan_id);
            $validated['alat_berat_id'] = null;
        } elseif (strpos($kendaraan_id, 'alat_') === 0) {
            $validated['alat_berat_id'] = str_replace('alat_', '', $kendaraan_id);
            $validated['mobil_id'] = null;
        }

        if (empty($validated['harga_per_liter']) && $validated['liter'] > 0) {
            $validated['harga_per_liter'] = round($validated['biaya'] / $validated['liter'], 2);
        }

        if ($request->hasFile('bukti_beli')) {
            $file = $request->file('bukti_beli');
            $filename = time().'_'.preg_replace('/\s+/', '_', $file->getClientOriginalName());
            $path = $file->storeAs('bukti-bensin', $filename, 'public');
            $validated['bukti_beli'] = $path;
        }

        $validated['created_by'] = Auth::id();
        $validated['status'] = 'approved';

        $item = DB::transaction(function () use ($validated) {
            $createdItem = BiayaBensin::create($validated);

            if (! empty($validated['nomor_kartu'])) {
                $mobilPlat = Mobil::find($validated['mobil_id'] ?? null)?->nomor_polisi ?? '-';
                app(BiayaBensinCardService::class)->adjust(
                    $createdItem, null, 0, $validated['nomor_kartu'], (float) $validated['biaya'],
                    'Pengisian bensin Kendaraan '.$mobilPlat
                );
            }

            return $createdItem;
        });

        AuditLog::create([
            'user_id' => Auth::id(),
            'user_name' => Auth::user()->name,
            'auditable_type' => 'App\Models\BiayaBensin',
            'auditable_id' => $item->id,
            'action' => 'created',
            'module' => 'biaya_bensin',
            'description' => 'Mencatat biaya bensin baru dan memotong saldo kartu',
            'old_values' => null,
            'new_values' => $item->toArray(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
        ]);

        return redirect()->route('biaya-bensin.index')->with('success', 'Biaya bensin berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $item = BiayaBensin::with(['mobil', 'supir', 'creator'])->findOrFail($id);

        return view('biaya-bensin.show', compact('item'));
    }

    /**
     * Print permohonan transfer untuk Biaya Bensin.
     */
    public function print(string $id)
    {
        $biayaBensin = BiayaBensin::with(['mobil', 'supir', 'creator'])->findOrFail($id);

        return view('biaya-bensin.print', compact('biayaBensin'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $item = BiayaBensin::findOrFail($id);
        $mobils = Mobil::all();
        $alatBerats = \App\Models\AlatBerat::all();
        $supirs = $this->supirOptions();
        $kartus = MasterKartuBensinBatam::all();

        return view('biaya-bensin.edit', compact('item', 'mobils', 'alatBerats', 'supirs', 'kartus'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $item = BiayaBensin::findOrFail($id);

        $validated = $request->validate([
            'tanggal' => 'required|date',
            'kendaraan_id' => 'required|string',
            'nomor_kartu' => 'nullable|string|max:50|exists:master_kartu_bensin_batams,nomor_kartu',
            'karyawan_id' => 'required|exists:karyawans,id',
            'km_awal' => 'nullable|integer',
            'km_akhir' => 'nullable|integer',
            'liter' => 'required|numeric',
            'biaya' => 'required|numeric',
            'harga_per_liter' => 'nullable|numeric',
            'keterangan' => 'nullable|string',
            'nama_bank' => 'nullable|string|max:50',
            'nomor_rekening' => 'nullable|string|max:100',
            'penerima_rekening' => 'nullable|string|max:150',
            'bukti_beli' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
        ]);

        $kendaraan_id = $validated['kendaraan_id'];
        unset($validated['kendaraan_id']);

        if (strpos($kendaraan_id, 'mobil_') === 0) {
            $validated['mobil_id'] = str_replace('mobil_', '', $kendaraan_id);
            $validated['alat_berat_id'] = null;
        } elseif (strpos($kendaraan_id, 'alat_') === 0) {
            $validated['alat_berat_id'] = str_replace('alat_', '', $kendaraan_id);
            $validated['mobil_id'] = null;
        }

        if (empty($validated['harga_per_liter']) && $validated['liter'] > 0) {
            $validated['harga_per_liter'] = round($validated['biaya'] / $validated['liter'], 2);
        }

        $oldBukti = $item->bukti_beli;
        $newBukti = null;
        if ($request->hasFile('bukti_beli')) {
            $file = $request->file('bukti_beli');
            $filename = time().'_'.preg_replace('/\s+/', '_', $file->getClientOriginalName());
            $newBukti = $file->storeAs('bukti-bensin', $filename, 'public');
            $validated['bukti_beli'] = $newBukti;
        }

        try {
            DB::transaction(function () use ($id, $validated, $request) {
                $item = BiayaBensin::lockForUpdate()->findOrFail($id);
                $oldValues = $item->toArray();
                $oldCardNumber = $item->nomor_kartu;
                $oldAmount = (float) $item->biaya;
                $item->update($validated);

                $cardService = app(BiayaBensinCardService::class);
                if (filled($oldCardNumber) && $oldCardNumber !== $item->nomor_kartu) {
                    $cardService->removeUsage($item, $oldCardNumber);
                    if (filled($item->nomor_kartu)) {
                        $cardService->adjust(
                            $item, null, 0, $item->nomor_kartu, (float) $item->biaya,
                            'Pengisian bensin setelah perubahan kartu '.$item->nomor_transaksi
                        );
                    }
                } else {
                    $cardService->adjust(
                        $item, $oldCardNumber, $oldAmount,
                        $item->nomor_kartu, (float) $item->biaya,
                        'Koreksi biaya bensin '.$item->nomor_transaksi
                    );
                }

                AuditLog::create([
                    'user_id' => Auth::id(),
                    'user_name' => Auth::user()->name,
                    'auditable_type' => 'App\Models\BiayaBensin',
                    'auditable_id' => $item->id,
                    'action' => 'updated',
                    'module' => 'biaya_bensin',
                    'description' => 'Mengubah data biaya bensin',
                    'old_values' => $oldValues,
                    'new_values' => $item->toArray(),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'url' => $request->fullUrl(),
                ]);
            });
        } catch (\Throwable $e) {
            if ($newBukti) {
                Storage::disk('public')->delete($newBukti);
            }

            throw $e;
        }

        if ($newBukti && $oldBukti) {
            Storage::disk('public')->delete($oldBukti);
        }

        return redirect()->route('biaya-bensin.index')->with('success', 'Biaya bensin berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        DB::transaction(function () use ($id, $request) {
            $item = BiayaBensin::lockForUpdate()->findOrFail($id);
            $oldValues = $item->toArray();

            if (filled($item->nomor_kartu)) {
                app(BiayaBensinCardService::class)->removeUsage($item, $item->nomor_kartu);
            }
            $item->delete();

            AuditLog::create([
                'user_id' => Auth::id(),
                'user_name' => Auth::user()->name,
                'auditable_type' => 'App\Models\BiayaBensin',
                'auditable_id' => $id,
                'action' => 'deleted',
                'module' => 'biaya_bensin',
                'description' => 'Menghapus data biaya bensin',
                'old_values' => $oldValues,
                'new_values' => null,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'url' => $request->fullUrl(),
            ]);
        });

        return redirect()->route('biaya-bensin.index')->with('success', 'Biaya bensin berhasil dihapus.');
    }

    /**
     * Display pending fuel entries for approval.
     */
    public function approvalList()
    {
        $items = BiayaBensin::with(['mobil', 'supir', 'creator'])
            ->where('status', 'pending')
            ->orderBy('tanggal', 'desc')
            ->paginate(20);

        return view('biaya-bensin.approval', compact('items'));
    }

    /**
     * Approve the specified fuel entry.
     */
    public function approve(Request $request, $id)
    {
        $item = BiayaBensin::findOrFail($id);
        $oldValues = $item->toArray();

        $item->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        AuditLog::create([
            'user_id' => Auth::id(),
            'user_name' => Auth::user()->name,
            'auditable_type' => 'App\Models\BiayaBensin',
            'auditable_id' => $item->id,
            'action' => 'approved',
            'module' => 'biaya_bensin',
            'description' => 'Menyetujui biaya bensin',
            'old_values' => $oldValues,
            'new_values' => $item->toArray(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
        ]);

        return redirect()->back()->with('success', 'Catatan bensin berhasil disetujui.');
    }

    /**
     * Reject the specified fuel entry.
     */
    public function reject(Request $request, $id)
    {
        $item = BiayaBensin::findOrFail($id);
        $oldValues = $item->toArray();

        $item->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        AuditLog::create([
            'user_id' => Auth::id(),
            'user_name' => Auth::user()->name,
            'auditable_type' => 'App\Models\BiayaBensin',
            'auditable_id' => $item->id,
            'action' => 'rejected',
            'module' => 'biaya_bensin',
            'description' => 'Menolak biaya bensin',
            'old_values' => $oldValues,
            'new_values' => $item->toArray(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
        ]);

        return redirect()->back()->with('success', 'Catatan bensin berhasil ditolak.');
    }
}
