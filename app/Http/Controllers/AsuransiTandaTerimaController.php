<?php

namespace App\Http\Controllers;

use App\Models\AsuransiTandaTerima;
use App\Models\VendorAsuransi;
use App\Models\TandaTerima;
use App\Models\TandaTerimaTanpaSuratJalan;
use App\Models\TandaTerimaLcl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AsuransiTandaTerimaController extends Controller
{
    public function index(Request $request)
    {
        $query = AsuransiTandaTerima::with(['vendorAsuransi', 'tandaTerima', 'tandaTerimaTanpaSj', 'tandaTerimaLcl']);

        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor_polis', 'like', "%{$search}%")
                  ->orWhereHas('vendorAsuransi', function($v) use ($search) {
                      $v->where('nama_asuransi', 'like', "%{$search}%");
                  });
            });
        }

        $asuransiList = $query->latest()->paginate(15);

        return view('asuransi-tanda-terima.index', compact('asuransiList'));
    }

    public function create()
    {
        $vendors = VendorAsuransi::orderBy('nama_asuransi')->get();
        
        // Fetch receipts that don't have insurance yet (optional, or just fetch all recent)
        // For simplicity, fetch recent receipts of each type
        $tandaTerimas = TandaTerima::latest()->limit(50)->get();
        $tandaTerimaTanpaSjs = TandaTerimaTanpaSuratJalan::latest()->limit(50)->get();
        $tandaTerimaLcls = TandaTerimaLcl::latest()->limit(50)->get();

        return view('asuransi-tanda-terima.create', compact('vendors', 'tandaTerimas', 'tandaTerimaTanpaSjs', 'tandaTerimaLcls'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'vendor_asuransi_id' => 'required|exists:vendor_asuransi,id',
            'receipt_type' => 'required|in:tt,tttsj,lcl',
            'receipt_id' => 'required',
            'nomor_polis' => 'required|string|max:255',
            'tanggal_polis' => 'required|date',
            'nilai_pertanggungan' => 'required|numeric|min:0',
            'premi' => 'required|numeric|min:0',
            'asuransi_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'status' => 'required|in:Aktif,Selesai,Batal',
        ]);

        $data = $request->only([
            'vendor_asuransi_id', 'nomor_polis', 'tanggal_polis', 
            'nilai_pertanggungan', 'premi', 'keterangan', 'status'
        ]);

        // Map receipt type to column
        if ($request->receipt_type == 'tt') {
            $data['tanda_terima_id'] = $request->receipt_id;
        } elseif ($request->receipt_type == 'tttsj') {
            $data['tanda_terima_tanpa_sj_id'] = $request->receipt_id;
        } elseif ($request->receipt_type == 'lcl') {
            $data['tanda_terima_lcl_id'] = $request->receipt_id;
        }

        if ($request->hasFile('asuransi_file')) {
            $path = $request->file('asuransi_file')->store('asuransi_tanda_terima', 'public');
            $data['asuransi_path'] = $path;
        }

        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();

        AsuransiTandaTerima::create($data);

        return redirect()->route('asuransi-tanda-terima.index')->with('success', 'Data asuransi berhasil disimpan.');
    }

    public function show(AsuransiTandaTerima $asuransiTandaTerima)
    {
        $asuransiTandaTerima->load(['vendorAsuransi', 'tandaTerima', 'tandaTerimaTanpaSj', 'tandaTerimaLcl', 'creator', 'updater']);
        return view('asuransi-tanda-terima.show', compact('asuransiTandaTerima'));
    }

    public function edit(AsuransiTandaTerima $asuransiTandaTerima)
    {
        $vendors = VendorAsuransi::orderBy('nama_asuransi')->get();
        return view('asuransi-tanda-terima.edit', compact('asuransiTandaTerima', 'vendors'));
    }

    public function update(Request $request, AsuransiTandaTerima $asuransiTandaTerima)
    {
        $request->validate([
            'vendor_asuransi_id' => 'required|exists:vendor_asuransi,id',
            'nomor_polis' => 'required|string|max:255',
            'tanggal_polis' => 'required|date',
            'nilai_pertanggungan' => 'required|numeric|min:0',
            'premi' => 'required|numeric|min:0',
            'asuransi_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'status' => 'required|in:Aktif,Selesai,Batal',
        ]);

        $data = $request->only([
            'vendor_asuransi_id', 'nomor_polis', 'tanggal_polis', 
            'nilai_pertanggungan', 'premi', 'keterangan', 'status'
        ]);

        if ($request->hasFile('asuransi_file')) {
            // Delete old file
            if ($asuransiTandaTerima->asuransi_path) {
                Storage::disk('public')->delete($asuransiTandaTerima->asuransi_path);
            }
            $path = $request->file('asuransi_file')->store('asuransi_tanda_terima', 'public');
            $data['asuransi_path'] = $path;
        }

        $data['updated_by'] = Auth::id();

        $asuransiTandaTerima->update($data);

        return redirect()->route('asuransi-tanda-terima.index')->with('success', 'Data asuransi berhasil diperbarui.');
    }

    public function destroy(AsuransiTandaTerima $asuransiTandaTerima)
    {
        if ($asuransiTandaTerima->asuransi_path) {
            Storage::disk('public')->delete($asuransiTandaTerima->asuransi_path);
        }
        $asuransiTandaTerima->delete();
        return redirect()->route('asuransi-tanda-terima.index')->with('success', 'Data asuransi berhasil dihapus.');
    }
}
