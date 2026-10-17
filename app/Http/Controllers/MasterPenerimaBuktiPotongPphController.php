<?php

namespace App\Http\Controllers;

use App\Models\PenerimaBuktiPotongPph;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MasterPenerimaBuktiPotongPphController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', Rule::in(['aktif', 'nonaktif'])],
        ]);

        $penerimas = PenerimaBuktiPotongPph::query()
            ->when(filled($filters['search'] ?? null), function ($query) use ($filters) {
                $search = $filters['search'];
                $query->where(function ($query) use ($search) {
                    $query->where('nama', 'like', "%{$search}%")
                        ->orWhere('npwp', 'like', "%{$search}%")
                        ->orWhere('nik', 'like', "%{$search}%");
                });
            })
            ->when(filled($filters['status'] ?? null), fn ($query) => $query->where('is_active', $filters['status'] === 'aktif'))
            ->orderBy('nama')
            ->paginate(15)
            ->withQueryString();

        return view('master.penerima-bukti-potong-pph.index', compact('penerimas'));
    }

    public function create()
    {
        return view('master.penerima-bukti-potong-pph.form');
    }

    public function store(Request $request)
    {
        PenerimaBuktiPotongPph::create($this->validated($request));

        return redirect()->route('master.penerima-bukti-potong-pph.index')
            ->with('success', 'Penerima bukti potong PPh berhasil ditambahkan.');
    }

    public function edit(PenerimaBuktiPotongPph $penerimaBuktiPotongPph)
    {
        return view('master.penerima-bukti-potong-pph.form', ['penerima' => $penerimaBuktiPotongPph]);
    }

    public function update(Request $request, PenerimaBuktiPotongPph $penerimaBuktiPotongPph)
    {
        $penerimaBuktiPotongPph->update($this->validated($request));

        return redirect()->route('master.penerima-bukti-potong-pph.index')
            ->with('success', 'Penerima bukti potong PPh berhasil diperbarui.');
    }

    public function destroy(PenerimaBuktiPotongPph $penerimaBuktiPotongPph)
    {
        $penerimaBuktiPotongPph->delete();

        return redirect()->route('master.penerima-bukti-potong-pph.index')
            ->with('success', 'Penerima bukti potong PPh berhasil dihapus.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'npwp' => ['nullable', 'string', 'max:32'],
            'nik' => ['nullable', 'string', 'max:20'],
            'alamat' => ['nullable', 'string'],
            'email' => ['nullable', 'email', 'max:255'],
            'telepon' => ['nullable', 'string', 'max:30'],
            'is_active' => ['required', Rule::in(['0', '1'])],
        ]);

        $data['is_active'] = (bool) $data['is_active'];

        return $data;
    }
}
