<?php

namespace App\Http\Controllers;

use App\Models\MasterTypeGajiKaryawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

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
        $data = $this->validated($request);
        DB::transaction(function () use ($data) {
            $sequence = MasterTypeGajiKaryawan::lockIdSequence();
            $lastId = max((int) $sequence->nomor_terakhir, (int) MasterTypeGajiKaryawan::max('type_gaji_id'));
            if ($lastId >= PHP_INT_MAX) {
                throw ValidationException::withMessages(['type_gaji_id' => 'Rentang Type Gaji ID sudah habis.']);
            }
            $nextId = $lastId + 1;
            MasterTypeGajiKaryawan::create(array_merge($data, ['type_gaji_id' => $nextId]));
            DB::table('nomor_terakhir')->where('id', $sequence->id)->update(['nomor_terakhir' => $nextId, 'updated_at' => now()]);
        });

        return redirect()->route('master.type-gaji-karyawan.index')->with('success', 'Type gaji karyawan berhasil ditambahkan.');
    }

    public function edit(MasterTypeGajiKaryawan $typeGajiKaryawan)
    {
        return view('master-type-gaji-karyawan.edit', compact('typeGajiKaryawan'));
    }

    public function update(Request $request, MasterTypeGajiKaryawan $typeGajiKaryawan)
    {
        DB::transaction(function () use ($request, $typeGajiKaryawan) {
            $sequence = MasterTypeGajiKaryawan::lockIdSequence();
            $type = MasterTypeGajiKaryawan::whereKey($typeGajiKaryawan->id)->lockForUpdate()->firstOrFail();
            $type->update($this->validated($request, $type));
            if ($type->type_gaji_id > $sequence->nomor_terakhir) {
                DB::table('nomor_terakhir')->where('id', $sequence->id)->update(['nomor_terakhir' => $type->type_gaji_id, 'updated_at' => now()]);
            }
        });

        return redirect()->route('master.type-gaji-karyawan.index')->with('success', 'Type gaji karyawan berhasil diperbarui.');
    }

    public function destroy(MasterTypeGajiKaryawan $typeGajiKaryawan)
    {
        $typeGajiKaryawan->delete();

        return redirect()->route('master.type-gaji-karyawan.index')->with('success', 'Type gaji karyawan berhasil dihapus.');
    }

    private function validated(Request $request, ?MasterTypeGajiKaryawan $type = null): array
    {
        $canEditId = $request->user()?->canEditTypeGajiId() ?? false;
        if ($type && ! $canEditId && $request->has('type_gaji_id')) {
            $submitted = $request->input('type_gaji_id');
            abort_unless((is_scalar($submitted) || $submitted === null)
                && (string) $submitted === (string) $type->type_gaji_id, 403, 'Type Gaji ID hanya dapat diubah oleh karyawan dengan NIK 1593.');
        }

        return $request->validate([
            'type_gaji_id' => ! $type ? ['prohibited'] : ($canEditId
                ? ['sometimes', 'required', 'integer', 'min:1', 'max:9223372036854775807', Rule::unique('type_gaji_karyawan', 'type_gaji_id')->ignore($type)]
                : ['exclude']),
            'nama' => ['required', 'string', 'max:100', Rule::unique('type_gaji_karyawan', 'nama')->ignore($type)],
            'keterangan' => ['nullable', 'string', 'max:2000'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);
    }
}
