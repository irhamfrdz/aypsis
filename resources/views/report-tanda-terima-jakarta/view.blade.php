@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Report Tanda Terima Jakarta</h1>
            <p class="text-gray-600">Period: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('report.tanda-terima-jakarta.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Change Filter
            </a>
            <a href="{{ route('report.tanda-terima-jakarta.export', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Export Excel
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    @php
        $counts = $data->groupBy('source')->map->count();
        $sudahNaikKapal = $data->where('naik_kapal', true)->count();
        $belumNaikKapal = $data->where('naik_kapal', false)->count();
        $totalData = $data->count();
    @endphp
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="text-sm font-medium text-gray-500 mb-1">Standard</div>
            <div class="text-2xl font-bold text-purple-600">{{ $counts->get('Standard', 0) }}</div>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="text-sm font-medium text-gray-500 mb-1">Tanpa SJ</div>
            <div class="text-2xl font-bold text-blue-600">{{ $counts->get('Tanpa SJ', 0) }}</div>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="text-sm font-medium text-gray-500 mb-1">LCL</div>
            <div class="text-2xl font-bold text-orange-600">{{ $counts->get('LCL', 0) }}</div>
        </div>
        {{-- Clickable stat card → langsung filter Sudah Naik Kapal --}}
        <div onclick="applyFilter('sudah')"
             class="bg-emerald-50 p-6 rounded-xl shadow-sm border border-emerald-200 cursor-pointer hover:bg-emerald-100 hover:shadow-md transition-all group">
            <div class="flex items-center gap-2 mb-1">
                <i class="fas fa-ship text-emerald-500 text-xs group-hover:scale-110 transition-transform"></i>
                <span class="text-sm font-medium text-emerald-700">Sudah Naik Kapal</span>
            </div>
            <div class="text-2xl font-bold text-emerald-600">{{ $sudahNaikKapal }}</div>
            <div class="text-[10px] text-emerald-500 mt-1">Klik untuk filter →</div>
        </div>
        {{-- Clickable stat card → langsung filter Belum Naik Kapal --}}
        <div onclick="applyFilter('belum')"
             class="bg-amber-50 p-6 rounded-xl shadow-sm border border-amber-200 cursor-pointer hover:bg-amber-100 hover:shadow-md transition-all group">
            <div class="flex items-center gap-2 mb-1">
                <i class="fas fa-clock text-amber-500 text-xs group-hover:scale-110 transition-transform"></i>
                <span class="text-sm font-medium text-amber-700">Belum Naik Kapal</span>
            </div>
            <div class="text-2xl font-bold text-amber-600">{{ $belumNaikKapal }}</div>
            <div class="text-[10px] text-amber-500 mt-1">Klik untuk filter →</div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 px-5 py-3 mb-4 flex flex-wrap items-center gap-3">
        <span class="text-sm font-semibold text-gray-600 mr-1">
            <i class="fas fa-filter text-gray-400 mr-1"></i>Filter Status:
        </span>

        <button id="filter-semua" onclick="applyFilter('semua')"
            class="filter-btn active-filter inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full text-sm font-semibold border-2 transition-all border-gray-300 bg-gray-100 text-gray-700 hover:border-gray-400">
            <i class="fas fa-list text-xs"></i>
            Semua
            <span class="ml-1 bg-gray-300 text-gray-700 text-[10px] font-bold px-1.5 py-0.5 rounded-full">{{ $totalData }}</span>
        </button>

        <button id="filter-sudah" onclick="applyFilter('sudah')"
            class="filter-btn inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full text-sm font-semibold border-2 transition-all border-emerald-200 bg-white text-emerald-700 hover:bg-emerald-50 hover:border-emerald-400">
            <i class="fas fa-ship text-xs"></i>
            Sudah Naik Kapal
            <span class="ml-1 bg-emerald-100 text-emerald-700 text-[10px] font-bold px-1.5 py-0.5 rounded-full">{{ $sudahNaikKapal }}</span>
        </button>

        <button id="filter-belum" onclick="applyFilter('belum')"
            class="filter-btn inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full text-sm font-semibold border-2 transition-all border-amber-200 bg-white text-amber-700 hover:bg-amber-50 hover:border-amber-400">
            <i class="fas fa-clock text-xs"></i>
            Belum Naik Kapal
            <span class="ml-1 bg-amber-100 text-amber-700 text-[10px] font-bold px-1.5 py-0.5 rounded-full">{{ $belumNaikKapal }}</span>
        </button>

        <div class="ml-auto text-xs text-gray-400 italic" id="filter-info">
            Menampilkan <span id="visible-count" class="font-semibold text-gray-600">{{ $totalData }}</span> dari {{ $totalData }} data
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse" id="tt-table">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">Sumber</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">No. TT / SJ</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">No. Kontainer / Seal</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">Size</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">Pengirim</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">Penerima</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">Tujuan</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100" id="tt-tbody">
                    @forelse($data as $row)
                        <tr class="hover:bg-gray-50 transition-colors {{ $row['naik_kapal'] ? '' : 'bg-amber-50/40' }}"
                            data-naik-kapal="{{ $row['naik_kapal'] ? 'sudah' : 'belum' }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($row['source'] == 'Standard') bg-purple-100 text-purple-700
                                    @elseif($row['source'] == 'Tanpa SJ') bg-blue-100 text-blue-700
                                    @elseif($row['source'] == 'LCL') bg-orange-100 text-orange-700
                                    @else bg-emerald-100 text-emerald-700
                                    @endif">
                                    {{ $row['source'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($row['naik_kapal'])
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">
                                        <i class="fas fa-ship text-[10px]"></i>
                                        Sudah Naik Kapal
                                    </span>
                                    @if($row['nama_kapal'] || $row['no_voyage'])
                                        <div class="text-[10px] text-emerald-600 mt-0.5 font-medium leading-tight">
                                            @if($row['nama_kapal']) {{ $row['nama_kapal'] }} @endif
                                            @if($row['no_voyage']) <span class="text-gray-400">/ Voy.</span> {{ $row['no_voyage'] }} @endif
                                        </div>
                                    @endif
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">
                                        <i class="fas fa-clock text-[10px]"></i>
                                        Belum Naik Kapal
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $row['tanggal'] ? $row['tanggal']->format('d/m/Y') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">
                                {{ $row['no_tt'] }}
                                @if($row['no_sj_pabrik'] && $row['no_sj_pabrik'] != '-')
                                    <div class="text-xs text-gray-400 mt-0.5">SJ Pabrik: {{ $row['no_sj_pabrik'] }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $row['no_kontainer'] ?: '-' }}
                                @if($row['no_seal'] && $row['no_seal'] != '-')
                                    <div class="text-[10px] text-green-600 font-bold mt-0.5"><i class="fas fa-lock text-[9px] mr-1"></i>{{ $row['no_seal'] }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $row['size'] ?: '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ Str::limit($row['pengirim'], 20) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ Str::limit($row['penerima'], 20) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ Str::limit($row['tujuan'], 20) }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ Str::limit($row['keterangan'], 30) }}</td>
                        </tr>
                    @empty
                        <tr id="empty-row">
                            <td colspan="10" class="px-6 py-10 text-center text-gray-500 italic">
                                No data found for the selected period.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pesan kosong saat filter aktif -->
            <div id="no-filter-result" class="hidden px-6 py-10 text-center text-gray-500 italic">
                <i class="fas fa-search text-gray-300 text-3xl mb-3 block"></i>
                Tidak ada data yang cocok dengan filter yang dipilih.
            </div>
        </div>
    </div>
</div>

<script>
    let currentFilter = 'semua';

    function applyFilter(filter) {
        currentFilter = filter;
        const rows = document.querySelectorAll('#tt-tbody tr[data-naik-kapal]');
        let visibleCount = 0;

        rows.forEach(row => {
            const status = row.getAttribute('data-naik-kapal');
            let show = false;
            if (filter === 'semua') show = true;
            else if (filter === 'sudah' && status === 'sudah') show = true;
            else if (filter === 'belum' && status === 'belum') show = true;

            row.style.display = show ? '' : 'none';
            if (show) visibleCount++;
        });

        // Update visible count
        const countEl = document.getElementById('visible-count');
        if (countEl) countEl.textContent = visibleCount;

        // Show/hide empty message
        const noResult = document.getElementById('no-filter-result');
        if (noResult) noResult.classList.toggle('hidden', visibleCount > 0);

        // Update active button styles
        updateFilterButtons(filter);
    }

    function updateFilterButtons(active) {
        const configs = {
            semua:  { id: 'filter-semua',  base: 'border-gray-300 bg-gray-100 text-gray-700',   active: 'border-gray-500 bg-gray-200 text-gray-800 ring-2 ring-gray-300' },
            sudah:  { id: 'filter-sudah',  base: 'border-emerald-200 bg-white text-emerald-700', active: 'border-emerald-500 bg-emerald-100 text-emerald-800 ring-2 ring-emerald-300' },
            belum:  { id: 'filter-belum',  base: 'border-amber-200 bg-white text-amber-700',     active: 'border-amber-500 bg-amber-100 text-amber-800 ring-2 ring-amber-300' },
        };

        Object.entries(configs).forEach(([key, cfg]) => {
            const btn = document.getElementById(cfg.id);
            if (!btn) return;
            // Remove all possible classes then apply correct set
            const allClasses = (cfg.base + ' ' + cfg.active).split(' ');
            allClasses.forEach(cls => btn.classList.remove(cls));
            const toAdd = (key === active ? cfg.active : cfg.base).split(' ');
            toAdd.forEach(cls => btn.classList.add(cls));
        });
    }
</script>
@endsection

