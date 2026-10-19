<?php

namespace App\Http\Controllers;

use App\Models\PenerimaBuktiPotongPph;
use Illuminate\Http\Request;

class MasterPenerimaBuktiPotongPphController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
        ]);

        $penerimas = PenerimaBuktiPotongPph::query()
            ->when(filled($filters['search'] ?? null), function ($query) use ($filters) {
                $search = $filters['search'];
                $query->where(function ($query) use ($search) {
                    $query->where('nama', 'like', "%{$search}%")
                        ->orWhere('nik_npwp', 'like', "%{$search}%")
                        ->orWhere('pph', 'like', "%{$search}%")
                        ->orWhere('grup', 'like', "%{$search}%");
                });
            })
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
            'nik_npwp' => ['nullable', 'string', 'max:64'],
            'pph' => ['nullable', 'string', 'max:100'],
            'grup' => ['nullable', 'string', 'max:100'],
        ]);

        return $data;
    }
}
