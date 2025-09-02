<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class InspectPranotaKontainer extends Command
{
    protected $signature = 'pranota:inspect {--id=} {--nomor=} {--fix : Actually delete pivot rows for the container} {--force : Skip confirmation when running --fix}';
    protected $description = 'Inspect and optionally clean pranota/pivot rows for a container (tagihan_kontainer_sewa_kontainers)';

    public function handle()
    {
        $id = $this->option('id');
        $nomor = $this->option('nomor');

        if (!$id && !$nomor) {
            $this->error('Provide --id=<kontainer_id> or --nomor=<container_number>');
            return 1;
        }

        if (!$id) {
            // Try common columns used for container numbering
            $kontQuery = DB::table('kontainers')
                ->where('nomor_seri_gabungan', $nomor)
                ->orWhere('nomor_seri_kontainer', $nomor)
                ->orWhereRaw("CONCAT(IFNULL(awalan_kontainer,''), IFNULL(nomor_seri_kontainer,''), IFNULL(akhiran_kontainer,'')) = ?", [$nomor]);

            $kont = $kontQuery->first();
            if (!$kont) {
                $this->error("No kontainer found with nomor = $nomor (checked nomor_seri_gabungan, nomor_seri_kontainer, and awalan+nomor+akhiran)");
                return 2;
            }
            $id = $kont->id;
        }

        $this->info("Inspecting kontainer_id = $id");

        $pivots = DB::table('tagihan_kontainer_sewa_kontainers')->where('kontainer_id', $id)->get();

        if ($pivots->isEmpty()) {
            $this->info('No pivot rows found in tagihan_kontainer_sewa_kontainers for this kontainer.');
        } else {
            $this->info('Found pivot rows:');
            foreach ($pivots as $p) {
                $this->line(" - pivot id={$p->id} tagihan_id={$p->tagihan_id} created_at={$p->created_at}");
            }

            // fetch related tagihan rows
            $tagihanIds = $pivots->pluck('tagihan_id')->unique()->values()->all();
            $tagihans = DB::table('tagihan_kontainer_sewa')->whereIn('id', $tagihanIds)->get();

            foreach ($tagihans as $t) {
                $tid = $t->id ?? '(no-id)';
                $ttarif = $t->tarif ?? '(no-tarif)';
                $tvendor = $t->vendor ?? '(no-vendor)';
                $ttanggal = $t->tanggal ?? ($t->tanggal_harga_awal ?? '(no-tanggal)');
                $tstatus = $t->status_pembayaran ?? '(no-status)';
                $tket = $t->keterangan ?? '(no-keterangan)';
                $tnomor = $t->nomor_pranota ?? ($t->nomor ?? '(no-nomor)');
                $this->line("Tagihan id={$tid} tarif={$ttarif} vendor={$tvendor} tanggal={$ttanggal} status_pembayaran={$tstatus} keterangan={$tket} nomor_pranota={$tnomor}");
            }
        }

        // Also show any tagihan_kontainer_sewa rows (non-pranota) that reference this kontainer via other logic
        $this->info('Checking tagihan_kontainer_sewa rows that mention this kontainer via joins (for additional context)');
        $rows = DB::table('tagihan_kontainer_sewa_kontainers as tkk')
            ->join('tagihan_kontainer_sewa as tk', 'tkk.tagihan_id', '=', 'tk.id')
            ->where('tkk.kontainer_id', $id)
            ->select(
                'tkk.*',
                'tk.tarif',
                'tk.vendor',
                'tk.tanggal_harga_awal',
                'tk.tanggal_harga_akhir',
                'tk.status_pembayaran',
                'tk.keterangan',
                'tk.nomor_pranota'
            )
            ->get();

        foreach ($rows as $r) {
            $dateInfo = $r->tanggal_harga_awal ?? $r->tanggal_harga_akhir ?? '(no-tanggal)';
            $this->line("Row: pivot_id={$r->id} tagihan_id={$r->tagihan_id} tarif={$r->tarif} vendor={$r->vendor} tanggal={$dateInfo} status={$r->status_pembayaran}");
        }

        if ($this->option('fix')) {
            $this->warn('Running in --fix mode: will delete pivot rows for this kontainer (non-reversible via this command).');
            $force = $this->option('force');
            if ($force) {
                $this->warn('Running in --fix mode with --force: skipping interactive confirmation.');
            } else {
                $confirm = $this->confirm('Are you sure you want to delete all pivot rows for this kontainer?');
                if (!$confirm) {
                    $this->info('Aborted by user. No changes made.');
                    return 0;
                }
            }

            DB::transaction(function () use ($id) {
                $deleted = DB::table('tagihan_kontainer_sewa_kontainers')->where('kontainer_id', $id)->delete();
                // optional: you may want to adjust tagihan_kontainer_sewa.status_pembayaran/keterangan here
                $this->info("Deleted pivot rows: $deleted");
            });

            $this->info('Fix complete.');
        }

        $this->info('Done.');
        return 0;
    }
}
