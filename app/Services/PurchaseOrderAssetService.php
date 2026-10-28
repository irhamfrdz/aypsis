<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\PurchaseOrderItem;

class PurchaseOrderAssetService
{
    public function remove(PurchaseOrderItem $item): void
    {
        Asset::query()
            ->where('source_table', 'purchase_order_items')
            ->where('source_id', $item->id)
            ->first()?->delete();
    }
}
