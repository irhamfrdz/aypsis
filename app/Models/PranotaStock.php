<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PranotaStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_pranota',
        'tanggal_pranota',
        'nomor_accurate',
        'vendor',
        'bank',
        'rekening',
        'penerima',
        'adjustment',
        'keterangan',
        'items',
        'status',
        'created_by',
    ];

    protected $casts = [
        'items' => 'array',
        'tanggal_pranota' => 'date',
        'adjustment' => 'decimal:2',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function pembayaranPranotaStocks()
    {
        return $this->belongsToMany(
            PembayaranPranotaStock::class,
            'pembayaran_pranota_stock_items',
            'pranota_stock_id',
            'pembayaran_pranota_stock_id'
        )->withPivot('subtotal')->withTimestamps();
    }

    public function getHydratedItemsAttribute()
    {
        $items = $this->items;
        if (is_array($items)) {
            $itemIds = collect($items)->pluck('id')->filter()->toArray();
            $stockItems = \App\Models\StockAmprahan::with(['masterNamaBarangAmprahan'])
                ->whereIn('id', $itemIds)
                ->get()
                ->keyBy('id');

            return array_map(function ($it) use ($stockItems) {
                $id = $it['id'] ?? null;
                if ($id && isset($stockItems[$id])) {
                    $item = $stockItems[$id];

                    return array_merge($it, [
                        'nama_barang' => $item->nama_barang ?? ($item->masterNamaBarangAmprahan->nama_barang ?? ($it['nama_barang'] ?? '-')),
                        'harga' => $item->harga_satuan ?? ($it['harga'] ?? 0),
                        'adjustment' => $item->adjustment ?? ($it['adjustment'] ?? 0),
                        'satuan' => $item->satuan ?? ($it['satuan'] ?? '-'),
                    ]);
                }

                return $it;
            }, $items);
        }
        return $items;
    }
}
