<?php

namespace App\Http\Controllers;

use App\Models\MasterTujuanKirim;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class MasterTujuanKirimController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');

        $query = MasterTujuanKirim::query();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('kode', 'like', '%' . $search . '%')
                  ->orWhere('nama_tujuan', 'like', '%' . $search . '%')
                  ->orWhere('catatan', 'like', '%' . $search . '%');
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        $tujuanKirim = $query->orderBy('nama_tujuan')->paginate(10);

        return view('master.tujuan-kirim.index', compact('tujuanKirim', 'search', 'status'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master.tujuan-kirim.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode' => 'required|string|max:10|unique:master_tujuan_kirim,kode',
            'nama_tujuan' => 'required|string|max:100',
            'catatan' => 'nullable|string|max:500',
            'status' => 'required|in:active,inactive'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        MasterTujuanKirim::create($request->all());

        return redirect()->route('tujuan-kirim.index')
            ->with('success', 'Tujuan kirim berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MasterTujuanKirim $tujuanKirim)
    {
        return view('master.tujuan-kirim.show', compact('tujuanKirim'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MasterTujuanKirim $tujuanKirim)
    {
        return view('master.tujuan-kirim.edit', compact('tujuanKirim'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MasterTujuanKirim $tujuanKirim)
    {
        $validator = Validator::make($request->all(), [
            'kode' => ['required', 'string', 'max:10', Rule::unique('master_tujuan_kirim')->ignore($tujuanKirim->id)],
            'nama_tujuan' => 'required|string|max:100',
            'catatan' => 'nullable|string|max:500',
            'status' => 'required|in:active,inactive'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $tujuanKirim->update($request->all());

        return redirect()->route('tujuan-kirim.index')
            ->with('success', 'Tujuan kirim berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MasterTujuanKirim $tujuanKirim)
    {
        $tujuanKirim->delete();

        return redirect()->route('tujuan-kirim.index')
            ->with('success', 'Tujuan kirim berhasil dihapus.');
    }
}
