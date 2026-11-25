<?php

namespace App\Services;

use App\Models\NaikKapal;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ObMobilPanjangService
{
    public const BIAYA_PER_KONTAINER = 250000;

    public const BIAYA_REGULER_MUAT_TEMAS = 325000;

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
