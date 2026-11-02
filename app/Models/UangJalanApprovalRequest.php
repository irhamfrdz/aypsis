<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UangJalanApprovalRequest extends Model
{
    protected $fillable = [
        'jenis_surat_jalan', 'surat_jalan_id', 'nomor_surat_jalan', 'supir',
        'jumlah_tugas_berjalan', 'data_uang_jalan', 'status', 'requested_by',
        'reviewed_by', 'reviewed_at', 'catatan_approval', 'uang_jalan_id',
    ];

    protected $casts = [
        'data_uang_jalan' => 'array',
        'reviewed_at' => 'datetime',
    ];

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function uangJalan()
    {
        return $this->belongsTo(UangJalan::class);
    }
}
