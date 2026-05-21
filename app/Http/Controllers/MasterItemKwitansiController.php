<?php

namespace App\Http\Controllers;

use App\Exports\MasterItemKwitansiTemplateExport;
use App\Imports\MasterItemKwitansiImport;
use App\Models\MasterItemKwitansi;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class MasterItemKwitansiController extends Controller
{
    public function index()
    {
        $items = MasterItemKwitansi::latest()->get();

        return view('master.item-kwitansi.index', compact('items'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        try {
            Excel::import(new MasterItemKwitansiImport, $request->file('file'));

            return redirect()->back()->with('success', 'Data Item Kwitansi berhasil diimport.');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $error_messages = [];
            foreach ($failures as $failure) {
                $error_messages[] = 'Baris '.$failure->row().': '.implode(', ', $failure->errors());
            }

            return redirect()->back()->withErrors($error_messages);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['Terjadi kesalahan saat mengimport data: '.$e->getMessage()]);
        }
    }

    public function downloadTemplate()
    {
        return Excel::download(new MasterItemKwitansiTemplateExport, 'template_item_kwitansi.xlsx');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required|string|max:50|unique:master_item_kwitansis,kode',
            'nama_item' => 'required|string|max:255',
            'group' => 'required|string|max:100',
        ]);

        MasterItemKwitansi::create($request->all());

        return redirect()->back()->with('success', 'Item Kwitansi berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kode' => 'required|string|max:50|unique:master_item_kwitansis,kode,'.$id,
            'nama_item' => 'required|string|max:255',
            'group' => 'required|string|max:100',
        ]);

        $item = MasterItemKwitansi::findOrFail($id);
        $item->update($request->all());

        return redirect()->back()->with('success', 'Item Kwitansi berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $item = MasterItemKwitansi::findOrFail($id);
        $item->delete();

        return redirect()->back()->with('success', 'Item Kwitansi berhasil dihapus.');
    }
}
