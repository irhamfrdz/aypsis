<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const MARKER = '[backfill-ob-muat-on-board-20261117]';

    public function up(): void
    {
        $onBoardId = DB::table('gudangs')->where('nama_gudang', 'ON BOARD')->value('id');
        if (! $onBoardId) {
            throw new RuntimeException('Gudang ON BOARD tidak ditemukan.');
        }

        DB::table('naik_kapal')
            ->where('sudah_ob', 1)
            ->whereNotNull('tanggal_ob')
            ->whereNotNull('nomor_kontainer')
            ->where('nomor_kontainer', '<>', '')
            ->whereNotNull('no_voyage')
            ->where('no_voyage', '<>', '')
            ->where(function ($query) {
                $query->whereNull('ke')->orWhereRaw('UPPER(TRIM(ke)) = ?', ['ON BOARD']);
            })
            ->orderBy('id')
            ->chunkById(200, function ($rows) use ($onBoardId): void {
                $numbers = $rows->pluck('nomor_kontainer')->unique()->all();
                $histories = DB::table('history_kontainers')
                    ->whereIn('nomor_kontainer', $numbers)
                    ->where('jenis_kegiatan', 'Masuk')
                    ->where('gudang_id', $onBoardId)
                    ->get(['nomor_kontainer', 'keterangan'])
                    ->groupBy('nomor_kontainer');
                $masterNumbers = DB::table('kontainers')
                    ->whereIn('nomor_seri_gabungan', $numbers)
                    ->pluck('nomor_seri_gabungan')
                    ->flip();
                $scheduled = [];
                $inserts = [];
                $now = now();

                foreach ($rows as $row) {
                    $voyage = trim((string) $row->no_voyage);
                    $key = $row->nomor_kontainer.'|'.$voyage;
                    if (isset($scheduled[$key])) {
                        continue;
                    }

                    $alreadyRecorded = ($histories[$row->nomor_kontainer] ?? collect())
                        ->contains(static function ($history) use ($voyage): bool {
                            $description = (string) $history->keterangan;

                            return preg_match('/Voyage:\s*'.preg_quote($voyage, '/').'(?=\s|\(|\[|$)/i', $description) === 1
                                && preg_match('/OB Muat|OB TL ke Kapal|OB Bulk via Textarea/i', $description) === 1;
                        });
                    if ($alreadyRecorded) {
                        continue;
                    }

                    $scheduled[$key] = true;
                    $inserts[] = [
                        'nomor_kontainer' => $row->nomor_kontainer,
                        'tipe_kontainer' => $masterNumbers->has($row->nomor_kontainer) ? 'kontainer' : 'stock',
                        'jenis_kegiatan' => 'Masuk',
                        'tanggal_kegiatan' => substr((string) $row->tanggal_ob, 0, 10),
                        'gudang_id' => $onBoardId,
                        'keterangan' => 'Masuk via OB Muat ke Kapal: '.($row->nama_kapal ?: '-').'. Voyage: '.$voyage.' '.self::MARKER,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                foreach (array_chunk($inserts, 100) as $batch) {
                    DB::table('history_kontainers')->insert($batch);
                }
            });
    }

    public function down(): void
    {
        DB::table('history_kontainers')
            ->where('keterangan', 'like', '%'.self::MARKER.'%')
            ->delete();
    }
};
