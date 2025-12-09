@extends('layouts.app')

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
                        <span class="text-sm font-medium text-gray-500">Uang Jalan (Penyesuaian)</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Main Form Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <!-- Header with Blue Background -->
            <div class="bg-blue-500 px-4 py-3 rounded-t-lg">
                <h1 class="text-base font-semibold text-white">Pilih Uang Jalan untuk Penyesuaian</h1>
            </div>

            <!-- Form Content -->
            <div class="p-4">
                <!-- Info Surat Jalan -->
                <div class="mb-3 p-2 bg-blue-50 border border-blue-200 rounded text-xs">
                    <strong>Surat Jalan Terpilih:</strong> {{ $suratJalan->no_surat_jalan }} - {{ $suratJalan->supir }}
                    <br>
                    <strong>Jumlah Uang Jalan Tersedia:</strong> {{ $uangJalans->count() }}
                </div>

                <form id="selectUangJalanForm" method="GET">
                    <div class="mb-4">
                        <label for="no_uang_jalan" class="block text-sm font-medium text-gray-700 mb-1">
                            No Uang Jalan <span class="text-red-500">*</span>
                        </label>
                        <div class="flex gap-2">
                            <input type="text"
                                   id="selected_uang_jalan_display"
                                   class="flex-1 px-3 py-1.5 text-sm border border-gray-300 rounded bg-gray-50 cursor-pointer focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                   placeholder="Klik untuk memilih uang jalan"
                                   readonly
                                   onclick="openUangJalanModal()">
                            <button type="button"
                                    onclick="openUangJalanModal()"
                                    class="inline-flex items-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded transition-colors">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                Pilih
                            </button>
                        </div>
                        <input type="hidden" id="selected_uang_jalan_id" name="uang_jalan_id" value="">
                        <p class="mt-0.5 text-xs text-gray-500">Klik "Pilih" untuk memilih uang jalan yang akan disesuaikan</p>
                    </div>

                    <!-- Preview Information (Hidden by default) -->
                    <div id="uangJalanPreview" class="hidden mb-4 p-3 bg-blue-50 border border-blue-200 rounded">
                        <h3 class="text-xs font-medium text-blue-900 mb-2">Detail Uang Jalan</h3>
                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 text-xs">
                            <div>
                                <span class="text-gray-600">Tanggal:</span>
                                <div id="preview-tanggal" class="font-medium text-gray-900">-</div>
                            </div>
                            <div>
                                <span class="text-gray-600">Total:</span>
                                <div id="preview-total" class="font-medium text-gray-900">-</div>
                            </div>
                            <div>
                                <span class="text-gray-600">Supir:</span>
                                <div id="preview-supir" class="font-medium text-gray-900">-</div>
                            </div>
                            <div>
                                <span class="text-gray-600">No Plat:</span>
                                <div id="preview-plat" class="font-medium text-gray-900">-</div>
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
                            Lanjutkan ke Form Penyesuaian
                        </button>
                                <a href="{{ route('uang-jalan.adjustment.select-surat-jalan') }}"
                           class="inline-flex items-center justify-center px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded transition-colors">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Kembali Pilih Surat Jalan
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Pilih Uang Jalan -->
<div id="uangJalanModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen pt-2 px-2 pb-2 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeUangJalanModal()"></div>

        <!-- Modal panel -->
        <div class="inline-block align-middle bg-white rounded text-left overflow-hidden shadow-xl transform transition-all sm:my-2 sm:align-middle sm:max-w-[95vw] sm:w-full max-h-[95vh]">
            <!-- Modal Header -->
            <div class="bg-blue-600 px-4 py-2">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm leading-6 font-medium text-white" id="modal-title">
                        DATA Uang Jalan untuk Surat Jalan: {{ $suratJalan->no_surat_jalan }}
                    </h3>
                    <button type="button" class="text-white hover:text-gray-200" onclick="closeUangJalanModal()">
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
                               placeholder="Cari no uang jalan, supir, plat..."
                               class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                               onkeyup="filterUangJalan()">
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
                                <th class="px-1 py-1 text-left text-xs font-medium text-gray-500 uppercase cursor-pointer w-20" onclick="sortTable('no_uj')">
                                    No UJ ↕
                                </th>
                                <th class="px-1 py-1 text-left text-xs font-medium text-gray-500 uppercase cursor-pointer w-16" onclick="sortTable('tanggal')">
                                    Tanggal ↕
                                </th>
                                <th class="px-1 py-1 text-left text-xs font-medium text-gray-500 uppercase cursor-pointer w-16" onclick="sortTable('nama_supir')">
                                    Supir ↕
                                </th>
                                <th class="px-1 py-1 text-left text-xs font-medium text-gray-500 uppercase cursor-pointer w-16" onclick="sortTable('no_plat')">
                                    Plat ↕
                                </th>
                                <th class="px-1 py-1 text-left text-xs font-medium text-gray-500 uppercase cursor-pointer w-20" onclick="sortTable('total')">
                                    Total ↕
                                </th>
                                <th class="px-1 py-1 text-left text-xs font-medium text-gray-500 uppercase cursor-pointer w-16" onclick="sortTable('status')">
                                    Status ↕
                                </th>
                            </tr>
                        </thead>
                        <tbody id="uangJalanTableBody" class="bg-white divide-y divide-gray-100">
                            @forelse($uangJalans as $uangJalan)
                                <tr class="hover:bg-blue-50 cursor-pointer transition-colors" onclick="selectUangJalan({{ $uangJalan->id }}, '{{ addslashes($uangJalan->nomor_uang_jalan) }}', '{{ $uangJalan->tanggal_uang_jalan ? \Carbon\Carbon::parse($uangJalan->tanggal_uang_jalan)->format('d/m/Y') : '' }}', '{{ addslashes($uangJalan->supir) }}', '{{ addslashes($uangJalan->no_plat) }}', '{{ number_format($uangJalan->jumlah_total ?? 0, 0, ',', '.') }}')">
                                    <td class="px-1 py-1">
                                        <div class="text-xs font-medium text-blue-600 hover:text-blue-800 leading-tight">
                                            {{ $uangJalan->nomor_uang_jalan }}
                                        </div>
                                    </td>
                                    <td class="px-1 py-1 text-xs text-gray-900">
                                        {{ $uangJalan->tanggal_uang_jalan ? $uangJalan->tanggal_uang_jalan->format('d/m/Y') : '-' }}
                                    </td>
                                    <td class="px-1 py-1 text-xs text-gray-900 truncate" style="max-width: 60px;" title="{{ $uangJalan->supir }}">
                                        {{ $uangJalan->supir }}
                                    </td>
                                    <td class="px-1 py-1 text-xs text-gray-900 truncate" style="max-width: 60px;" title="{{ $uangJalan->no_plat }}">
                                        {{ $uangJalan->no_plat }}
                                    </td>
                                    <td class="px-1 py-1 text-xs text-gray-900 font-medium">
                                        Rp {{ number_format($uangJalan->jumlah_total ?? 0, 0, ',', '.') }}
                                    </td>
                                    <td class="px-1 py-1">
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                            Aktif
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-2 py-4 text-center text-gray-500">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-6 h-6 text-gray-300 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <p class="text-xs font-medium">Tidak ada data uang jalan</p>
                                            <p style="font-size: 10px;" class="text-gray-400">Belum ada uang jalan untuk surat jalan ini</p>
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
                        Showing <span id="showingFrom">1</span> to <span id="showingTo">{{ min(10, $uangJalans->count()) }}</span> of <span id="totalEntries">{{ $uangJalans->count() }}</span> entries
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script>
let allUangJalans = {!! json_encode($uangJalans) !!};
let filteredUangJalans = [...allUangJalans];
let currentSort = { column: '', direction: 'asc' };
let currentPage = 1;
let itemsPerPage = 10;

document.addEventListener('DOMContentLoaded', function() {
    console.log('All Uang Jalans:', allUangJalans);
    console.log('Filtered Uang Jalans:', filteredUangJalans);

    const submitBtn = document.getElementById('submitBtn');
    const previewDiv = document.getElementById('uangJalanPreview');
    const form = document.getElementById('selectUangJalanForm');

    // Handle form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const selectedId = document.getElementById('selected_uang_jalan_id').value;

        if (selectedId) {
            // Redirect to create adjustment page with selected uang jalan id
            const createAdjustmentUrl = '{{ route("uang-jalan.adjustment.create") }}?uang_jalan_id=' + selectedId;
            window.location.href = createAdjustmentUrl;
        }
    });
});

// Modal functions
function openUangJalanModal() {
    document.getElementById('uangJalanModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';

    // Reset filter dan update display
    document.getElementById('modal-search').value = '';
    document.getElementById('modal-show').value = '10';
    filteredUangJalans = [...allUangJalans];
    currentPage = 1;
    itemsPerPage = 10;
    updateTableDisplay();
}

function closeUangJalanModal() {
    document.getElementById('uangJalanModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function selectUangJalan(id, nomorUangJalan, tanggal, supir, plat, total) {
    // Set hidden input value
    document.getElementById('selected_uang_jalan_id').value = id;

    // Set display input value
    document.getElementById('selected_uang_jalan_display').value = nomorUangJalan + ' - ' + supir + ' - Rp ' + total;

    // Update preview
    document.getElementById('preview-tanggal').textContent = tanggal || '-';
    document.getElementById('preview-total').textContent = 'Rp ' + total;
    document.getElementById('preview-supir').textContent = supir || '-';
    document.getElementById('preview-plat').textContent = plat || '-';

    // Show preview and enable submit button
    document.getElementById('uangJalanPreview').classList.remove('hidden');
    document.getElementById('submitBtn').disabled = false;

    // Close modal
    closeUangJalanModal();
}

function filterUangJalan() {
    const searchTerm = document.getElementById('modal-search').value.toLowerCase();

    filteredUangJalans = allUangJalans.filter(item => {
        return (item.nomor_uang_jalan && item.nomor_uang_jalan.toLowerCase().includes(searchTerm)) ||
               (item.supir && item.supir.toLowerCase().includes(searchTerm)) ||
               (item.no_plat && item.no_plat.toLowerCase().includes(searchTerm));
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

    filteredUangJalans.sort((a, b) => {
        let aVal = '', bVal = '';

        switch(column) {
            case 'no_uj':
                aVal = a.nomor_uang_jalan || '';
                bVal = b.nomor_uang_jalan || '';
                break;
            case 'tanggal':
                aVal = a.tanggal_uang_jalan || '';
                bVal = b.tanggal_uang_jalan || '';
                break;
            case 'nama_supir':
                aVal = a.supir || '';
                bVal = b.supir || '';
                break;
            case 'no_plat':
                aVal = a.no_plat || '';
                bVal = b.no_plat || '';
                break;
            case 'total':
                aVal = parseFloat(a.jumlah_total || 0);
                bVal = parseFloat(b.jumlah_total || 0);
                break;
            case 'status':
                aVal = 'Aktif';
                bVal = 'Aktif';
                break;
        }

        if (typeof aVal === 'number' && typeof bVal === 'number') {
            return currentSort.direction === 'asc' ? aVal - bVal : bVal - aVal;
        } else {
            if (currentSort.direction === 'asc') {
                return aVal.localeCompare(bVal);
            } else {
                return bVal.localeCompare(aVal);
            }
        }
    });

    updateTableDisplay();
}

function updateTableDisplay() {
    itemsPerPage = parseInt(document.getElementById('modal-show').value);
    const startIndex = (currentPage - 1) * itemsPerPage;
    const endIndex = startIndex + itemsPerPage;
    const paginatedData = filteredUangJalans.slice(startIndex, endIndex);

    const tbody = document.getElementById('uangJalanTableBody');
    tbody.innerHTML = '';

    paginatedData.forEach(item => {
        const tanggal = item.tanggal_uang_jalan ? new Date(item.tanggal_uang_jalan).toLocaleDateString('id-ID') : '-';
        const total = 'Rp ' + (item.jumlah_total ? parseFloat(item.jumlah_total).toLocaleString('id-ID') : '0');

        const row = document.createElement('tr');
        row.className = 'hover:bg-gray-50 cursor-pointer transition-colors';
        row.onclick = () => selectUangJalan(item.id, item.nomor_uang_jalan, tanggal, item.supir, item.no_plat, total.replace('Rp ', ''));

        row.innerHTML = `
            <td class="px-1 py-1">
                <div class="text-xs font-medium text-blue-600 hover:text-blue-800 leading-tight">
                    ${item.nomor_uang_jalan}
                </div>
            </td>
            <td class="px-1 py-1 text-xs text-gray-900">
                ${tanggal}
            </td>
            <td class="px-1 py-1 text-xs text-gray-900 truncate" style="max-width: 60px;" title="${item.supir || '-'}">
                ${item.supir || '-'}
            </td>
            <td class="px-1 py-1 text-xs text-gray-900 truncate" style="max-width: 60px;" title="${item.no_plat || '-'}">
                ${item.no_plat || '-'}
            </td>
            <td class="px-1 py-1 text-xs text-gray-900 font-medium">
                ${total}
            </td>
            <td class="px-1 py-1">
                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                    Aktif
                </span>
            </td>
        `;

        tbody.appendChild(row);
    });

    // Update pagination info
    const showingFrom = Math.min(startIndex + 1, filteredUangJalans.length);
    const showingTo = Math.min(endIndex, filteredUangJalans.length);

    document.getElementById('showingFrom').textContent = showingFrom;
    document.getElementById('showingTo').textContent = showingTo;
    document.getElementById('totalEntries').textContent = filteredUangJalans.length;
}

// Close modal when clicking outside
document.addEventListener('click', function(event) {
    const modal = document.getElementById('uangJalanModal');
    if (event.target === modal) {
        closeUangJalanModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeUangJalanModal();
    }
});
</script>
@endsection