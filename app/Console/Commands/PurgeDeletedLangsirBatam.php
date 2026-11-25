<?php

namespace App\Console\Commands;

use App\Models\LangsirBatam;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class PurgeDeletedLangsirBatam extends Command
{
    protected $signature = 'langsir-batam:purge-deleted
        {--no-surat-jalan= : Batasi ke satu nomor surat jalan}
        {--apply : Hapus permanen; tanpa opsi ini hanya pratinjau}';

    protected $description = 'Hapus permanen Langsir Batam yang sudah soft delete beserta history terkait';

    public function handle(): int
    {
        $query = LangsirBatam::onlyTrashed();
        if ($number = trim((string) $this->option('no-surat-jalan'))) {
            $query->where('no_surat_jalan', $number);
        }
        $apply = (bool) $this->option('apply');
        $this->info($apply ? 'Mode HAPUS PERMANEN.' : 'Mode PRATINJAU. Tidak ada data diubah.');
        $count = 0;
        $query->chunkById(200, function ($rows) use ($apply, &$count) {
            foreach ($rows as $row) {
                $this->line("#{$row->id} | {$row->no_transaksi} | SJ: {$row->no_surat_jalan} | {$row->no_kontainer} | Dihapus: {$row->deleted_at}");
                if ($apply) {
                    if (! $row->deletePermanently(true)) {
                        $this->warn("LEWATI #{$row->id}: data sudah dipulihkan.");
                        continue;
                    }
                    Log::info('Soft-deleted Langsir Batam permanently removed', [
                        'id' => $row->id, 'no_transaksi' => $row->no_transaksi, 'no_surat_jalan' => $row->no_surat_jalan,
                    ]);
                }
                $count++;
            }
        });
        $this->info("Total: {$count} data ".($apply ? 'dihapus permanen.' : 'akan dihapus permanen. Tambahkan --apply untuk menjalankan.'));

        return self::SUCCESS;
    }
}
