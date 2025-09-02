<?php

namespace App\Services;

use App\Models\Permohonan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class ApprovalService
{
    /**
     * Process a single permohonan: update kontainer status and create/merge tagihan.
     * Returns tagihan id or null.
     */
    public function processPermohonan(Permohonan $permohonan, $dateForTagihan = null)
    {
        // mark selesai
        $permohonan->status = 'Selesai';

        // determine kegiatan and isReturnSewa
        $kegiatanName = \App\Models\MasterKegiatan::where('kode_kegiatan', $permohonan->kegiatan)
            ->value('nama_kegiatan') ?? ($permohonan->kegiatan ?? '');
        $kegiatanLower = strtolower($kegiatanName);
        $isReturnSewa = (stripos($kegiatanLower, 'tarik') !== false && stripos($kegiatanLower, 'sewa') !== false)
            || (stripos($kegiatanLower, 'pengambilan') !== false)
            || ($kegiatanLower === 'pengambilan');

        // determine done date (earliest checkpoint) if not supplied
        if (!$dateForTagihan) {
            if ($permohonan->checkpoints && $permohonan->checkpoints->count()) {
                $dateForTagihan = $permohonan->checkpoints->min('tanggal_checkpoint');
                $dateForTagihan = Carbon::parse($dateForTagihan)->toDateString();
            } else {
                $dateForTagihan = now()->toDateString();
            }
        }

        // update kontainer statuses
        if ($permohonan->kontainers && $permohonan->kontainers->count()) {
            foreach ($permohonan->kontainers as $kontainer) {
                if ($isReturnSewa && in_array($permohonan->vendor_perusahaan, ['ZONA', 'DPE', 'SOC'])) {
                    $kontainer->tanggal_selesai_sewa = $dateForTagihan;
                    $kontainer->status = 'dikembalikan';
                } else {
                    // if incoming delivery with sewa dates set, mark Disewa
                    if ($permohonan->kegiatan == 'pengiriman' && !empty($permohonan->tanggal_masuk_sewa)) {
                        $kontainer->status = 'Disewa';
                        $kontainer->tanggal_masuk_sewa = $permohonan->tanggal_masuk_sewa;
                        $kontainer->tanggal_selesai_sewa = $permohonan->tanggal_selesai_sewa ?? null;
                    } else {
                        $kontainer->status = 'Tersedia';
                    }
                }
                $kontainer->save();
            }
        }

    // Tagihan creation removed: approval processing no longer auto-creates/merges tagihan rows.
    // Keep a placeholder variable for compatibility with callers.
    $tagihanId = null;
        $permohonan->save();
        return $tagihanId;
    }

    /**
     * Bulk process permohonan ids. Returns counts array.
     */
    public function processBulk(array $permohonanIds)
    {
        $processed = 0; $inserted = 0; $updated = 0; $skipped = 0; $errors = 0;
        DB::beginTransaction();
        try {
            $rows = Permohonan::whereIn('id', $permohonanIds)->with(['kontainers','checkpoints'])->get();
            foreach ($rows as $p) {
                $pid = $this->processPermohonan($p);
                if ($pid) $processed++;
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('ApprovalService: processBulk failed', ['error' => $e->getMessage()]);
            $errors++;
        }
        return compact('processed','inserted','updated','skipped','errors');
    }

    /**
     * Internal: create or update tagihan row. Returns tagihan id or null.
     */
    protected function createOrUpdateTagihan(Permohonan $permohonan, $dateForTagihan)
    {
        // Tagihan creation logic removed to simplify the approval flow.
        // Keep a no-op helper to maintain backward compatibility with callers.
        Log::debug('ApprovalService::createOrUpdateTagihan is a no-op in simplified mode', [
            'permohonan_id' => $permohonan->id ?? null,
            'dateForTagihan' => $dateForTagihan,
        ]);
        return null;
    }
}
