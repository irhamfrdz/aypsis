<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AsuransiTandaTerimaBatch extends Model
{
    use Auditable, HasFactory, SoftDeletes;

    protected $table = 'asuransi_tanda_terima_batches';

    protected $fillable = [
        'nomor_polis',
        'tanggal_polis',
        'vendor_asuransi_id',
        'total_nilai_barang',
        'asuransi_rate',
        'premi',
        'biaya_admin',
        'grand_total',
        'keterangan',
        'asuransi_path',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tanggal_polis' => 'date',
        'total_nilai_barang' => 'decimal:2',
        'asuransi_rate' => 'decimal:5',
        'premi' => 'decimal:2',
        'biaya_admin' => 'decimal:2',
        'grand_total' => 'decimal:2',
    ];

    public function items()
    {
        return $this->hasMany(AsuransiTandaTerimaBatchItem::class, 'batch_id');
    }

    public function vendorAsuransi()
    {
        return $this->belongsTo(VendorAsuransi::class, 'vendor_asuransi_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
