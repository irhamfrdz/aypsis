<?php

namespace App\Http\Controllers;

use App\Models\Gudang;
use App\Models\HistoryKontainer;
use App\Models\Kontainer;
use App\Models\StockKontainer;
use App\Models\TandaTerima;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ApprovalTandaTerimaPelabuhanController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status', 'pending');
        if (! in_array($status, ['pending', 'approved', 'all'], true)) {
            $status = 'pending';
        }

        $search = trim((string) $request->query('search', ''));
        $query = TandaTerima::query();

        if ($status === 'pending') {
            $query->where('approval_pelabuhan_status', 'pending');
        } elseif ($status === 'approved') {
            $query->where('approval_pelabuhan_status', 'approved');
        }

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder->where('no_surat_jalan', 'like', "%{$search}%")
                    ->orWhere('no_kontainer', 'like', "%{$search}%")
                    ->orWhere('supir', 'like', "%{$search}%");
            });
        }

        $tandaTerimas = $query->with('suratJalan')->latest('id')->paginate(20)->withQueryString();
        $gudangs = Gudang::where('status', 'aktif')->orderBy('nama_gudang')->get();

        $nomorKontainers = $tandaTerimas->getCollection()
            ->flatMap(fn (TandaTerima $tandaTerima) => $this->nomorKontainers($tandaTerima))
            ->unique()
            ->values();

        $lokasiKontainers = $nomorKontainers->isEmpty() ? collect() : Kontainer::whereIn('nomor_seri_gabungan', $nomorKontainers)
            ->where('status', '!=', 'inactive')
            ->pluck('gudangs_id', 'nomor_seri_gabungan');
        $lokasiStock = $nomorKontainers->isEmpty() ? collect() : StockKontainer::whereIn('nomor_seri_gabungan', $nomorKontainers)
            ->pluck('gudangs_id', 'nomor_seri_gabungan');

        foreach ($tandaTerimas as $tandaTerima) {
            $nomorKontainer = $this->nomorKontainers($tandaTerima)[0] ?? null;
            $tandaTerima->gudang_tujuan_id = $nomorKontainer
                ? ($lokasiKontainers->get($nomorKontainer) ?? $lokasiStock->get($nomorKontainer))
                : null;
        }

        return view('approval-tanda-terima-pelabuhan.index', compact('tandaTerimas', 'gudangs', 'status', 'search'));
    }

    public function approve(Request $request, TandaTerima $tandaTerima): RedirectResponse
    {
        $tandaTerima->loadMissing('suratJalan');
        $isCargo = strtolower((string) ($tandaTerima->suratJalan?->tipe_kontainer ?? $tandaTerima->tipe_kontainer)) === 'cargo';
        $validated = $request->validate([
            'tanggal' => ['required', 'date'],
            'tanggal_checkpoint_supir' => ['required', 'date'],
            'supir_pengganti' => ['nullable', 'string', 'max:255'],
            'lembur' => ['nullable', 'boolean'],
            'nginap' => ['nullable', 'boolean'],
            'tidak_lembur_nginap' => ['nullable', 'boolean'],
            'gudang_id' => [Rule::requiredIf(! $isCargo), 'nullable', Rule::exists('gudangs', 'id')->where('status', 'aktif')],
        ]);

        $lembur = $request->boolean('lembur');
        $nginap = $request->boolean('nginap');
        $tidakLemburNginap = $request->boolean('tidak_lembur_nginap');
        if ((! $lembur && ! $nginap && ! $tidakLemburNginap) || ($tidakLemburNginap && ($lembur || $nginap))) {
            throw ValidationException::withMessages([
                'lembur' => 'Pilih Lembur, Nginap, atau Tidak Lembur & Nginap. Pilihan Tidak Lembur & Nginap tidak dapat digabung dengan pilihan lain.',
            ]);
        }

        DB::transaction(function () use ($tandaTerima, $validated, $request, $lembur, $nginap, $tidakLemburNginap) {
            $tandaTerima = TandaTerima::whereKey($tandaTerima->id)->lockForUpdate()->firstOrFail();
            if (! in_array($tandaTerima->approval_pelabuhan_status, ['pending', 'approved'], true)) {
                abort(422, 'Status tanda terima ini tidak dapat disetujui.');
            }

            $tandaTerima->forceFill([
                'tanggal' => $validated['tanggal'],
                'tanggal_checkpoint_supir' => $validated['tanggal_checkpoint_supir'],
                'supir_pengganti' => $validated['supir_pengganti'] ?? null,
                'lembur' => $lembur,
                'nginap' => $nginap,
                'tidak_lembur_nginap' => $tidakLemburNginap,
                'approval_pelabuhan_status' => 'approved',
                'updated_by' => $request->user()->id,
            ])->save();

            if ($tandaTerima->surat_jalan_id) {
                $tandaTerima->suratJalan()->update([
                    'tanggal_checkpoint' => $validated['tanggal_checkpoint_supir'],
                    'lembur' => $lembur,
                    'nginap' => $nginap,
                    'tidak_lembur_nginap' => $tidakLemburNginap,
                ]);
            }

            if (! empty($validated['gudang_id'])) {
                $this->updateGudangTujuan($tandaTerima, (int) $validated['gudang_id'], $validated['tanggal'], $request->user()->id);
            }
        });

        return redirect()->route('approval-tanda-terima-pelabuhan.index', $request->only('status', 'search'))
            ->with('success', 'Tanda terima pelabuhan berhasil disetujui.');
    }

    private function nomorKontainers(TandaTerima $tandaTerima): array
    {
        $nomor = $tandaTerima->no_kontainer ?: $tandaTerima->suratJalan?->no_kontainer;

        return array_values(array_unique(array_filter(array_map('trim', explode(',', (string) $nomor)))));
    }

    private function updateGudangTujuan(TandaTerima $tandaTerima, int $gudangId, string $tanggal, int $userId): void
    {
        $nomorKontainers = $this->nomorKontainers($tandaTerima);
        if ($nomorKontainers === []) {
            return;
        }

        $logged = [];
        $getAsalGudang = function (string $nomor, ?int $gudangSaatIni): ?int {
            return $gudangSaatIni ?? HistoryKontainer::where('nomor_kontainer', $nomor)
                ->whereNotNull('gudang_id')
                ->latest('id')
                ->value('gudang_id');
        };
        $logMasuk = function (string $nomor, string $tipe, ?int $asalGudangId) use ($tandaTerima, $gudangId, $tanggal, $userId): void {
            HistoryKontainer::create([
                'nomor_kontainer' => $nomor,
                'tipe_kontainer' => $tipe,
                'jenis_kegiatan' => 'Masuk',
                'tanggal_kegiatan' => $tanggal,
                'asal_gudang_id' => $asalGudangId,
                'gudang_id' => $gudangId,
                'keterangan' => 'Masuk via Tanda Terima: '.$tandaTerima->no_surat_jalan,
                'created_by' => $userId,
            ]);
        };

        foreach (Kontainer::whereIn('nomor_seri_gabungan', $nomorKontainers)->where('status', '!=', 'inactive')->lockForUpdate()->get() as $kontainer) {
            $nomor = $kontainer->nomor_seri_gabungan;
            $logged[$nomor] = true;
            if ((int) $kontainer->gudangs_id === $gudangId) {
                continue;
            }

            $asalGudangId = $getAsalGudang($nomor, $kontainer->gudangs_id);
            $kontainer->update(['gudangs_id' => $gudangId]);
            $logMasuk($nomor, $kontainer->tipe_kontainer ?? 'kontainer', $asalGudangId);
        }

        foreach (StockKontainer::whereIn('nomor_seri_gabungan', $nomorKontainers)->lockForUpdate()->get() as $stockKontainer) {
            $nomor = $stockKontainer->nomor_seri_gabungan;
            if ((int) $stockKontainer->gudangs_id === $gudangId) {
                continue;
            }

            $asalGudangId = $getAsalGudang($nomor, $stockKontainer->gudangs_id);
            $stockKontainer->update(['gudangs_id' => $gudangId]);
            if (! isset($logged[$nomor])) {
                $logMasuk($nomor, $stockKontainer->tipe_kontainer ?? 'stock', $asalGudangId);
            }
        }
    }
}
