<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\BiayaBensin;
use App\Models\Mobil;
use App\Models\Karyawan;
use App\Models\AuditLog;
use Carbon\Carbon;
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
        $mobils = Mobil::all();
        $supirs = Karyawan::where('divisi', 'LIKE', '%supir%')->orWhere('pekerjaan', 'LIKE', '%supir%')->get();
        return view('biaya-bensin.create', compact('mobils', 'supirs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'mobil_id' => 'required|exists:mobils,id',
            'karyawan_id' => 'required|exists:karyawans,id',
            'km_awal' => 'nullable|integer',
            'km_akhir' => 'nullable|integer',
            'liter' => 'required|numeric',
            'biaya' => 'required|numeric',
            'keterangan' => 'nullable|string',
        ]);

        $validated['created_by'] = Auth::id();

        $item = BiayaBensin::create($validated);

        AuditLog::create([
            'user_id' => Auth::id(),
            'user_name' => Auth::user()->name,
            'auditable_type' => 'App\Models\BiayaBensin',
            'auditable_id' => $item->id,
            'action' => 'created',
            'module' => 'biaya_bensin',
            'description' => 'Mencatat biaya bensin baru',
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
        return view('biaya-bensin.edit', compact('item', 'mobils', 'supirs'));
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
            'karyawan_id' => 'required|exists:karyawans,id',
            'km_awal' => 'nullable|integer',
            'km_akhir' => 'nullable|integer',
            'liter' => 'required|numeric',
            'biaya' => 'required|numeric',
            'keterangan' => 'nullable|string',
        ]);

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
}
