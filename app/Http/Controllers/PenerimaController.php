<?php

namespace App\Http\Controllers;

use App\Exports\PenerimaExport;
use App\Models\Penerima;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class PenerimaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Penerima::query();

        // Handle search functionality
        if ($request->has('search') && ! empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('nama_penerima', 'LIKE', '%'.$searchTerm.'%')
                    ->orWhere('contact_person', 'LIKE', '%'.$searchTerm.'%')
                    ->orWhere('catatan', 'LIKE', '%'.$searchTerm.'%');
            });
        }

        // Handle quick filters
        if ($request->has('filter')) {
            if ($request->filter === 'no_alamat') {
                $query->where(function ($q) {
                    $q->whereNull('alamat')
                        ->orWhere('alamat', '')
                        ->orWhere('alamat', '-');
                });
            } elseif ($request->filter === 'similar') {
                $allPenerimas = Penerima::select('id', 'nama_penerima')->get();
                $grouped = [];
                foreach ($allPenerimas as $p) {
                    $clean = strtoupper($p->nama_penerima);
                    $clean = preg_replace('/\b(PT|CV|UD|TB|TOKO|Tbk)\b\.?/i', '', $clean);
                    $clean = preg_replace('/[^A-Z0-9]/', '', $clean);
                    $clean = trim($clean);
                    if (! empty($clean)) {
                        $grouped[$clean][] = $p->id;
                    }
                }
                $duplicateIds = [];
                foreach ($grouped as $ids) {
                    if (count($ids) > 1) {
                        $duplicateIds = array_merge($duplicateIds, $ids);
                    }
                }
                $query->whereIn('id', $duplicateIds);
            }
        }

        $penerimas = $query->paginate(15)->withQueryString();

        return view('master-penerima.index', compact('penerimas'));
    }

    /**
     * Export the resource to Excel.
     */
    public function exportExcel(Request $request)
    {
        return Excel::download(new PenerimaExport($request->all()), 'master-penerima-'.date('YmdHis').'.xlsx');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master-penerima.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_penerima' => 'required|string|max:255|unique:penerimas,nama_penerima',
            'contact_person' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
            'npwp' => 'nullable|string',
            'nitku' => 'nullable|string',
            'catatan' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'iu_bp_kawasan' => 'nullable|in:ada,tidak ada',
        ]);

        $penerima = Penerima::create($request->all());

        return redirect()->route('penerima.index')->with('success', 'Penerima berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $penerima = Penerima::findOrFail($id);

        return view('master-penerima.show', compact('penerima'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $penerima = Penerima::findOrFail($id);

        return view('master-penerima.edit', compact('penerima'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $penerima = Penerima::findOrFail($id);

        $request->validate([
            'nama_penerima' => 'required|string|max:255|unique:penerimas,nama_penerima,'.$id,
            'contact_person' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
            'npwp' => 'nullable|string',
            'nitku' => 'nullable|string',
            'catatan' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'iu_bp_kawasan' => 'nullable|in:ada,tidak ada',
        ]);

        $penerima->update($request->all());

        return redirect()->route('penerima.index')->with('success', 'Penerima berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $penerima = Penerima::findOrFail($id);
        $penerima->delete();

        return redirect()->route('penerima.index')
            ->with('success', 'Data penerima berhasil dihapus.');
    }

    public function downloadTemplate()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\PenerimaTemplateExport, 'template_penerima.xlsx');
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt,xlsx,xls|max:2048',
        ]);

        try {
            DB::beginTransaction();

            \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\PenerimaImport, $request->file('file'));

            DB::commit();

            return back()->with('success', 'Berhasil mengimport data penerima.');

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            DB::rollBack();
            $failures = $e->failures();
            $errorMessages = [];
            foreach ($failures as $failure) {
                $errorMessages[] = 'Baris '.$failure->row().': '.implode(', ', $failure->errors());
            }

            return back()->with('error', 'Validasi gagal: '.implode('<br>', $errorMessages));
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Terjadi kesalahan saat import: '.$e->getMessage());
        }
    }

    public function generateCode()
    {
        // Because the 'penerimas' table doesn't have a 'kode' column,
        // we use the 'id' to generate a unique MR number.
        // If we want a sequential number independent of ID, we might need to add the column.
        // For now, let's use the max ID + 1.
        $lastId = Penerima::max('id') ?: 0;
        $nextNumber = $lastId + 1;

        return 'MR'.str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Show the form for creating from Order form (popup mode)
     */
    public function createForOrder(Request $request)
    {
        $nextKode = $this->generateCode();
        $search = $request->get('search', '');
        $isPopup = $request->has('popup');

        return view('master-penerima.create-for-order', compact('nextKode', 'search', 'isPopup'));
    }

    /**
     * Store for Order form (popup mode)
     */
    public function storeForOrder(Request $request)
    {
        $request->validate([
            'nama_penerima' => 'required|string|max:255|unique:penerimas,nama_penerima',
            'contact_person' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
            'npwp' => 'nullable|string',
            'nitku' => 'nullable|string',
            'catatan' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'iu_bp_kawasan' => 'nullable|in:ada,tidak ada',
        ]);

        $kode = $this->generateCode();

        $penerima = Penerima::create($request->all());
        $penerima->kode = $kode; // Add virtual property for the success view

        if ($request->has('popup')) {
            // Return HTML with postMessage script for popup mode
            return view('master-penerima.popup-success', [
                'penerima' => $penerima,
                'message' => 'Penerima berhasil ditambahkan!',
            ]);
        }

        return redirect()->route('penerima.index')
            ->with('success', 'Penerima berhasil ditambahkan!');
    }

    /**
     * Show the form for creating from Tanda Terima form (popup mode)
     */
    public function createForTandaTerima()
    {
        return view('master-penerima.create-for-tanda-terima');
    }

    /**
     * Store from Tanda Terima form (popup mode)
     */
    public function storeForTandaTerima(Request $request)
    {
        $validated = $request->validate([
            'nama_penerima' => 'required|string|max:255|unique:penerimas,nama_penerima',
            'alamat' => 'nullable|string',
            'npwp' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive',
        ]);

        DB::beginTransaction();
        try {
            $penerima = Penerima::create($validated);

            DB::commit();

            // Store penerima data in session for popup to send to parent
            session([
                'penerima_nama' => $penerima->nama_penerima,
                'penerima_alamat' => $penerima->alamat,
            ]);

            return redirect()->back()
                ->with('success', 'Penerima berhasil ditambahkan')
                ->with('popup', true);
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->withInput()
                ->with('error', 'Gagal menambahkan penerima: '.$e->getMessage());
        }
    }

    /**
     * Show the form for editing from Tanda Terima form (popup mode)
     */
    public function editForTandaTerima(Penerima $penerima)
    {
        return view('master-penerima.edit-for-tanda-terima', compact('penerima'));
    }

    /**
     * Update from Tanda Terima form (popup mode)
     */
    public function updateForTandaTerima(Request $request, Penerima $penerima)
    {
        $validated = $request->validate([
            'nama_penerima' => 'required|string|max:255|unique:penerimas,nama_penerima,'.$penerima->id,
            'contact_person' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
            'npwp' => 'nullable|string|max:20',
            'nitku' => 'nullable|string|max:255',
            'catatan' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'iu_bp_kawasan' => 'nullable|in:ada,tidak ada',
        ]);

        DB::beginTransaction();
        try {
            $penerima->update($validated);

            DB::commit();

            // Store updated data in session for popup message
            session([
                'penerima_nama' => $penerima->nama_penerima,
                'penerima_alamat' => $penerima->alamat,
            ]);

            return redirect()->back()
                ->with('success', 'Penerima berhasil diperbarui')
                ->with('popup', true);
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->withInput()
                ->with('error', 'Gagal memperbarui penerima: '.$e->getMessage());
        }
    }
}
