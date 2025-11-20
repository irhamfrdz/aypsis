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

    /**
     * Import data tanggal sewa dan status kontainer dari CSV
     * Format CSV: nomor_kontainer;tanggal_mulai_sewa;tanggal_selesai_sewa;status
     * Hanya update data yang sudah ada, tidak create kontainer baru
     */
    public function importTanggalSewa(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:csv,txt|max:5120', // max 5MB
        ]);

        try {
            $file = $request->file('excel_file');
            $fileContent = file_get_contents($file->getRealPath());
            
            // Handle UTF-8 BOM
            $fileContent = str_replace("\xEF\xBB\xBF", '', $fileContent);
            
            // Split into lines
            $lines = array_filter(array_map('trim', explode("\n", $fileContent)));
            
            if (count($lines) < 2) {
                return redirect()->route('master.kontainer.index')
                    ->with('error', 'File CSV kosong atau tidak valid. Minimal harus ada header dan 1 baris data.');
            }

            // Skip header
            array_shift($lines);
            
            $updated = 0;
            $notFound = [];
            $errors = [];
            $skipped = 0;

            foreach ($lines as $lineNumber => $line) {
                $actualLine = $lineNumber + 2; // +2 karena array index 0 + skip header
                
                // Parse CSV with semicolon delimiter
                $data = str_getcsv($line, ';');
                
                // Skip empty lines
                if (empty(array_filter($data))) {
                    $skipped++;
                    continue;
                }

                // Minimal harus ada nomor kontainer (kolom 1)
                if (!isset($data[0]) || empty(trim($data[0]))) {
                    $errors[] = "Baris {$actualLine}: Nomor kontainer tidak boleh kosong";
                    continue;
                }

                $nomorKontainer = strtoupper(trim($data[0]));
                
                // Cari kontainer berdasarkan nomor gabungan
                $kontainer = Kontainer::where('nomor_seri_gabungan', $nomorKontainer)->first();
                
                if (!$kontainer) {
                    $notFound[] = "Baris {$actualLine}: Kontainer '{$nomorKontainer}' tidak ditemukan";
                    continue;
                }

                // Prepare update data
                $updateData = [];

                // Parse tanggal mulai sewa (kolom 2)
                if (isset($data[1]) && !empty(trim($data[1]))) {
                    try {
                        $tanggalMulai = trim($data[1]);
                        $date = \DateTime::createFromFormat('d/M/Y', $tanggalMulai);
                        if ($date) {
                            $updateData['tanggal_mulai_sewa'] = $date->format('Y-m-d');
                        } else {
                            $errors[] = "Baris {$actualLine}: Format tanggal mulai sewa tidak valid (gunakan format dd/Mmm/yyyy, contoh: 01/Jan/2024)";
                            continue;
                        }
                    } catch (\Exception $e) {
                        $errors[] = "Baris {$actualLine}: Error parsing tanggal mulai sewa - " . $e->getMessage();
                        continue;
                    }
                }

                // Parse tanggal selesai sewa (kolom 3)
                if (isset($data[2]) && !empty(trim($data[2]))) {
                    try {
                        $tanggalSelesai = trim($data[2]);
                        $date = \DateTime::createFromFormat('d/M/Y', $tanggalSelesai);
                        if ($date) {
                            $updateData['tanggal_selesai_sewa'] = $date->format('Y-m-d');
                        } else {
                            $errors[] = "Baris {$actualLine}: Format tanggal selesai sewa tidak valid (gunakan format dd/Mmm/yyyy, contoh: 31/Des/2024)";
                            continue;
                        }
                    } catch (\Exception $e) {
                        $errors[] = "Baris {$actualLine}: Error parsing tanggal selesai sewa - " . $e->getMessage();
                        continue;
                    }
                }

                // Parse status (kolom 4)
                if (isset($data[3]) && !empty(trim($data[3]))) {
                    $status = trim($data[3]);
                    // Accept both "Tersedia"/"Tidak Tersedia" format
                    if (in_array($status, ['Tersedia', 'Tidak Tersedia'])) {
                        $updateData['status'] = $status;
                    } else {
                        $errors[] = "Baris {$actualLine}: Status harus 'Tersedia' atau 'Tidak Tersedia'";
                        continue;
                    }
                }

                // Update jika ada data yang diubah
                if (!empty($updateData)) {
                    $kontainer->update($updateData);
                    $updated++;
                }
            }

            // Build success message
            $message = "Import selesai: {$updated} kontainer berhasil diupdate";
            
            if ($skipped > 0) {
                $message .= ", {$skipped} baris kosong dilewati";
            }

            // Add warnings for not found containers
            if (!empty($notFound)) {
                $notFoundMessage = implode('; ', array_slice($notFound, 0, 5));
                if (count($notFound) > 5) {
                    $notFoundMessage .= ' dan ' . (count($notFound) - 5) . ' lainnya';
                }
                session()->flash('warning', 'Kontainer tidak ditemukan: ' . $notFoundMessage);
            }

            // Add errors if any
            if (!empty($errors)) {
                $errorMessage = implode('; ', array_slice($errors, 0, 5));
                if (count($errors) > 5) {
                    $errorMessage .= ' dan ' . (count($errors) - 5) . ' error lainnya';
                }
                session()->flash('error', 'Error: ' . $errorMessage);
            }

            return redirect()->route('master.kontainer.index')->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->route('master.kontainer.index')
                ->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }
    }

    /**
     * Download template CSV untuk import tanggal sewa
     */
    public function downloadTemplateTanggalSewa()
    {
        $filename = 'template_tanggal_sewa_kontainer.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Header
            fputcsv($file, [
                'nomor_kontainer',
                'tanggal_mulai_sewa',
                'tanggal_selesai_sewa',
                'status'
            ], ';');
            
            // Example data
            fputcsv($file, [
                'ALLU2202097',
                '01/Jan/2024',
                '31/Des/2024',
                'Tersedia'
            ], ';');
            
            fputcsv($file, [
                'AMFU3153692',
                '15/Feb/2024',
                '',
                'Tidak Tersedia'
            ], ';');
            
            fputcsv($file, [
                'DNAU2622206',
                '',
                '',
                'Tersedia'
            ], ';');
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
