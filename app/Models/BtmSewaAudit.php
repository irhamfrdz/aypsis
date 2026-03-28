<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BtmSewaAudit extends Model
{
    protected $table = 'btm_sewa_audits';

    protected $fillable = [
        'unit_number',
        'transaction_id',
        'period_name',
        'aypsis_nominal',
        'vendor_nominal',
        'is_approved',
        'notes',
    ];

    public function transaction()
    {
        return $this->belongsTo(BtmSewaTransaction::class, 'transaction_id');
    }
}
