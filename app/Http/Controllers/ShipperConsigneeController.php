<?php

namespace App\Http\Controllers;

use App\Exports\ShipperConsigneeDataExport;
use App\Models\ShipperConsignee;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ShipperConsigneeController extends Controller
{
    public function index(Request $request)
    {
        $shipperConsignees = $this->filteredQuery($request)->orderByDesc('id')->paginate(50)->withQueryString();

        return view('master.shipper-consignee.index', compact('shipperConsignees'));
    }

    public function export(Request $request)
    {
        return Excel::download(
            new ShipperConsigneeDataExport($this->filteredQuery($request)->orderByDesc('id')),
            'Master_Shipper_Consignee_'.now()->format('Ymd_His').'.xlsx'
        );
    }

    private function filteredQuery(Request $request): Builder
    {
        $query = ShipperConsignee::query();
        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function (Builder $query) use ($search) {
                $query->where('shipper', 'like', "%{$search}%")
                    ->orWhere('consignee', 'like', "%{$search}%")
                    ->orWhere('alamat_shipper', 'like', "%{$search}%")
                    ->orWhere('alamat_consignee', 'like', "%{$search}%")
                    ->orWhere('npwp_shipper', 'like', "%{$search}%")
                    ->orWhere('npwp_consignee', 'like', "%{$search}%")
                    ->orWhere('notify_party_consignee', 'like', "%{$search}%")
                    ->orWhere('delivery_address_contact_person', 'like', "%{$search}%")
                    ->orWhere('document_ppftz_03', 'like', "%{$search}%")
                    ->orWhere('condition', 'like', "%{$search}%");
            });
        }

        return $query;
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
        // Support either delivery_address_contact_person or legacy delivery_address input
        if (isset($dataToStore['delivery_address']) && ! isset($dataToStore['delivery_address_contact_person'])) {
            $dataToStore['delivery_address_contact_person'] = $dataToStore['delivery_address'];
        }

        $shipperConsignee = ShipperConsignee::create($dataToStore);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $shipperConsignee,
                'message' => 'Data Shipper / Consignee berhasil ditambahkan.',
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
        if (isset($dataToUpdate['delivery_address']) && ! isset($dataToUpdate['delivery_address_contact_person'])) {
            $dataToUpdate['delivery_address_contact_person'] = $dataToUpdate['delivery_address'];
        }

        $shipper_consignee->update($dataToUpdate);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $shipper_consignee,
                'message' => 'Data Shipper / Consignee berhasil diperbarui.',
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
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        try {
            \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\ShipperConsigneeImport, $request->file('file'));

            return redirect()->back()->with('success', 'Data Shipper / Consignee berhasil diimport.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal import data: '.$e->getMessage());
        }
    }

    public function templateContact()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\ShipperContactExport, 'Template_Update_Contact_Person.xlsx');
    }

    public function importContact(Request $request)
    {
        $request->validate([
            'file_contact' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        try {
            \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\ShipperContactImport, $request->file('file_contact'));

            return redirect()->back()->with('success', 'Data Contact Person berhasil diupdate secara massal.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal update data: '.$e->getMessage());
        }
    }
}
