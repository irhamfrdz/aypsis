<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockAmprahan extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $fillable = [
        'nomor_bukti',
        'master_nama_barang_amprahan_id',
        'nama_barang',
        'type_barang',
        'harga_satuan',
        'jumlah',
        'satuan',
        'lokasi',
        'keterangan',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'harga_satuan' => 'decimal:2',
    ];

    public function masterNamaBarangAmprahan()
    {
        return $this->belongsTo(MasterNamaBarangAmprahan::class, 'master_nama_barang_amprahan_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
