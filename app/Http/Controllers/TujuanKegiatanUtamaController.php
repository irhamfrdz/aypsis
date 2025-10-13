<?php
namespace App\Http\Controllers;

use App\Models\TujuanKegiatanUtama;
use Illuminate\Http\Request;

class TujuanKegiatanUtamaController extends Controller
{
    /**
     * Tampilkan daftar tujuan kegiatan utama.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $tujuanKegiatanUtamas = TujuanKegiatanUtama::paginate(10);
        return view('master-tujuan-kegiatan-utama.index', compact('tujuanKegiatanUtamas'));
    }

    /**
     * Tampilkan form untuk membuat tujuan kegiatan utama baru.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('master-tujuan-kegiatan-utama.create');
    }

    /**
     * Simpan tujuan kegiatan utama baru ke database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode' => 'nullable|string|max:255',
            'cabang' => 'nullable|string|max:255',
            'wilayah' => 'nullable|string|max:255',
            'dari' => 'nullable|string|max:255',
            'ke' => 'nullable|string|max:255',
            'uang_jalan_20ft' => 'nullable|numeric|min:0',
            'uang_jalan_40ft' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string',
            'liter' => 'nullable|numeric|min:0',
            'jarak_dari_penjaringan_km' => 'nullable|numeric|min:0',
            'mel_20ft' => 'nullable|numeric|min:0',
            'mel_40ft' => 'nullable|numeric|min:0',
            'ongkos_truk_20ft' => 'nullable|numeric|min:0',
            'ongkos_truk_40ft' => 'nullable|numeric|min:0',
            'antar_lokasi_20ft' => 'nullable|numeric|min:0',
            'antar_lokasi_40ft' => 'nullable|numeric|min:0',
            'aktif' => 'boolean',
        ]);

        TujuanKegiatanUtama::create($validated);

                return redirect()->route('master.tujuan-kegiatan-utama.index')->with('success', 'Data Transportasi berhasil ditambahkan!');
    }

    /**
     * Tampilkan detail tujuan kegiatan utama.
     *
     * @param  \App\Models\TujuanKegiatanUtama  $tujuanKegiatanUtama
     * @return \Illuminate\View\View
     */
    public function show(TujuanKegiatanUtama $tujuanKegiatanUtama)
    {
        return view('master-tujuan-kegiatan-utama.show', compact('tujuanKegiatanUtama'));
    }

    /**
     * Tampilkan form untuk mengedit tujuan kegiatan utama yang ada.
     *
     * @param  \App\Models\TujuanKegiatanUtama  $tujuanKegiatanUtama
     * @return \Illuminate\View\View
     */
    public function edit(TujuanKegiatanUtama $tujuanKegiatanUtama)
    {
        return view('master-tujuan-kegiatan-utama.edit', compact('tujuanKegiatanUtama'));
    }

    /**
     * Perbarui tujuan kegiatan utama yang ada di database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TujuanKegiatanUtama  $tujuanKegiatanUtama
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, TujuanKegiatanUtama $tujuanKegiatanUtama)
    {
        $validated = $request->validate([
            'kode' => 'nullable|string|max:255',
            'cabang' => 'nullable|string|max:255',
            'wilayah' => 'nullable|string|max:255',
            'dari' => 'nullable|string|max:255',
            'ke' => 'nullable|string|max:255',
            'uang_jalan_20ft' => 'nullable|numeric|min:0',
            'uang_jalan_40ft' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string',
            'liter' => 'nullable|numeric|min:0',
            'jarak_dari_penjaringan_km' => 'nullable|numeric|min:0',
            'mel_20ft' => 'nullable|numeric|min:0',
            'mel_40ft' => 'nullable|numeric|min:0',
            'ongkos_truk_20ft' => 'nullable|numeric|min:0',
            'ongkos_truk_40ft' => 'nullable|numeric|min:0',
            'antar_lokasi_20ft' => 'nullable|numeric|min:0',
            'antar_lokasi_40ft' => 'nullable|numeric|min:0',
            'aktif' => 'boolean',
        ]);

        $tujuanKegiatanUtama->update($validated);

        return redirect()->route('master.tujuan-kegiatan-utama.index')->with('success', 'Data Transportasi berhasil diperbarui!');
    }

    /**
     * Hapus tujuan kegiatan utama dari database.
     *
     * @param  \App\Models\TujuanKegiatanUtama  $tujuanKegiatanUtama
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(TujuanKegiatanUtama $tujuanKegiatanUtama)
    {
        $tujuanKegiatanUtama->delete();

        return redirect()->route('master.tujuan-kegiatan-utama.index')->with('success', 'Data Transportasi berhasil diperbarui!');
    }

    /**
     * Export data tujuan kegiatan utama ke CSV.
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function export()
    {
        $tujuanKegiatanUtamas = TujuanKegiatanUtama::all();

        $response = new \Symfony\Component\HttpFoundation\StreamedResponse(function() use ($tujuanKegiatanUtamas) {
            $handle = fopen('php://output', 'w');
            
            // Header CSV dengan semua kolom transportasi
            fputcsv($handle, [
                'ID',
                'Kode',
                'Cabang', 
                'Wilayah',
                'Dari',
                'Ke',
                'Uang Jalan 20ft',
                'Uang Jalan 40ft',
                'Keterangan',
                'Liter',
                'Jarak dari Penjaringan (km)',
                'MEL 20ft',
                'MEL 40ft',
                'Ongkos Truk 20ft',
                'Ongkos Truk 40ft',
                'Antar Lokasi 20ft',
                'Antar Lokasi 40ft',
                'Status',
                'Dibuat',
                'Diupdate'
            ]);
            
            // Data transportasi
            foreach ($tujuanKegiatanUtamas as $item) {
                fputcsv($handle, [
                    $item->id,
                    $item->kode,
                    $item->cabang,
                    $item->wilayah,
                    $item->dari,
                    $item->ke,
                    $item->uang_jalan_20ft,
                    $item->uang_jalan_40ft,
                    $item->keterangan,
                    $item->liter,
                    $item->jarak_dari_penjaringan_km,
                    $item->mel_20ft,
                    $item->mel_40ft,
                    $item->ongkos_truk_20ft,
                    $item->ongkos_truk_40ft,
                    $item->antar_lokasi_20ft,
                    $item->antar_lokasi_40ft,
                    $item->aktif ? 'Aktif' : 'Tidak Aktif',
                    $item->created_at->format('Y-m-d H:i:s'),
                    $item->updated_at->format('Y-m-d H:i:s')
                ]);
            }
            
            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="transportasi_data_' . date('Y-m-d_H-i-s') . '.csv"');

        return $response;
    }

    /**
     * Download template CSV untuk import data transportasi.
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_transportasi_' . date('Y-m-d_H-i-s') . '.csv"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            
            // Header CSV dengan semua kolom yang diperlukan
            fputcsv($file, [
                'kode',
                'cabang', 
                'wilayah',
                'dari',
                'ke',
                'uang_jalan_20ft',
                'uang_jalan_40ft',
                'keterangan',
                'liter',
                'jarak_dari_penjaringan_km',
                'mel_20ft',
                'mel_40ft',
                'ongkos_truk_20ft',
                'ongkos_truk_40ft',
                'antar_lokasi_20ft',
                'antar_lokasi_40ft',
                'aktif'
            ]);
            
            // Contoh data untuk panduan user
            fputcsv($file, [
                'JKT001',
                'Jakarta',
                'DKI Jakarta',
                'Jakarta',
                'Surabaya',
                '2500000',
                '3500000',
                'Rute reguler Jakarta-Surabaya',
                '150.50',
                '850.75',
                '500000',
                '750000',
                '1500000',
                '2000000',
                '200000',
                '300000',
                '1'
            ]);
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Import data transportasi dari CSV.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:10240', // Max 10MB
        ]);

        $file = $request->file('csv_file');
        $path = $file->getRealPath();
        
        $data = [];
        $errors = [];
        $successCount = 0;
        $errorCount = 0;

        if (($handle = fopen($path, 'r')) !== FALSE) {
            $headers = fgetcsv($handle, 0, ';'); // Menggunakan semicolon sebagai delimiter
            if (!$headers) {
                $headers = fgetcsv($handle, 0, ','); // Fallback ke comma jika semicolon gagal
                fseek($handle, 0); // Reset file pointer
                $headers = fgetcsv($handle, 0, ',');
                $delimiter = ',';
            } else {
                $delimiter = ';';
            }
            
            $rowNumber = 1;

            while (($row = fgetcsv($handle, 0, $delimiter)) !== FALSE) {
                $rowNumber++;
                
                // Skip empty rows
                if (count(array_filter($row)) === 0) {
                    continue;
                }

                try {
                    // Helper function untuk membersihkan dan mengkonversi angka
                    $cleanNumber = function($value) {
                        if (empty($value) || trim($value) === '') {
                            return null;
                        }
                        // Remove spaces, dots as thousand separators, and convert to float
                        $cleaned = str_replace([' ', '.'], '', trim($value));
                        $cleaned = str_replace(',', '.', $cleaned); // Convert comma decimal to dot
                        return is_numeric($cleaned) ? (float)$cleaned : null;
                    };

                    // Mapping data dengan handling untuk format file user yang berbeda
                    if ($delimiter === ';') {
                        // Format file user (semicolon separated)
                        $rowData = [
                            'kode' => trim($row[0] ?? '') ?: null,
                            'cabang' => trim($row[1] ?? '') ?: null,
                            'wilayah' => trim($row[2] ?? '') ?: null,
                            'dari' => trim($row[3] ?? '') ?: null,
                            'ke' => trim($row[4] ?? '') ?: null,
                            'uang_jalan_20ft' => $cleanNumber($row[5] ?? null),
                            'uang_jalan_40ft' => $cleanNumber($row[6] ?? null),
                            'keterangan' => trim($row[7] ?? '') ?: null,
                            'liter' => $cleanNumber($row[8] ?? null),
                            'jarak_dari_penjaringan_km' => $cleanNumber($row[9] ?? null),
                            'mel_20ft' => $cleanNumber($row[10] ?? null),
                            'mel_40ft' => $cleanNumber($row[11] ?? null),
                            'ongkos_truk_20ft' => $cleanNumber($row[12] ?? null),
                            'ongkos_truk_40ft' => $cleanNumber($row[14] ?? null), // Index 14 untuk kolom Antar Lokasi 40ft sebagai fallback
                            'antar_lokasi_20ft' => $cleanNumber($row[13] ?? null),
                            'antar_lokasi_40ft' => $cleanNumber($row[14] ?? null),
                            'aktif' => true, // Default aktif untuk file user
                        ];
                    } else {
                        // Format template standard (comma separated)
                        $rowData = [
                            'kode' => $row[0] ?? null,
                            'cabang' => $row[1] ?? null,
                            'wilayah' => $row[2] ?? null,
                            'dari' => $row[3] ?? null,
                            'ke' => $row[4] ?? null,
                            'uang_jalan_20ft' => is_numeric($row[5] ?? null) ? (float)$row[5] : null,
                            'uang_jalan_40ft' => is_numeric($row[6] ?? null) ? (float)$row[6] : null,
                            'keterangan' => $row[7] ?? null,
                            'liter' => is_numeric($row[8] ?? null) ? (float)$row[8] : null,
                            'jarak_dari_penjaringan_km' => is_numeric($row[9] ?? null) ? (float)$row[9] : null,
                            'mel_20ft' => is_numeric($row[10] ?? null) ? (float)$row[10] : null,
                            'mel_40ft' => is_numeric($row[11] ?? null) ? (float)$row[11] : null,
                            'ongkos_truk_20ft' => is_numeric($row[12] ?? null) ? (float)$row[12] : null,
                            'ongkos_truk_40ft' => is_numeric($row[13] ?? null) ? (float)$row[13] : null,
                            'antar_lokasi_20ft' => is_numeric($row[14] ?? null) ? (float)$row[14] : null,
                            'antar_lokasi_40ft' => is_numeric($row[15] ?? null) ? (float)$row[15] : null,
                            'aktif' => isset($row[16]) ? (bool)$row[16] : true,
                        ];
                    }

                    // Validasi data minimal
                    if (empty($rowData['dari']) || empty($rowData['ke'])) {
                        $errors[] = "Baris {$rowNumber}: Kolom 'dari' dan 'ke' wajib diisi";
                        $errorCount++;
                        continue;
                    }

                    // Simpan data
                    TujuanKegiatanUtama::create($rowData);
                    $successCount++;

                } catch (\Exception $e) {
                    $errors[] = "Baris {$rowNumber}: " . $e->getMessage();
                    $errorCount++;
                }
            }
            
            fclose($handle);
        }

        // Prepare flash message
        $message = "Import selesai. {$successCount} data berhasil diimport.";
        if ($errorCount > 0) {
            $message .= " {$errorCount} data gagal diimport.";
        }

        if (!empty($errors)) {
            return redirect()->back()
                ->with('success', $message)
                ->with('errors', $errors);
        }

        return redirect()->route('master.tujuan-kegiatan-utama.index')
            ->with('success', $message);
    }

    /**
     * Tampilkan halaman form import.
     *
     * @return \Illuminate\View\View
     */
    public function showImportForm()
    {
        return view('master-tujuan-kegiatan-utama.import');
    }

    /**
     * Print data tujuan kegiatan utama.
     *
     * @return \Illuminate\View\View
     */
    public function print()
    {
        $tujuanKegiatanUtamas = TujuanKegiatanUtama::all();
        return view('master-tujuan-kegiatan-utama.print', compact('tujuanKegiatanUtamas'));
    }
}