<?php

use App\Models\AuditLog;
use App\Models\BiayaBensin;
use App\Models\MasterKartuBensinBatam;
use App\Services\BiayaBensinCardService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function (): void {
            $expense = BiayaBensin::query()->lockForUpdate()->find(298);
            if (! $expense) {
                return;
            }

            $cardNumber = '20000063107';
            $amount = 4750000;
            $created = AuditLog::query()->where('auditable_type', BiayaBensin::class)
                ->where('auditable_id', $expense->id)->where('action', 'created')->sole();
            $removed = AuditLog::query()->where('auditable_type', BiayaBensin::class)
                ->where('auditable_id', $expense->id)->where('action', 'updated')->get()
                ->contains(fn (AuditLog $audit) =>
                    data_get($audit->old_values, 'nomor_kartu') === $cardNumber
                    && blank(data_get($audit->new_values, 'nomor_kartu'))
                );
            if (filled($expense->nomor_kartu)
                || data_get($created->new_values, 'nomor_kartu') !== $cardNumber
                || abs((float) data_get($created->new_values, 'biaya') - $amount) >= 0.01
                || ! $removed) {
                throw new RuntimeException('Data BBS-000298 tidak cocok dengan audit perubahan kartu.');
            }

            $card = MasterKartuBensinBatam::query()
                ->where('nomor_kartu', $cardNumber)->lockForUpdate()->firstOrFail();
            $histories = $card->histories()
                ->where('nomor_transaksi', $expense->nomor_transaksi)
                ->orderBy('id')->lockForUpdate()->get();
            if ($histories->isEmpty()) {
                return; // Sudah diperbaiki.
            }
            if ($histories->count() !== 2
                || $histories->pluck('tipe')->all() !== ['berkurang', 'bertambah']
                || $histories->contains(fn ($history) => abs((float) $history->nominal - $amount) >= 0.01)
                || $histories->first()->keterangan !== 'Pengisian bensin Kendaraan -'
                || $histories->last()->keterangan !== 'Koreksi biaya bensin BBS-000298') {
                throw new RuntimeException('Pasangan riwayat kartu BBS-000298 tidak sesuai.');
            }

            app(BiayaBensinCardService::class)->removeUsage($expense, $cardNumber);
        });
    }

    public function down(): void
    {
        // Riwayat yang dihapus tidak dipulihkan otomatis.
    }
};
