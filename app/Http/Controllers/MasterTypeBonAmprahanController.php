<?php

namespace App\Http\Controllers;

use App\Models\MasterTypeBonAmprahan;
use Illuminate\Http\Request;

class MasterTypeBonAmprahanController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:master-type-bon-amprahan-view')->only(['index', 'show']);
        $this->middleware('can:master-type-bon-amprahan-create')->only(['create', 'store']);
        $this->middleware('can:master-type-bon-amprahan-update')->only(['edit', 'update']);
        $this->middleware('can:master-type-bon-amprahan-delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = MasterTypeBonAmprahan::query();

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(fn ($q) => $q->where('kode', 'like', "%{$search}%")
                ->orWhere('nama', 'like', "%{$search}%"));
        }

        $types = $query->latest()->paginate(15)->withQueryString();

        return view('master-type-bon-amprahan.index', compact('types'));
    }

    public function create()
    {
        return view('master-type-bon-amprahan.create');
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        MasterTypeBonAmprahan::create($data);

        return redirect()->route('master.type-bon-amprahan.index')
            ->with('success', 'Type Bon Amprahan berhasil ditambahkan.');
    }

    public function show(MasterTypeBonAmprahan $typeBonAmprahan)
    {
        return redirect()->route('master.type-bon-amprahan.edit', $typeBonAmprahan);
    }

    public function edit(MasterTypeBonAmprahan $typeBonAmprahan)
    {
        return view('master-type-bon-amprahan.edit', compact('typeBonAmprahan'));
    }

    public function update(Request $request, MasterTypeBonAmprahan $typeBonAmprahan)
    {
        $typeBonAmprahan->update($this->validated($request, $typeBonAmprahan->id));

        return redirect()->route('master.type-bon-amprahan.index')
            ->with('success', 'Type Bon Amprahan berhasil diperbarui.');
    }

    public function destroy(MasterTypeBonAmprahan $typeBonAmprahan)
    {
        $typeBonAmprahan->delete();

        return redirect()->route('master.type-bon-amprahan.index')
            ->with('success', 'Type Bon Amprahan berhasil dihapus.');
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        $unique = 'unique:master_type_bon_amprahans,kode'.($ignoreId ? ','.$ignoreId : '');

        return $request->validate([
            'kode' => ['required', 'string', 'max:50', $unique],
            'nama' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);
    }
}
