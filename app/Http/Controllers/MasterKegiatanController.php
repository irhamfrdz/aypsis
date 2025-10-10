<?php

namespace App\Http\Controllers;

use App\Models\MasterKegiatan;
use Illuminate\Http\Request;

class MasterKegiatanController extends Controller
{
    public function index()
    {
        $items = MasterKegiatan::orderBy('kode_kegiatan')->paginate(15);
        return view('master-kegiatan.index', compact('items'));
    }

    public function create()
    {
        return view('master-kegiatan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_kegiatan' => 'required|unique:master_kegiatans,kode_kegiatan',
            'nama_kegiatan' => 'required',
            'type' => 'nullable|string|max:50',
            'status' => 'required|in:aktif,nonaktif',
        ]);
        MasterKegiatan::create($request->only(['kode_kegiatan','nama_kegiatan','type','keterangan','status']));
        return redirect()->route('master.kegiatan.index')->with('success', 'Master kegiatan dibuat');
    }

    public function edit($id)
    {
        $item = MasterKegiatan::findOrFail($id);
        return view('master-kegiatan.edit', compact('item'));
    }

    public function update(Request $request, $id)
    {
        $item = MasterKegiatan::findOrFail($id);
        $request->validate([
            'kode_kegiatan' => 'required|unique:master_kegiatans,kode_kegiatan,' . $id,
            'nama_kegiatan' => 'required',
            'type' => 'nullable|string|max:50',
            'status' => 'required|in:aktif,nonaktif',
        ]);
        $item->update($request->only(['kode_kegiatan','nama_kegiatan','type','keterangan','status']));
        return redirect()->route('master.kegiatan.index')->with('success', 'Master kegiatan diupdate');
    }

    public function destroy($id)
    {
        $item = MasterKegiatan::findOrFail($id);
        $item->delete();
        return redirect()->route('master.kegiatan.index')->with('success', 'Master kegiatan dihapus');
    }

    /**
     * Download a CSV template for Master Kegiatan import
     */
    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="master_kegiatan_template.csv"',
        ];

        $callback = function() {
            $handle = fopen('php://output', 'w');
            // Write UTF-8 BOM so Excel recognizes encoding
            fwrite($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
            // Use semicolon as delimiter for Excel regional compatibility
            $delimiter = ';';
            // template columns: kode_kegiatan;nama_kegiatan;type;keterangan;status
            fputcsv($handle, ['kode_kegiatan', 'nama_kegiatan', 'type', 'keterangan', 'status'], $delimiter);
            // example row
            fputcsv($handle, ['KGT001', 'Pengiriman Lokal', 'Operasional', 'Contoh keterangan', 'aktif'], $delimiter);
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Import Master Kegiatan from uploaded CSV
     */
    public function importCsv(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        $file = $request->file('csv_file');
        $path = $file->getRealPath();

        $handle = fopen($path, 'r');
        if ($handle === false) {
            return redirect()->back()->with('error', 'Tidak dapat membaca file CSV');
        }

        // Detect delimiter (semicolon or comma)
        $firstLine = fgets($handle);
        rewind($handle);
        $delimiter = (substr_count($firstLine, ';') > substr_count($firstLine, ',')) ? ';' : ',';

        // Read header with detected delimiter
        $header = fgetcsv($handle, 0, $delimiter);

        // Remove BOM from first header if present
        if (!empty($header[0])) {
            $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', $header[0]);
            $header[0] = preg_replace('/^[\x{FEFF}]/u', '', $header[0]);
        }

        $expected = ['kode_kegiatan', 'nama_kegiatan', 'type', 'keterangan', 'status'];
        // normalize header lower-case and trim
        $norm = array_map(function($v){ return strtolower(trim($v)); }, (array)$header);

        // Check if header contains required columns (more flexible)
        $hasRequiredColumns = in_array('kode_kegiatan', $norm) && in_array('nama_kegiatan', $norm);

        if (!$hasRequiredColumns) {
            fclose($handle);
            return redirect()->back()->with('error', 'Format CSV tidak sesuai. Minimal harus ada kolom: kode_kegiatan dan nama_kegiatan.');
        }

        $created = 0;
        $errors = [];
        $line = 1;

        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            $line++;
            $row = array_map('trim', $row);

            // skip empty rows
            if (count($row) < 2 || (empty($row[0]) && empty($row[1]))) continue;

            // Map row to associative array
            $data = [];
            foreach ($norm as $index => $headerName) {
                $data[$headerName] = $row[$index] ?? '';
            }

            // validate minimal fields
            if (empty($data['kode_kegiatan']) || empty($data['nama_kegiatan'])) {
                $errors[] = "Baris {$line}: kode_kegiatan dan nama_kegiatan wajib.";
                continue;
            }

            // skip if kode already exists
            if (MasterKegiatan::where('kode_kegiatan', $data['kode_kegiatan'])->exists()) {
                $errors[] = "Baris {$line}: kode_kegiatan {$data['kode_kegiatan']} sudah ada, dilewati.";
                continue;
            }

            // ensure status valid
            $status = strtolower($data['status'] ?? '');
            if (!in_array($status, ['aktif','nonaktif'])) {
                $status = 'aktif'; // default aktif instead of nonaktif
            }

            try {
                MasterKegiatan::create([
                    'kode_kegiatan' => $data['kode_kegiatan'],
                    'nama_kegiatan' => $data['nama_kegiatan'],
                    'type' => $data['type'] ?? null,
                    'keterangan' => $data['keterangan'] ?? null,
                    'status' => $status,
                ]);
                $created++;
            } catch (\Exception $e) {
                $errors[] = "Baris {$line}: " . $e->getMessage();
            }
        }

        fclose($handle);

        $msg = "Import selesai. Berhasil dibuat: {$created} data.";
        if (!empty($errors)) {
            session()->flash('import_errors', $errors);
        }

        return redirect()->route('master.kegiatan.index')->with('success', $msg);
    }

    /**
     * Export Master Kegiatan data to CSV
     */
    public function exportCsv()
    {
        $items = MasterKegiatan::orderBy('kode_kegiatan')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="master_kegiatan_export_' . date('Y-m-d_H-i-s') . '.csv"',
        ];

        $callback = function() use ($items) {
            $handle = fopen('php://output', 'w');
            // Write UTF-8 BOM so Excel recognizes encoding
            fwrite($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
            // Use semicolon as delimiter for Excel regional compatibility
            $delimiter = ';';
            // Write header
            fputcsv($handle, ['kode_kegiatan', 'nama_kegiatan', 'type', 'keterangan', 'status'], $delimiter);
            // Write data rows
            foreach ($items as $item) {
                fputcsv($handle, [
                    $item->kode_kegiatan,
                    $item->nama_kegiatan,
                    $item->type,
                    $item->keterangan,
                    $item->status,
                ], $delimiter);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
