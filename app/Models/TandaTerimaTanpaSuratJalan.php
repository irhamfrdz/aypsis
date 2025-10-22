<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TandaTerimaTanpaSuratJalan extends Model
{
    use HasFactory;

    protected $table = 'tanda_terima_tanpa_surat_jalan';

    protected $fillable = [
        'no_tanda_terima',
        'tanggal_tanda_terima',
        'nomor_surat_jalan_customer',
        'nomor_tanda_terima', // Keep both for backward compatibility
        'term_id',
        'aktifitas',
        'no_kontainer',
        'size_kontainer',
        'pengirim',
        'telepon',
        'pic',
        'supir',
        'kenek',
        'no_plat',
        'tujuan_pengiriman',
        'estimasi_naik_kapal',
        'no_seal',
        'penerima',
        'nama_barang',
        'alamat_pengirim',
        'alamat_penerima',
        'jenis_barang',
        'jumlah_barang',
        'satuan_barang',
        'keterangan_barang',
        'berat',
        'satuan_berat',
        // Keep original dimensi fields for backward compatibility
        'panjang',
        'lebar',
        'tinggi',
        'meter_kubik',
        'tonase',
        'status',
        'catatan',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tanggal_tanda_terima' => 'date',
        'berat' => 'decimal:2',
        'panjang' => 'decimal:2',
        'lebar' => 'decimal:2',
        'tinggi' => 'decimal:2',
        'meter_kubik' => 'decimal:6',
        'tonase' => 'decimal:2',
        'jumlah_barang' => 'integer',
    ];

    /**
     * Get the status badge color
     */
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'draft' => 'bg-gray-100 text-gray-800',
            'terkirim' => 'bg-blue-100 text-blue-800',
            'diterima' => 'bg-yellow-100 text-yellow-800',
            'selesai' => 'bg-green-100 text-green-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    /**
     * Get the status label
     */
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'draft' => 'Draft',
            'terkirim' => 'Terkirim',
            'diterima' => 'Diterima',
            'selesai' => 'Selesai',
            default => 'Unknown'
        };
    }

    /**
     * Generate automatic tanda terima number
     */
    public static function generateNoTandaTerima()
    {
        $year = date('Y');
        $month = date('m');

        // Get last number for current month
        $lastRecord = self::whereYear('tanggal_tanda_terima', $year)
                         ->whereMonth('tanggal_tanda_terima', $month)
                         ->orderBy('no_tanda_terima', 'desc')
                         ->first();

        if ($lastRecord) {
            // Extract number from last record
            $lastNumber = (int) substr($lastRecord->no_tanda_terima, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return 'TTTSJ/' . $year . '/' . $month . '/' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Scope for filtering by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for filtering by date range
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal_tanda_terima', [$startDate, $endDate]);
    }

    /**
     * Scope for search
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('no_tanda_terima', 'like', "%{$search}%")
              ->orWhere('penerima', 'like', "%{$search}%")
              ->orWhere('pengirim', 'like', "%{$search}%")
              ->orWhere('jenis_barang', 'like', "%{$search}%")
              ->orWhere('tujuan_pengambilan', 'like', "%{$search}%")
              ->orWhere('tujuan_pengiriman', 'like', "%{$search}%");
        });
    }

    /**
     * Relationship with Term
     */
    public function term()
    {
        return $this->belongsTo(Term::class);
    }

    /**
     * Relationship with Dimensi Items
     */
    public function dimensiItems()
    {
        return $this->hasMany(TandaTerimaDimensiItem::class, 'tanda_terima_tanpa_surat_jalan_id')
                    ->orderBy('item_order');
    }

    /**
     * Calculate total volume from all dimensi items
     */
    public function getTotalVolumeAttribute()
    {
        return $this->dimensiItems()->sum('meter_kubik') ?: $this->meter_kubik ?: 0;
    }

    /**
     * Calculate total tonase from all dimensi items
     */
    public function getTotalTonaseAttribute()
    {
        return $this->dimensiItems()->sum('tonase') ?: $this->tonase ?: 0;
    }
}
