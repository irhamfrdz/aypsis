<?php

namespace App\Services;

use App\Models\BiayaKapal;
use App\Models\PembayaranBiayaKapal;
use App\Models\PembayaranBiayaKapalItem;
use Illuminate\Validation\ValidationException;

class TemasPaymentService
{
    public function isTemas(BiayaKapal $invoice): bool
    {
        return $invoice->temasDetails()->exists();
    }

    private function activeItems(BiayaKapal $invoice)
    {
        return PembayaranBiayaKapalItem::where('biaya_kapal_id', $invoice->id)
            ->whereHas('pembayaran', fn ($query) => $query->where('status_pembayaran', 'paid'));
    }

    // Perform monetary comparisons in integer cents, never formatted currency strings.
    public function cents($amount): int
    {
        return (int) round((float) $amount * 100);
    }

    private function decimal(int $cents): string
    {
        return number_format($cents / 100, 2, '.', '');
    }

    public function summary(BiayaKapal $invoice): array
    {
        $total = $this->cents($invoice->temasDetails()->sum('grand_total'));
        $items = $this->activeItems($invoice)->with('pembayaran')->orderBy('id')->get();
        $paid = $items->sum(fn ($item) => $this->cents($item->nominal));
        $dp = $items->firstWhere('payment_mode', 'dp');

        return [
            'biaya_kapal_id' => $invoice->id,
            'nilai_tagihan' => $this->decimal($total),
            'total_dibayar' => $this->decimal($paid),
            'sisa_pembayaran' => $this->decimal(max(0, $total - $paid)),
            'status' => $invoice->status_pembayaran === 'cancelled' ? 'cancelled'
                : ($total > 0 && $paid >= $total ? 'lunas' : ($paid > 0 ? 'dp' : 'belum_dibayar')),
            'dp_item_id' => $dp?->id,
            'riwayat' => $items->map(fn ($item) => [
                'id' => $item->id,
                'pembayaran_id' => $item->pembayaran_biaya_kapal_id,
                'nomor_pembayaran' => $item->pembayaran->nomor_pembayaran,
                'tanggal_pembayaran' => $item->pembayaran->tanggal_pembayaran->format('Y-m-d'),
                'payment_mode' => $item->payment_mode,
                'dp_item_id' => $item->dp_item_id,
                'nominal' => $item->nominal,
            ])->all(),
        ];
    }

    /** Caller must hold the invoice row lock until payment + item + status are committed. */
    public function prepare(BiayaKapal $invoice, string $mode, $amount, ?int $dpItemId, string $date): array
    {
        if (! $this->isTemas($invoice) || ! in_array($mode, ['lunas', 'dp', 'pelunasan_dp'], true)) {
            $this->reject('payment_mode', 'DP dan pelunasan ini hanya berlaku untuk invoice TEMAS.');
        }
        if (in_array($invoice->status_pembayaran, ['paid', 'cancelled'], true)) {
            $this->reject('biaya_kapal_ids', 'Invoice sudah lunas atau dibatalkan.');
        }
        $summary = $this->summary($invoice);
        $total = $this->cents($summary['nilai_tagihan']);
        $paid = $this->cents($summary['total_dibayar']);
        $remaining = $total - $paid;
        if ($remaining <= 0) {
            $this->reject('biaya_kapal_ids', 'Invoice tidak memiliki sisa tagihan yang dapat dibayar.');
        }
        if ($mode === 'dp') {
            $payment = $this->cents($amount);
            if ($paid > 0 || $payment <= 0 || $payment >= $total || $dpItemId !== null) {
                $this->reject('nominal_dp', 'DP hanya dapat dibuat sekali, harus lebih dari nol dan lebih kecil dari nilai tagihan.');
            }
        } elseif ($mode === 'pelunasan_dp') {
            $dp = $this->activeItems($invoice)->whereKey($dpItemId)->where('payment_mode', 'dp')->first();
            if (! $dp || $paid !== $this->cents($dp->nominal)) {
                $this->reject('dp_item_id', 'Referensi DP tidak aktif, berbeda invoice, atau sudah dilunasi.');
            }
            if ($date < $dp->pembayaran->tanggal_pembayaran->format('Y-m-d')) {
                $this->reject('tanggal_pembayaran', 'Tanggal pelunasan tidak boleh mendahului DP.');
            }
            $payment = $remaining;
        } else {
            if ($paid > 0 || $dpItemId !== null) {
                $this->reject('payment_mode', 'Invoice sudah memiliki DP. Gunakan pelunasan_dp dengan referensi DP tersebut.');
            }
            $payment = $total;
        }

        return [
            'nominal' => $this->decimal($payment),
            'payment_mode' => $mode,
            'dp_item_id' => $mode === 'pelunasan_dp' ? $dpItemId : null,
            'nilai_tagihan' => $this->decimal($total),
            'sisa_setelah_bayar' => $this->decimal($remaining - $payment),
        ];
    }

    public function syncInvoice(BiayaKapal $invoice): void
    {
        $summary = $this->summary($invoice);
        $dp = $this->activeItems($invoice)->where('payment_mode', 'dp')->sum('nominal');
        $invoice->update([
            'dp' => $dp,
            'sisa_pembayaran' => $summary['sisa_pembayaran'],
            // Keep the existing enum: partially paid invoices remain selectable as pending.
            'status_pembayaran' => $summary['status'] === 'lunas' ? 'paid' : 'pending',
        ]);
    }

    public function assertInvoiceEditable(BiayaKapal $invoice): void
    {
        if ($this->isTemas($invoice) && $this->activeItems($invoice)->exists()) {
            $this->reject('temas', 'Invoice TEMAS sudah memiliki pembayaran. Batalkan pelunasan dan DP terlebih dahulu sebelum mengubah atau menghapus invoice.');
        }
    }

    public function assertPaymentCanBeCancelled(PembayaranBiayaKapal $payment): void
    {
        $dpIds = $payment->items()->where('payment_mode', 'dp')->pluck('id');
        if (PembayaranBiayaKapalItem::whereIn('dp_item_id', $dpIds)
            ->whereHas('pembayaran', fn ($query) => $query->where('status_pembayaran', 'paid'))->exists()) {
            $this->reject('payment_mode', 'Batalkan pelunasan terlebih dahulu sebelum membatalkan DP.');
        }
    }

    private function reject(string $field, string $message): never
    {
        throw ValidationException::withMessages([$field => $message]);
    }
}
