<?php

namespace App\Http\Controllers;

use App\Models\MasterCustomerBuruh;
use App\Imports\MasterCustomerBuruhImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class MasterCustomerBuruhController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:master-customer-buruh-view')->only(['index', 'show']);
        $this->middleware('can:master-customer-buruh-create')->only(['create', 'store', 'import']);
        $this->middleware('can:master-customer-buruh-update')->only(['edit', 'update']);
        $this->middleware('can:master-customer-buruh-delete')->only('destroy');
    }

    public function index()
    {
        $customers = MasterCustomerBuruh::orderBy('id', 'desc')->get();
        return view('master-customer-buruh.index', compact('customers'));
    }

    public function create()
    {
        return view('master-customer-buruh.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_customer' => 'required|string|max:255',
            'bank' => 'nullable|string|max:255',
            'nomor_rekening' => 'nullable|string|max:255',
            'penerima' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $data = $request->all();
        
        // Auto generate kode
        $lastCustomer = MasterCustomerBuruh::orderBy('id', 'desc')->first();
        if ($lastCustomer) {
            $lastId = intval(substr($lastCustomer->kode, 4));
            $newId = $lastId + 1;
        } else {
            $newId = 1;
        }
        $data['kode'] = 'CBB-' . str_pad($newId, 4, '0', STR_PAD_LEFT);

        if (!isset($data['is_active'])) {
            $data['is_active'] = false;
        }

        MasterCustomerBuruh::create($data);

        return redirect()->route('master-customer-buruh.index')->with('success', 'Customer Buruh berhasil ditambahkan.');
    }

    public function show(MasterCustomerBuruh $masterCustomerBuruh)
    {
        return view('master-customer-buruh.show', compact('masterCustomerBuruh'));
    }

    public function edit(MasterCustomerBuruh $masterCustomerBuruh)
    {
        return view('master-customer-buruh.edit', compact('masterCustomerBuruh'));
    }

    public function update(Request $request, MasterCustomerBuruh $masterCustomerBuruh)
    {
        $request->validate([
            'nama_customer' => 'required|string|max:255',
            'bank' => 'nullable|string|max:255',
            'nomor_rekening' => 'nullable|string|max:255',
            'penerima' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $data = $request->all();
        if (!isset($data['is_active'])) {
            $data['is_active'] = false;
        }

        $masterCustomerBuruh->update($data);

        return redirect()->route('master-customer-buruh.index')->with('success', 'Customer Buruh berhasil diperbarui.');
    }

    public function destroy(MasterCustomerBuruh $masterCustomerBuruh)
    {
        $masterCustomerBuruh->delete();
        return redirect()->route('master-customer-buruh.index')->with('success', 'Customer Buruh berhasil dihapus.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            Excel::import(new MasterCustomerBuruhImport, $request->file('file'));
            return redirect()->route('master-customer-buruh.index')->with('success', 'Data Customer Buruh berhasil diimport.');
        } catch (\Exception $e) {
            return redirect()->route('master-customer-buruh.index')->with('error', 'Gagal mengimport data: ' . $e->getMessage());
        }
    }
}
