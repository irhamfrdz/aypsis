<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


use App\Traits\Auditable;
class PranotaTagihanCat extends Model
{
    use HasFactory;

    use Auditable;
    protected $table = "pranota_tagihan_cat";
    protected $fillable = [
        "no_invoice",
        "total_amount",
        "keterangan",
        "supplier",
        "status",
        "tagihan_cat_ids",
        "jumlah_tagihan",
        "tanggal_pranota",
        "due_date"
    ];
    protected $casts = [
        "tagihan_cat_ids" => "array",
        "total_amount" => "decimal:2",
        "tanggal_pranota" => "date",
        "due_date" => "date"
    ];

    public function tagihanCatItems()
    {
        if (empty($this->tagihan_cat_ids)) {
            return collect();
        }
        return TagihanCat::whereIn('id', $this->tagihan_cat_ids)->get();
    }

    public function tagihanCats()
    {
        return $this->belongsToMany(
            TagihanCat::class,
            'pranota_tagihan_cat_items',
            'pranota_tagihan_cat_id',
            'tagihan_cat_id'
        )->withTimestamps();
    }

    public function calculateTotalAmount()
    {
        if (empty($this->tagihan_cat_ids)) {
            return 0;
        }

        $catItems = TagihanCat::whereIn('id', $this->tagihan_cat_ids)->get();
        return $catItems->sum('realisasi_biaya');
    }

    public function updateTotalAmount()
    {
        $this->total_amount = $this->calculateTotalAmount();
        $this->jumlah_tagihan = count($this->tagihan_cat_ids);
        $this->save();
    }

    public function isPaid()
    {
        return $this->status === 'paid';
    }

    public function getStatusLabel()
    {
        if ($this->status === 'paid') {
            return 'Lunas';
        } else {
            return 'Belum Lunas';
        }
    }

    public function getStatusColor()
    {
        if ($this->status === 'paid') {
            return 'bg-green-100 text-green-800';
        } else {
            return 'bg-red-100 text-red-800';
        }
    }

    public function pembayaranPranotaCats()
    {
        return $this->belongsToMany(
            PembayaranPranotaCat::class,
            'pembayaran_pranota_cat_items',
            'pranota_tagihan_cat_id',
            'pembayaran_pranota_cat_id'
        )->withPivot('amount')->withTimestamps();
    }
}
