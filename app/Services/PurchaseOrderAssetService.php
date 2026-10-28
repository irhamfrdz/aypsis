<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Illuminate\Support\Str;

class PurchaseOrderAssetService
{
    public function sync(PurchaseOrderItem $item, PurchaseOrder $order): void
    {
        $asset = Asset::withTrashed()
            ->where('source_table', 'purchase_order_items')
            ->where('source_id', $item->id)
            ->first();

        if (! $item->masuk_asset) {
            if ($asset && ! $asset->trashed()) {
                $asset->delete();
            }

            return;
        }

        $asset ??= new Asset;
        $asset->fill([
            'source_table' => 'purchase_order_items',
            'source_id' => $item->id,
            'kode_asset' => 'AST-PO-'.$item->id,
            'nama_asset' => Str::limit($item->deskripsi, 255, ''),
            'kategori' => $asset->kategori ?: 'Lain-lain',
            'kuantitas' => $item->kuantitas,
            'satuan' => $item->satuan,
            'vendor' => Str::limit($order->vendor, 150, ''),
            'nomor_faktur' => $order->nomor_po,
            'updated_by' => auth()->id(),
        ]);
        if (! $asset->exists) {
            $asset->created_by = auth()->id();
        }
        if ($asset->trashed()) {
            $asset->deleted_at = null;
        }
        $asset->save();
    }

    public function remove(PurchaseOrderItem $item): void
    {
        Asset::query()
            ->where('source_table', 'purchase_order_items')
            ->where('source_id', $item->id)
            ->first()?->delete();
    }
}
