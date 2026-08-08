<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Kontainer;
use App\Models\HistoryKontainer;
use App\Models\Gudang;
use Illuminate\Support\Facades\DB;

class UpdateKontainerDepoZona extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kontainer:update-depo-zona';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update tanggal selesai sewa and status for containers in Depo Zona based on their entry date';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Temukan gudang Depo Zona
        $gudang = Gudang::where('nama_gudang', 'like', '%Zona%')->first();
        
        if (!$gudang) {
            $this->error('Gudang Depo Zona tidak ditemukan.');
            return;
        }

        $this->info("Menemukan Gudang: {$gudang->nama_gudang} (ID: {$gudang->id})");

        // Ambil semua kontainer sewa yang berada di gudang ini
        $kontainers = Kontainer::where('gudangs_id', $gudang->id)
            ->where('status', '!=', 'inactive')
            ->get();

        $this->info("Ditemukan {$kontainers->count()} kontainer sewa di gudang ini.");

        $updatedCount = 0;

        DB::beginTransaction();
        try {
            foreach ($kontainers as $kontainer) {
                // Cari tanggal masuk dari HistoryKontainer
                $history = HistoryKontainer::where('nomor_kontainer', $kontainer->nomor_seri_gabungan)
                    ->where('gudang_id', $gudang->id)
                    ->orderBy('tanggal_kegiatan', 'desc')
                    ->orderBy('id', 'desc')
                    ->first();

                if ($history && $history->tanggal_kegiatan) {
                    $kontainer->tanggal_selesai_sewa = $history->tanggal_kegiatan;
                    $kontainer->status = 'Tidak Tersedia';
                    $kontainer->save();
                    
                    $this->line("Diupdate: {$kontainer->nomor_seri_gabungan} -> Tgl Selesai Sewa: {$history->tanggal_kegiatan}");
                    $updatedCount++;
                }
            }
            DB::commit();
            $this->info("Berhasil mengupdate $updatedCount kontainer.");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
