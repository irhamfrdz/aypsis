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
            'status' => 'required|in:aktif,nonaktif',
        ]);
        MasterKegiatan::create($request->only(['kode_kegiatan','nama_kegiatan','keterangan','status']));
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
            'status' => 'required|in:aktif,nonaktif',
        ]);
        $item->update($request->only(['kode_kegiatan','nama_kegiatan','keterangan','status']));
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
            // template columns: kode_kegiatan;nama_kegiatan;keterangan;status
            fputcsv($handle, ['kode_kegiatan', 'nama_kegiatan', 'keterangan', 'status'], $delimiter);
            // example row
            fputcsv($handle, ['KGT001', 'Pengiriman Lokal', 'Contoh keterangan', 'aktif'], $delimiter);
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

        $header = fgetcsv($handle);
        $expected = ['kode_kegiatan', 'nama_kegiatan', 'keterangan', 'status'];
        // normalize header lower-case
        $norm = array_map(function($v){ return strtolower(trim($v)); }, (array)$header);
        if ($norm !== $expected) {
            fclose($handle);
            return redirect()->back()->with('error', 'Format CSV tidak sesuai. Gunakan template yang disediakan.');
        }

        $created = 0;
        $errors = [];
        $line = 1;
        while (($row = fgetcsv($handle)) !== false) {
            $line++;
            $row = array_map('trim', $row);
            // skip empty rows
            if (count($row) < 2 || ($row[0] === '' && $row[1] === '')) continue;

            $data = array_combine($expected, array_pad($row, count($expected), ''));

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
                $status = 'nonaktif';
            }

            MasterKegiatan::create([
                'kode_kegiatan' => $data['kode_kegiatan'],
                'nama_kegiatan' => $data['nama_kegiatan'],
                'keterangan' => $data['keterangan'] ?? null,
                'status' => $status,
            ]);
            $created++;
        }

        fclose($handle);

        $msg = "Import selesai. Dibuat: {$created}.";
        if (!empty($errors)) {
            // attach errors to session (string)
            $msg .= ' Beberapa baris dilewati: ' . implode(' | ', array_slice($errors,0,10));
            session()->flash('import_errors', $errors);
        }

        return redirect()->route('master.kegiatan.index')->with('success', $msg);
    }
}
