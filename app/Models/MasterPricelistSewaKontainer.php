<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterPricelistSewaKontainer extends Model
{
    protected $table = 'master_pricelist_sewa_kontainers';
    protected $fillable = [
        'vendor',
        'tarif',
        'ukuran_kontainer',
        'harga',
        'tanggal_harga_awal',
        'tanggal_harga_akhir',
        'keterangan',
    // legacy column present in some test DBs (sqlite); allow mass assignment when present
    'nomor_tagihan',
    ];

    protected $casts = [
        'tanggal_harga_awal' => 'date',
        'tanggal_harga_akhir' => 'date',
    ];

    /**
     * Use date-only format when storing dates so sqlite tests match expected values
     */
    protected $dateFormat = 'Y-m-d';
}
