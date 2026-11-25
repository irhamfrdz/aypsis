<?php

namespace App\Console\Commands;

use App\Models\NaikKapal;
use App\Models\PranotaObItem;
use App\Models\TagihanOb;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FixObMuatTemasMobilPanjangRate extends Command
{
    protected $signature = 'ob:fix-mobil-panjang-rate
        {--kapal= : Nama kapal yang diperbaiki}
        {--voyage= : Voyage yang diperbaiki}
        {--apply : Simpan perubahan; tanpa opsi ini hanya pratinjau}';

    protected $description = 'Perbaiki tarif OB Muat Temas mobil panjang lama dari Rp125.000 ke Rp250.000 per kontainer yang belum masuk pranota';

    public function handle(): int
    {
        $kapal = strtoupper(preg_replace('/\s+/', ' ', str_replace('.', '', trim((string) $this->option('kapal')))));
        $voyage = strtoupper(trim((string) $this->option('voyage')));
        if ($kapal === '' || $voyage === '') {
            $this->error('Opsi --kapal dan --voyage wajib diisi.');

            return self::FAILURE;
        }
        $apply = (bool) $this->option('apply');
        $this->info($apply ? 'Mode SIMPAN' : 'Mode PRATINJAU: tidak ada data diubah.');
        $changed = $skipped = 0;
        DB::transaction(function () use ($kapal, $voyage, $apply, &$changed, &$skipped) {
            $rows = TagihanOb::where('kegiatan', 'MUAT TEMAS')->where('biaya', 125000)
                ->whereRaw("UPPER(REPLACE(REPLACE(TRIM(kapal), '.', ''), '  ', ' ')) = ?", [$kapal])
                ->whereRaw('UPPER(TRIM(voyage)) = ?', [$voyage])
                ->where(fn ($query) => $query->where('is_ckls_mobil_panjang', true)
                    ->orWhere('is_combo', true)
                    ->orWhereHas('naikKapal', fn ($record) => $record->where('ob_mobil_panjang', true)))
                ->orderBy('id')->lockForUpdate()->get();

            foreach ($rows as $row) {
                if ($row->naik_kapal_id) {
                    $record = NaikKapal::whereKey($row->naik_kapal_id)->first();
                    if (! $record) {
                        $skipped++;
                        $this->warn("LEWATI #{$row->id}: data Naik Kapal tidak ditemukan.");
                        continue;
                    }
                }
                if ($row->pranotaMuatTemasItem()->exists() || $row->pranotaObItem()->exists()
                    || $row->pranotaObAntarGudangItem()->exists()
                    || ($row->naik_kapal_id && PranotaObItem::where('item_type', NaikKapal::class)->where('item_id', $row->naik_kapal_id)->exists())) {
                    $skipped++;
                    $this->warn("LEWATI #{$row->id} {$row->nomor_kontainer}: sudah masuk pranota.");
                    continue;
                }
                $this->line("#{$row->id} | {$row->nomor_kontainer} | {$row->nama_supir} | Rp125.000 -> Rp250.000");
                $changed++;
                if ($apply) {
                    DB::table('tagihan_ob')->where('id', $row->id)->where('biaya', 125000)
                        ->update(['biaya' => 250000, 'updated_at' => now()]);
                    Log::info('OB Muat Temas mobil panjang rate corrected', [
                        'tagihan_ob_id' => $row->id, 'kapal' => $row->kapal, 'voyage' => $row->voyage,
                        'old_biaya' => 125000, 'new_biaya' => 250000,
                    ]);
                }
            }
        });
        $this->info("Kontainer cocok: {$changed}. Dilewati: {$skipped}. Tambahan nominal: Rp".number_format($changed * 125000, 0, ',', '.').'.');
        if (! $apply) {
            $this->info('Tambahkan --apply untuk menyimpan. Setelahnya refresh halaman OB dan pilih ulang kontainer agar modal memakai tarif terbaru.');
        }

        return self::SUCCESS;
    }
}
