@extends('layouts.app')

@section('title', 'Tagihan Kontainer Sewa')
@section('page_title', 'Tagihan Kontainer Sewa')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-4 text-gray-800">Daftar Tagihan Kontainer Sewa</h2>

    <div class="flex justify-end items-center space-x-3 mb-4">
    <a href="{{ route('tagihan-kontainer-sewa.export') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">Download CSV</a>

    <a href="{{ route('tagihan-kontainer-sewa.template') }}" class="inline-flex items-center px-4 py-2 bg-white text-gray-800 border rounded-lg hover:bg-gray-50">Download Template (via app)</a>

    <!-- Direct public template download for quick edits without permission checks -->
    <a href="/exports/tagihan_kontainer_sewa_template_semicolon.csv" class="inline-flex items-center px-4 py-2 bg-white text-gray-800 border rounded-lg hover:bg-gray-50">Download Template (CSV ;)</a>
    <a href="/exports/tagihan_kontainer_sewa_template.csv" class="inline-flex items-center px-4 py-2 bg-white text-gray-800 border rounded-lg hover:bg-gray-50">Download Template (CSV ,)</a>

        <form action="{{ route('tagihan-kontainer-sewa.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <label class="inline-flex items-center px-4 py-2 bg-white border rounded cursor-pointer text-sm">
                <input type="file" name="csv_file" accept=".csv,text/csv" class="hidden" onchange="this.form.submit()">
                Import CSV
            </label>
        </form>
    </div>

    <!-- Notifikasi Sukses -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Import errors/warnings (jika ada) --}}
    @if(session('import_errors'))
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded mb-4">
            <p class="font-bold text-yellow-800">Beberapa baris gagal diimpor</p>
            <p class="text-sm text-yellow-800">Periksa baris yang dilaporkan di bawah. Pastikan file CSV menggunakan delimiter <strong>;</strong> dan kolom sesuai format: <code>vendor;tarif;ukuran_kontainer;harga;tanggal_harga_awal;periode</code>.</p>
            <ul class="list-disc ml-5 mt-2 text-sm text-yellow-800">
                @foreach(session('import_errors') as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Tabel Daftar Tagihan Kontainer Sewa -->
    <div class="mb-4">
        <label for="tagihan-search" class="sr-only">Cari Tagihan</label>
        <div class="flex items-center space-x-2">
            <input id="tagihan-search" type="search" placeholder="Cari vendor, group, tanggal, keterangan atau nomor kontainer..." class="w-full border rounded-md px-3 py-2" />
            <button id="tagihan-clear" type="button" class="bg-gray-200 text-gray-700 px-3 py-2 rounded-md">Clear</button>
        </div>
        <div id="tagihan-search-results" class="mt-2"></div>
    </div>

    <div class="overflow-x-auto shadow-md sm:rounded-lg">
        <table class="min-w-full bg-white divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                    <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vendor</th>
                    <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarif (tipe)</th>
                    <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Awal</th>
                    <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Periode</th>
                    <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Massa</th>
                    <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Akhir</th>
                    <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                    <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Group</th>
                    <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Kontainer</th>
                    <th class="py-3 px-6 text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap text-right">Total Biaya</th>
                    <th class="py-3 px-6 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 text-gray-700">
        @forelse ($tagihanKontainerSewa as $index => $tagihan)
                            @php
                                // mark pranota rows so we can display them differently instead of skipping
                                $isPranotaRow = false;
                                try { $isPranotaRow = !empty($tagihan->tarif) && strtolower((string)$tagihan->tarif) === 'pranota'; } catch (\Exception $e) { $isPranotaRow = false; }
                        // Defensive helpers
                        $displayIndex = $tagihanKontainerSewa->firstItem() + $index;
                        // Determine tarif type vs numeric value
                        $tarifRaw = $tagihan->tarif ?? null;
                        // default tarif when none provided
                        $tarifType = 'Bulanan';
                        if ($tarifRaw) {
                            $lower = strtolower((string) $tarifRaw);
                            if (in_array($lower, ['bulanan', 'harian'])) {
                                // Standard types from pricelist
                                $tarifType = ucfirst($lower);
                            } elseif ($lower === 'custom' || is_numeric($tarifRaw)) {
                                // Legacy numeric values or explicit 'Custom' should show as Custom
                                $tarifType = 'Custom';
                            } else {
                                // Unknown string stored in tarif - display it capitalized
                                $tarifType = ucfirst($lower);
                            }
                        }
                        // price removed from index; shown in group detail instead

                        // date formatting with fallback
                        try {
                            $tanggalAwal = $tagihan->tanggal_harga_awal ? (method_exists($tagihan->tanggal_harga_awal, 'format') ? $tagihan->tanggal_harga_awal->format('d/m/Y') : \Carbon\Carbon::parse($tagihan->tanggal_harga_awal)->format('d/m/Y')) : '-';
                        } catch (Exception $e) {
                            $tanggalAwal = '-';
                        }
                        try {
                            $tanggalAkhir = $tagihan->tanggal_harga_akhir ? (method_exists($tagihan->tanggal_harga_akhir, 'format') ? $tagihan->tanggal_harga_akhir->format('d/m/Y') : \Carbon\Carbon::parse($tagihan->tanggal_harga_akhir)->format('d/m/Y')) : '-';
                        } catch (Exception $e) {
                            $tanggalAkhir = '-';
                        }

                        // route date parameter for group link (Y-m-d) fallback
                        try {
                            $routeDate = $tagihan->tanggal_harga_awal ? (method_exists($tagihan->tanggal_harga_awal, 'format') ? $tagihan->tanggal_harga_awal->format('Y-m-d') : \Carbon\Carbon::parse($tagihan->tanggal_harga_awal)->format('Y-m-d')) : null;
                        } catch (Exception $e) {
                            $routeDate = null;
                        }

                        // compute 'Massa' display: start date and (end date - 1 day) in Indonesian locale
                        try {
                            $masaStart = '-';
                            $masaEnd = '-';
                            if (!empty($tagihan->tanggal_harga_awal)) {
                                if (method_exists($tagihan->tanggal_harga_awal, 'format')) {
                                    $startObj = method_exists($tagihan->tanggal_harga_awal, 'copy') ? $tagihan->tanggal_harga_awal->copy() : $tagihan->tanggal_harga_awal;
                                } else {
                                    $startObj = \Carbon\Carbon::parse($tagihan->tanggal_harga_awal);
                                }
                                $masaStart = $startObj->locale('id')->isoFormat('D MMMM');
                            }
                            if (!empty($tagihan->tanggal_harga_akhir)) {
                                if (method_exists($tagihan->tanggal_harga_akhir, 'format')) {
                                    $endObj = method_exists($tagihan->tanggal_harga_akhir, 'copy') ? $tagihan->tanggal_harga_akhir->copy()->subDay() : $tagihan->tanggal_harga_akhir->subDay();
                                } else {
                                    $endObj = \Carbon\Carbon::parse($tagihan->tanggal_harga_akhir)->subDay();
                                }
                                $masaEnd = $endObj->locale('id')->isoFormat('D MMMM');
                            } else {
                                // if there's no explicit tanggal_harga_akhir, derive an end from periode (periode months from tanggal_harga_awal, minus 1 day)
                                try {
                                    $p = isset($tagihan->periode) ? intval($tagihan->periode) : 0;
                                    if (!empty($startObj) && $p > 0) {
                                        $derivedEnd = $startObj->copy()->addMonths($p)->subDay();
                                        $masaEnd = $derivedEnd->locale('id')->isoFormat('D MMMM');
                                    }
                                } catch (Exception $e) {
                                    // leave masaEnd as '-'
                                }
                            }
                            if ($masaStart === '-' && $masaEnd === '-') {
                                $massaDisplay = '-';
                            } elseif ($masaEnd === '-') {
                                $massaDisplay = $masaStart;
                            } else {
                                $massaDisplay = $masaStart . ' - ' . $masaEnd;
                            }
                        } catch (Exception $e) {
                            $massaDisplay = '-';
                        }
                    @endphp

                    <tr class="hover:bg-gray-50">
                        <td class="py-4 px-6">{{ $displayIndex }}</td>
                        <td class="py-4 px-6">{{ $tagihan->vendor ?? '-' }}</td>
                        <td class="py-4 px-6">
                            @if($isPranotaRow)
                                <span class="inline-flex items-center px-2 py-1 rounded-full bg-indigo-100 text-indigo-800 text-xs font-semibold">Pranota</span>
                            @else
                                {{ $tarifType ?? '-' }}
                            @endif
                        </td>
                        <!-- Harga moved to group detail view -->
                        <td class="py-4 px-6">
                            @php
                                // Prefer driver's checkpoint date computed in controller; fallback to tanggal_harga_awal
                                try {
                                    if (!empty($tagihan->tanggal_checkpoint_supir)) {
                                        $displayTanggalAwal = method_exists($tagihan->tanggal_checkpoint_supir, 'format') ? $tagihan->tanggal_checkpoint_supir->format('d/m/Y') : \Carbon\Carbon::parse($tagihan->tanggal_checkpoint_supir)->format('d/m/Y');
                                    } else {
                                        $displayTanggalAwal = $tanggalAwal;
                                    }
                                } catch (Exception $e) {
                                    $displayTanggalAwal = $tanggalAwal;
                                }
                            @endphp
                            {{ $displayTanggalAwal }}
                        </td>
                        <td class="py-4 px-6">{{ $tagihan->periode ?? '-' }}</td>
                        <td class="py-4 px-6">{{ $tagihan->masa ?? ($massaDisplay ?? '-') }}</td>
                        <td class="py-4 px-6">{{ $tanggalAkhir }}</td>
                        @php
                            $keteranganDisplay = $tagihan->keterangan ?? '';
                            $pranotaCount = $tagihan->group_pranota_count ?? 0;
                        @endphp
                        <td class="py-3 px-6 align-top">
                            <div class="flex flex-col">
                                @php
                                    // split keterangan into lines, remove empty and duplicate lines
                                    $rawLines = preg_split('/\r\n|\n|\r/', trim((string) $keteranganDisplay));
                                    $cleanLines = array_values(array_filter(array_map('trim', (array) $rawLines)));
                                    $uniqLines = array_values(array_unique($cleanLines));
                                    $showLines = array_slice($uniqLines, 0, 2); // show up to 2 lines
                                    $more = max(0, count($uniqLines) - count($showLines));
                                @endphp

                                @if($isPranotaRow)
                                    <div class="text-sm font-semibold text-indigo-700">Pranota group — {{ $tagihan->group_container_count ?? 0 }} kontainer</div>
                                    @if(!empty($uniqLines))
                                        <div class="text-xs text-indigo-600 mt-1">
                                            @foreach($showLines as $ln)
                                                <div title="{{ $keteranganDisplay }}">{{ $ln }}</div>
                                            @endforeach
                                            @if($more > 0)
                                                <div class="text-xs text-indigo-600">... {{ $more }} lainnya</div>
                                            @endif
                                        </div>
                                    @endif
                                @elseif(!empty($pranotaCount) && $pranotaCount > 0)
                                    <div class="text-sm font-semibold text-gray-800">{{ $pranotaCount }} kontainer sudah masuk pranota</div>
                                    @if(!empty($uniqLines))
                                        <div class="text-xs text-gray-500 mt-1">
                                            @foreach($showLines as $ln)
                                                <div title="{{ $keteranganDisplay }}">{{ $ln }}</div>
                                            @endforeach
                                            @if($more > 0)
                                                <div class="text-xs text-gray-500">... {{ $more }} lainnya</div>
                                            @endif
                                        </div>
                                    @endif
                                @else
                                    <div class="text-sm font-semibold text-gray-800">{{ $showLines[0] ?? '-' }}</div>
                                    @if(!empty($uniqLines))
                                        <div class="text-xs text-gray-500 mt-1">
                                            @if(isset($showLines[1]))
                                                <div title="{{ $keteranganDisplay }}">{{ $showLines[1] }}</div>
                                            @endif
                                            @if($more > 0)
                                                <div class="text-xs text-gray-500">... {{ $more }} lainnya</div>
                                            @endif
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </td>
                        @php
                            // Normalize group code: treat '-' or empty as null so UX shows '-' consistently
                            $gcode = isset($tagihan->group_code) ? trim((string)$tagihan->group_code) : null;
                            if ($gcode === '-' || $gcode === '') $gcode = null;
                        @endphp
                        <td class="py-3 px-6 text-center align-middle">
                            @if(!empty($gcode))
                                @if(!empty($routeDate))
                                    <a href="{{ route('tagihan-kontainer-sewa.group.show', ['vendor' => $tagihan->vendor, 'tanggal' => $routeDate]) }}" class="inline-block text-indigo-600 hover:underline px-2 py-1 text-sm">{{ $gcode }}</a>
                                @else
                                    {{ $gcode }}
                                @endif
                            @else
                                -
                            @endif
                        </td>
                        <td class="py-4 px-6 text-center">{{ $tagihan->group_container_count ?? 0 }}</td>
                        <td class="py-4 px-6 whitespace-nowrap text-right">
                            @php
                                // Prefer master-based total to match group detail page
                                $groupTotalMaster = $tagihan->group_total_master ?? 0;
                                $groupTotalDisplay = $groupTotalMaster ? 'Rp&nbsp;' . number_format($groupTotalMaster, 2, ',', '.') : '-';
                            @endphp
                            {!! $groupTotalDisplay !!}
                        </td>
                        <td class="py-4 px-6 text-center">
                            <div class="flex item-center justify-center space-x-2">
                                <!-- Tombol Edit -->
                                <a href="{{ route('tagihan-kontainer-sewa.edit', $tagihan->id) }}" class="bg-yellow-500 text-white py-1 px-3 rounded-md hover:bg-yellow-600 transition-colors duration-200 text-sm">
                                    Edit
                                </a>
                                @php
                                    // show rollover if tanggal_harga_awal older than 1 month
                                    $showRollover = false;
                                    try {
                                        if (!empty($tagihan->tanggal_harga_awal)) {
                                            $dt = method_exists($tagihan->tanggal_harga_awal, 'lt') ? $tagihan->tanggal_harga_awal : \Carbon\Carbon::parse($tagihan->tanggal_harga_awal);
                                            $showRollover = $dt->lessThan(now()->subMonth());
                                        }
                                    } catch (Exception $e) { $showRollover = false; }
                                @endphp
                                @if($showRollover)
                                    <form action="{{ route('tagihan-kontainer-sewa.rollover', $tagihan->id) }}" method="POST" onsubmit="return confirm('Pindahkan tagihan ini ke periode berikutnya? Data lama tidak akan dihapus.');">
                                        @csrf
                                        <button type="submit" class="bg-blue-500 text-white py-1 px-3 rounded-md hover:bg-blue-600 transition-colors duration-200 text-sm">Roll Over</button>
                                    </form>
                                @endif
                                <!-- Tombol Hapus -->
                                <form action="{{ route('tagihan-kontainer-sewa.destroy', $tagihan->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-500 text-white py-1 px-3 rounded-md hover:bg-red-600 transition-colors duration-200 text-sm">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="py-4 px-6 text-center text-gray-500">
                            Tidak ada data tagihan kontainer sewa yang ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">
    {{ $tagihanKontainerSewa->links() }}
    </div>
</div>
@endsection

@push('scripts')
<script>
(function(){
    function debounce(fn, wait){
        var t;
        return function(){
            var args = arguments, ctx = this;
            clearTimeout(t);
            t = setTimeout(function(){ fn.apply(ctx, args); }, wait);
        };
    }

    function initSearch(){
        var input = document.getElementById('tagihan-search');
        var clearBtn = document.getElementById('tagihan-clear');
        if (!input) return;
        var table = document.querySelector('table.min-w-full');
        if (!table) return;
        var tbody = table.querySelector('tbody');

        function filterRows(){
            var q = String(input.value || '').trim().toLowerCase();
            var rows = tbody.querySelectorAll('tr');
            if (q === '') {
                rows.forEach(function(r){ r.style.display = ''; });
                return;
            }
            rows.forEach(function(r){
                var text = (r.textContent || '').toLowerCase();
                r.style.display = text.indexOf(q) !== -1 ? '' : 'none';
            });
        }

        var debounced = debounce(filterRows, 180);
        input.addEventListener('input', debounced);
        clearBtn && clearBtn.addEventListener('click', function(){ input.value = ''; filterRows(); input.focus(); });

        // AJAX search for container numbers: heuristic => numeric-ish and length >= 5
        var resultsContainer = document.getElementById('tagihan-search-results');
        function isContainerLike(q){
            if (!q) return false;
            var digits = q.replace(/\D/g,'');
            return digits.length >= 5;
        }

        var ajaxTimer = null;
        input.addEventListener('input', function(){
            var q = String(input.value || '').trim();
            if (!isContainerLike(q)) { resultsContainer.innerHTML = ''; return; }
            clearTimeout(ajaxTimer);
            ajaxTimer = setTimeout(function(){
                fetch("{{ route('tagihan-kontainer-sewa.search_by_kontainer') }}?q=" + encodeURIComponent(q), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(function(r){ return r.json(); })
                        .then(function(json){
                            var items = (json.data || []);
                            // Clear any floating results box
                            resultsContainer.innerHTML = '';

                            var matchSet = new Set(items.map(function(it){ return (it.vendor || '') + '|' + (it.tanggal || ''); }));
                            var rows = tbody.querySelectorAll('tr');

                            if (!items.length) {
                                // show a single message row in the table
                                rows.forEach(function(r){ r.style.display = 'none'; });
                                var noRow = document.createElement('tr');
                                noRow.className = '';
                                var td = document.createElement('td');
                                td.setAttribute('colspan', '11');
                                td.className = 'py-4 px-6 text-center text-gray-500';
                                td.textContent = 'Tidak ada grup ditemukan untuk nomor kontainer ini.';
                                noRow.appendChild(td);
                                tbody.appendChild(noRow);
                                return;
                            }

                            // iterate rows: keep only rows whose group link points to one of the returned groups
                            rows.forEach(function(r){
                                // find anchor in Group column (8th column)
                                var groupCell = r.querySelector('td:nth-child(8) a');
                                if (!groupCell) { r.style.display = 'none'; return; }
                                try {
                                    var link = document.createElement('a');
                                    link.href = groupCell.href;
                                    var parts = link.pathname.split('/').filter(function(p){ return p !== ''; });
                                    // expected: ['tagihan-kontainer-sewa','group','{vendor}','{tanggal}']
                                    var vendor = parts[2] || '';
                                    var tanggal = parts[3] || '';
                                    var key = decodeURIComponent(vendor) + '|' + decodeURIComponent(tanggal);
                                    if (matchSet.has(key)) {
                                        r.style.display = '';
                                    } else {
                                        r.style.display = 'none';
                                    }
                                } catch (e) {
                                    r.style.display = 'none';
                                }
                            });
                        }).catch(function(){
                            resultsContainer.innerHTML = '<div class="text-sm text-red-600">Error searching</div>'; });
            }, 250);
        });
    }

    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initSearch); else initSearch();
})();
</script>
@endpush
