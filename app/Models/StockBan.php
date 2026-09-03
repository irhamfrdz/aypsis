<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockBan extends Model
{
    use Auditable, HasFactory;

    protected $table = 'stock_bans';

    protected $fillable = [
        'nama_stock_ban_id',
        'nomor_seri',
        'nomor_faktur',
        'nomor_bukti',
        'merk',
        'ukuran',
        'kondisi',
        'status',
        'harga_beli',
        'harga_jual',
        'pembeli',
        'tanggal_jual',
        'tempat_beli',
        'tanggal_masuk',
        'tanggal_keluar',
        'tanggal_kembali',
        'lokasi',
        'keterangan',
        'mobil_id',
        'alat_berat_id',
        'penerima_id',
        'penerima_manual',
        'kapal_id',
        'tanggal_kirim',
        'tanggal_digunakan',
        'status_ban_luar',
        'status_masak',
        'jumlah_masak',
        'nomor_bukti_pakai',
        'nomor_kirim',
        'nomor_bukti_jual',
        'nomor_bukti_kembali',
    ];

    public function namaStockBan()
    {
        return $this->belongsTo(NamaStockBan::class, 'nama_stock_ban_id');
    }

    public function mobil()
    {
        return $this->belongsTo(Mobil::class, 'mobil_id');
    }

    public function penerima()
    {
        return $this->belongsTo(Karyawan::class, 'penerima_id');
    }

    public function alatBerat()
    {
        return $this->belongsTo(AlatBerat::class, 'alat_berat_id');
    }

    public function kapal()
    {
        return $this->belongsTo(MasterKapal::class, 'kapal_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    protected $casts = [
        'harga_beli' => 'decimal:2',
        'tanggal_masuk' => 'date',
        'tanggal_keluar' => 'date',
        'tanggal_kembali' => 'date',
        'tanggal_kirim' => 'date',
        'tanggal_jual' => 'date',
        'tanggal_digunakan' => 'date',
    ];

    public static function generateNextInvoice()
    {
        $yearMonth = date('Ym');
        $prefix = 'INV-KS-'.$yearMonth.'-';

        $lastInvoice = self::where('nomor_bukti', 'like', $prefix.'%')
            ->orderBy('nomor_bukti', 'desc')
            ->first();

        if (! $lastInvoice) {
            return $prefix.'001';
        }

        $lastNumber = intval(substr($lastInvoice->nomor_bukti, -3));
        $nextNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);

        return $prefix.$nextNumber;
    }

    public static function generateNextNomorBuktiPakai()
    {
        $yearMonth = date('ym');
        $prefix = 'P'.$yearMonth;

        $lastBukti = self::where('nomor_bukti_pakai', 'like', $prefix.'%')
            ->orderBy('nomor_bukti_pakai', 'desc')
            ->first();

        if (! $lastBukti) {
            return $prefix.'00001';
        }

        $lastNumber = intval(substr($lastBukti->nomor_bukti_pakai, -5));
        $nextNumber = str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);

        return $prefix.$nextNumber;
    }

    /**
     * Generate the next JYYMMXXXXX sequence for Nomor Bukti Jual.
     */
    public static function generateNextNomorBuktiJual()
    {
        $prefix = 'J' . date('ym');
        
        $lastRecord = self::where('nomor_bukti_jual', 'like', $prefix . '%')
            ->orderBy('nomor_bukti_jual', 'desc')
            ->first();
            
        if (!$lastRecord) {
            return $prefix . '00001';
        }
        
        $lastSeq = (int) substr($lastRecord->nomor_bukti_jual, -5);
        $nextSeq = $lastSeq + 1;
        
        return $prefix . str_pad($nextSeq, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Generate the next BYYMMXXXXX sequence for Nomor Bukti Kirim (Batam/Tanjung Pinang).
     */
    public static function generateNextNomorBuktiKirim()
    {
        $prefix = 'B' . date('ym');
        
        $lastRecord = self::where('nomor_kirim', 'like', $prefix . '%')
            ->orderBy('nomor_kirim', 'desc')
            ->first();
            
        if (!$lastRecord) {
            return $prefix . '00001';
        }
        
        $lastSeq = (int) substr($lastRecord->nomor_kirim, -5);
        $nextSeq = $lastSeq + 1;
        
        return $prefix . str_pad($nextSeq, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Generate the next MYYMMXXXXX sequence for Nomor Bukti Kembali.
     */
    public static function generateNextNomorBuktiKembali()
    {
        $prefix = 'M' . date('ym');
        
        $lastRecord = self::where('nomor_bukti_kembali', 'like', $prefix . '%')
            ->orderBy('nomor_bukti_kembali', 'desc')
            ->first();
            
        if (!$lastRecord) {
            return $prefix . '00001';
        }
        
        $lastSeq = (int) substr($lastRecord->nomor_bukti_kembali, -5);
        $nextSeq = $lastSeq + 1;
        
        return $prefix . str_pad($nextSeq, 5, '0', STR_PAD_LEFT);
    }
}
