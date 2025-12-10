<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pranota extends Model
{
    protected $table = 'pranotalist';
    protected $fillable = [
        'nomor_pranota',
        'tanggal_pranota',
        'jenis_pranota',
        'tagihan_ids',
        'total_amount',
        'jumlah_tagihan',
        'status',
        'keterangan'
    ];

    protected $casts = [
        'tagihan_ids' => 'array',
        'tanggal_pranota' => 'date',
        'total_amount' => 'decimal:2'
    ];

    // Factory methods for creating specific types of pranota
    public static function forKontainerSewa()
    {
        return new PranotaTagihanKontainerSewa();
    }

    public static function forSuratJalan()
    {
        return new PranotaSuratJalan();
    }

    public static function forUangJalan()
    {
        return new PranotaUangJalan();
    }

    public static function forPerbaikanKontainer()
    {
        return new PranotaPerbaikanKontainer();
    }

    public function getTagihanItems()
    {
        if (empty($this->tagihan_ids)) {
            return collect();
        }
        return \App\Models\DaftarTagihanKontainerSewa::whereIn('id', $this->tagihan_ids)->get();
    }

    public function tagihan()
    {
        if (empty($this->tagihan_ids)) {
            return collect();
        }
        return \App\Models\DaftarTagihanKontainerSewa::whereIn('id', $this->tagihan_ids)->get();
    }

    public function calculateTotalAmount()
    {
        if (empty($this->tagihan_ids)) {
            return 0;
        }

        // First check if there are CAT items
        $catItems = \App\Models\TagihanCat::whereIn('id', $this->tagihan_ids)->get();
        if ($catItems->isNotEmpty()) {
            return $catItems->sum('realisasi_biaya');
        }

        // Check if there are perbaikan kontainer items
        $perbaikanItems = \App\Models\PerbaikanKontainer::whereIn('id', $this->tagihan_ids)->get();
        if ($perbaikanItems->isNotEmpty()) {
            return $perbaikanItems->sum('realisasi_biaya_perbaikan');
        }

        // If no CAT or perbaikan items, check kontainer sewa items
        $tagihanItems = $this->getTagihanItems();
        return $tagihanItems->sum('grand_total');
    }

    public function updateTotalAmount()
    {
        $this->total_amount = $this->calculateTotalAmount();
        $this->jumlah_tagihan = count($this->tagihan_ids);
        $this->save();
    }

    public function pembayaranKontainer()
    {
        return $this->belongsToMany(
            \App\Models\PembayaranPranotaKontainer::class,
            'pembayaran_pranota_kontainer_items',
            'pranota_id',
            'pembayaran_pranota_kontainer_id'
        )->withPivot('amount', 'keterangan')->withTimestamps();
    }

    public function tagihanCatItems()
    {
        return $this->belongsToMany(
            \App\Models\TagihanCat::class,
            'pranota_tagihan_cat_items',
            'pranota_id',
            'tagihan_cat_id'
        )->withTimestamps();
    }

    public function tagihanKontainerSewa()
    {
        if (empty($this->tagihan_ids)) {
            return collect();
        }
        return \App\Models\DaftarTagihanKontainerSewa::whereIn('id', $this->tagihan_ids);
    }

    public function perbaikanKontainer()
    {
        // Since tagihan_ids contains IDs from various tables including perbaikan_kontainer
        // We need to filter PerbaikanKontainer where ID is in tagihan_ids
        if (empty($this->tagihan_ids)) {
            return collect();
        }
        return \App\Models\PerbaikanKontainer::whereIn('id', $this->tagihan_ids)->get();
    }

    public function getLatestPayment()
    {
        return $this->pembayaranKontainer()->latest()->first();
    }

    public function isPaid()
    {
        return $this->status === 'paid';
    }

    public function hasPaymentPending()
    {
        return $this->pembayaranKontainer()
            ->where('status', 'pending')
            ->exists();
    }

    public function getStatusLabel()
    {
        if ($this->status === 'paid') {
            return 'Sudah Dibayar';
        } else {
            return 'Belum Dibayar';
        }
    }

    public function getSimplePaymentStatus()
    {
        if ($this->status === 'paid') {
            return 'Sudah Dibayar';
        } else {
            return 'Belum Dibayar';
        }
    }

    public function getSimplePaymentStatusColor()
    {
        if ($this->status === 'paid') {
            return 'bg-green-100 text-green-800';
        } else {
            return 'bg-red-100 text-red-800';
        }
    }

    public function getPaymentStatus()
    {
        if ($this->status === 'paid') {
            return 'Sudah Dibayar';
        } elseif ($this->hasPaymentPending()) {
            return 'Pembayaran Pending';
        } else {
            return 'Belum Dibayar';
        }
    }

    public function getPaymentStatusColor()
    {
        if ($this->status === 'paid') {
            return 'bg-green-100 text-green-800';
        } elseif ($this->hasPaymentPending()) {
            return 'bg-yellow-100 text-yellow-800';
        } else {
            return 'bg-red-100 text-red-800';
        }
    }

    public function getPaymentDate()
    {
        if ($this->status === 'paid') {
            $payment = $this->pembayaranKontainer()
                ->where('status', 'approved')
                ->orderBy('tanggal_persetujuan', 'desc')
                ->first();

            if ($payment && $payment->tanggal_persetujuan) {
                return \Carbon\Carbon::parse($payment->tanggal_persetujuan);
            }
        }
        return null;
    }

    public function getAllPayments()
    {
        return $this->pembayaranKontainer()
            ->orderBy('tanggal_persetujuan', 'desc')
            ->get();
    }

    public function getTotalPaidAmount()
    {
        return $this->pembayaranKontainer()
            ->where('status', 'approved')
            ->sum('pembayaran_pranota_kontainer_items.amount');
    }

    public function getRemainingAmount()
    {
        $totalPaid = $this->getTotalPaidAmount();
        return max(0, $this->total_amount - $totalPaid);
    }

    public function isFullyPaid()
    {
        return $this->getRemainingAmount() <= 0;
    }

    public function canCreatePayment()
    {
        return $this->status === 'unpaid' && !$this->hasPaymentPending();
    }

    public function tagihanCat()
    {
        return $this->belongsToMany(
            \App\Models\TagihanCat::class,
            'pranota_tagihan_cat_items',
            'pranota_id',
            'tagihan_cat_id'
        )->withTimestamps();
    }
}
