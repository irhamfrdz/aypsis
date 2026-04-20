<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockAmprahan extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $fillable = [
        'nomor_bukti',
        'tanggal_beli',
        'type_amprahan',
        'status_pranota',
        'master_nama_barang_amprahan_id',
        'nama_barang',
        'type_barang',
        'harga_satuan',
        'adjustment',
        'jumlah',
        'satuan',
        'lokasi',
        'keterangan',
        'vendor_amprahan_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'harga_satuan' => 'decimal:2',
        'adjustment' => 'decimal:2',
        'tanggal_beli' => 'date',
    ];

    public function masterNamaBarangAmprahan()
    {
        return $this->belongsTo(MasterNamaBarangAmprahan::class, 'master_nama_barang_amprahan_id');
    }

    public function vendorAmprahan()
    {
        return $this->belongsTo(VendorAmprahan::class, 'vendor_amprahan_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function usages()
    {
        return $this->hasMany(StockAmprahanUsage::class, 'stock_amprahan_id');
    }
}
