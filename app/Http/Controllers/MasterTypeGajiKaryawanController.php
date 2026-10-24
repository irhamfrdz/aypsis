<?php

namespace App\Http\Controllers;

use App\Models\MasterTypeGajiKaryawan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MasterTypeGajiKaryawanController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:master-type-gaji-karyawan-view')->only('index');
        $this->middleware('can:master-type-gaji-karyawan-create')->only(['create', 'store']);
        $this->middleware('can:master-type-gaji-karyawan-update')->only(['edit', 'update']);
        $this->middleware('can:master-type-gaji-karyawan-delete')->only('destroy');
    }

    public function index(Request $request)
    {
        $types = MasterTypeGajiKaryawan::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->input('search'));
                $query->where(fn ($query) => $query->where('nama', 'like', '%'.$search.'%')
                    ->orWhere('type_gaji_id', 'like', '%'.$search.'%')
                    ->orWhere('keterangan', 'like', '%'.$search.'%'));
            })
            ->when(in_array($request->input('status'), ['active', 'inactive']), fn ($query) => $query->where('status', $request->input('status')))
            ->orderBy('nama')->paginate(15)->withQueryString();

        return view('master-type-gaji-karyawan.index', compact('types'));
    }

    public function create()
    {
        return view('master-type-gaji-karyawan.create');
    }

    public function store(Request $request)
    {
        MasterTypeGajiKaryawan::create($this->validated($request));

        return redirect()->route('master.type-gaji-karyawan.index')->with('success', 'Type gaji karyawan berhasil ditambahkan.');
    }

    public function edit(MasterTypeGajiKaryawan $typeGajiKaryawan)
    {
        return view('master-type-gaji-karyawan.edit', compact('typeGajiKaryawan'));
    }

    public function update(Request $request, MasterTypeGajiKaryawan $typeGajiKaryawan)
    {
        $typeGajiKaryawan->update($this->validated($request, $typeGajiKaryawan));

        return redirect()->route('master.type-gaji-karyawan.index')->with('success', 'Type gaji karyawan berhasil diperbarui.');
    }

    public function destroy(MasterTypeGajiKaryawan $typeGajiKaryawan)
    {
        $typeGajiKaryawan->delete();

        return redirect()->route('master.type-gaji-karyawan.index')->with('success', 'Type gaji karyawan berhasil dihapus.');
    }

    private function validated(Request $request, ?MasterTypeGajiKaryawan $type = null): array
    {
        return $request->validate([
            'type_gaji_id' => ['nullable', 'integer', 'min:1', 'max:9223372036854775807'],
            'nama' => ['required', 'string', 'max:100', Rule::unique('type_gaji_karyawan', 'nama')->ignore($type)],
            'keterangan' => ['nullable', 'string', 'max:2000'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);
    }
}
