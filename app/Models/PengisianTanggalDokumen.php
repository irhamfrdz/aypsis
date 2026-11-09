<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengisianTanggalDokumen extends Model
{
    protected $table = 'pengisian_tanggal_dokumens';

    protected $fillable = [
        'kapal_id', 'nama_kapal', 'no_voyage', 'jenis_dokumen', 'nomor_dokumen', 'identifier_hash',
        'tanggal_terima_dokumen_jkt_customer', 'tanggal_terima_dokumen_packinglist',
        'tanggal_dokumen_invoice_faktur_pajak', 'tanggal_kirim_dokumen_ppjk',
        'tanggal_terbit_sppb_ppftz', 'tanggal_terima_dokumen_dari_ppjk',
        'tanggal_kirim_dokumen_jakarta', 'tanggal_terima_sj_cabang', 'tanggal_terima_ppftz_cabang',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_terima_dokumen_jkt_customer' => 'date',
            'tanggal_terima_dokumen_packinglist' => 'date',
            'tanggal_dokumen_invoice_faktur_pajak' => 'date',
            'tanggal_kirim_dokumen_ppjk' => 'date',
            'tanggal_terbit_sppb_ppftz' => 'date',
            'tanggal_terima_dokumen_dari_ppjk' => 'date',
            'tanggal_kirim_dokumen_jakarta' => 'date',
            'tanggal_terima_sj_cabang' => 'date',
            'tanggal_terima_ppftz_cabang' => 'date',
        ];
    }
}
