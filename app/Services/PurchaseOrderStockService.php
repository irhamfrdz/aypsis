<?php

namespace App\Services;

use App\Models\PurchaseOrderItem;
use App\Models\StockAmprahan;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PurchaseOrderStockService
{
    public function sync(PurchaseOrderItem $item): void
    {
        $stock = StockAmprahan::where('purchase_order_item_id', $item->id)->first();

        if ($item->masuk_asset) {
            $this->remove($item);

            return;
        }

        $data = [
            'nomor_bukti' => $item->purchaseOrder->nomor_po,
            'tanggal_beli' => $item->purchaseOrder->tanggal_po,
            'nama_barang' => Str::limit($item->deskripsi, 255, ''),
            'type_amprahan' => 'Inventory',
            'harga_satuan' => $item->harga_satuan,
            'satuan' => $item->satuan,
            'updated_by' => auth()->id(),
        ];

        if ($stock) {
            $stock->update($data + ($stock->usages()->exists() ? [] : ['jumlah' => $item->kuantitas]));

            return;
        }

        $cabang = strtoupper((string) auth()->user()?->karyawan?->cabang);
        $lokasi = match ($cabang) {
            'BATAM' => 'KANTOR AYP BATAM',
            'JAKARTA' => 'KANTOR AYP JAKARTA',
            default => null,
        };

        StockAmprahan::create($data + [
            'purchase_order_item_id' => $item->id,
            'jumlah' => $item->kuantitas,
            'lokasi' => $lokasi,
            'lokasi_penyimpanan' => $lokasi,
            'created_by' => auth()->id(),
        ]);
    }

    public function remove(PurchaseOrderItem $item): void
    {
        $stock = StockAmprahan::where('purchase_order_item_id', $item->id)->first();
        if (! $stock) {
            return;
        }

        if ($stock->usages()->exists()) {
            throw ValidationException::withMessages([
                'items' => 'Stok dari PO ini sudah dipakai dan tidak dapat dikeluarkan dari PO.',
            ]);
        }

        $stock->delete();
    }
}
