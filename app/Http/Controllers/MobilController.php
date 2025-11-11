<?php

namespace App\Http\Controllers;

use App\Models\Mobil;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;

class MobilController extends Controller
{
    /**
     * Menampilkan daftar semua mobil.
     */
    public function index(Request $request)
    {
        $query = Mobil::with('karyawan');

        // Filter berdasarkan lokasi mobil untuk user cabang BTM
        $currentUser = auth()->user();
        if ($currentUser && $currentUser->karyawan && $currentUser->karyawan->cabang === 'BTM') {
            // Filter mobil berdasarkan lokasi BTM (Batam) untuk user BTM
            $query->where('lokasi', 'BTM');
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('kode_no', 'like', "%{$search}%")
                  ->orWhere('nomor_polisi', 'like', "%{$search}%")
                  ->orWhere('no_kir', 'like', "%{$search}%")
                  ->orWhere('merek', 'like', "%{$search}%")
                  ->orWhere('jenis', 'like', "%{$search}%")
                  ->orWhere('lokasi', 'like', "%{$search}%")
                  ->orWhere('no_mesin', 'like', "%{$search}%")
                  ->orWhere('nomor_rangka', 'like', "%{$search}%")
                  ->orWhere('bpkb', 'like', "%{$search}%")
                  ->orWhere('atas_nama', 'like', "%{$search}%")
                  ->orWhere('pemakai', 'like', "%{$search}%")
                  ->orWhereHas('karyawan', function($subQ) use ($search) {
                      $subQ->where('nama_lengkap', 'like', "%{$search}%")
                           ->orWhere('nik', 'like', "%{$search}%");
                  });
            });
        }

        // Get per_page from request, default to 15
        $perPage = $request->get('per_page', 15);
        $perPage = in_array($perPage, [15, 50, 100]) ? $perPage : 15;
        
        $mobils = $query->latest()->paginate($perPage);
        
        // Preserve all query parameters in pagination links
        $mobils->appends($request->query());

        return view('master-mobil.index', compact('mobils'));
    }

    /**
     * Menampilkan form untuk membuat mobil baru.
     */
    public function create()
    {
        $karyawansQuery = \App\Models\Karyawan::select('id', 'nama_lengkap', 'nama_panggilan', 'nik', 'divisi', 'cabang')
            ->where('divisi', 'Supir');
            
        // Filter karyawan berdasarkan cabang user yang login - HANYA untuk user cabang BTM
        $currentUser = auth()->user();
        if ($currentUser && $currentUser->karyawan && $currentUser->karyawan->cabang === 'BTM') {
            $karyawansQuery->where('cabang', 'BTM');
        }
        
        $karyawans = $karyawansQuery->orderBy('nama_panggilan')->get();
        
        // Generate kode nomor otomatis untuk ditampilkan di form
        $nextKodeNomor = $this->generateKodeNomor();
            
        return view('master-mobil.create', compact('karyawans', 'nextKodeNomor'));
    }

    /**
     * Menyimpan mobil baru ke dalam database.
     */
    public function store(Request $request)
    {
        // Generate kode nomor otomatis jika tidak diisi
        if (empty($request->kode_no)) {
            $request->merge(['kode_no' => $this->generateKodeNomor()]);
        }

        $validated = $request->validate([
            'kode_no' => 'nullable|string|max:50|unique:mobils,kode_no',
            'nomor_polisi' => 'nullable|string|max:20|unique:mobils,nomor_polisi',
            'lokasi' => 'nullable|string|max:100',
            'merek' => 'nullable|string|max:50',
            'jenis' => 'nullable|string|max:50',
            'tahun_pembuatan' => 'nullable|integer|min:1990|max:' . (date('Y') + 1),
            'bpkb' => 'nullable|string|max:50',
            'no_mesin' => 'nullable|string|max:50',
            'nomor_rangka' => 'nullable|string|max:50',
            'pajak_stnk' => 'nullable|date',
            'pajak_plat' => 'nullable|date',
            'no_kir' => 'nullable|string|max:50',
            'pajak_kir' => 'nullable|date',
            'atas_nama' => 'nullable|string|max:100',
            'pemakai' => 'nullable|string|max:100',
            'asuransi' => 'nullable|string|max:100',
            'jatuh_tempo_asuransi' => 'nullable|date',
            'warna_plat' => 'nullable|string|max:20',
            'catatan' => 'nullable|string|max:500',
            'karyawan_id' => 'nullable|exists:karyawans,id',
        ]);

        $mobil = Mobil::create($validated);

        // Update nomor polisi pada karyawan jika ada karyawan dan nomor polisi diisi
        if ($validated['karyawan_id'] && !empty($validated['nomor_polisi'])) {
            \App\Models\Karyawan::where('id', $validated['karyawan_id'])
                ->update(['plat' => $validated['nomor_polisi']]);
        }

        return redirect()->route('master.mobil.index')->with('success', 'Data mobil berhasil ditambahkan.');
    }

    /**
     * Generate kode nomor otomatis dengan format: AT1 + MMYY + XXXXX
     * Running number berjalan global tanpa reset per bulan/tahun
     */
    private function generateKodeNomor()
    {
        $prefix = 'AT1';
        $month = date('m'); // 2 digit bulan
        $year = date('y');  // 2 digit tahun
        
        // Format pattern untuk bulan dan tahun saat ini
        $pattern = $prefix . $month . $year;
        
        // Cari nomor terakhir dari SELURUH database (tidak hanya bulan ini)
        // untuk mendapatkan running number yang terus berjalan
        $lastRecord = Mobil::orderBy('kode_no', 'desc')->first();
        
        $runningNumber = 1;
        
        if ($lastRecord) {
            // Extract running number dari kode terakhir di database
            $lastKode = $lastRecord->kode_no;
            $lastRunning = substr($lastKode, -5); // Ambil 5 digit terakhir
            $runningNumber = intval($lastRunning) + 1;
        }
        
        // Format running number menjadi 5 digit dengan leading zeros
        $formattedRunning = str_pad($runningNumber, 5, '0', STR_PAD_LEFT);
        
        return $pattern . $formattedRunning;
    }

    /**
     * Menampilkan detail mobil.
     */
    public function show($id)
    {
        $mobil = Mobil::with('karyawan')->findOrFail($id);
        
        // Verifikasi akses berdasarkan lokasi mobil - HANYA untuk user cabang BTM
        $currentUser = auth()->user();
        if ($currentUser && $currentUser->karyawan && $currentUser->karyawan->cabang === 'BTM') {
            // Cek apakah mobil ini memiliki lokasi BTM (Batam)
            if ($mobil->lokasi !== 'BTM') {
                abort(404, 'Data mobil tidak ditemukan.');
            }
        }
        
        return view('master-mobil.show', compact('mobil'));
    }

    /**
     * Menampilkan form untuk mengedit mobil.
     */
    public function edit($id)
    {
        $mobil = Mobil::with('karyawan')->findOrFail($id);
        
        // Verifikasi akses berdasarkan lokasi mobil - HANYA untuk user cabang BTM
        $currentUser = auth()->user();
        if ($currentUser && $currentUser->karyawan && $currentUser->karyawan->cabang === 'BTM') {
            // Cek apakah mobil ini memiliki lokasi BTM (Batam)
            if ($mobil->lokasi !== 'BTM') {
                abort(404, 'Data mobil tidak ditemukan.');
            }
        }
        
        $karyawansQuery = \App\Models\Karyawan::select('id', 'nama_lengkap', 'nama_panggilan', 'nik', 'divisi', 'cabang')
            ->where('divisi', 'Supir');
            
        // Filter karyawan berdasarkan cabang user yang login - HANYA untuk user cabang BTM
        if ($currentUser && $currentUser->karyawan && $currentUser->karyawan->cabang === 'BTM') {
            $karyawansQuery->where('cabang', 'BTM');
        }
        
        $karyawans = $karyawansQuery->orderBy('nama_panggilan')->get();
            
        return view('master-mobil.edit', compact('mobil', 'karyawans'));
    }

    /**
     * Memperbarui data mobil di database.
     */
    public function update(Request $request, $id)
    {
        $mobil = Mobil::findOrFail($id);
        
        // Jika kode_no kosong pada form edit, generate otomatis
        if (empty($request->kode_no)) {
            $request->merge(['kode_no' => $this->generateKodeNomor()]);
        }
        
        $validated = $request->validate([
            'kode_no' => 'required|string|max:50|unique:mobils,kode_no,' . $id,
            'nomor_polisi' => 'nullable|string|max:20|unique:mobils,nomor_polisi,' . $id,
            'lokasi' => 'nullable|string|max:100',
            'merek' => 'nullable|string|max:50',
            'jenis' => 'nullable|string|max:50',
            'tahun_pembuatan' => 'nullable|integer|min:1990|max:' . (date('Y') + 1),
            'bpkb' => 'nullable|string|max:50',
            'no_mesin' => 'nullable|string|max:50',
            'nomor_rangka' => 'nullable|string|max:50',
            'pajak_stnk' => 'nullable|date',
            'pajak_plat' => 'nullable|date',
            'no_kir' => 'nullable|string|max:50',
            'pajak_kir' => 'nullable|date',
            'atas_nama' => 'nullable|string|max:100',
            'pemakai' => 'nullable|string|max:100',
            'asuransi' => 'nullable|string|max:100',
            'jatuh_tempo_asuransi' => 'nullable|date',
            'warna_plat' => 'nullable|string|max:20',
            'catatan' => 'nullable|string|max:500',
            'karyawan_id' => 'nullable|exists:karyawans,id',
        ]);

        // Jika karyawan lama ada, hapus nomor plat dari karyawan lama
        if ($mobil->karyawan_id && $mobil->karyawan_id != $validated['karyawan_id']) {
            \App\Models\Karyawan::where('id', $mobil->karyawan_id)
                ->update(['plat' => null]);
        }

        // Update data mobil
        $mobil->update($validated);

        // Update nomor polisi pada karyawan baru jika ada karyawan dan nomor polisi diisi
        if ($validated['karyawan_id'] && !empty($validated['nomor_polisi'])) {
            \App\Models\Karyawan::where('id', $validated['karyawan_id'])
                ->update(['plat' => $validated['nomor_polisi']]);
        }

        return redirect()->route('master.mobil.index')->with('success', 'Data mobil berhasil diperbarui.');
    }

    /**
     * Menghapus mobil dari database.
     */
    public function destroy(Mobil $mobil)
    {
        // Verifikasi akses berdasarkan cabang
        $currentUser = auth()->user();
        if ($currentUser && $currentUser->karyawan && $currentUser->karyawan->cabang) {
            $userCabang = $currentUser->karyawan->cabang;
            
            // Load relationship jika belum ter-load
            if (!$mobil->relationLoaded('karyawan')) {
                $mobil->load('karyawan');
            }
            
            // Cek apakah mobil ini memiliki karyawan dengan cabang yang sama
            if (!$mobil->karyawan || $mobil->karyawan->cabang !== $userCabang) {
                abort(404, 'Data mobil tidak ditemukan.');
            }
        }
        
        // Hapus nomor plat dari karyawan jika ada
        if ($mobil->karyawan_id) {
            \App\Models\Karyawan::where('id', $mobil->karyawan_id)
                ->update(['plat' => null]);
        }

        $mobil->delete();

        return redirect()->route('master.mobil.index')
                         ->with('success', 'Mobil berhasil dihapus.');
    }

    /**
     * Import data mobil dari Excel.
     */
    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls|max:10240', // Max 10MB
        ]);

        $file = $request->file('excel_file');
        
        try {
            $spreadsheet = IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $data = $worksheet->toArray();
            
            // Remove header row
            $header = array_shift($data);
            
            $imported = 0;
            $skipped = 0;
            $errors = [];
            $warnings = [];

            foreach ($data as $index => $row) {
            $rowNumber = $index + 2; // +2 because index starts at 0 and we removed header
            
            // Skip empty rows
            if (empty(array_filter($row))) {
                continue;
            }

            try {
                // Map CSV columns to database fields
                $kodeAktiva = trim($row[0] ?? '');
                $nomorPolisi = trim($row[1] ?? '');
                $nik = trim($row[2] ?? '');
                $namaLengkap = trim($row[3] ?? '');
                $lokasi = trim($row[4] ?? '');
                $merek = trim($row[5] ?? '');
                $jenis = trim($row[6] ?? '');
                $tahunPembuatan = trim($row[7] ?? '');
                $bpkb = trim($row[8] ?? '');
                $noMesin = trim($row[9] ?? '');
                $noRangka = trim($row[10] ?? '');
                $pajakStnk = trim($row[11] ?? '');
                $pajakPlat = trim($row[12] ?? '');
                $noKir = trim($row[13] ?? '');
                $pajakKir = trim($row[14] ?? '');
                $atasNama = trim($row[15] ?? '');
                $pemakai = trim($row[16] ?? '');
                $asuransi = trim($row[17] ?? '');
                $jteAsuransi = trim($row[18] ?? '');
                $warnaPlat = trim($row[19] ?? '');
                $catatan = trim($row[20] ?? '');

                // Check if nomor polisi already exists (hanya jika nomor polisi diisi)
                if (!empty($nomorPolisi) && Mobil::where('nomor_polisi', $nomorPolisi)->exists()) {
                    $warnings[] = "Baris $rowNumber: Nomor polisi $nomorPolisi sudah ada, dilewati.";
                    $skipped++;
                    continue;
                }

                // Find karyawan by NIK if provided
                $karyawanId = null;
                if (!empty($nik)) {
                    $karyawan = \App\Models\Karyawan::where('nik', $nik)->first();
                    if ($karyawan) {
                        $karyawanId = $karyawan->id;
                    } else {
                        $warnings[] = "Baris $rowNumber: NIK $nik tidak ditemukan di database karyawan.";
                    }
                }

                // Parse dates
                $pajakStnkDate = $this->parseDate($pajakStnk);
                $pajakPlatDate = $this->parseDate($pajakPlat);
                $pajakKirDate = $this->parseDate($pajakKir);
                $jteAsuransiDate = $this->parseDate($jteAsuransi);

                // Create mobil record
                Mobil::create([
                    'kode_no' => $kodeAktiva ?: null,
                    'nomor_polisi' => $nomorPolisi,
                    'lokasi' => $lokasi ?: null,
                    'merek' => $merek ?: null,
                    'jenis' => $jenis ?: null,
                    'tahun_pembuatan' => is_numeric($tahunPembuatan) ? (int)$tahunPembuatan : null,
                    'bpkb' => $bpkb ?: null,
                    'no_mesin' => $noMesin ?: null,
                    'nomor_rangka' => $noRangka ?: null,
                    'pajak_stnk' => $pajakStnkDate,
                    'pajak_plat' => $pajakPlatDate,
                    'no_kir' => $noKir ?: null,
                    'pajak_kir' => $pajakKirDate,
                    'atas_nama' => $atasNama ?: null,
                    'pemakai' => $pemakai ?: null,
                    'asuransi' => $asuransi ?: null,
                    'jatuh_tempo_asuransi' => $jteAsuransiDate,
                    'warna_plat' => $warnaPlat ?: null,
                    'catatan' => $catatan ?: null,
                    'karyawan_id' => $karyawanId,
                ]);

                $imported++;

            } catch (\Exception $e) {
                $errors[] = "Baris $rowNumber: " . $e->getMessage();
                $skipped++;
            }
        }

            $message = "Import selesai. $imported data berhasil diimport, $skipped data dilewati.";

            return redirect()->route('master.mobil.index')
                             ->with('success', $message)
                             ->with('import_errors', count($errors) > 0 ? $errors : null)
                             ->with('import_warnings', count($warnings) > 0 ? $warnings : null);
                             
        } catch (\Exception $e) {
            return redirect()->route('master.mobil.index')
                             ->with('error', 'Error saat membaca file Excel: ' . $e->getMessage());
        }
    }

    /**
     * Parse tanggal dari berbagai format.
     */
    private function parseDate($dateString)
    {
        if (empty($dateString)) {
            return null;
        }

        // Try different date formats
        $formats = [
            'd M y',  // 24 Sep 26
            'd M Y',  // 24 Sep 2026
            'd/m/Y',  // 24/09/2026
            'Y-m-d',  // 2026-09-24
            'd-m-Y',  // 24-09-2026
        ];

        foreach ($formats as $format) {
            $date = \DateTime::createFromFormat($format, $dateString);
            if ($date !== false) {
                // Convert 2-digit year to 4-digit
                if (strlen($dateString) <= 9 && strpos($format, 'y') !== false) {
                    $year = $date->format('y');
                    if ($year < 50) {
                        $date->setDate(2000 + (int)$year, $date->format('m'), $date->format('d'));
                    } else {
                        $date->setDate(1900 + (int)$year, $date->format('m'), $date->format('d'));
                    }
                }
                return $date->format('Y-m-d');
            }
        }

        return null;
    }
}
