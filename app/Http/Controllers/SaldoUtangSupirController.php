<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Coa;
use App\Models\RiwayatUtangKaryawan;
use App\Models\SaldoUtangKaryawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaldoUtangSupirController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Karyawan::query()->with('saldoUtang');

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                    ->orWhere('nama_panggilan', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        $karyawans = $query->orderBy('nama_lengkap', 'asc')->paginate(20);

        return view('saldo-utang-supir.index', compact('karyawans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $karyawans = Karyawan::orderBy('nama_lengkap', 'asc')->get();
        $accounts = Coa::orderBy('nomor_akun')->get();
        $bankAccounts = $accounts->filter(fn ($account) => preg_match('/kas|bank/i', $account->tipe_akun ?? ''));

        return view('saldo-utang-supir.create', compact('karyawans', 'accounts', 'bankAccounts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'karyawan_id' => 'required|exists:karyawans,id',
            'tanggal' => 'required|date',
            'tipe' => 'required|in:penambahan,pengurangan',
            'referensi' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'bank_coa_id' => ['required', 'integer', \Illuminate\Validation\Rule::exists('akun_coa', 'id')->where(fn ($q) => $q->where(fn ($bank) => $bank->where('tipe_akun', 'like', '%kas%')->orWhere('tipe_akun', 'like', '%bank%')))],
            'rincian_coa' => 'required|array|min:1|max:100',
            'rincian_coa.*.coa_id' => 'required|integer|exists:akun_coa,id',
            'rincian_coa.*.nominal' => 'required|numeric|decimal:0,2|min:0.01|max:9999999999999.99',
            'rincian_coa.*.memo' => 'nullable|string|max:1000',
        ]);

        $totalCents = collect($request->rincian_coa)->sum(fn ($row) => (int) round((float) $row['nominal'] * 100));
        if ($totalCents > 999999999999999) {
            throw \Illuminate\Validation\ValidationException::withMessages(['rincian_coa' => 'Grand Total melebihi batas nominal transaksi.']);
        }
        $grandTotal = $totalCents / 100;

        DB::beginTransaction();
        try {
            $accounts = Coa::whereIn('id', collect($request->rincian_coa)->pluck('coa_id'))->get()->keyBy('id');
            $rincianCoa = collect($request->rincian_coa)->map(function ($row) use ($accounts) {
                $account = $accounts->get($row['coa_id']);

                return ['coa_id' => $account->id, 'nomor_akun' => $account->nomor_akun,
                    'nama_akun' => $account->nama_akun, 'nominal' => round((float) $row['nominal'], 2), 'memo' => $row['memo'] ?? null];
            })->values()->all();
            $saldoUtang = SaldoUtangKaryawan::firstOrCreate(
                ['karyawan_id' => $request->karyawan_id],
                ['saldo' => 0.00]
            );

            if ($request->tipe === 'penambahan') {
                $saldoUtang->increment('saldo', $grandTotal);
            } else {
                $saldoUtang->decrement('saldo', $grandTotal);
            }

            RiwayatUtangKaryawan::create([
                'karyawan_id' => $request->karyawan_id,
                'tanggal' => $request->tanggal,
                'tipe' => $request->tipe,
                'nominal' => $grandTotal,
                'referensi' => $request->referensi,
                'keterangan' => $request->keterangan,
                'bank_coa_id' => $request->bank_coa_id,
                'rincian_coa' => $rincianCoa,
            ]);

            DB::commit();

            return redirect()->route('saldo-utang-karyawan.index')
                ->with('success', 'Transaksi utang karyawan berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Terjadi kesalahan: '.$e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $karyawan = Karyawan::with('saldoUtang')->findOrFail($id);

        $riwayat = RiwayatUtangKaryawan::where('karyawan_id', $id)
            ->with('bankCoa')
            ->orderBy('tanggal', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(30);

        return view('saldo-utang-supir.show', compact('karyawan', 'riwayat'));
    }

    /**
     * Show import CSV form.
     */
    public function showImportForm()
    {
        return view('saldo-utang-supir.import');
    }

    /**
     * Process import CSV.
     */
    public function importProcess(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        $file = $request->file('csv_file');
        $path = $file->getRealPath();

        $successCount = 0;
        $unmatchedRows = [];

        DB::beginTransaction();
        try {
            $handle = fopen($path, 'r');
            if ($handle !== false) {
                // Read header row
                $header = fgetcsv($handle, 1000, ';');

                while (($row = fgetcsv($handle, 1000, ';')) !== false) {
                    // Skip empty rows
                    if (count($row) < 3 || empty($row[0]) && empty($row[1])) {
                        continue;
                    }

                    $nikInput = trim($row[0]);
                    $namaInput = trim($row[1]);
                    $saldoInput = trim($row[2]);

                    // Parse balance value
                    // e.g. "650.000,00" -> 650000.00 or "-70.000,00" -> -70000.00
                    $cleanSaldo = str_replace('.', '', $saldoInput); // Remove thousands dots
                    $cleanSaldo = str_replace(',', '.', $cleanSaldo); // Replace decimal comma with dot
                    $saldoFloat = floatval($cleanSaldo);

                    // Find supir / karyawan
                    $karyawan = null;

                    // 1. Match by NIK
                    if (! empty($nikInput)) {
                        // Pad NIK if necessary to match DB representation (e.g. "0012" vs "12")
                        $paddedNik = str_pad($nikInput, 4, '0', STR_PAD_LEFT);
                        $karyawan = Karyawan::where('nik', $nikInput)
                            ->orWhere('nik', $paddedNik)
                            ->first();
                    }

                    // 2. Match by Name if NIK didn't yield a result
                    if (! $karyawan && ! empty($namaInput)) {
                        $cleanedName = $this->cleanKaryawanName($namaInput);
                        $karyawan = Karyawan::where('nama_lengkap', 'like', "%{$cleanedName}%")
                            ->orWhere('nama_panggilan', 'like', "%{$cleanedName}%")
                            ->first();
                    }

                    if ($karyawan) {
                        // Update or create saldo
                        $saldoUtang = SaldoUtangKaryawan::firstOrCreate(
                            ['karyawan_id' => $karyawan->id],
                            ['saldo' => 0.00]
                        );

                        // Overwrite with initial balance from CSV
                        $saldoUtang->saldo = $saldoFloat;
                        $saldoUtang->save();

                        // Add transaction log
                        RiwayatUtangKaryawan::create([
                            'karyawan_id' => $karyawan->id,
                            'tanggal' => now(),
                            'tipe' => $saldoFloat >= 0 ? 'penambahan' : 'pengurangan',
                            'nominal' => abs($saldoFloat),
                            'referensi' => 'Saldo Awal CSV',
                            'keterangan' => "Import saldo awal dari CSV. NIK Asal: {$nikInput}, Nama Asal: {$namaInput}",
                        ]);

                        $successCount++;
                    } else {
                        $unmatchedRows[] = [
                            'nik' => $nikInput,
                            'nama' => $namaInput,
                            'saldo' => $saldoInput,
                        ];
                    }
                }
                fclose($handle);
            }

            DB::commit();

            if (count($unmatchedRows) > 0) {
                return redirect()->route('saldo-utang-karyawan.index')
                    ->with('success', "Sukses mengimpor {$successCount} saldo karyawan.")
                    ->with('warning_data', $unmatchedRows);
            }

            return redirect()->route('saldo-utang-karyawan.index')
                ->with('success', "Sukses mengimpor seluruh {$successCount} data saldo karyawan.");
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Terjadi kesalahan saat memproses file: '.$e->getMessage());
        }
    }

    /**
     * Clean driver name from CSV trailing details
     */
    private function cleanKaryawanName(string $name): string
    {
        $name = preg_replace('/,\s*BP(\s*\(KENEK\))?/i', '', $name);
        $name = preg_replace('/\s*BP\s*(\(KENEK\))?/i', '', $name);
        $name = preg_replace('/\s*\(KENEK\)/i', '', $name);
        $name = preg_replace('/\s*\/[A-Z]+/i', '', $name); // remove e.g. /DULOH

        return trim($name);
    }
}
