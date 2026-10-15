<?php

namespace App\Http\Controllers;

use App\Models\Departemen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class DepartemenController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $departemens = Departemen::query()
            ->when($search !== '', fn ($query) => $query->where('nama_departemen', 'like', '%'.$search.'%'))
            ->orderBy('nama_departemen')->paginate(15)->withQueryString();

        $usage = DB::table('karyawans')->whereNotNull('departemen')
            ->select('departemen', DB::raw('count(*) as total'))
            ->groupBy('departemen')->pluck('total', 'departemen');

        return view('master-departemen.index', compact('departemens', 'usage'));
    }

    public function store(Request $request)
    {
        $request->merge(['nama_departemen' => trim((string) $request->input('nama_departemen'))]);
        $data = $request->validate([
            'nama_departemen' => ['required', 'string', 'max:255', Rule::unique('departemens', 'nama_departemen')],
        ]);

        Departemen::create($data + ['is_active' => true]);

        return redirect()->route('master.departemen.index')->with('success', 'Departemen berhasil ditambahkan.');
    }

    public function update(Request $request, Departemen $departemen)
    {
        $request->merge(['nama_departemen' => trim((string) $request->input('nama_departemen'))]);
        $data = $request->validate([
            'nama_departemen' => ['required', 'string', 'max:255', Rule::unique('departemens', 'nama_departemen')->ignore($departemen->id)],
            'is_active' => ['required', 'boolean'],
        ]);

        if ($departemen->nama_departemen !== $data['nama_departemen']
            && $this->isUsed($departemen)) {
            return back()->withInput()->withErrors(['nama_departemen' => 'Nama departemen yang sudah digunakan tidak dapat diubah.']);
        }

        $departemen->update($data);

        return redirect()->route('master.departemen.index')->with('success', 'Departemen berhasil diperbarui.');
    }

    public function destroy(Departemen $departemen)
    {
        if ($this->isUsed($departemen)) {
            return back()->with('error', 'Departemen masih digunakan dan tidak dapat dihapus.');
        }

        $departemen->delete();

        return redirect()->route('master.departemen.index')->with('success', 'Departemen berhasil dihapus.');
    }

    private function isUsed(Departemen $departemen): bool
    {
        return DB::table('karyawans')->where('departemen', $departemen->nama_departemen)->exists()
            || DB::table('jam_kerjas')->where('departemen', $departemen->nama_departemen)->exists();
    }
}
