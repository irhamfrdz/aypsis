<?php

namespace Tests\Feature;

use App\Models\NamaStockBan;
use App\Models\StockBanDalam;
use Tests\TestCase;

class StockBanSummaryTest extends TestCase
{
    public function test_other_stock_cards_show_saved_quantities_grouped_by_item_and_unit(): void
    {
        $stocks = collect();
        foreach ([
            [1, 'POMPA AIR ALKON BESAR', 2, 'unit'],
            [1, 'POMPA AIR ALKON BESAR', 3, 'UNIT'],
            [2, 'POMPA AIR ALKON KECIL', 0, 'pcs'],
            [3, 'PULLEY CRANE', 7, 'set'],
            [3, 'PULLEY CRANE', 4, 'pcs'],
            [4, 'CAT PUTIH', 10, 'pail'],
            [5, 'MAJUN', 1, 'kg'],
            [6, 'THINNER', 70, 'liter'],
        ] as [$id, $name, $qty, $type]) {
            $stock = new StockBanDalam(['nama_stock_ban_id' => $id, 'qty' => $qty, 'type' => $type]);
            $stock->setRelation('namaStockBan', new NamaStockBan(['nama' => $name]));
            $stocks->push($stock);
        }

        $html = view('stock-ban.partials.other-stock-summary', compact('stocks'))->render();
        $dom = new \DOMDocument;
        @$dom->loadHTML('<?xml encoding="utf-8" ?>'.$html);
        $xpath = new \DOMXPath($dom);
        $cards = $xpath->query('//*[@data-filter-name]');
        $this->assertCount(3, $cards);
        $this->assertSame('POMPA AIR ALKON BESAR 5 UNIT', preg_replace('/\s+/', ' ', trim($cards->item(0)->textContent)));
        $this->assertSame('POMPA AIR ALKON KECIL 0 PCS', preg_replace('/\s+/', ' ', trim($cards->item(1)->textContent)));
        $this->assertSame('PULLEY CRANE 7 SET + 4 PCS', preg_replace('/\s+/', ' ', trim($cards->item(2)->textContent)));
        $this->assertStringContainsString('filterBarangLainnya(this.dataset.filterName, this)', $html);
    }
}
