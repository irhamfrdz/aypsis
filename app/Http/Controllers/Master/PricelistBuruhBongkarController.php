<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\PricelistBuruhBongkar;
use Illuminate\Http\Request;

class PricelistBuruhBongkarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PricelistBuruhBongkar::query();

        // Search
        if ($search = $request->get('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('size', 'like', "%{$search}%")
                  ->orWhere('lokasi', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $items = $query->orderBy('size')->paginate(25);

        return view('master.pricelist-buruh-bongkar.index', compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master.pricelist-buruh-bongkar.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'size' => 'nullable|string|max:255',
                'lokasi' => 'required|in:Batam,Jakarta',
                'nominal' => 'required|numeric|min:0',
                'keterangan' => 'nullable|string',
            ], [
                'lokasi.required' => 'Lokasi wajib dipilih.',
                'lokasi.in' => 'Lokasi harus Batam atau Jakarta.',
                'nominal.required' => 'Nominal wajib diisi.',
                'nominal.numeric' => 'Nominal harus berupa angka.',
                'nominal.min' => 'Nominal tidak boleh kurang dari 0.',
            ]);

            $data['status'] = $request->has('status');
            $data['created_by'] = auth()->id();

            PricelistBuruhBongkar::create($data);

            return redirect()->route('master.pricelist-buruh-bongkar.index')->with('success', 'Pricelist buruh bongkar berhasil ditambahkan.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Error saving pricelist buruh bongkar: '.$e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan pricelist buruh bongkar: '.$e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PricelistBuruhBongkar $pricelistBuruhBongkar)
    {
        return view('master.pricelist-buruh-bongkar.edit', ['item' => $pricelistBuruhBongkar]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PricelistBuruhBongkar $pricelistBuruhBongkar)
    {
        try {
            $data = $request->validate([
                'size' => 'nullable|string|max:255',
                'lokasi' => 'required|in:Batam,Jakarta',
                'nominal' => 'required|numeric|min:0',
                'keterangan' => 'nullable|string',
            ], [
                'lokasi.required' => 'Lokasi wajib dipilih.',
                'lokasi.in' => 'Lokasi harus Batam atau Jakarta.',
                'nominal.required' => 'Nominal wajib diisi.',
                'nominal.numeric' => 'Nominal harus berupa angka.',
                'nominal.min' => 'Nominal tidak boleh kurang dari 0.',
            ]);

            $data['status'] = $request->has('status');
            $data['updated_by'] = auth()->id();

            $pricelistBuruhBongkar->update($data);

            return redirect()->route('master.pricelist-buruh-bongkar.index')->with('success', 'Pricelist buruh bongkar berhasil diperbarui.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Error updating pricelist buruh bongkar: '.$e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui pricelist buruh bongkar: '.$e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PricelistBuruhBongkar $pricelistBuruhBongkar)
    {
        try {
            $pricelistBuruhBongkar->delete();

            return redirect()->route('master.pricelist-buruh-bongkar.index')->with('success', 'Pricelist buruh bongkar berhasil dihapus.');
        } catch (\Exception $e) {
            \Log::error('Error deleting pricelist buruh bongkar: '.$e->getMessage());

            return redirect()->back()->with('error', 'Gagal menghapus pricelist buruh bongkar: '.$e->getMessage());
        }
    }
}
