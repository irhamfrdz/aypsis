<?php

namespace App\Http\Controllers;

use App\Models\UangLembur;
use App\Models\UangLemburRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UangLemburController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $query = UangLembur::with('rules');
        
        if ($search) {
            $query->where('group', 'like', "%{$search}%")
                  ->orWhere('sub_group', 'like', "%{$search}%");
        }
        
        $lemburs = $query->paginate(15);
        
        return view('uang-lembur.index', compact('lemburs'));
    }

    public function create()
    {
        return view('uang-lembur.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'group' => 'required|string|max:255',
            'sub_group' => 'required|string|max:255',
            'rules' => 'required|array|min:1',
            'rules.*.tipe_hari' => 'required|in:Hari Biasa,Hari Libur',
            'rules.*.jam_mulai' => 'nullable|date_format:H:i',
            'rules.*.jam_selesai' => 'nullable|date_format:H:i',
            'rules.*.is_sampai_selesai' => 'nullable|boolean',
            'rules.*.satuan' => 'required|in:Hari,Jam',
            'rules.*.nominal' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $lembur = UangLembur::create([
                'group' => $request->group,
                'sub_group' => $request->sub_group,
            ]);

            foreach ($request->rules as $rule) {
                $isSampaiSelesai = isset($rule['is_sampai_selesai']) ? 1 : 0;
                
                UangLemburRule::create([
                    'uang_lembur_id' => $lembur->id,
                    'tipe_hari' => $rule['tipe_hari'],
                    'jam_mulai' => $rule['jam_mulai'] ?? null,
                    'jam_selesai' => $isSampaiSelesai ? null : ($rule['jam_selesai'] ?? null),
                    'is_sampai_selesai' => $isSampaiSelesai,
                    'satuan' => $rule['satuan'],
                    'nominal' => $rule['nominal'],
                ]);
            }

            DB::commit();
            return redirect()->route('uang-lembur.index')->with('success', 'Master tarif lembur dan aturan jam berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(UangLembur $uangLembur)
    {
        $uangLembur->load('rules');
        return view('uang-lembur.edit', compact('uangLembur'));
    }

    public function update(Request $request, UangLembur $uangLembur)
    {
        $request->validate([
            'group' => 'required|string|max:255',
            'sub_group' => 'required|string|max:255',
            'rules' => 'required|array|min:1',
            'rules.*.tipe_hari' => 'required|in:Hari Biasa,Hari Libur',
            'rules.*.jam_mulai' => 'nullable|date_format:H:i',
            'rules.*.jam_selesai' => 'nullable|date_format:H:i',
            'rules.*.is_sampai_selesai' => 'nullable|boolean',
            'rules.*.satuan' => 'required|in:Hari,Jam',
            'rules.*.nominal' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $uangLembur->update([
                'group' => $request->group,
                'sub_group' => $request->sub_group,
            ]);

            // Delete old rules
            $uangLembur->rules()->delete();

            // Insert new rules
            foreach ($request->rules as $rule) {
                $isSampaiSelesai = isset($rule['is_sampai_selesai']) ? 1 : 0;
                
                UangLemburRule::create([
                    'uang_lembur_id' => $uangLembur->id,
                    'tipe_hari' => $rule['tipe_hari'],
                    'jam_mulai' => $rule['jam_mulai'] ?? null,
                    'jam_selesai' => $isSampaiSelesai ? null : ($rule['jam_selesai'] ?? null),
                    'is_sampai_selesai' => $isSampaiSelesai,
                    'satuan' => $rule['satuan'],
                    'nominal' => $rule['nominal'],
                ]);
            }

            DB::commit();
            return redirect()->route('uang-lembur.index')->with('success', 'Master tarif lembur dan aturan jam berhasil diupdate.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(UangLembur $uangLembur)
    {
        $uangLembur->delete(); // Rules will cascade delete
        return redirect()->route('uang-lembur.index')->with('success', 'Master tarif lembur berhasil dihapus.');
    }
}
