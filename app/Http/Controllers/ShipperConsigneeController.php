<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ShipperConsignee;
use Illuminate\Http\Request;

class ShipperConsigneeController extends Controller
{
    public function index(Request $request)
    {
        $query = ShipperConsignee::query();

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('shipper', 'like', "%{$search}%")
                  ->orWhere('consignee', 'like', "%{$search}%")
                  ->orWhere('telepon', 'like', "%{$search}%")
                  ->orWhere('hs_code', 'like', "%{$search}%")
                  ->orWhere('commodity', 'like', "%{$search}%");
            });
        }

        $shipperConsignees = $query->orderBy('id', 'desc')->get();
        return view('master.shipper-consignee.index', compact('shipperConsignees'));
    }

    public function create()
    {
        return view('master.shipper-consignee.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'shipper' => 'nullable|string|max:255',
            'consignee' => 'nullable|string|max:255',
        ]);

        $dataToStore = $request->all();
        $shipperConsignee = ShipperConsignee::create($dataToStore);
        
        // Memastikan contact_person tersimpan, bypass fillable cache jika ada
        if (array_key_exists('contact_person', $dataToStore)) {
            $shipperConsignee->contact_person = $dataToStore['contact_person'];
            $shipperConsignee->save();
        }

        // Jika contact_person diisi, perbarui juga untuk semua baris dengan nama shipper yang sama
        if (!empty($dataToStore['contact_person']) && !empty($shipperConsignee->shipper)) {
            ShipperConsignee::where('shipper', $shipperConsignee->shipper)
                ->where('id', '!=', $shipperConsignee->id)
                ->update(['contact_person' => $dataToStore['contact_person']]);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $shipperConsignee,
                'message' => 'Data Shipper / Consignee berhasil ditambahkan.'
            ]);
        }

        return redirect()->route('master.shipper-consignee.index')
            ->with('success', 'Data Shipper / Consignee berhasil ditambahkan.');
    }

    public function show(Request $request, ShipperConsignee $shipper_consignee)
    {
        if ($request->wantsJson()) {
            return response()->json($shipper_consignee);
        }
        return view('master.shipper-consignee.show', compact('shipper_consignee'));
    }

    public function edit(ShipperConsignee $shipper_consignee)
    {
        return view('master.shipper-consignee.edit', compact('shipper_consignee'));
    }

    public function update(Request $request, ShipperConsignee $shipper_consignee)
    {
        $request->validate([
            'shipper' => 'nullable|string|max:255',
            'consignee' => 'nullable|string|max:255',
        ]);

        $dataToUpdate = $request->all();
        $shipper_consignee->update($dataToUpdate);
        
        // Memastikan contact_person tersimpan, bypass fillable cache jika ada
        if (array_key_exists('contact_person', $dataToUpdate)) {
            $shipper_consignee->contact_person = $dataToUpdate['contact_person'];
            $shipper_consignee->save();
        }

        // Jika contact_person diisi, perbarui juga untuk semua baris dengan nama shipper yang sama
        if (!empty($dataToUpdate['contact_person']) && !empty($shipper_consignee->shipper)) {
            ShipperConsignee::where('shipper', $shipper_consignee->shipper)
                ->where('id', '!=', $shipper_consignee->id)
                ->update(['contact_person' => $dataToUpdate['contact_person']]);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $shipper_consignee,
                'message' => 'Data Shipper / Consignee berhasil diperbarui.'
            ]);
        }

        return redirect()->route('master.shipper-consignee.index')
            ->with('success', 'Data Shipper / Consignee berhasil diperbarui.');
    }

    public function destroy(ShipperConsignee $shipper_consignee)
    {
        $shipper_consignee->delete();

        return redirect()->route('master.shipper-consignee.index')
            ->with('success', 'Data Shipper / Consignee berhasil dihapus.');
    }

    public function template()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\ShipperConsigneeTemplateExport, 'Template_Import_Shipper_Consignee.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048'
        ]);

        try {
            \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\ShipperConsigneeImport, $request->file('file'));
            return redirect()->back()->with('success', 'Data Shipper / Consignee berhasil diimport.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal import data: ' . $e->getMessage());
        }
    }

    public function templateContact()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\ShipperContactExport, 'Template_Update_Contact_Person.xlsx');
    }

    public function importContact(Request $request)
    {
        $request->validate([
            'file_contact' => 'required|mimes:xlsx,xls,csv|max:2048'
        ]);

        try {
            \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\ShipperContactImport, $request->file('file_contact'));
            return redirect()->back()->with('success', 'Data Contact Person berhasil diupdate secara massal.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal update data: ' . $e->getMessage());
        }
    }
}
