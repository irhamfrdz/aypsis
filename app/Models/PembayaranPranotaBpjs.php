<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembayaranPranotaBpjs extends Model
{
    protected $table = 'pembayaran_pranota_bpjs';

    protected $guarded = ['id'];

    protected $casts = ['tanggal_pembayaran' => 'date', 'total_pembayaran' => 'decimal:2', 'bank_allocations' => 'array'];

    public function pranotas()
    {
        return $this->belongsToMany(PranotaBpjsHeader::class, 'pembayaran_pranota_bpjs_items', 'pembayaran_pranota_bpjs_id', 'pranota_bpjs_header_id')->withPivot('subtotal')->withTimestamps();
    }

    public function bank()
    {
        return $this->belongsTo(Coa::class, 'bank_coa_id');
    }

    public function akunCoa()
    {
        return $this->belongsTo(Coa::class, 'akun_coa_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
