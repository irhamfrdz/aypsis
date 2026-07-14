<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockAmprahanUsage extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'stock_amprahan_usages';

    protected $fillable = [
        'stock_amprahan_id',
        'penerima_id',
        'kendaraan_id',
        'truck_id',
        'buntut_id',
        'chasis_batam_id',
        'kapal_id',
        'nomor_voyage',
        'alat_berat_id',
        'kantor',
        'kilometer',
        'odometer',
        'jumlah',
        'tanggal_pengambilan',
        'keterangan',
        'created_by',
    ];

    protected $dates = [
        'tanggal_pengambilan',
    ];

    public function stockAmprahan()
    {
        return $this->belongsTo(StockAmprahan::class);
    }

    public function penerima()
    {
        return $this->belongsTo(Karyawan::class, 'penerima_id');
    }

    public function kendaraan()
    {
        return $this->belongsTo(Mobil::class, 'kendaraan_id');
    }

    public function truck()
    {
        return $this->belongsTo(Mobil::class, 'truck_id');
    }

    public function buntut()
    {
        return $this->belongsTo(Mobil::class, 'buntut_id');
    }

    public function chasisBatam()
    {
        return $this->belongsTo(MasterChasisBatam::class, 'chasis_batam_id');
    }

    public function kapal()
    {
        return $this->belongsTo(MasterKapal::class, 'kapal_id');
    }

    public function alatBerat()
    {
        return $this->belongsTo(AlatBerat::class, 'alat_berat_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
