<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\BiayaBensin;
use App\Models\Karyawan;
use App\Models\MasterKartuBensinBatam;
use App\Models\Mobil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BiayaBensinController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = BiayaBensin::with(['mobil', 'supir', 'creator']);

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('tanggal', [$request->start_date, $request->end_date]);
        }

        $items = $query->orderBy('tanggal', 'desc')->paginate(20);

        return view('biaya-bensin.index', compact('items'));
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
        $supirs = Karyawan::where('divisi', 'LIKE', '%supir%')->orWhere('pekerjaan', 'LIKE', '%supir%')->get();
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

        return view('biaya-bensin.create', compact('mobils', 'supirs', 'lastHargaPerLiter', 'lastNomorKartu', 'kartus'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'mobil_id' => 'required|exists:mobils,id',
            'nomor_kartu' => 'nullable|string|max:50',
            'karyawan_id' => 'required|exists:karyawans,id',
            'km_awal' => 'nullable|integer',
            'km_akhir' => 'nullable|integer',
            'liter' => 'required|numeric',
            'biaya' => 'required|numeric',
            'harga_per_liter' => 'nullable|numeric',
            'keterangan' => 'nullable|string',
            'bukti_beli' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
        ]);

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

        $item = \Illuminate\Support\Facades\DB::transaction(function () use ($validated) {
            $createdItem = BiayaBensin::create($validated);

            if (! empty($validated['nomor_kartu'])) {
                $card = MasterKartuBensinBatam::where('nomor_kartu', $validated['nomor_kartu'])->first();
                if ($card) {
                    $oldSaldo = floatval($card->saldo);
                    $newSaldo = $oldSaldo - floatval($validated['biaya']);
                    $card->saldo = $newSaldo;
                    $card->save();

                    $mobil = Mobil::find($validated['mobil_id']);
                    $mobilPlat = $mobil ? $mobil->nomor_polisi : '-';

                    $card->histories()->create([
                        'tanggal' => now(),
                        'tipe' => 'berkurang',
                        'nominal' => floatval($validated['biaya']),
                        'saldo_sebelum' => $oldSaldo,
                        'saldo_sesudah' => $newSaldo,
                        'keterangan' => 'Pengisian bensin Kendaraan '.$mobilPlat,
                        'created_by' => Auth::id(),
                    ]);
                }
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
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $item = BiayaBensin::findOrFail($id);
        $mobils = Mobil::all();
        $supirs = Karyawan::where('divisi', 'LIKE', '%supir%')->orWhere('pekerjaan', 'LIKE', '%supir%')->get();
        $kartus = MasterKartuBensinBatam::all();

        return view('biaya-bensin.edit', compact('item', 'mobils', 'supirs', 'kartus'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $item = BiayaBensin::findOrFail($id);
        $oldValues = $item->toArray();

        $validated = $request->validate([
            'tanggal' => 'required|date',
            'mobil_id' => 'required|exists:mobils,id',
            'nomor_kartu' => 'nullable|string|max:50',
            'karyawan_id' => 'required|exists:karyawans,id',
            'km_awal' => 'nullable|integer',
            'km_akhir' => 'nullable|integer',
            'liter' => 'required|numeric',
            'biaya' => 'required|numeric',
            'harga_per_liter' => 'nullable|numeric',
            'keterangan' => 'nullable|string',
            'bukti_beli' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
        ]);

        if (empty($validated['harga_per_liter']) && $validated['liter'] > 0) {
            $validated['harga_per_liter'] = round($validated['biaya'] / $validated['liter'], 2);
        }

        if ($request->hasFile('bukti_beli')) {
            if ($item->bukti_beli && \Illuminate\Support\Facades\Storage::disk('public')->exists($item->bukti_beli)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($item->bukti_beli);
            }
            $file = $request->file('bukti_beli');
            $filename = time().'_'.preg_replace('/\s+/', '_', $file->getClientOriginalName());
            $path = $file->storeAs('bukti-bensin', $filename, 'public');
            $validated['bukti_beli'] = $path;
        }

        $item->update($validated);

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

        return redirect()->route('biaya-bensin.index')->with('success', 'Biaya bensin berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $item = BiayaBensin::findOrFail($id);
        $oldValues = $item->toArray();
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
