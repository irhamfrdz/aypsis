@php
    // Items with dedicated summary cards are rendered separately in the index.
    $otherStockStats = $stocks->filter(function ($item) {
        $name = $item->namaStockBan->nama ?? '';

        return $name !== ''
            && stripos($name, 'cat') === false
            && stripos($name, 'majun') === false
            && stripos($name, 'thinner') === false;
    })->groupBy('nama_stock_ban_id');
@endphp

@foreach($otherStockStats as $items)
    @php
        $name = strtoupper($items->first()->namaStockBan->nama);
        $units = $items->groupBy(function ($item) {
            return strtoupper(trim($item->type ?? '')) ?: 'PCS';
        })->map(fn ($group) => $group->sum('qty'));
    @endphp
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 transition-all hover:shadow-md cursor-pointer border-b-4 border-b-blue-500 hover:bg-blue-50/30"
         data-filter-name="{{ $name }}" onclick="filterBarangLainnya(this.dataset.filterName, this)">
        <div class="flex items-center gap-3">
            <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-lg bg-blue-50 text-blue-600">
                <i class="fas fa-gears text-lg"></i>
            </div>
            <div class="min-w-0">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-tight truncate" title="{{ $name }}">{{ $name }}</p>
                <div class="flex flex-wrap items-baseline gap-1 mt-1">
                    @foreach($units as $unit => $qty)
                        @unless($loop->first)<span class="text-xs text-gray-400">+</span>@endunless
                        <span class="{{ $units->count() > 1 ? 'text-sm' : 'text-xl' }} font-black text-gray-800">{{ $qty }}</span>
                        <span class="text-[10px] font-medium text-gray-400 uppercase">{{ $unit }}</span>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endforeach
