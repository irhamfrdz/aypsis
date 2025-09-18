<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Exports\MasterBankTemplateExport;
use App\Imports\MasterBankImport;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MasterBankController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Bank::query();

        // Handle search functionality
        if ($request->has('search') && !empty($request->search)) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('code', 'like', '%' . $request->search . '%')
                  ->orWhere('keterangan', 'like', '%' . $request->search . '%');
        }

        $banks = $query->orderBy('name')->paginate(15);

        return view('master-bank.index', compact('banks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master-bank.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:banks,code',
            'keterangan' => 'nullable|string|max:1000'
        ]);

        Bank::create([
            'name' => $request->name,
            'code' => $request->code,
            'keterangan' => $request->keterangan
        ]);

        return redirect()->route('master-bank-index')->with('success', 'Bank berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Bank $bank)
    {
        return view('master-bank.show', compact('bank'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Bank $bank)
    {
        return view('master-bank.edit', compact('bank'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Bank $bank)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => ['required', 'string', 'max:10', Rule::unique('banks')->ignore($bank->id)],
            'keterangan' => 'nullable|string|max:1000'
        ]);

        $bank->update([
            'name' => $request->name,
            'code' => $request->code,
            'keterangan' => $request->keterangan
        ]);

        return redirect()->route('master-bank-index')->with('success', 'Bank berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bank $bank)
    {
        $bank->delete();

        return redirect()->route('master-bank-index')->with('success', 'Bank berhasil dihapus!');
    }

    /**
     * Download template Excel untuk import bank
     */
    public function downloadTemplate()
    {
        $export = new MasterBankTemplateExport();
        return $export->download();
    }

    /**
     * Import data bank dari Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx,xls|max:2048'
        ]);

        try {
            $import = new MasterBankImport();
            $result = $import->import($request->file('file'));

            if ($result['success_count'] > 0) {
                $message = "Berhasil mengimport {$result['success_count']} data bank";
                if (!empty($result['errors'])) {
                    $message .= ". Namun ada " . count($result['errors']) . " error: " . implode('; ', $result['errors']);
                }
                return redirect()->route('master-bank-index')->with('success', $message);
            } else {
                return redirect()->route('master-bank-index')->with('error', 'Gagal mengimport data: ' . implode('; ', $result['errors']));
            }
        } catch (\Exception $e) {
            return redirect()->route('master-bank-index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
