<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const SHIP = 'KM MERATUS SORONG';

    private const VOYAGE = 'MERATUSJB497N';

    private const CORRECT_DATE = '2026-11-18';

    public function up(): void
    {
        $naikKapalRows = DB::table('naik_kapal')
            ->where('nama_kapal', self::SHIP)
            ->where('no_voyage', self::VOYAGE)
            ->where('sudah_ob', 1)
            ->where('catatan_ob', 'like', 'OB Muat Temas - Surat Jalan:%')
            ->get(['id', 'nomor_kontainer']);

        if ($naikKapalRows->isEmpty()) {
            return;
        }

        $naikKapalIds = $naikKapalRows->pluck('id')->all();
        $containerNumbers = $naikKapalRows->pluck('nomor_kontainer')
            ->filter(fn ($number) => filled($number))
            ->unique()
            ->values();

        DB::table('naik_kapal')->whereIn('id', $naikKapalIds)->update([
            'tanggal_ob' => self::CORRECT_DATE.' 00:00:00',
            'updated_at' => now(),
        ]);

        DB::table('tagihan_ob')
            ->whereIn('naik_kapal_id', $naikKapalIds)
            ->where('kegiatan', 'MUAT TEMAS')
            ->update([
                'tanggal_ob' => self::CORRECT_DATE,
                'updated_at' => now(),
            ]);

        if ($containerNumbers->isEmpty()) {
            return;
        }

        DB::table('history_kontainers')
            ->whereIn('nomor_kontainer', $containerNumbers)
            ->where('jenis_kegiatan', 'Pindahan Gudang')
            ->where('keterangan', 'like', '%OB Muat Temas:%')
            ->where('keterangan', 'like', '%Kapal: '.self::SHIP.'. Voyage: '.self::VOYAGE.'%')
            ->update([
                'tanggal_kegiatan' => self::CORRECT_DATE,
                'updated_at' => now(),
            ]);

        // Keep the denormalized warehouse columns aligned with the latest history.
        foreach ($containerNumbers as $containerNumber) {
            $latestHistory = DB::table('history_kontainers')
                ->where('nomor_kontainer', $containerNumber)
                ->orderByDesc('tanggal_kegiatan')
                ->orderByDesc('id')
                ->first(['gudang_id']);

            DB::table('kontainers')
                ->where('nomor_seri_gabungan', $containerNumber)
                ->update(['gudangs_id' => $latestHistory?->gudang_id]);

            DB::table('stock_kontainers')
                ->where('nomor_seri_gabungan', $containerNumber)
                ->update(['gudangs_id' => $latestHistory?->gudang_id]);
        }
    }

    public function down(): void
    {
        // The incorrect original date was not provided, so it cannot be safely restored.
    }
};
