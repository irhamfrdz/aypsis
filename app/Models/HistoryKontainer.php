<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoryKontainer extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $fillable = [
        'nomor_kontainer',
        'tipe_kontainer',
        'jenis_kegiatan',
        'tanggal_kegiatan',
        'gudang_id',
        'keterangan',
        'created_by'
    ];

    protected $casts = [
        'tanggal_kegiatan' => 'date',
    ];

    public function gudang()
    {
        return $this->belongsTo(Gudang::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
