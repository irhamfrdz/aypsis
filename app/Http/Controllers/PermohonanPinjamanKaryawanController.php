<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\PermohonanPinjamanKaryawan;
use App\Models\RiwayatUtangKaryawan;
use App\Models\SaldoUtangKaryawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class PermohonanPinjamanKaryawanController extends Controller
{
    public function index(Request $request)
    {
        $query = PermohonanPinjamanKaryawan::with(['karyawan', 'creator', 'approver', 'rejector'])
            ->latest('tanggal_permohonan')
            ->latest('id');

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($builder) use ($search) {
                $builder->where('nomor_permohonan', 'like', "%{$search}%")
                    ->orWhereHas('karyawan', function ($karyawanQuery) use ($search) {
                        $karyawanQuery->where('nama_lengkap', 'like', "%{$search}%")
                            ->orWhere('nama_panggilan', 'like', "%{$search}%")
                            ->orWhere('nik', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $permohonans = $query->paginate(20)->withQueryString();
        $summary = PermohonanPinjamanKaryawan::query()
            ->selectRaw('status, COUNT(*) as jumlah, COALESCE(SUM(COALESCE(nominal_disetujui, nominal)), 0) as total')
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        return view('permohonan-pinjaman-karyawan.index', compact('permohonans', 'summary'));
    }

    public function create()
    {
        $karyawans = Karyawan::orderBy('nama_lengkap')->get([
            'id', 'nik', 'nama_lengkap', 'nama_panggilan', 'divisi', 'pekerjaan',
        ]);

        return view('permohonan-pinjaman-karyawan.create', compact('karyawans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'karyawan_id' => 'required|integer|exists:karyawans,id',
            'tanggal_permohonan' => 'required|date',
            'tanggal_dibutuhkan' => 'nullable|date|after_or_equal:tanggal_permohonan',
            'nominal' => 'required|numeric|min:1|max:9999999999999.99',
            'tenor_bulan' => 'required|integer|min:1|max:120',
            'tujuan_pinjaman' => 'required|string|max:5000',
            'catatan_pemohon' => 'nullable|string|max:5000',
        ]);

        $permohonan = DB::transaction(function () use ($validated) {
            $nominal = (float) $validated['nominal'];
            $tenor = (int) $validated['tenor_bulan'];

            return PermohonanPinjamanKaryawan::create([
                ...$validated,
                'nomor_permohonan' => $this->generateNomorPermohonan(),
                'rencana_cicilan' => round($nominal / $tenor, 2),
                'status' => 'pending',
                'created_by' => auth()->id(),
            ]);
        });

        return redirect()->route('permohonan-pinjaman-karyawan.show', $permohonan)
            ->with('success', 'Permohonan pinjaman berhasil diajukan dan menunggu persetujuan.');
    }

    public function show(PermohonanPinjamanKaryawan $permohonanPinjamanKaryawan)
    {
        $permohonanPinjamanKaryawan->load(['karyawan.saldoUtang', 'creator', 'approver', 'rejector']);

        return view('permohonan-pinjaman-karyawan.show', [
            'permohonan' => $permohonanPinjamanKaryawan,
        ]);
    }

    public function approve(Request $request, PermohonanPinjamanKaryawan $permohonanPinjamanKaryawan)
    {
        $validated = $request->validate([
            'nominal_disetujui' => 'required|numeric|min:1|max:'.$permohonanPinjamanKaryawan->nominal,
            'tenor_disetujui' => 'required|integer|min:1|max:120',
            'catatan_approval' => 'nullable|string|max:5000',
        ]);

        DB::transaction(function () use ($permohonanPinjamanKaryawan, $validated) {
            $permohonan = PermohonanPinjamanKaryawan::query()
                ->lockForUpdate()
                ->findOrFail($permohonanPinjamanKaryawan->id);

            if ($permohonan->status !== 'pending') {
                throw ValidationException::withMessages([
                    'status' => 'Permohonan ini sudah diproses sebelumnya.',
                ]);
            }

            $nominal = (float) $validated['nominal_disetujui'];
            $permohonan->update([
                'status' => 'approved',
                'nominal_disetujui' => $nominal,
                'tenor_disetujui' => $validated['tenor_disetujui'],
                'catatan_approval' => $validated['catatan_approval'] ?? null,
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'rejected_by' => null,
                'rejected_at' => null,
            ]);

            $saldo = SaldoUtangKaryawan::firstOrCreate(
                ['karyawan_id' => $permohonan->karyawan_id],
                ['saldo' => 0]
            );
            $saldo->increment('saldo', $nominal);

            RiwayatUtangKaryawan::create([
                'karyawan_id' => $permohonan->karyawan_id,
                'tanggal' => now()->toDateString(),
                'tipe' => 'penambahan',
                'nominal' => $nominal,
                'referensi' => $permohonan->nomor_permohonan,
                'keterangan' => 'Pinjaman karyawan disetujui. '.$permohonan->tujuan_pinjaman,
            ]);

            $permohonan->update(['saldo_recorded_at' => now()]);
        });

        return redirect()->route('permohonan-pinjaman-karyawan.show', $permohonanPinjamanKaryawan)
            ->with('success', 'Permohonan disetujui dan saldo utang karyawan berhasil ditambahkan.');
    }

    public function reject(Request $request, PermohonanPinjamanKaryawan $permohonanPinjamanKaryawan)
    {
        $validated = $request->validate([
            'catatan_approval' => 'required|string|max:5000',
        ]);

        DB::transaction(function () use ($permohonanPinjamanKaryawan, $validated) {
            $permohonan = PermohonanPinjamanKaryawan::query()
                ->lockForUpdate()
                ->findOrFail($permohonanPinjamanKaryawan->id);

            if ($permohonan->status !== 'pending') {
                throw ValidationException::withMessages([
                    'status' => 'Permohonan ini sudah diproses sebelumnya.',
                ]);
            }

            $permohonan->update([
                'status' => 'rejected',
                'catatan_approval' => $validated['catatan_approval'],
                'rejected_by' => auth()->id(),
                'rejected_at' => now(),
                'approved_by' => null,
                'approved_at' => null,
            ]);
        });

        return redirect()->route('permohonan-pinjaman-karyawan.show', $permohonanPinjamanKaryawan)
            ->with('success', 'Permohonan pinjaman telah ditolak.');
    }

    public function cancel(PermohonanPinjamanKaryawan $permohonanPinjamanKaryawan)
    {
        if ((int) $permohonanPinjamanKaryawan->created_by !== (int) auth()->id()
            && ! Gate::allows('permohonan-pinjaman-karyawan-approve')) {
            abort(403);
        }

        DB::transaction(function () use ($permohonanPinjamanKaryawan) {
            $permohonan = PermohonanPinjamanKaryawan::query()
                ->lockForUpdate()
                ->findOrFail($permohonanPinjamanKaryawan->id);

            if ($permohonan->status !== 'pending') {
                throw ValidationException::withMessages([
                    'status' => 'Hanya permohonan pending yang dapat dibatalkan.',
                ]);
            }

            $permohonan->update(['status' => 'cancelled']);
        });

        return redirect()->route('permohonan-pinjaman-karyawan.index')
            ->with('success', 'Permohonan pinjaman berhasil dibatalkan.');
    }

    private function generateNomorPermohonan(): string
    {
        do {
            $nomor = 'PUK-'.now()->format('ymd').'-'.str_pad((string) random_int(1, 999999), 6, '0', STR_PAD_LEFT);
        } while (PermohonanPinjamanKaryawan::where('nomor_permohonan', $nomor)->exists());

        return $nomor;
    }
}
