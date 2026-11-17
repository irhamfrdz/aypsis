<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $onBoardId = DB::table('gudangs')->where('nama_gudang', 'ON BOARD')->value('id');
        if (! $onBoardId) {
            throw new RuntimeException('Gudang ON BOARD tidak ditemukan.');
        }

        DB::table('naik_kapal')->where('sudah_ob', 1)
            ->whereNotNull('nomor_kontainer')->whereNotNull('tanggal_ob')
            ->orderBy('id')->chunkById(500, function ($rows) use ($onBoardId) {
                $numbers = $rows->pluck('nomor_kontainer')->unique()->all();
                $histories = DB::table('history_kontainers')
                    ->whereIn('nomor_kontainer', $numbers)->where('jenis_kegiatan', 'Masuk')
                    ->get(['nomor_kontainer', 'keterangan'])->groupBy('nomor_kontainer');
                $sewaNumbers = DB::table('kontainers')->whereIn('nomor_seri_gabungan', $numbers)
                    ->pluck('nomor_seri_gabungan')->flip();
                $now = now();
                $inserts = [];
                $scheduled = [];
                foreach ($rows as $row) {
                    $key = $row->nomor_kontainer.'|'.$row->no_voyage;
                    $found = ($histories[$row->nomor_kontainer] ?? collect())->contains(function ($history) use ($row) {
                        $description = (string) $history->keterangan;

                        return $row->no_voyage && stripos($description, (string) $row->no_voyage) !== false
                            && (stripos($description, 'OB') !== false || stripos($description, 'TL') !== false);
                    });
                    if ($found || isset($scheduled[$key])) {
                        continue;
                    }
                    $scheduled[$key] = true;
                    $inserts[] = [
                        'nomor_kontainer' => $row->nomor_kontainer,
                        'tipe_kontainer' => $sewaNumbers->has($row->nomor_kontainer) ? 'kontainer' : 'stock',
                        'jenis_kegiatan' => 'Masuk',
                        'tanggal_kegiatan' => $row->tanggal_ob,
                        'gudang_id' => $onBoardId,
                        'keterangan' => 'Masuk via OB Muat ke Kapal: '.($row->nama_kapal ?: '-').'. Voyage: '.($row->no_voyage ?: '-').' [backfill-ob-muat-20261117]',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
                foreach (array_chunk($inserts, 200) as $batch) {
                    DB::table('history_kontainers')->insert($batch);
                }
            });
    }

    public function down(): void
    {
        DB::table('history_kontainers')->where('keterangan', 'like', '%[backfill-ob-muat-20261117]%')->delete();
    }
};
