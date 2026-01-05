<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\PricelistBuruh;
use Illuminate\Http\Request;

class PricelistBuruhController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PricelistBuruh::query();

        // Search
        if ($search = $request->get('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('barang', 'like', "%{$search}%")
                  ->orWhere('size', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $items = $query->orderBy('barang')->orderBy('size')->paginate(25);

        return view('master.pricelist-buruh.index', compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master.pricelist-buruh.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'barang' => 'required|string|max:255',
                'size' => 'nullable|string|max:255',
                'tarif' => 'required|numeric|min:0',
                'keterangan' => 'nullable|string',
            ], [
                'barang.required' => 'Nama barang wajib diisi.',
                'tarif.required' => 'Tarif wajib diisi.',
                'tarif.numeric' => 'Tarif harus berupa angka.',
                'tarif.min' => 'Tarif tidak boleh kurang dari 0.',
            ]);

            $data['is_active'] = $request->has('is_active');
            $data['created_by'] = auth()->id();

            PricelistBuruh::create($data);

            return redirect()->route('master.pricelist-buruh.index')->with('success', 'Pricelist buruh berhasil ditambahkan.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Error saving pricelist buruh: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan pricelist buruh: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(PricelistBuruh $pricelistBuruh)
    {
        return view('master.pricelist-buruh.show', ['item' => $pricelistBuruh]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PricelistBuruh $pricelistBuruh)
    {
        return view('master.pricelist-buruh.edit', ['item' => $pricelistBuruh]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PricelistBuruh $pricelistBuruh)
    {
        try {
            $data = $request->validate([
                'barang' => 'required|string|max:255',
                'size' => 'nullable|string|max:255',
                'tarif' => 'required|numeric|min:0',
                'full' => 'nullable|numeric|min:0',
                'empty' => 'nullable|numeric|min:0',
                'keterangan' => 'nullable|string',
            ], [
                'barang.required' => 'Nama barang wajib diisi.',
                'tarif.required' => 'Tarif wajib diisi.',
                'tarif.numeric' => 'Tarif harus berupa angka.',
                'tarif.min' => 'Tarif tidak boleh kurang dari 0.',
                'full.numeric' => 'Tarif full harus berupa angka.',
                'full.min' => 'Tarif full tidak boleh kurang dari 0.',
                'empty.numeric' => 'Tarif empty harus berupa angka.',
                'empty.min' => 'Tarif empty tidak boleh kurang dari 0.',
            ]);

            $data['is_active'] = $request->has('is_active');
            $data['updated_by'] = auth()->id();

            $pricelistBuruh->update($data);

            return redirect()->route('master.pricelist-buruh.index')->with('success', 'Pricelist buruh berhasil diperbarui.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Error updating pricelist buruh: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui pricelist buruh: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PricelistBuruh $pricelistBuruh)
    {
        try {
            $pricelistBuruh->delete();
            return redirect()->route('master.pricelist-buruh.index')->with('success', 'Pricelist buruh berhasil dihapus.');
        } catch (\Exception $e) {
            \Log::error('Error deleting pricelist buruh: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus pricelist buruh: ' . $e->getMessage());
        }
    }
}
