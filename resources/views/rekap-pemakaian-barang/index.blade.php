@extends('layouts.app')

@section('title', 'Rekap Pemakaian Barang')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<style>
    .rekap-filter .select2-container { width: 100% !important; }
    .rekap-filter .select2-container--default .select2-selection--single {
        height: 44px;
        border: 1px solid #d1d5db;
        border-radius: 0.75rem;
        background: #fff;
        transition: border-color 150ms ease, box-shadow 150ms ease;
    }
    .rekap-filter .select2-container--default .select2-selection--single .select2-selection__rendered {
        display: flex;
        align-items: center;
        height: 42px;
        padding: 0 4.5rem 0 0.875rem;
        color: #111827;
        font-size: 0.875rem;
        line-height: 1.25rem;
    }
    .rekap-filter .select2-container--default .select2-selection--single .select2-selection__placeholder { color: #9ca3af; }
    .rekap-filter .select2-container--default .select2-selection--single .select2-selection__arrow {
        top: 1px;
        right: 0.625rem;
        height: 40px;
    }
    .rekap-filter .select2-container--default .select2-selection--single .select2-selection__clear {
        position: absolute;
        top: 50%;
        right: 2.5rem;
        margin: 0;
        transform: translateY(-50%);
        color: #6b7280;
    }
    .rekap-filter .select2-container--default.select2-container--focus .select2-selection--single,
    .rekap-filter .select2-container--default.select2-container--open .select2-selection--single {
        border-color: #6366f1;
        outline: 0;
        box-shadow: 0 0 0 3px rgb(99 102 241 / 16%);
    }
    .rekap-filter {
        display: grid;
        grid-template-columns: minmax(0, 1fr);
        align-items: end;
        gap: 1rem;
    }
    @media (min-width: 640px) {
        .rekap-filter { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .rekap-filter__barang,
        .rekap-filter__actions { grid-column: span 2 / span 2; }
    }
    @media (min-width: 1280px) {
        .rekap-filter { grid-template-columns: repeat(12, minmax(0, 1fr)); }
        .rekap-filter__barang { grid-column: span 6 / span 6; }
        .rekap-filter__aktiva,
        .rekap-filter__lokasi,
        .rekap-filter__date { grid-column: span 3 / span 3; }
        .rekap-filter:not(.has-location) .rekap-filter__aktiva { grid-column: span 6 / span 6; }
        .rekap-filter__actions { grid-column: span 6 / span 6; }
    }
</style>
@endpush

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-boxes mr-2 text-indigo-600"></i>
            Rekap Pemakaian Barang
        </h2>
        <p class="text-gray-500 mt-1 text-sm">Laporan riwayat pemakaian barang dari Stock Amprahan maupun Stock Ban.</p>
    </div>

    <!-- Filter Form -->
    <div class="mb-8 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-100 bg-gray-50/80 px-5 py-4 sm:px-6">
            <h3 class="flex items-center gap-2 font-semibold text-gray-900">
                <i class="fas fa-filter text-indigo-600" aria-hidden="true"></i>
                Filter Laporan
            </h3>
            <p class="mt-1 text-xs text-gray-500">Pilih tipe barang dan periode pemakaian yang ingin ditampilkan.</p>
        </div>
        <form method="GET" action="{{ route('rekap-pemakaian-barang.index') }}" class="rekap-filter {{ in_array($aktiva, ['kendaraan', 'alat_berat'], true) ? 'has-location' : '' }} p-5 sm:p-6">
            <!-- Select Barang -->
            <div class="rekap-filter__barang min-w-0">
                <label for="nama_barang" class="mb-2 block text-sm font-semibold text-gray-700">Pilih Tipe Barang <span class="text-red-500">*</span></label>
                <select name="nama_barang" id="nama_barang" class="select2 w-full" required>
                    <option value="" disabled {{ empty($namaBarang) ? 'selected' : '' }}>-- Ketik untuk mencari tipe barang --</option>
                    @foreach($allBarang as $barang)
                        <option value="{{ $barang }}" {{ $namaBarang === $barang ? 'selected' : '' }}>{{ $barang }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Asset Type -->
            <div class="rekap-filter__aktiva min-w-0">
                <label for="aktiva" class="mb-2 block text-sm font-semibold text-gray-700">Aktiva</label>
                <select name="aktiva" id="aktiva" class="h-11 w-full rounded-xl border border-gray-300 bg-white px-3 text-sm text-gray-900 shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">
                    <option value="">Semua Aktiva</option>
                    <option value="kendaraan" @selected($aktiva === 'kendaraan')>Kendaraan</option>
                    <option value="alat_berat" @selected($aktiva === 'alat_berat')>Alat Berat</option>
                    <option value="kantor" @selected($aktiva === 'kantor')>Kantor</option>
                    <option value="kapal" @selected($aktiva === 'kapal')>Kapal</option>
                </select>
            </div>

            <!-- Asset Location -->
            <div id="lokasi-filter" class="rekap-filter__lokasi min-w-0 {{ in_array($aktiva, ['kendaraan', 'alat_berat'], true) ? '' : 'hidden' }}">
                <label for="lokasi" class="mb-2 block text-sm font-semibold text-gray-700">Lokasi</label>
                <select name="lokasi" id="lokasi" class="h-11 w-full rounded-xl border border-gray-300 bg-white px-3 text-sm text-gray-900 shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200" @disabled(!in_array($aktiva, ['kendaraan', 'alat_berat'], true))>
                    <option value="">Semua Lokasi</option>
                    <option value="jakarta" @selected($lokasi === 'jakarta')>Jakarta</option>
                    <option value="batam" @selected($lokasi === 'batam')>Batam</option>
                    <option value="tanjung_pinang" @selected($lokasi === 'tanjung_pinang')>Tanjung Pinang</option>
                </select>
            </div>

            <!-- Start Date -->
            <div class="rekap-filter__date min-w-0">
                <label for="start_date" class="mb-2 block text-sm font-semibold text-gray-700">Dari Tanggal</label>
                <input type="date" name="start_date" id="start_date" value="{{ $startDate }}" class="h-11 w-full rounded-xl border border-gray-300 bg-white px-3 text-sm text-gray-900 shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">
            </div>

            <!-- End Date -->
            <div class="rekap-filter__date min-w-0">
                <label for="end_date" class="mb-2 block text-sm font-semibold text-gray-700">Sampai Tanggal</label>
                <input type="date" name="end_date" id="end_date" value="{{ $endDate }}" class="h-11 w-full rounded-xl border border-gray-300 bg-white px-3 text-sm text-gray-900 shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">
            </div>

            <div class="rekap-filter__actions flex flex-wrap gap-2 sm:justify-end">
                <a href="{{ route('rekap-pemakaian-barang.index') }}" class="inline-flex h-11 items-center justify-center gap-2 rounded-xl border border-gray-300 bg-white px-4 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-300">
                    <i class="fas fa-rotate-left" aria-hidden="true"></i>Reset
                </a>
                <button type="submit" class="inline-flex h-11 flex-1 items-center justify-center gap-2 whitespace-nowrap rounded-xl bg-indigo-600 px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:flex-none">
                    <i class="fas fa-search" aria-hidden="true"></i>Tampilkan Laporan
                </button>
            </div>
        </form>
    </div>

    <!-- Results Table -->
    @if(isset($namaBarang))
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                <div>
                    <h3 class="font-bold text-gray-800">Hasil Pencarian: <span class="text-indigo-600">{{ $namaBarang }}</span></h3>
                    <p class="text-xs text-gray-500 mt-1">Periode: {{ $startDate && $endDate ? \Carbon\Carbon::parse($startDate)->format('d M Y') . ' - ' . \Carbon\Carbon::parse($endDate)->format('d M Y') : 'Semua Waktu' }}</p>
                </div>
                <div class="bg-indigo-100 text-indigo-700 text-xs font-bold px-3 py-1 rounded-full border border-indigo-200" id="total-data-badge">
                    Total: {{ $results->count() }} Data
                </div>
            </div>
            
            @php
                $rekapUnit = [];
                foreach($results as $row) {
                    $u = $row->unit;
                    if ($u === '-' || empty($u)) continue;
                    
                    if(!isset($rekapUnit[$u])) {
                        $rekapUnit[$u] = [
                            'count' => 0,
                            'satuan' => $row->satuan ?? ''
                        ];
                    }
                    $rekapUnit[$u]['count'] += $row->raw_qty;
                }
                ksort($rekapUnit);
            @endphp
            
            @if(count($rekapUnit) > 0)
            <div class="p-4 border-b border-gray-100 bg-white">
                <h4 class="font-semibold text-gray-700 mb-3"><i class="fas fa-chart-pie text-indigo-500 mr-2"></i>Rekap Per Unit / Tujuan</h4>
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">
                    @foreach($rekapUnit as $name => $data)
                    <div class="bg-indigo-50 border border-indigo-100 rounded-lg p-3 shadow-sm hover:shadow-md hover:bg-indigo-100 transition text-center cursor-pointer unit-card" onclick="filterByUnit('{{ addslashes($name) }}', this)">
                        <div class="font-bold text-indigo-900 text-sm mb-1 truncate" title="{{ $name }}">{{ $name }}</div>
                        <div class="text-xs font-medium text-indigo-600 bg-indigo-200/50 rounded-full px-2 py-0.5 inline-block">{{ $data['count'] }} {{ $data['satuan'] }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
            
            <div class="overflow-x-auto" id="table-container" style="{{ count($rekapUnit) > 0 ? 'display: none;' : '' }}">
                <div class="px-4 py-2 bg-indigo-50 border-b border-indigo-100 text-sm text-indigo-800 font-medium flex justify-between items-center" id="active-filter-banner" style="display: none;">
                    <span>Menampilkan data untuk: <span id="active-unit-name" class="font-bold"></span></span>
                    <button type="button" onclick="showAllData()" class="text-xs bg-white text-indigo-600 px-2 py-1 rounded border border-indigo-200 hover:bg-indigo-50">Tampilkan Semua</button>
                </div>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-16">No</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Detail Barang</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Penerima</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Unit / Tujuan</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Qty</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($results as $idx => $row)
                            <tr class="hover:bg-indigo-50/30 transition-colors result-row" data-unit="{{ $row->unit }}">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $idx + 1 }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">
                                    {{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-800">
                                    <div class="font-medium text-gray-900">{{ $row->nama_barang }}</div>
                                    <div class="text-xs text-gray-400 mt-0.5">Sumber: {{ $row->sumber }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $row->penerima }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs font-bold rounded">
                                        {{ $row->unit }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 text-right">
                                    {{ $row->qty }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 italic max-w-xs truncate" title="{{ $row->keterangan }}">
                                    {{ $row->keterangan ?: '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="fas fa-inbox text-4xl text-gray-300 mb-3"></i>
                                        <p class="text-gray-500 font-medium">Tidak ada data pemakaian untuk kriteria ini.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "Ketik untuk mencari barang...",
            allowClear: true,
            width: '100%'
        });

        const aktiva = document.getElementById('aktiva');
        const lokasiFilter = document.getElementById('lokasi-filter');
        const lokasi = document.getElementById('lokasi');

        function toggleLokasiFilter() {
            const supportsLocation = ['kendaraan', 'alat_berat'].includes(aktiva.value);
            aktiva.form.classList.toggle('has-location', supportsLocation);
            lokasiFilter.classList.toggle('hidden', !supportsLocation);
            lokasi.disabled = !supportsLocation;
            if (!supportsLocation) lokasi.value = '';
        }

        aktiva.addEventListener('change', toggleLokasiFilter);
        toggleLokasiFilter();
    });

    function filterByUnit(unitName, cardElement) {
        // Highlight active card
        $('.unit-card').removeClass('ring-2 ring-indigo-500 bg-indigo-100').addClass('bg-indigo-50');
        $(cardElement).removeClass('bg-indigo-50').addClass('ring-2 ring-indigo-500 bg-indigo-100');
        
        // Show table container
        $('#table-container').show();
        $('#active-filter-banner').show();
        $('#active-unit-name').text(unitName);
        
        // Filter rows
        let visibleCount = 0;
        $('.result-row').each(function() {
            if ($(this).data('unit') == unitName) {
                $(this).show();
                visibleCount++;
            } else {
                $(this).hide();
            }
        });
        
        // Update badge
        $('#total-data-badge').text('Total: ' + visibleCount + ' Data (Filtered)');
    }
    
    function showAllData() {
        $('.unit-card').removeClass('ring-2 ring-indigo-500 bg-indigo-100').addClass('bg-indigo-50');
        $('#active-filter-banner').hide();
        $('.result-row').show();
        $('#total-data-badge').text('Total: {{ $results->count() ?? 0 }} Data');
        
        @if(count($rekapUnit ?? []) > 0)
            $('#table-container').hide();
        @endif
    }
</script>
@endpush
