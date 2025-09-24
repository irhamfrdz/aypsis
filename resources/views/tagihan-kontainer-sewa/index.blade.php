@extends('layouts.app')

@section('title', 'Tagihan Kontainer Sewa')
@section('page_title', 'Tagihan Kontainer Sewa')

@section('content')
<div class="container mx-auto p-6">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-semibold">Tagihan Kontainer Sewa</h1>
        <div class="flex items-center space-x-2">
            @if(Route::has('tagihan-kontainer-sewa.export'))
                <a href="{{ route('tagihan-kontainer-sewa.export') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">Download CSV</a>
            @else
                <a href="#" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-400 rounded-lg">Download CSV</a>
            @endif

            @if(Route::has('tagihan-kontainer-sewa.template'))
                <a href="{{ route('tagihan-kontainer-sewa.template') }}" class="inline-flex items-center px-4 py-2 bg-white text-gray-800 border rounded-lg hover:bg-gray-50">Download Template</a>
            @else
                <a href="/exports/tagihan_kontainer_sewa_template.csv" class="inline-flex items-center px-4 py-2 bg-white text-gray-800 border rounded-lg hover:bg-gray-50">Download Template</a>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('import_errors'))
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded mb-4">
            <p class="font-bold text-yellow-800">Beberapa baris gagal diimpor</p>
            <ul class="list-disc ml-5 mt-2 text-sm text-yellow-800">
                @foreach(session('import_errors') as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="flex items-center space-x-2 mb-4">
        <input id="tagihan-search" type="search" placeholder="Cari vendor, group, tanggal, keterangan atau nomor kontainer..." class="w-full border rounded-md px-3 py-2" />
        <button id="tagihan-clear" type="button" class="bg-gray-200 text-gray-700 px-3 py-2 rounded-md">Clear</button>
        @if(Route::has('tagihan-kontainer-sewa.import'))
            <form action="{{ route('tagihan-kontainer-sewa.import') }}" method="POST" enctype="multipart/form-data" class="ml-2">
            @csrf
            <label class="inline-flex items-center px-4 py-2 bg-white border rounded cursor-pointer text-sm">
                <input type="file" name="csv_file" accept=".csv,text/csv" class="hidden" onchange="this.form.submit()">
                Import CSV
            </label>
            </form>
        @else
            <label class="inline-flex items-center px-4 py-2 bg-white border rounded cursor-not-allowed text-sm text-gray-400 ml-2">Import CSV (disabled)</label>
        @endif
    </div>

    <!-- Filter Form -->
    <form method="GET" class="mb-4">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 p-4 bg-gray-50 rounded-lg">
            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status Container</label>
                <select name="status" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                    <option value="">Semua Status</option>
                    @foreach(($statusOptions ?? ['ongoing' => 'Container Ongoing', 'selesai' => 'Container Selesai']) as $value => $label)
                        <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Vendor Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Vendor</label>
                <select name="vendor" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                    <option value="">Semua Vendor</option>
                    @foreach(($vendors ?? []) as $vendor)
                        <option value="{{ $vendor }}" {{ request('vendor') == $vendor ? 'selected' : '' }}>
                            {{ $vendor }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Size Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Size</label>
                <select name="size" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                    <option value="">Semua Size</option>
                    @foreach(($sizes ?? []) as $size)
                        <option value="{{ $size }}" {{ request('size') == $size ? 'selected' : '' }}>
                            {{ $size }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Tarif Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tarif</label>
                <select name="tarif" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                    <option value="">Semua Tarif</option>
                    @foreach(($tarifs ?? []) as $tarif)
                        <option value="{{ $tarif }}" {{ request('tarif') == $tarif ? 'selected' : '' }}>
                            {{ $tarif }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Actions -->
            <div class="flex items-end space-x-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">
                    Filter
                </button>
                <a href="{{ request()->url() }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 text-sm">
                    Reset
                </a>
            </div>
        </div>

        <!-- Keep search query -->
        @if(request('q'))
            <input type="hidden" name="q" value="{{ request('q') }}">
        @endif
    </form>

    <div class="overflow-x-auto bg-white rounded-lg shadow">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50 sticky top-0 z-10">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Vendor</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nomor Kontainer</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Group</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Awal</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Akhir</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Periode</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Massa</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">DPP</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">DPP (nilai lain)</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">PPN</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">PPH</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Grand Total</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse (($tagihanKontainerSewa ?? collect()) as $i => $t)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">{{ ($tagihanKontainerSewa instanceof \Illuminate\Pagination\LengthAwarePaginator) ? $tagihanKontainerSewa->firstItem() + $i : $i + 1 }}</td>
                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">{{ $t->vendor ?? '-' }}</td>
                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">{{ $t->nomor_kontainer ?? '-' }}</td>
                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">{{ $t->group ?? '-' }}</td>
                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">{{ isset($t->tanggal_awal) ? (method_exists($t->tanggal_awal,'format') ? $t->tanggal_awal->format('Y-m-d') : \Carbon\Carbon::parse($t->tanggal_awal)->format('Y-m-d')) : '-' }}</td>
                        <td class="px-4 py-2 whitespace-nowrap text-sm">
                            @if(isset($t->tanggal_akhir) && $t->tanggal_akhir)
                                <span class="text-gray-700">{{ method_exists($t->tanggal_akhir,'format') ? $t->tanggal_akhir->format('Y-m-d') : \Carbon\Carbon::parse($t->tanggal_akhir)->format('Y-m-d') }}</span>
                                <span class="ml-2 px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full">Selesai</span>
                            @else
                                <span class="text-gray-400">-</span>
                                <span class="ml-2 px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Ongoing</span>
                            @endif
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">{{ $t->periode ?? '-' }}</td>
                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">{{ $t->masa ?? '-' }}</td>
                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700 text-right">{{ isset($t->dpp) ? number_format($t->dpp,2,',','.') : '-' }}</td>
                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700 text-right">{{ isset($t->dpp_nilai_lain) ? number_format($t->dpp_nilai_lain,2,',','.') : '-' }}</td>
                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700 text-right">{{ isset($t->ppn) ? number_format($t->ppn,2,',','.') : '-' }}</td>
                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700 text-right">{{ isset($t->pph) ? number_format($t->pph,2,',','.') : '-' }}</td>
                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700 text-right">{{ isset($t->grand_total) ? number_format($t->grand_total,2,',','.') : '-' }}</td>
                    </tr>

                    @php
                        // Parse container numbers and sizes into arrays
                        $containers = collect(explode(',', (string)($t->nomor_kontainer ?? '')))
                            ->map(fn($c) => trim($c))
                            ->filter()
                            ->values();
                        // simplified schema: ukuran is not part of tagihan table anymore
                        $sizes = collect([]);
                        $count = $containers->count() ?: 0;
                        // Distribute totals evenly per container when individual values are missing
                        $perGrand = $count ? ((float)($t->grand_total ?? 0) / $count) : null;
                        $perPpn = $count ? ((float)($t->ppn ?? 0) / $count) : null;
                        $perPph = $count ? ((float)($t->pph ?? 0) / $count) : null;
                        $perDpp = $count ? ((float)($t->dpp ?? 0) / $count) : null;
                    @endphp

                    {{-- Simple flat rows: one table row per container (CRUD-like) --}}
                    @foreach($containers as $idx => $cont)
                        @php
                            $size = $sizes->get($idx) ?? $sizes->first() ?? '-';
                            $dppVal = isset($perDpp) ? round($perDpp,2) : null;
                            $ppnVal = isset($perPpn) ? round($perPpn,2) : null;
                            $pphVal = isset($perPph) ? round($perPph,2) : null;
                            $grandVal = isset($perGrand) ? round($perGrand,2) : null;
                        @endphp
                        <tr class="bg-gray-50">
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">&nbsp;</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">&nbsp;</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">{{ $cont }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">{{ $size }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">&nbsp;</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">&nbsp;</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">&nbsp;</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">&nbsp;</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700 text-right">{{ isset($dppVal) ? number_format($dppVal,2,',','.') : '-' }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700 text-right">-</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700 text-right">{{ isset($ppnVal) ? number_format($ppnVal,2,',','.') : '-' }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700 text-right">{{ isset($pphVal) ? number_format($pphVal,2,',','.') : '-' }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700 text-right">{{ isset($grandVal) ? number_format($grandVal,2,',','.') : '-' }}</td>
                        </tr>
                    @endforeach
                @empty
                    <tr>
                        <td colspan="13" class="px-4 py-6 text-center text-sm text-gray-500">Tidak ada data.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(method_exists($tagihanKontainerSewa ?? null, 'links'))
        <div class="mt-4">
            {{ $tagihanKontainerSewa->links() }}
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
(function(){
    function debounce(fn, wait){ var t; return function(){ var args = arguments, ctx = this; clearTimeout(t); t = setTimeout(function(){ fn.apply(ctx, args); }, wait); } }
    function initSearch(){
        var input = document.getElementById('tagihan-search');
        var clearBtn = document.getElementById('tagihan-clear');
        if (!input) return;
        var table = document.querySelector('table.min-w-full');
        if (!table) return;
        var tbody = table.querySelector('tbody');

        function filterRows(){
            var q = String(input.value || '').trim().toLowerCase();
            var rows = Array.prototype.slice.call(tbody.querySelectorAll('tr'));
            if (q === '') { rows.forEach(function(r){ r.style.display = ''; }); return; }
            rows.forEach(function(r){ var text = (r.textContent || '').toLowerCase(); r.style.display = text.indexOf(q) !== -1 ? '' : 'none'; });
        }

        var debounced = debounce(filterRows, 180);
        input.addEventListener('input', debounced);
        clearBtn && clearBtn.addEventListener('click', function(){ input.value = ''; filterRows(); input.focus(); });

        // Lightweight AJAX search for container numbers
        var resultsContainer = document.getElementById('tagihan-search-results');
        function isContainerLike(q){ if (!q) return false; var digits = q.replace(/\D/g,''); return digits.length >= 5; }
        var ajaxTimer = null;
        input.addEventListener('input', function(){
            var q = String(input.value || '').trim();
            if (!isContainerLike(q)) { if (resultsContainer) resultsContainer.innerHTML = ''; return; }
            clearTimeout(ajaxTimer);
            ajaxTimer = setTimeout(function(){
                @if(Route::has('tagihan-kontainer-sewa.search_by_kontainer'))
                    fetch("{{ route('tagihan-kontainer-sewa.search_by_kontainer') }}?q=" + encodeURIComponent(q), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                @else
                    // search endpoint not available on this installation
                    return Promise.resolve({ json: function(){ return { data: [] } } });
                @endif
                    .then(function(r){ return r.json(); })
                    .then(function(json){
                        var items = (json.data || []);
                        if (!items.length) { if (resultsContainer) resultsContainer.innerHTML = '<div class="text-sm text-gray-500">No groups found</div>'; return; }
                        // build a simple summary list
                        if (resultsContainer) {
                            resultsContainer.innerHTML = items.slice(0,6).map(function(it){ return '<div class="text-sm py-1">' + (it.vendor || '-') + ' — ' + (it.tanggal || '-') + '</div>'; }).join('');
                        }
                        // highlight matching rows by vendor+tanggal if group link exists
                        var matchSet = new Set(items.map(function(it){ return (it.vendor || '') + '|' + (it.tanggal || ''); }));
                        var rows = tbody.querySelectorAll('tr');
                        rows.forEach(function(r){
                            var link = r.querySelector('td:nth-child(4) a');
                            if (!link) { r.style.display = 'none'; return; }
                            try {
                                var parts = link.pathname.split('/').filter(function(p){ return p !== ''; });
                                var vendor = parts[2] || '';
                                var tanggal = parts[3] || '';
                                var key = decodeURIComponent(vendor) + '|' + decodeURIComponent(tanggal);
                                r.style.display = matchSet.has(key) ? '' : 'none';
                            } catch (e) { r.style.display = 'none'; }
                        });
                    }).catch(function(){ if (resultsContainer) resultsContainer.innerHTML = '<div class="text-sm text-red-600">Error searching</div>'; });
            }, 250);
        });
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initSearch); else initSearch();
})();
</script>
@endpush
