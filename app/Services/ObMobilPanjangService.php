<?php

namespace App\Services;

use App\Models\NaikKapal;
use App\Models\HistoryKontainer;
use App\Models\TagihanOb;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ObMobilPanjangService
{
    public const BIAYA_PER_KONTAINER = 125000;

    public function recordTemas(NaikKapal $record, int $historyBefore): void
    {
        $supir = $record->supir;
        $namaSupir = $supir->nama_panggilan ?: $supir->nama_lengkap;
        $barang = strtolower(trim((string) $record->jenis_barang));
        $status = ! $barang || str_contains($barang, 'empty') || in_array($barang, ['mt', 'mty']) ? 'empty' : 'full';
        TagihanOb::create([
            'naik_kapal_id' => $record->id, 'kapal' => $record->nama_kapal, 'voyage' => $record->no_voyage,
            'kegiatan' => 'MUAT TEMAS', 'tanggal_ob' => $record->tanggal_ob,
            'nomor_kontainer' => $record->nomor_kontainer, 'size_kontainer' => $record->size_kontainer,
            'nama_supir' => $namaSupir, 'barang' => $record->jenis_barang ?? '-',
            'status_kontainer' => $status, 'biaya' => self::BIAYA_PER_KONTAINER,
            'is_combo' => true, 'created_by' => Auth::id(),
            'keterangan' => 'OB Muat Temas - Mobil panjang / combo - '.$record->ke,
        ]);
        // Keep the same signature as other Temas movements for bulk cancellation.
        $record->catatan_ob = 'OB Muat Temas - Surat Jalan: ';
        $record->save();
        $history = HistoryKontainer::where('nomor_kontainer', $record->nomor_kontainer)
            ->where('id', '>', $historyBefore)->where('created_by', Auth::id())
            ->where('keterangan', 'Masuk via OB Muat ke Kapal: '.$record->nama_kapal.'. Voyage: '.$record->no_voyage)
            ->orderByDesc('id')->firstOrFail();
        $history->update([
            'jenis_kegiatan' => 'Pindahan Gudang',
            'keterangan' => 'OB Muat Temas: '.$record->asal_kontainer.' -> '.$record->ke
                .'. Surat Jalan: . Supir: '.$namaSupir
                .'. Kapal: '.$record->nama_kapal.'. Voyage: '.$record->no_voyage,
        ]);
    }

    public function process(string $type, array $ids, callable $mark): void
    {
        if ($type !== 'naik_kapal') {
            throw ValidationException::withMessages(['type' => 'Mobil panjang hanya tersedia untuk OB Muat Temas dari data Naik Kapal.']);
        }
        $model = NaikKapal::class;
        DB::transaction(function () use ($model, $ids, $mark) {
            $records = $model::whereIn('id', $ids)->orderBy('id')->lockForUpdate()->get();
            if (count(array_unique($ids)) !== 2 || $records->count() !== 2) {
                throw ValidationException::withMessages(['pasangan_id' => 'Pilih dua kontainer yang berbeda.']);
            }
            $first = $records->first();
            foreach ($records as $record) {
                $size = preg_replace('/\s+/', '', str_ireplace('ft', '', (string) $record->size_kontainer));
                if ($size !== '20' || $record->sudah_ob
                    || ! trim((string) $record->nomor_kontainer)
                    || strtoupper(trim((string) $record->tipe_kontainer)) === 'CARGO'
                    || str_contains(strtoupper($record->nomor_kontainer), 'CARGO')
                    || $record->nama_kapal !== $first->nama_kapal || $record->no_voyage !== $first->no_voyage) {
                    throw ValidationException::withMessages(['pasangan_id' => 'Mobil panjang membutuhkan dua kontainer 20 ft yang belum OB pada kapal dan voyage yang sama.']);
                }
            }
            if (strcasecmp(trim($first->nomor_kontainer), trim($records->last()->nomor_kontainer)) === 0) {
                throw ValidationException::withMessages(['pasangan_id' => 'Nomor kontainer pasangan harus berbeda.']);
            }
            foreach ($records as $record) {
                $mark($record);
                $model::whereKey($record->id)->update([
                    'ob_mobil_panjang' => true,
                    'ob_pasangan_id' => $records->firstWhere('id', '!=', $record->id)->id,
                ]);
            }
        });
    }
}
