<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


use App\Traits\Auditable;
class PranotaTagihanKontainerSewa extends Model
{
    use HasFactory;

    use Auditable;
    protected $table = "pranota_tagihan_kontainer_sewa";
    protected $fillable = [
        "no_invoice",
        "total_amount",
        "keterangan",
        "status",
        "tagihan_kontainer_sewa_ids",
        "jumlah_tagihan",
        "tanggal_pranota",
        "due_date"
    ];
    protected $casts = [
        "tagihan_kontainer_sewa_ids" => "array",
        "total_amount" => "decimal:2",
        "tanggal_pranota" => "date",
        "due_date" => "date"
    ];

    public function tagihanKontainerSewaItems()
    {
        $ids = $this->tagihan_kontainer_sewa_ids;
        if (empty($ids) || !is_array($ids)) {
            return collect();
        }
        return DaftarTagihanKontainerSewa::whereIn('id', $ids)->get();
    }

    public function calculateTotalAmount()
    {
        $ids = $this->tagihan_kontainer_sewa_ids;
        if (empty($ids) || !is_array($ids)) {
            return 0;
        }

        $tagihanItems = DaftarTagihanKontainerSewa::whereIn('id', $ids)->get();
        return $tagihanItems->sum('grand_total');
    }

    public function updateTotalAmount()
    {
        $this->total_amount = $this->calculateTotalAmount();
        $ids = $this->tagihan_kontainer_sewa_ids;
        $this->jumlah_tagihan = is_array($ids) ? count($ids) : 0;
        $this->save();
    }

    public function generateNomorPranota()
    {
        if (empty($this->no_invoice)) {
            $year = date('y');
            $month = date('m');
            $latest = self::whereYear('created_at', date('Y'))
                         ->whereMonth('created_at', date('m'))
                         ->where('no_invoice', 'like', "PTKS{$year}{$month}%")
                         ->orderBy('no_invoice', 'desc')
                         ->first();
            
            if ($latest && preg_match("/PTKS{$year}{$month}(\d+)$/", $latest->no_invoice, $matches)) {
                $nextNumber = intval($matches[1]) + 1;
            } else {
                $nextNumber = 1;
            }
            
            $this->no_invoice = sprintf("PTKS%s%s%04d", $year, $month, $nextNumber);
            $this->save();
        }
        return $this->no_invoice;
    }

    public function isPaid()
    {
        return $this->status === 'paid';
    }

    public function getStatusLabel()
    {
        if ($this->status === 'paid') {
            return 'Sudah Dibayar';
        } elseif ($this->status === 'cancelled') {
            return 'Dibatalkan';
        } else {
            return 'Belum Dibayar';
        }
    }

    public function getStatusColor()
    {
        if ($this->status === 'paid') {
            return 'bg-green-100 text-green-800';
        } elseif ($this->status === 'cancelled') {
            return 'bg-red-100 text-red-800';
        } else {
            return 'bg-yellow-100 text-yellow-800';
        }
    }

    public function getPaymentDate()
    {
        $paymentItem = $this->paymentItem()->first();
        if ($paymentItem && $paymentItem->pembayaran) {
            return $paymentItem->pembayaran->tanggal_pembayaran;
        }
        return null;
    }

    /**
     * Get payment item relationship
     */
    public function paymentItem()
    {
        return $this->hasOne(\App\Models\PembayaranPranotaKontainerItem::class, 'pranota_id');
    }

    /**
     * Get payment information
     */
    public function payment()
    {
        $paymentItem = $this->paymentItem()->first();
        if ($paymentItem) {
            return $paymentItem->pembayaran;
        }
        return null;
    }
}
