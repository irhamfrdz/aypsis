<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PranotaObMuatTemas extends Model
{
    protected $table = 'pranota_ob_muat_temas';

    protected $fillable = [
        'nomor_pranota', 'tanggal_pranota', 'nama_kapal', 'no_voyage',
        'nomor_accurate', 'nominal', 'adjustment', 'grand_total', 'keterangan', 'created_by', 'status',
    ];

    protected $casts = [
        'tanggal_pranota' => 'date',
        'nominal' => 'decimal:2',
        'adjustment' => 'decimal:2',
        'grand_total' => 'decimal:2',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(PranotaObMuatTemasItem::class, 'pranota_ob_muat_temas_id');
    }

    public function calculateTotalAmount(): float
    {
        return (float) $this->grand_total;
    }

    public function getEnrichedItems(): array
    {
        return $this->items->map(function ($item) {
            $snapshot = $item->snapshot;

            return [
                'nomor_kontainer' => $snapshot->nomor_kontainer ?? '-',
                'nama_barang' => $snapshot->barang ?? '-',
                'supir' => $snapshot->nama_supir ?? '-',
                'size' => $snapshot->size_kontainer ?? '-',
                'biaya' => (float) ($snapshot->biaya ?? 0),
            ];
        })->all();
    }

    /** Allocate header adjustment proportionally for the driver's payment breakdown. */
    public function getPaymentItems(): array
    {
        $items = $this->getEnrichedItems();
        $nominal = array_sum(array_column($items, 'biaya'));
        $remaining = round($this->calculateTotalAmount() - $nominal, 2);
        $adjustment = $remaining;
        $last = count($items) - 1;
        foreach ($items as $index => &$item) {
            $share = $index === $last ? $remaining : round($nominal > 0 ? $adjustment * $item['biaya'] / $nominal : $adjustment / count($items), 2);
            $item['biaya'] = round($item['biaya'] + $share, 2);
            $remaining = round($remaining - $share, 2);
        }
        unset($item);

        return $items;
    }
}
