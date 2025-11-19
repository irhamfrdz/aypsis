<?php

namespace App\Http\Controllers;

use App\Models\Kontainer;
use Illuminate\Http\Request;

class KontainerController extends Controller
{
    /**
     * Menampilkan daftar semua kontainer.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Kontainer::query();

        // Search functionality
        if ($search = $request->get('search')) {
            $query->where('nomor_seri_gabungan', 'like', '%' . $search . '%')
                  ->orWhere('awalan_kontainer', 'like', '%' . $search . '%')
                  ->orWhere('nomor_seri_kontainer', 'like', '%' . $search . '%')
                  ->orWhere('akhiran_kontainer', 'like', '%' . $search . '%');
        }

        // Vendor filter
        if ($vendor = $request->get('vendor')) {
            $query->where('vendor', $vendor);
        }

        // Ukuran filter
        if ($ukuran = $request->get('ukuran')) {
            $query->where('ukuran', $ukuran);
        }

        // Status filter
        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        // Tanggal sewa filter
        if ($tanggalSewa = $request->get('tanggal_sewa')) {
            switch ($tanggalSewa) {
                case 'tanpa_tanggal_akhir':
                    $query->whereNotNull('tanggal_mulai_sewa')
                          ->whereNull('tanggal_selesai_sewa');
                    break;
                case 'ada_tanggal_akhir':
                    $query->whereNotNull('tanggal_selesai_sewa');
                    break;
                case 'tanpa_tanggal_mulai':
                    $query->whereNull('tanggal_mulai_sewa');
                    break;
                case 'lengkap':
                    $query->whereNotNull('tanggal_mulai_sewa')
                          ->whereNotNull('tanggal_selesai_sewa');
                    break;
            }
        }

        // Get distinct vendors for filter dropdown
        $vendors = Kontainer::distinct()
                           ->whereNotNull('vendor')
                           ->where('vendor', '!=', '')
                           ->orderBy('vendor')
                           ->pluck('vendor');

        // Menggunakan paginasi untuk performa yang lebih baik
        $perPage = $request->input('per_page', 15); // Default 15 jika tidak ada parameter
        $kontainers = $query->latest()->paginate($perPage);
        $kontainers->appends($request->query());

        return view('master-kontainer.index', compact('kontainers', 'vendors'));
    }

    /**
     * Menampilkan formulir untuk membuat kontainer baru.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('master-kontainer.create');
    }

    /**
     * Menyimpan kontainer baru ke dalam database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Convert date format from dd/mmm/yyyy to yyyy-mm-dd for date fields
        $dateFields = ['tanggal_masuk_sewa', 'tanggal_selesai_sewa'];
        foreach ($dateFields as $field) {
            if ($request->filled($field)) {
                try {
                    $date = \DateTime::createFromFormat('d/M/Y', $request->input($field));
                    if ($date) {
                        $request->merge([$field => $date->format('Y-m-d')]);
                    }
                } catch (\Exception $e) {
                    // If conversion fails, keep original value for validation to catch
                }
            }
        }

        // Gabungkan awalan, nomor seri, dan akhiran untuk membuat nomor seri gabungan
        $nomor_seri_gabungan = $request->input('awalan_kontainer') .
                               $request->input('nomor_seri_kontainer') .
                               $request->input('akhiran_kontainer');

        $request->merge(['nomor_seri_gabungan' => $nomor_seri_gabungan]);

        // Custom validation rules
        $rules = [
            'awalan_kontainer' => 'required|string|size:4',
            'nomor_seri_kontainer' => 'required|string|size:6',
            'akhiran_kontainer' => 'required|string|size:1',
            'nomor_seri_gabungan' => 'required|string|size:11',
            'ukuran' => 'required|string|in:10,20,40',
            'tipe_kontainer' => 'required|string',
            'vendor' => 'nullable|string|in:ZONA,DPE',
            'keterangan' => 'nullable|string',
            'tanggal_mulai_sewa' => 'nullable|date',
            'tanggal_selesai_sewa' => 'nullable|date',
            'status' => 'nullable|string|in:Tersedia,Disewa',
        ];

        // Add after_or_equal rule only if both dates are present
        if ($request->filled('tanggal_mulai_sewa') && $request->filled('tanggal_selesai_sewa')) {
            $rules['tanggal_selesai_sewa'] .= '|after_or_equal:tanggal_mulai_sewa';
        }

        // Custom error messages
        $messages = [
            'tanggal_selesai_sewa.after_or_equal' => 'Tanggal selesai sewa harus sama dengan atau setelah tanggal mulai sewa.',
        ];

        $request->validate($rules, $messages);

        // Validasi khusus: Cek duplikasi nomor_seri_kontainer + akhiran_kontainer
        $existingWithSameSerialAndSuffix = Kontainer::where('nomor_seri_kontainer', $request->nomor_seri_kontainer)
            ->where('akhiran_kontainer', $request->akhiran_kontainer)
            ->where('status', 'active')
            ->first();

        if ($existingWithSameSerialAndSuffix) {
            // Set kontainer yang sudah ada ke inactive
            $existingWithSameSerialAndSuffix->update(['status' => 'inactive']);

            $warningMessage = "Kontainer dengan nomor seri {$request->nomor_seri_kontainer} dan akhiran {$request->akhiran_kontainer} sudah ada. Kontainer lama telah dinonaktifkan.";
            session()->flash('warning', $warningMessage);
        }

        // Get request data
        $data = $request->all();

        // Set status default jika tidak ada
        if (!$request->filled('status')) {
            $data['status'] = 'active';
        }

        Kontainer::create($data);

        return redirect()->route('master.kontainer.index')
                         ->with('success', 'Kontainer berhasil ditambahkan!');
    }

    /**
     * Menampilkan formulir untuk mengedit kontainer.
     *
     * @param  \App\Models\Kontainer  $kontainer
     * @return \Illuminate\View\View
     */
    public function edit(Kontainer $kontainer)
    {
        return view('master-kontainer.edit', compact('kontainer'));
    }

    /**
     * Memperbarui data kontainer di database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Kontainer  $kontainer
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Kontainer $kontainer)
    {
        // Convert date format from dd/mmm/yyyy to yyyy-mm-dd for date fields
        $dateFields = ['tanggal_mulai_sewa', 'tanggal_selesai_sewa'];
        foreach ($dateFields as $field) {
            if ($request->filled($field)) {
                try {
                    $date = \DateTime::createFromFormat('d/M/Y', $request->input($field));
                    if ($date) {
                        $request->merge([$field => $date->format('Y-m-d')]);
                    }
                } catch (\Exception $e) {
                    // If conversion fails, keep original value for validation to catch
                }
            }
        }

        // Gabungkan awalan, nomor seri, dan akhiran untuk membuat nomor seri gabungan
        $nomor_seri_gabungan = $request->input('awalan_kontainer') .
                               $request->input('nomor_seri_kontainer') .
                               $request->input('akhiran_kontainer');

        $request->merge(['nomor_seri_gabungan' => $nomor_seri_gabungan]);

        // Custom validation rules
        $rules = [
            'awalan_kontainer' => 'required|string|size:4',
            'nomor_seri_kontainer' => 'required|string|size:6',
            'akhiran_kontainer' => 'required|string|size:1',
            'nomor_seri_gabungan' => 'required|string|size:11',
            'ukuran' => 'required|string|in:10,20,40',
            'tipe_kontainer' => 'required|string',
            'vendor' => 'nullable|string|in:ZONA,DPE',
            'keterangan' => 'nullable|string',
            'tanggal_mulai_sewa' => 'nullable|date',
            'tanggal_selesai_sewa' => 'nullable|date',
            'status' => 'nullable|string|in:Tersedia,Disewa',
        ];

        // Add after_or_equal rule only if both dates are present
        if ($request->filled('tanggal_mulai_sewa') && $request->filled('tanggal_selesai_sewa')) {
            $rules['tanggal_selesai_sewa'] .= '|after_or_equal:tanggal_mulai_sewa';
        }

        // Custom error messages
        $messages = [
            'tanggal_selesai_sewa.after_or_equal' => 'Tanggal selesai sewa harus sama dengan atau setelah tanggal mulai sewa.',
        ];

        $request->validate($rules, $messages);

        // Validasi khusus: Cek duplikasi nomor_seri_kontainer + akhiran_kontainer (selain diri sendiri)
        $existingWithSameSerialAndSuffix = Kontainer::where('nomor_seri_kontainer', $request->nomor_seri_kontainer)
            ->where('akhiran_kontainer', $request->akhiran_kontainer)
            ->where('status', 'active')
            ->where('id', '!=', $kontainer->id)
            ->first();

        if ($existingWithSameSerialAndSuffix) {
            // Set kontainer yang sudah ada ke inactive
            $existingWithSameSerialAndSuffix->update(['status' => 'inactive']);

            $warningMessage = "Kontainer lain dengan nomor seri {$request->nomor_seri_kontainer} dan akhiran {$request->akhiran_kontainer} sudah ada. Kontainer lama telah dinonaktifkan.";
            session()->flash('warning', $warningMessage);
        }

        $data = $request->all();

        $kontainer->update($data);

        return redirect()->route('master.kontainer.index')
                         ->with('success', 'Kontainer berhasil diperbarui!');
    }

    /**
     * Menghapus kontainer dari database.
     *
     * @param  \App\Models\Kontainer  $kontainer
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Kontainer $kontainer)
    {
        $kontainer->delete();

        return redirect()->route('master.kontainer.index')
                         ->with('success', 'Kontainer berhasil dihapus!');
    }
}
