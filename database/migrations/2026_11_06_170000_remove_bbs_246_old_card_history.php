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
            $expense = BiayaBensin::query()->lockForUpdate()->find(246);
            if (! $expense) {
                return;
            }

            $oldCardNumber = '20000063109';
            $newCardNumber = '20000063107';
            $cardChanged = AuditLog::query()
                ->where('auditable_type', BiayaBensin::class)
                ->where('auditable_id', $expense->id)
                ->where('action', 'updated')
                ->get()
                ->contains(fn (AuditLog $audit) =>
                    data_get($audit->old_values, 'nomor_kartu') === $oldCardNumber
                    && data_get($audit->new_values, 'nomor_kartu') === $newCardNumber
                    && abs((float) data_get($audit->old_values, 'biaya') - 204000) < 0.01
                );
            if ($expense->nomor_kartu !== $newCardNumber || ! $cardChanged) {
                throw new RuntimeException('Kartu BBS-000246 tidak sesuai dengan audit perubahan.');
            }

            $oldCard = MasterKartuBensinBatam::query()
                ->where('nomor_kartu', $oldCardNumber)->lockForUpdate()->firstOrFail();
            $created = AuditLog::query()->where('auditable_type', BiayaBensin::class)
                ->where('auditable_id', $expense->id)->where('action', 'created')->sole();
            $initial = $created->new_values;
            if (data_get($initial, 'nomor_kartu') !== $oldCardNumber
                || abs((float) data_get($initial, 'biaya') - 204000) >= 0.01) {
                throw new RuntimeException('Audit pembuatan BBS-000246 tidak sesuai.');
            }
            $numbered = $oldCard->histories()
                ->where('nomor_transaksi', $expense->nomor_transaksi)
                ->orderBy('id')->lockForUpdate()->get();
            $legacy = $oldCard->histories()->whereNull('nomor_transaksi')
                ->where('tipe', 'berkurang')->where('nominal', 204000)
                ->where('created_by', data_get($initial, 'created_by'))
                ->where('keterangan', 'Pengisian bensin Kendaraan BP8064EJ')
                ->whereBetween('created_at', [
                    $created->created_at->copy()->subMinute(),
                    $created->created_at->copy()->addMinute(),
                ])->lockForUpdate()->get();
            $histories = $numbered->concat($legacy)->sortBy('id')->values();
            if ($histories->isEmpty()) {
                return; // Sudah diperbaiki.
            }
            if ($histories->count() !== 2
                || $histories->pluck('tipe')->all() !== ['berkurang', 'bertambah']
                || $histories->contains(fn ($history) => abs((float) $history->nominal - 204000) >= 0.01)) {
                throw new RuntimeException('Riwayat kartu lama BBS-000246 tidak sesuai.');
            }

            app(BiayaBensinCardService::class)->removeUsage($expense, $oldCardNumber);
        });
    }

    public function down(): void
    {
        // Dua baris riwayat lama tidak dipulihkan otomatis.
    }
};
