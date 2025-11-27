@extends('layouts.app')

@php
    $routePrefix = $routePrefix ?? 'uang-jalan';
    $isBongkaran = $routePrefix === 'uang-jalan-bongkaran';
    $pageTitleText = $isBongkaran ? 'Uang Jalan Bongkaran' : 'Uang Jalan';
    $selectRouteName = $isBongkaran ? 'uang-jalan-bongkaran.select-surat-jalan-bongkaran' : 'uang-jalan.select-surat-jalan';
    $indexRouteName = $routePrefix . '.index';
    $createRouteName = $routePrefix . '.create';
    $suratJalanQueryParam = $isBongkaran ? 'surat_jalan_bongkaran_id' : 'surat_jalan_id';
    $statusOptions = $statusOptions ?? ['all' => 'Semua Status'];
    $status = $status ?? 'all';
    $suratJalans = $suratJalans ?? collect([]);
    // The bongkaran variant uses 'nomor_surat_jalan' while normal uses 'no_surat_jalan'
    $noField = $isBongkaran ? 'nomor_surat_jalan' : 'no_surat_jalan';
@endphp

@section('page_title', 'Tambah Data ' . $pageTitleText)

@section('content')
<div class="min-h-screen bg-gray-50 py-4">
    <div class="max-w-5xl mx-auto px-3 sm:px-4">
        <!-- Breadcrumb / Header Navigation -->
        <nav class="flex mb-3" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                        <svg class="w-3 h-3 mr-2.5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z"/>
                        </svg>
                        Home
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-3 h-3 text-gray-400 mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                        <span class="text-sm font-medium text-gray-500">{{ $pageTitleText }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Main Form Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <!-- Header with Blue Background -->
            <div class="bg-blue-500 px-4 py-3 rounded-t-lg">
                <h1 class="text-base font-semibold text-white">Tambah Data {{ $pageTitleText }}</h1>
            </div>

            <!-- Form Content -->
            <div class="p-4">
                <!-- Info -->
                <div class="mb-3 p-2 bg-blue-50 border border-blue-200 rounded text-xs">
                    <strong>Info:</strong> {{ $suratJalans->total() }} surat jalan tersedia
                </div>
                
                <form id="selectSuratJalanForm" method="GET">
                    <div class="mb-4">
                        <label for="no_surat_jalan" class="block text-sm font-medium text-gray-700 mb-1">
                            No Surat Jalan <span class="text-red-500">*</span>
                        </label>
                        <div class="flex gap-2">
                            <input type="text" 
                                   id="selected_surat_jalan_display" 
                                   class="flex-1 px-3 py-1.5 text-sm border border-gray-300 rounded bg-gray-50 cursor-pointer focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                   placeholder="Klik untuk memilih surat jalan"
                                   readonly
                                   onclick="openSuratJalanModal()">
                            <button type="button" 
                                    onclick="openSuratJalanModal()"
                                    class="inline-flex items-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded transition-colors">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                Pilih
                            </button>
                        </div>
                        <input type="hidden" id="selected_surat_jalan_id" name="surat_jalan_id" value="">
                        <p class="mt-0.5 text-xs text-gray-500">Klik "Pilih" untuk memilih surat jalan</p>
                    </div>

                    <!-- Preview Information (Hidden by default) -->
                    <div id="suratJalanPreview" class="hidden mb-4 p-3 bg-blue-50 border border-blue-200 rounded">
                        <h3 class="text-xs font-medium text-blue-900 mb-2">Detail Surat Jalan</h3>
                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 text-xs">
                            <div>
                                <span class="text-gray-600">Tanggal:</span>
                                <div id="preview-tanggal" class="font-medium text-gray-900">-</div>
                            </div>
                            <div>
                                <span class="text-gray-600">Supir:</span>
                                <div id="preview-supir" class="font-medium text-gray-900">-</div>
                            </div>
                            <div>
                                <span class="text-gray-600">No Plat:</span>
                                <div id="preview-plat" class="font-medium text-gray-900">-</div>
                            </div>
                            <div>
                                <span class="text-gray-600">Pengirim:</span>
                                <div id="preview-pengirim" class="font-medium text-gray-900">-</div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-2 pt-3 border-t border-gray-200">
                        <button type="submit" 
                                id="submitBtn"
                                class="inline-flex items-center justify-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white text-sm font-medium rounded transition-colors"
                                disabled>
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            Lanjutkan
                        </button>
                                <a href="{{ route($indexRouteName) }}" 
                           class="inline-flex items-center justify-center px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded transition-colors">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Batal
                        </a>
                    </div>
                </form>
            </div>

            <!-- Filter Options (Collapsible) -->
            <div class="border-t border-gray-200">
                <button type="button" 
                        id="toggleFilter"
                        class="w-full px-4 py-2 text-left text-xs font-medium text-gray-700 hover:bg-gray-50 focus:outline-none flex items-center justify-between">
                    <span>Filter & Pencarian</span>
                    <svg id="filterIcon" class="w-3 h-3 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                
                <div id="filterPanel" class="hidden border-t border-gray-200 bg-gray-50 p-3">
                    <form method="GET" action="{{ route($selectRouteName) }}" id="filterForm">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <!-- Search Input -->
                            <div>
                                <label for="search" class="block text-xs font-medium text-gray-700 mb-1">Pencarian</label>
                                <input type="text" 
                                       id="search" 
                                       name="search" 
                                       value="{{ $search }}" 
                                       placeholder="Cari no surat jalan, supir, plat..."
                                       class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            </div>

                            <!-- Status Filter -->
                            <div>
                                <label for="status" class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                                <select id="status" name="status" 
                                        class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                    @foreach($statusOptions as $value => $label)
                                        <option value="{{ $value }}" {{ $status === $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Filter Action Buttons -->
                        <div class="flex gap-2 mt-3">
                            <button type="submit" 
                                    class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded transition-colors">
                                Terapkan
                            </button>
                            <a href="{{ route($selectRouteName) }}" 
                               class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium rounded transition-colors">
                                Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Pilih Surat Jalan -->
<div id="suratJalanModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen pt-2 px-2 pb-2 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeSuratJalanModal()"></div>

        <!-- Modal panel -->
        <div class="inline-block align-middle bg-white rounded text-left overflow-hidden shadow-xl transform transition-all sm:my-2 sm:align-middle sm:max-w-[95vw] sm:w-full max-h-[95vh]">
            <!-- Modal Header -->
            <div class="bg-blue-600 px-4 py-2">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm leading-6 font-medium text-white" id="modal-title">
                        DATA Surat Jalan
                    </h3>
                    <button type="button" class="text-white hover:text-gray-200" onclick="closeSuratJalanModal()">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Modal Content -->
            <div class="bg-white px-3 py-2 overflow-y-auto" style="max-height: calc(95vh - 80px);">
                <!-- Filter dan Search dalam Modal -->
                <div class="mb-3 flex flex-wrap gap-2 items-end">
                    <div class="flex-1 min-w-0">
                        <label for="modal-search" class="block text-xs font-medium text-gray-700 mb-1">Search:</label>
                        <input type="text" 
                               id="modal-search" 
                               placeholder="Cari no surat jalan, supir, plat..."
                               class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                               onkeyup="filterSuratJalan()">
                    </div>
                    <div class="w-20">
                        <label for="modal-show" class="block text-xs font-medium text-gray-700 mb-1">Show</label>
                        <select id="modal-show" 
                                class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                onchange="updateTableDisplay()">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto border border-gray-200 rounded">
                    <table class="min-w-full divide-y divide-gray-200" style="font-size: 11px;">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-1 py-1 text-left text-xs font-medium text-gray-500 uppercase cursor-pointer w-20" onclick="sortTable('no_sj')">
                                    No SJ ↕
                                </th>
                                <th class="px-1 py-1 text-left text-xs font-medium text-gray-500 uppercase cursor-pointer w-16" onclick="sortTable('nama_supir')">
                                    Supir ↕
                                </th>
                                <th class="px-1 py-1 text-left text-xs font-medium text-gray-500 uppercase cursor-pointer w-16" onclick="sortTable('no_plat')">
                                    Plat ↕
                                </th>
                                <th class="px-1 py-1 text-left text-xs font-medium text-gray-500 uppercase cursor-pointer w-24" onclick="sortTable('pengirim')">
                                    Pengirim ↕
                                </th>
                                <th class="px-1 py-1 text-left text-xs font-medium text-gray-500 uppercase cursor-pointer w-20" onclick="sortTable('tujuan_ambil')">
                                    T.Ambil ↕
                                </th>
                                <th class="px-1 py-1 text-left text-xs font-medium text-gray-500 uppercase cursor-pointer w-16" onclick="sortTable('seal')">
                                    Seal ↕
                                </th>
                                <th class="px-1 py-1 text-left text-xs font-medium text-gray-500 uppercase cursor-pointer w-20" onclick="sortTable('tujuan_kirim')">
                                    T.Kirim ↕
                                </th>
                                <th class="px-1 py-1 text-left text-xs font-medium text-gray-500 uppercase cursor-pointer w-16" onclick="sortTable('nama_barang')">
                                    Barang ↕
                                </th>
                            </tr>
                        </thead>
                        <tbody id="suratJalanTableBody" class="bg-white divide-y divide-gray-100">
                            @forelse($suratJalans as $suratJalan)
                                <tr class="hover:bg-blue-50 cursor-pointer transition-colors" onclick="selectSuratJalan({{ $suratJalan->id }}, '{{ addslashes($suratJalan->{$noField}) }}', '{{ addslashes($suratJalan->supir) }}', '{{ addslashes($suratJalan->no_plat) }}', '{{ $suratJalan->tanggal_surat_jalan ? \Carbon\Carbon::parse($suratJalan->tanggal_surat_jalan)->format('d/m/Y') : '' }}', '{{ $suratJalan->order && $suratJalan->order->pengirim ? addslashes($suratJalan->order->pengirim->nama_pengirim) : '' }}')">
                                    <td class="px-1 py-1">
                                        <div class="text-xs font-medium text-blue-600 hover:text-blue-800 leading-tight">
                                            {{ $suratJalan->{$noField} }}
                                        </div>
                                        @if($suratJalan->order)
                                            <div style="font-size: 9px;" class="text-gray-500 leading-tight">{{ $suratJalan->order->nomor_order }}</div>
                                        @endif
                                    </td>
                                    <td class="px-1 py-1 text-xs text-gray-900 truncate" style="max-width: 60px;" title="{{ $suratJalan->supir }}">
                                        {{ $suratJalan->supir }}
                                    </td>
                                    <td class="px-1 py-1 text-xs text-gray-900 truncate" style="max-width: 60px;" title="{{ $suratJalan->no_plat }}">
                                        {{ $suratJalan->no_plat }}
                                    </td>
                                    <td class="px-1 py-1 text-xs text-gray-900 truncate" style="max-width: 80px;" title="{{ $suratJalan->order && $suratJalan->order->pengirim ? $suratJalan->order->pengirim->nama_pengirim : '-' }}">
                                        {{ $suratJalan->order && $suratJalan->order->pengirim ? $suratJalan->order->pengirim->nama_pengirim : '-' }}
                                    </td>
                                    <td class="px-1 py-1 text-xs text-gray-900 truncate" style="max-width: 60px;" title="{{ $suratJalan->tujuan_pengambilan ?? '-' }}">
                                        {{ $suratJalan->tujuan_pengambilan ?? '-' }}
                                    </td>
                                    <td class="px-1 py-1 text-xs text-gray-900 truncate" style="max-width: 50px;" title="{{ $suratJalan->no_seal ?? '-' }}">
                                        {{ $suratJalan->no_seal ?? '-' }}
                                    </td>
                                    <td class="px-1 py-1 text-xs text-gray-900 truncate" style="max-width: 60px;" title="{{ $suratJalan->tujuan_pengiriman ?? '-' }}">
                                        {{ $suratJalan->tujuan_pengiriman ?? '-' }}
                                    </td>
                                    <td class="px-1 py-1 text-xs text-gray-900 truncate" style="max-width: 50px;" title="{{ $suratJalan->jenis_barang ?? '-' }}">
                                        {{ $suratJalan->jenis_barang ?? '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-2 py-4 text-center text-gray-500">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-6 h-6 text-gray-300 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <p class="text-xs font-medium">Tidak ada data</p>
                                            <p style="font-size: 10px;" class="text-gray-400">Semua surat jalan sudah memiliki uang jalan</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Info -->
                <div class="mt-2 flex justify-between items-center text-xs text-gray-500">
                    <div id="tableInfo">
                        Showing <span id="showingFrom">1</span> to <span id="showingTo">{{ min(10, $suratJalans->count()) }}</span> of <span id="totalEntries">{{ $suratJalans->count() }}</span> entries
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script>
let allSuratJalans = {!! json_encode($suratJalans->items()) !!};
// Normalize property name to 'no_surat_jalan' for uniform JS usage regardless of model field
allSuratJalans = allSuratJalans.map(item => {
    if (!item.no_surat_jalan) {
        if (item.nomor_surat_jalan) {
            item.no_surat_jalan = item.nomor_surat_jalan;
        } else if (item.nomor_surat) {
            item.no_surat_jalan = item.nomor_surat;
        }
    }
    return item;
});
let filteredSuratJalans = [...allSuratJalans];
let currentSort = { column: '', direction: 'asc' };
let currentPage = 1;
let itemsPerPage = 10;

document.addEventListener('DOMContentLoaded', function() {
    // Debug: Check if data is loaded
    console.log('All Surat Jalans:', allSuratJalans);
    console.log('Filtered Surat Jalans:', filteredSuratJalans);
    
    const submitBtn = document.getElementById('submitBtn');
    const previewDiv = document.getElementById('suratJalanPreview');
    const form = document.getElementById('selectSuratJalanForm');
    const toggleFilterBtn = document.getElementById('toggleFilter');
    const filterPanel = document.getElementById('filterPanel');
    const filterIcon = document.getElementById('filterIcon');

    // Handle form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const selectedId = document.getElementById('selected_surat_jalan_id').value;
        if (selectedId) {
            // Redirect to create page with selected surat jalan id
                const createUrl = '{{ route($createRouteName) }}?{{ $suratJalanQueryParam }}=' + selectedId;
            window.location.href = createUrl;
        }
    });

    // Handle filter toggle
    if (toggleFilterBtn) {
        toggleFilterBtn.addEventListener('click', function() {
            const isHidden = filterPanel.classList.contains('hidden');
            
            if (isHidden) {
                filterPanel.classList.remove('hidden');
                filterIcon.classList.add('rotate-180');
            } else {
                filterPanel.classList.add('hidden');
                filterIcon.classList.remove('rotate-180');
            }
        });
    }

    // Auto submit form saat filter berubah
    const statusSelect = document.getElementById('status');
    if (statusSelect) {
        statusSelect.addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
    }

    // Enter key untuk search
    const searchInput = document.getElementById('search');
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('filterForm').submit();
            }
        });
    }
});

// Modal functions
function openSuratJalanModal() {
    document.getElementById('suratJalanModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    // Reset filter dan update display
    document.getElementById('modal-search').value = '';
    document.getElementById('modal-show').value = '10';
    filteredSuratJalans = [...allSuratJalans];
    currentPage = 1;
    itemsPerPage = 10;
    updateTableDisplay();
}

function closeSuratJalanModal() {
    document.getElementById('suratJalanModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function selectSuratJalan(id, noSuratJalan, supir, plat, tanggal, pengirim) {
    // Set hidden input value
    document.getElementById('selected_surat_jalan_id').value = id;
    
    // Set display input value
    document.getElementById('selected_surat_jalan_display').value = noSuratJalan + ' - ' + supir;
    
    // Update preview
    document.getElementById('preview-tanggal').textContent = tanggal || '-';
    document.getElementById('preview-supir').textContent = supir || '-';
    document.getElementById('preview-plat').textContent = plat || '-';
    document.getElementById('preview-pengirim').textContent = pengirim || '-';
    
    // Show preview and enable submit button
    document.getElementById('suratJalanPreview').classList.remove('hidden');
    document.getElementById('submitBtn').disabled = false;
    
    // Close modal
    closeSuratJalanModal();
}

function filterSuratJalan() {
    const searchTerm = document.getElementById('modal-search').value.toLowerCase();
    
    filteredSuratJalans = allSuratJalans.filter(item => {
        return (item.no_surat_jalan && item.no_surat_jalan.toLowerCase().includes(searchTerm)) ||
               (item.supir && item.supir.toLowerCase().includes(searchTerm)) ||
               (item.no_plat && item.no_plat.toLowerCase().includes(searchTerm)) ||
               (item.order && item.order.pengirim && item.order.pengirim.nama_pengirim && item.order.pengirim.nama_pengirim.toLowerCase().includes(searchTerm));
    });
    
    currentPage = 1;
    updateTableDisplay();
}

function sortTable(column) {
    if (currentSort.column === column) {
        currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
    } else {
        currentSort.column = column;
        currentSort.direction = 'asc';
    }
    
    filteredSuratJalans.sort((a, b) => {
        let aVal = '', bVal = '';
        
        switch(column) {
            case 'no_sj':
                aVal = a.no_surat_jalan || '';
                bVal = b.no_surat_jalan || '';
                break;
            case 'nama_supir':
                aVal = a.supir || '';
                bVal = b.supir || '';
                break;
            case 'no_plat':
                aVal = a.no_plat || '';
                bVal = b.no_plat || '';
                break;
            case 'pengirim':
                aVal = (a.order && a.order.pengirim) ? a.order.pengirim.nama_pengirim || '' : '';
                bVal = (b.order && b.order.pengirim) ? b.order.pengirim.nama_pengirim || '' : '';
                break;
            case 'tujuan_ambil':
                aVal = a.tujuan_pengambilan || '';
                bVal = b.tujuan_pengambilan || '';
                break;
            case 'seal':
                aVal = a.no_seal || '';
                bVal = b.no_seal || '';
                break;
            case 'tujuan_kirim':
                aVal = a.tujuan_pengiriman || '';
                bVal = b.tujuan_pengiriman || '';
                break;
            case 'nama_barang':
                aVal = a.jenis_barang || '';
                bVal = b.jenis_barang || '';
                break;
        }
        
        if (currentSort.direction === 'asc') {
            return aVal.localeCompare(bVal);
        } else {
            return bVal.localeCompare(aVal);
        }
    });
    
    updateTableDisplay();
}

function updateTableDisplay() {
    itemsPerPage = parseInt(document.getElementById('modal-show').value);
    const startIndex = (currentPage - 1) * itemsPerPage;
    const endIndex = startIndex + itemsPerPage;
    const paginatedData = filteredSuratJalans.slice(startIndex, endIndex);
    
    const tbody = document.getElementById('suratJalanTableBody');
    tbody.innerHTML = '';
    
    paginatedData.forEach(item => {
        const tanggal = item.tanggal_surat_jalan ? new Date(item.tanggal_surat_jalan).toLocaleDateString('id-ID') : '';
        const pengirim = (item.order && item.order.pengirim) ? item.order.pengirim.nama_pengirim : '-';
        const orderNo = item.order ? item.order.nomor_order : '';
        
        const row = document.createElement('tr');
        row.className = 'hover:bg-gray-50 cursor-pointer transition-colors';
        row.onclick = () => selectSuratJalan(item.id, item.no_surat_jalan, item.supir, item.no_plat, tanggal, pengirim);
        
        row.innerHTML = `
            <td class="px-1 py-1">
                <div class="text-xs font-medium text-blue-600 hover:text-blue-800 leading-tight">
                    ${item.no_surat_jalan}
                </div>
                ${orderNo ? `<div style="font-size: 9px;" class="text-gray-500 leading-tight">${orderNo}</div>` : ''}
            </td>
            <td class="px-1 py-1 text-xs text-gray-900 truncate" style="max-width: 60px;" title="${item.supir || '-'}">
                ${item.supir || '-'}
            </td>
            <td class="px-1 py-1 text-xs text-gray-900 truncate" style="max-width: 60px;" title="${item.no_plat || '-'}">
                ${item.no_plat || '-'}
            </td>
            <td class="px-1 py-1 text-xs text-gray-900 truncate" style="max-width: 80px;" title="${pengirim}">
                ${pengirim}
            </td>
            <td class="px-1 py-1 text-xs text-gray-900 truncate" style="max-width: 60px;" title="${item.tujuan_pengambilan || '-'}">
                ${item.tujuan_pengambilan || '-'}
            </td>
            <td class="px-1 py-1 text-xs text-gray-900 truncate" style="max-width: 50px;" title="${item.no_seal || '-'}">
                ${item.no_seal || '-'}
            </td>
            <td class="px-1 py-1 text-xs text-gray-900 truncate" style="max-width: 60px;" title="${item.tujuan_pengiriman || '-'}">
                ${item.tujuan_pengiriman || '-'}
            </td>
            <td class="px-1 py-1 text-xs text-gray-900 truncate" style="max-width: 50px;" title="${item.jenis_barang || '-'}">
                ${item.jenis_barang || '-'}
            </td>
        `;
        
        tbody.appendChild(row);
    });
    
    // Update pagination info
    const showingFrom = Math.min(startIndex + 1, filteredSuratJalans.length);
    const showingTo = Math.min(endIndex, filteredSuratJalans.length);
    
    document.getElementById('showingFrom').textContent = showingFrom;
    document.getElementById('showingTo').textContent = showingTo;
    document.getElementById('totalEntries').textContent = filteredSuratJalans.length;
}

// Close modal when clicking outside
document.addEventListener('click', function(event) {
    const modal = document.getElementById('suratJalanModal');
    if (event.target === modal) {
        closeSuratJalanModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeSuratJalanModal();
    }
});
</script>
@endsection