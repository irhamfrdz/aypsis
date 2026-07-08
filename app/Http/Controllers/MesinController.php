<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Karyawan;
use App\Models\Mesin;
use App\Services\ZkTecoService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MesinController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Mesin::query();

        // Handle search functionality
        if ($request->has('search') && ! empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_mesin', 'like', "%{$search}%")
                    ->orWhere('kode_mesin', 'like', "%{$search}%")
                    ->orWhere('tipe_mesin', 'like', "%{$search}%")
                    ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        // Handle status filter
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        $mesins = $query->orderBy('kode_mesin')->paginate(15);

        return view('mesin.index', compact('mesins'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('mesin.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode_mesin' => 'required|string|max:50|unique:mesins,kode_mesin',
            'nama_mesin' => 'required|string|max:100',
            'tipe_mesin' => 'required|string|max:100',
            'ip_address' => 'nullable|string|max:45',
            'port' => 'required|integer|min:1|max:65535',
            'comm_key' => 'required|integer|min:0',
            'status' => 'required|string|in:Aktif,Rusak,Perbaikan,Nonaktif',
            'keterangan' => 'nullable|string',
        ]);

        try {
            Mesin::create([
                'kode_mesin' => strtoupper($request->kode_mesin),
                'nama_mesin' => $request->nama_mesin,
                'tipe_mesin' => $request->tipe_mesin,
                'ip_address' => $request->ip_address,
                'port' => $request->port,
                'comm_key' => $request->comm_key,
                'status' => $request->status,
                'keterangan' => $request->keterangan,
            ]);

            return redirect()->route('master.mesin.index')
                ->with('success', 'Mesin berhasil ditambahkan!');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $mesin = Mesin::findOrFail($id);

        return view('mesin.edit', compact('mesin'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $mesin = Mesin::findOrFail($id);

        $request->validate([
            'kode_mesin' => ['required', 'string', 'max:50', Rule::unique('mesins')->ignore($mesin->id)],
            'nama_mesin' => 'required|string|max:100',
            'tipe_mesin' => 'required|string|max:100',
            'ip_address' => 'nullable|string|max:45',
            'port' => 'required|integer|min:1|max:65535',
            'comm_key' => 'required|integer|min:0',
            'status' => 'required|string|in:Aktif,Rusak,Perbaikan,Nonaktif',
            'keterangan' => 'nullable|string',
        ]);

        try {
            $mesin->update([
                'kode_mesin' => strtoupper($request->kode_mesin),
                'nama_mesin' => $request->nama_mesin,
                'tipe_mesin' => $request->tipe_mesin,
                'ip_address' => $request->ip_address,
                'port' => $request->port,
                'comm_key' => $request->comm_key,
                'status' => $request->status,
                'keterangan' => $request->keterangan,
            ]);

            return redirect()->route('master.mesin.index')
                ->with('success', 'Mesin berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $mesin = Mesin::findOrFail($id);
            $mesin->delete();

            return redirect()->route('master.mesin.index')
                ->with('success', 'Mesin berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    /**
     * Test connection to a fingerprint machine using raw IP/Port before saving.
     */
    public function testConnectionRaw(Request $request)
    {
        $request->validate([
            'ip_address' => 'required|string|max:45',
            'port' => 'required|integer|min:1|max:65535',
        ]);

        $service = new ZkTecoService($request->ip_address, $request->port);

        if ($service->connect()) {
            $service->disconnect();

            return response()->json([
                'success' => true,
                'message' => 'Koneksi ke mesin berhasil!',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Gagal terhubung ke mesin fingerprint. Silakan periksa IP dan Port.',
        ]);
    }

    /**
     * Test connection to the fingerprint machine.
     */
    public function testConnection(string $id)
    {
        $mesin = Mesin::findOrFail($id);

        if (empty($mesin->ip_address)) {
            return response()->json([
                'success' => false,
                'message' => 'IP Address mesin belum dikonfigurasi!',
            ]);
        }

        $service = new ZkTecoService($mesin->ip_address, $mesin->port);

        if ($service->connect()) {
            $service->disconnect();

            return response()->json([
                'success' => true,
                'message' => 'Koneksi ke mesin berhasil!',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Gagal terhubung ke mesin fingerprint. Silakan periksa IP dan Port.',
        ]);
    }

    /**
     * Sync attendance logs from fingerprint machine.
     */
    public function syncLogs(string $id)
    {
        $mesin = Mesin::findOrFail($id);

        // Check if we use MS Access database (.mdb) file sync
        $mdbPath = env('MDB_PATH', 'C:\\Program Files (x86)\\Solution\\att2000.mdb');
        if (file_exists($mdbPath)) {
            try {
                // Prevent timeout for large datasets
                set_time_limit(900);

                $conn = new \PDO("odbc:Driver={Microsoft Access Driver (*.mdb, *.accdb)};Dbq=$mdbPath;Uid=;Pwd=;");
                $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

                // Fetch all historical logs
                $query = "SELECT c.USERID, u.Badgenumber, c.CHECKTIME, c.CHECKTYPE 
                          FROM CHECKINOUT c 
                          INNER JOIN USERINFO u ON c.USERID = u.USERID";
                $stmt = $conn->query($query);
                $mdbLogs = $stmt->fetchAll(\PDO::FETCH_ASSOC);

                // Cache existing logs to avoid N+1 database queries
                $existingLogs = Absensi::select('nik', 'waktu', 'tipe')
                    ->get()
                    ->mapWithKeys(function ($item) {
                        $timeStr = $item->waktu instanceof \Carbon\Carbon 
                            ? $item->waktu->format('Y-m-d H:i:s') 
                            : \Carbon\Carbon::parse($item->waktu)->format('Y-m-d H:i:s');
                        return [$item->nik . '_' . $timeStr . '_' . $item->tipe => true];
                    })
                    ->toArray();

                // Cache employees to avoid querying Karyawan table inside the loop
                $employees = Karyawan::select('id', 'nik')
                    ->whereNotNull('nik')
                    ->get()
                    ->pluck('id', 'nik')
                    ->toArray();

                $syncedCount = 0;
                foreach ($mdbLogs as $log) {
                    $nik = trim($log['Badgenumber']);
                    if (is_numeric($nik)) {
                        $nik = str_pad($nik, 4, '0', STR_PAD_LEFT);
                    }
                    $type = (in_array(strtoupper($log['CHECKTYPE']), ['I', '0', 'MASUK'])) ? 'Masuk' : 'Pulang';
                    $logTime = \Carbon\Carbon::parse($log['CHECKTIME'])->format('Y-m-d H:i:s');

                    $key = $nik . '_' . $logTime . '_' . $type;
                    if (isset($existingLogs[$key])) {
                        continue;
                    }

                    Absensi::create([
                        'nik' => $nik,
                        'waktu' => $logTime,
                        'tipe' => $type,
                        'karyawan_id' => $employees[$nik] ?? null,
                        'mesin_id' => $mesin->id,
                        'keterangan' => 'Sinkronisasi database lokal '.$mesin->nama_mesin,
                    ]);

                    $syncedCount++;
                }

                return redirect()->route('absensi.index')
                    ->with('success', "Sinkronisasi database lokal berhasil! {$syncedCount} data absensi baru telah diimpor.");

            } catch (\Exception $e) {
                return back()->with('error', 'Gagal membaca database lokal: ' . $e->getMessage());
            }
        }

        if (empty($mesin->ip_address)) {
            return back()->with('error', 'IP Address mesin belum dikonfigurasi!');
        }

        $service = new ZkTecoService($mesin->ip_address, $mesin->port);

        if (! $service->connect()) {
            return back()->with('error', 'Gagal terhubung ke mesin fingerprint. Silakan periksa koneksi jaringan.');
        }

        $logs = $service->getAttendance();
        $service->disconnect();

        if (empty($logs)) {
            return redirect()->route('absensi.index')->with('success', 'Koneksi berhasil, tetapi tidak ada data absensi baru yang ditemukan.');
        }

        $syncedCount = 0;

        foreach ($logs as $log) {
            $nik = trim($log['pin']);
            if (is_numeric($nik)) {
                $nik = str_pad($nik, 4, '0', STR_PAD_LEFT);
            }

            // Find employee by NIK
            $karyawan = Karyawan::where('nik', $nik)->first();

            // Insert or update attendance log
            $absensi = Absensi::updateOrCreate(
                [
                    'nik' => $nik,
                    'waktu' => $log['timestamp'],
                    'tipe' => $log['type'],
                ],
                [
                    'karyawan_id' => $karyawan ? $karyawan->id : null,
                    'mesin_id' => $mesin->id,
                    'keterangan' => 'Sinkronisasi mesin '.$mesin->nama_mesin,
                ]
            );

            if ($absensi->wasRecentlyCreated) {
                $syncedCount++;
            }
        }

        return redirect()->route('absensi.index')
            ->with('success', "Sinkronisasi berhasil! {$syncedCount} data absensi baru telah diimpor.");
    }
}
